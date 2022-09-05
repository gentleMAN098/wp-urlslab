<?php /** @noinspection SlowArrayOperationsInLoopInspection */

require_once URLSLAB_PLUGIN_DIR . '/includes/widgets/class-urlslab-widget.php';
require_once URLSLAB_PLUGIN_DIR . '/includes/class-urlslab-user-widget.php';
require_once URLSLAB_PLUGIN_DIR . '/includes/driver/class-urlslab-driver.php';

require_once URLSLAB_PLUGIN_DIR . '/includes/driver/class-urlslab-driver-file.php';
require_once URLSLAB_PLUGIN_DIR . '/includes/driver/class-urlslab-driver-s3.php';
require_once URLSLAB_PLUGIN_DIR . '/includes/driver/class-urlslab-driver-db.php';

require_once URLSLAB_PLUGIN_DIR . '/includes/services/class-urlslab-file-data.php';

// phpcs:disable WordPress.NamingConventions
class Urlslab_Media_Offloader_Widget extends Urlslab_Widget {

	private string $widget_slug;
	private string $widget_title;
	private string $widget_description;
	private string $landing_page_link;

	public const SETTING_NAME_IMPORT_POST_ATTACHMENTS_ON_BACKGROUND = 'urlslab_import_post_attachements';
	public const SETTING_DEFAULT_IMPORT_POST_ATTACHMENTS_ON_BACKGROUND = false;

	//automatically offload external images found in every page content (starting with damain name different as current page)
	public const SETTING_NAME_SAVE_EXTERNAL = 'urlslab_save_external_resources';
	public const SETTING_DEFAULT_SAVE_EXTERNAL = false;

	//automatically offload internal images found in every page content (starting with damain name same as current page)
	public const SETTING_NAME_SAVE_INTERNAL = 'urlslab_save_internal_resources';
	public const SETTING_DEFAULT_SAVE_INTERNAL = false;

	public const SETTING_NAME_NEW_FILE_DRIVER = 'urlslab_file_driver';
	public const SETTING_DEFAULT_NEW_FILE_DRIVER = Urlslab_Driver::DRIVER_LOCAL_FILE;

	//TRANSFER SETTINGS
	public const SETTING_NAME_TRANSFER_FROM_DRIVER_LOCAL_FILES = 'urlslab_transfer_all_data_from_local_files';
	public const SETTING_NAME_TRANSFER_FROM_DRIVER_S3 = 'urlslab_transfer_all_data_from_s3';
	public const SETTING_NAME_TRANSFER_FROM_DRIVER_DB = 'urlslab_transfer_all_data_from_database';
	public const SETTING_DEFAULT_TRANSFER_FROM_DRIVER_LOCAL_FILES = false;
	public const SETTING_DEFAULT_TRANSFER_FROM_DRIVER_S3 = false;
	public const SETTING_DEFAULT_TRANSFER_FROM_DRIVER_DB = false;

	//WEBP CONVERSION SETTINGS
	public const SETTING_NAME_USE_WEBP_ALTERNATIVE = 'urlslab_use_webp';
	public const SETTING_NAME_WEBP_TYPES_TO_CONVERT = 'urlslab_webp_types';
	public const SETTING_DEFAULT_WEBP_TYPES_TO_CONVERT = array( 'image/png', 'image/jpeg', 'image/bmp' );
	public const SETTING_NAME_WEPB_QUALITY = 'urlslab_webp_quality';
	public const SETTING_DEFAULT_WEPB_QUALITY = 80;

	// AVIF CONVERSION SETTINGS
	public const SETTING_NAME_USE_AVIF_ALTERNATIVE = 'urlslab_use_avif';
	public const SETTING_NAME_AVIF_TYPES_TO_CONVERT = 'urlslab_avif_types';
	public const SETTING_DEFAULT_AVIF_TYPES_TO_CONVERT = array( 'image/png', 'image/jpeg', 'image/bmp', 'image/gif' );
	public const SETTING_NAME_AVIF_QUALITY = 'urlslab_avif_quality';

	// quality: The accepted values are 0 (worst quality) through 100 (highest quality). Any integers out of this range are clamped to the 0-100 range.
	public const SETTING_DEFAULT_AVIF_QUALITY = 80;
	public const SETTING_NAME_AVIF_SPEED = 'urlslab_avif_speed';

	// speed: Default value 6. Accepted values are int the range of 0 (slowest) through 10 (fastest). Integers outside the 0-10 range are clamped.
	public const SETTING_DEFAULT_AVIF_SPEED = 5;


	//LAZY LOADING SETTINGS
	public const SETTING_NAME_IMG_LAZY_LOADING = 'urlslab_img_lazy';
	public const SETTING_NAME_VIDEO_LAZY_LOADING = 'urlslab_video_lazy';
	public const SETTING_NAME_YOUTUBE_LAZY_LOADING = 'urlslab_youtube_lazy';
	public const SETTING_NAME_YOUTUBE_API_KEY = 'urlslab_youtube_apikey';

	private $files = array();


	private $media_tags_attributes = array(
		'img' => array(
			'src',
			'data-src',
			'data-full-url',
			'srcset',
		),
		'video' => array(
			'src',
			'data-src',
		),
		'audio' => array(
			'src',
			'data-src',
		),
		'source' => array(
			'srcset',
			'data-srcset',
		),
	);

	/**
	 */
	public function __construct() {
		$this->widget_slug = 'urlslab-media-offloader';
		$this->widget_title = 'Media Files';
		$this->widget_description = 'Offload media files from local directory to database or S3';
		$this->landing_page_link = 'https://www.urlslab.com';
	}


	public function init_widget( Urlslab_Loader $loader ) {
		$loader->add_action( 'wp_handle_upload', $this, 'wp_handle_upload', 10, 1 );
		$loader->add_action( 'urlslab_content', $this, 'the_content' );
	}

	/**
	 * @return string
	 */
	public function get_widget_slug(): string {
		return $this->widget_slug;
	}

	/**
	 * @return string
	 */
	public function get_widget_title(): string {
		return 'Urlslab ' . $this->widget_title;
	}

	/**
	 * @return string
	 */
	public function get_widget_description(): string {
		return $this->widget_description;
	}

	/**
	 * @return string
	 */
	public function get_landing_page_link(): string {
		return $this->landing_page_link;
	}

	public function has_shortcode(): bool {
		return false;
	}

	public function wp_handle_upload( &$file, $overrides = false, $time = null ) {
		global $wpdb;
		$file_obj = new Urlslab_File_Data(
			array(
				'url' => $file['url'],
				'local_file' => $file['file'],
				'filetype' => $file['type'],
				'filename' => basename( $file['file'] ),
				'filesize' => filesize( $file['file'] ),
				'filestatus' => Urlslab_Driver::STATUS_NEW,
				'driver' => get_option( self::SETTING_NAME_NEW_FILE_DRIVER, self::SETTING_DEFAULT_NEW_FILE_DRIVER ),
			)
		);

		$data = array(
			'fileid' => $file_obj->get_fileid(),
			'url' => $file_obj->get_url(),
			'local_file' => $file_obj->get_local_file(),
			'filename' => $file_obj->get_filename(),
			'filesize' => $file_obj->get_filesize(),
			'filetype' => $file_obj->get_filetype(),
			'filestatus' => $file_obj->get_filestatus(),
			'driver' => $file_obj->get_driver(),
		);

		$result = $wpdb->query(
			$wpdb->prepare(
				'INSERT IGNORE INTO ' . URLSLAB_FILES_TABLE . // phpcs:ignore
				' (' . implode( ',', array_keys( $data ) ) . // phpcs:ignore
					') VALUES (%s, %s, %s, %s, %d, %s, %s, %s)',
				array_values( $data )
			)
		);

		if ( is_numeric( $result ) && 1 == $result ) {
			$driver = Urlslab_Driver::get_driver( $file_obj );
			if ( $driver->is_connected() ) {
				$driver->upload_content( $file_obj );
			}
		}

		return $file;
	}

	public function get_shortcode_content( $atts = array(), $content = null, $tag = '' ): string {
		return '';
	}

	public function the_content( DOMDocument $document ) {

		//TODO Lazy loading should be separated widget
		$this->process_lazy_loading( $document );

		try {
			$found_urls = array();
			$url_fileids = array();
			$elements_to_process = array();
			$element_ids_cnt = 0;

			//*********************************
			//find all elements to process
			//*********************************
			foreach ( $this->media_tags_attributes as $tag_name => $tag_attributes ) {
				$dom_elements = $document->getElementsByTagName( $tag_name );

				if ( empty( $dom_elements ) ) {
					continue;
				}
				foreach ( $dom_elements as $dom_element ) {
					//TODO we should allow to skip also any predefined pattern or regexp of urls (defined as setting)
					if ( $dom_element->hasAttribute( 'urlslab-skip' ) ) {
						continue;
					}
					foreach ( $tag_attributes as $attribute ) {
						if ( strlen( $dom_element->getAttribute( $attribute ) ) ) {
							$urlvalues = explode( ',', $dom_element->getAttribute( $attribute ) );
							foreach ( $urlvalues as $url_value ) {
								$url_val = explode( ' ', trim( $url_value ) );
								$file_obj = new Urlslab_File_Data( array( 'url' => $url_val[0] ) );

								if ( ! $dom_element->hasAttribute( 'urlslab-id' ) ) {
									$dom_element->setAttribute( 'urlslab-id', $element_ids_cnt++ );
								}
								$url_fileids[ $file_obj->get_fileid() ] = $url_val[0];
								$elements_to_process[ $tag_name ][ $dom_element->getAttribute( 'urlslab-id' ) ] = $dom_element;
							}
						}
					}
				}
			}

			//search urls in style attributes
			$xpath         = new DOMXPath( $document );
			$styled_elements = $xpath->query( "//*[contains(@style, 'url')]" );
			foreach ( $styled_elements as $styled_element ) {
				if ( preg_match_all( '/url\((.*?)\)/', $styled_element->getAttribute( 'style' ), $matches ) ) {
					foreach ( $matches[1] as $matched_url ) {
						$file_obj = new Urlslab_File_Data( array( 'url' => $matched_url ) );
						if ( ! $styled_element->hasAttribute( 'urlslab-id' ) ) {
							$styled_element->setAttribute( 'urlslab-id', $element_ids_cnt++ );
						}
						$url_fileids[ $file_obj->get_fileid() ] = $matched_url;
						$elements_to_process['style'][ $styled_element->getAttribute( 'urlslab-id' ) ] = $styled_element;
					}
				}
			}



			//*********************************
			//find files for elements
			//*********************************
			$this->files = $this->get_files_for_urls( array_keys( $url_fileids ) );


			//*********************************
			//process elements from page
			//*********************************

			foreach ( $elements_to_process as $tag_name => $tag_elements ) {
				foreach ( $tag_elements as $element_id => $dom_element ) {
					switch ( $tag_name ) {
						case 'img':
							$found_urls = array_merge( $this->process_img_tag( $dom_element, $document ), $found_urls );
							break;
						case 'source':
							$found_urls = array_merge( $this->process_source_tag( $dom_element, $document ), $found_urls );
							break;
						case 'audio': //for now we don't have alternatives for audio files
							$found_urls = array_merge( $this->replace_attributes( $dom_element ), $found_urls );
							break;
						case 'video': //for now we don't have alternatives for video files
							$found_urls = array_merge( $this->replace_attributes( $dom_element ), $found_urls );
							break;
						case 'style': //for now we don't have alternatives for video files
							$found_urls = array_merge( $this->replace_style_attribute( $dom_element ), $found_urls );
							break;
						default:
							$found_urls = array_merge( $this->replace_attributes( $dom_element ), $found_urls );
					}
				}
			}

			//remove files we know already from the list of missing files
			foreach ( $this->files as $fileid => $file_obj ) {
				unset( $url_fileids[ $fileid ] );
			}
			$this->schedule_missing_images( $url_fileids );

			if ( count( $found_urls ) > 0 ) {
				$this->update_last_seen_date( array_keys( $found_urls ) );
			}
		} catch ( Exception $e ) {
			//TODO log error
		}
	}

	/**
	 * this is workaround of parsing bug in php DOMDocument which doesn't understand the source as single tag
	 * @param DOMElement $dom_element
	 * @param $tag_name
	 * @return bool
	 */
	private function has_parent_node( DOMElement $dom_element, $tag_name ): bool {
		if ( property_exists( $dom_element, 'parentNode' ) ) {
			if ( property_exists( $dom_element->parentNode, 'tagName' ) && $dom_element->parentNode->tagName == $tag_name ) {
				return true;
			}
			return 'DOMElement' == get_class( $dom_element->parentNode ) && $this->has_parent_node( $dom_element->parentNode, $tag_name );
		}
		return false;
	}

	private function process_source_tag( DOMElement $dom_element, DOMDocument $document ) {
		$found_urls = array();
		if ( ! $dom_element->hasAttribute( 'type' ) && $this->has_parent_node( $dom_element, 'picture' ) ) {
			$files_in_srcset = array();
			$strValue = $dom_element->getAttribute( 'srcset' );
			if ( empty( $strValue ) ) {
				$strValue = $dom_element->getAttribute( 'data-srcset' );
			}
			$urlvalues = explode( ',', $strValue );
			foreach ( $urlvalues as $url_value ) {
				$url_val = explode( ' ', trim( $url_value ) );
				$old_file_obj = new Urlslab_File_Data( array( 'url' => $url_val[0] ) );
				if ( isset( $this->files[ $old_file_obj->get_fileid() ] ) ) {
					$found_urls[ $old_file_obj->get_fileid() ] = 1;
					if ( Urlslab_Driver::STATUS_ACTIVE === $this->files[ $old_file_obj->get_fileid() ]->get_filestatus() && $this->files[ $old_file_obj->get_fileid() ]->has_file_alternative() ) {
						foreach ( $this->files[ $old_file_obj->get_fileid() ]->get_alternatives() as $alternative_file_obj ) {
							$files_in_srcset[ $alternative_file_obj->get_filetype() ][] = array(
								'old_url' => $url_val[0],
								'new_url' => Urlslab_Driver::get_driver( $alternative_file_obj )->get_url( $alternative_file_obj ),
							);
						}
					}
				}
			}
			foreach ( $files_in_srcset as $type => $url_alternatives ) {
				if ( count( $url_alternatives ) == count( $urlvalues ) && ! $this->picture_has_source_for_type( $dom_element->parentNode, $type, $dom_element->hasAttribute( 'media' ) ? $dom_element->getAttribute( 'media' ) : false ) ) {
					//generate source element for this type - we have all alternatives
					$source_element = $document->createElement( 'source' );
					if ( $dom_element->hasAttribute( 'srcset' ) ) {
						$source_element->setAttribute( 'srcset', $dom_element->getAttribute( 'srcset' ) );
						foreach ( $url_alternatives as $arr_alternative ) {
							$source_element->setAttribute( 'srcset', str_replace( $arr_alternative['old_url'], $arr_alternative['new_url'], $source_element->getAttribute( 'srcset' ) ) );
						}
					}
					if ( $dom_element->hasAttribute( 'data-srcset' ) ) {
						$source_element->setAttribute( 'data-srcset', $dom_element->getAttribute( 'data-srcset' ) );
						$source_element->setAttribute( 'urlslab-lazy', 'yes' );
						foreach ( $url_alternatives as $arr_alternative ) {
							$source_element->setAttribute( 'data-srcset', str_replace( $arr_alternative['old_url'], $arr_alternative['new_url'], $source_element->getAttribute( 'data-srcset' ) ) );
						}
					}
					if ( $dom_element->hasAttribute( 'media' ) ) {
						$source_element->setAttribute( 'media', $dom_element->getAttribute( 'media' ) );
					}
					if ( $dom_element->hasAttribute( 'sizes' ) ) {
						$source_element->setAttribute( 'sizes', $dom_element->getAttribute( 'sizes' ) );
					}
					$source_element->setAttribute( 'type', $type );
					$dom_element->parentNode->insertBefore( $source_element, $dom_element );
				}
			}
			$found_urls = array_merge( $this->replace_attributes( $dom_element ), $found_urls );
			return $found_urls;
		}
		return array_merge( $this->replace_attributes( $dom_element ), $found_urls );
	}

	/**
	 * @param $dom_element
	 * @param $attributes
	 * @param $found_urls
	 * @param DOMDocument $document
	 * @return array
	 * @throws DOMException
	 */
	private function process_img_tag( $dom_element, DOMDocument $document ): array {
		$found_urls = array();
		if ( $this->has_parent_node( $dom_element, 'picture' ) ) {

			$lazy_loading = false;
			if ( ! empty( $dom_element->getAttribute( 'src' ) ) ) {
				$img_url_object = new Urlslab_File_Data( array( 'url' => $dom_element->getAttribute( 'src' ) ) );
			} else if ( ! empty( $dom_element->getAttribute( 'data-src' ) ) ) {
				$lazy_loading = true;
				$img_url_object = new Urlslab_File_Data( array( 'url' => $dom_element->getAttribute( 'data-src' ) ) );
			}

			if ( isset( $this->files[ $img_url_object->get_fileid() ] ) && $this->files[ $img_url_object->get_fileid() ]->has_file_alternative() ) {
				foreach ( $this->files[ $img_url_object->get_fileid() ]->get_alternatives() as $alternative_file_obj ) {
					if ( ! $this->picture_has_source_for_type( $dom_element->parentNode, $alternative_file_obj->get_filetype() ) ) {
						$source_element = $document->createElement( 'source' );
						$source_url = Urlslab_Driver::get_driver( $alternative_file_obj )->get_url( $alternative_file_obj );
						if ( $lazy_loading ) {
							$source_element->setAttribute( 'data-srcset', $source_url );
							$source_element->setAttribute( 'urlslab-lazy', 'yes' );
						} else {
							$source_element->setAttribute( 'srcset', $source_url );
						}
						$source_element->setAttribute( 'type', $alternative_file_obj->get_filetype() );
						$dom_element->parentNode->insertBefore( $source_element, $dom_element );
					}
				}
			}

			$found_urls = array_merge( $this->replace_attributes( $dom_element ), $found_urls );
		} else {
			//this is simple img tag
			$lazy_loading = false;
			if ( ! empty( $dom_element->getAttribute( 'src' ) ) ) {
				$img_url_object = new Urlslab_File_Data( array( 'url' => $dom_element->getAttribute( 'src' ) ) );
			} else if ( ! empty( $dom_element->getAttribute( 'data-src' ) ) ) {
				$lazy_loading = true;
				$img_url_object = new Urlslab_File_Data( array( 'url' => $dom_element->getAttribute( 'data-src' ) ) );
			}

			if ( isset( $this->files[ $img_url_object->get_fileid() ] ) && $this->files[ $img_url_object->get_fileid() ]->has_file_alternative() && count( $this->files[ $img_url_object->get_fileid() ]->get_alternatives() ) > 0 ) {
				//encapsulate img into picture element and add source tag for alternatives
				$picture_element = $document->createElement( 'picture' );

				$new_img_element = clone $dom_element;

				if ( $new_img_element->hasAttribute( 'srcset' ) ) {
					//create basic source with type from original img
					$source_element = $document->createElement( 'source' );
					if ( $lazy_loading ) {
						$source_element->setAttribute( 'data-srcset', $new_img_element->getAttribute( 'srcset' ) );
						$source_element->setAttribute( 'urlslab-lazy', 'yes' );
					} else {
						$source_element->setAttribute( 'srcset', $new_img_element->getAttribute( 'srcset' ) );
					}
					$new_img_element->removeAttribute( 'srcset' );

					if ( $new_img_element->hasAttribute( 'sizes' ) ) {
						$source_element->setAttribute( 'sizes', $new_img_element->getAttribute( 'sizes' ) );
						$new_img_element->removeAttribute( 'sizes' );
					}

					$picture_element->appendChild( $source_element );
					//process this source tag as other source elements
					$found_urls = array_merge( $this->process_source_tag( $source_element, $document ), $found_urls );
				}

				//add simple alternatives to src url
				foreach ( $this->files[ $img_url_object->get_fileid() ]->get_alternatives() as $alternative_file ) {
					$source_element = $document->createElement( 'source' );
					$source_url = Urlslab_Driver::get_driver( $alternative_file )->get_url( $alternative_file );
					if ( $lazy_loading ) {
						$source_element->setAttribute( 'data-srcset', $source_url );
						$source_element->setAttribute( 'urlslab-lazy', 'yes' );
					} else {
						$source_element->setAttribute( 'srcset', $source_url );
					}
					$source_element->setAttribute( 'type', $alternative_file->get_filetype() );
					$picture_element->appendChild( $source_element );
				}
				$picture_element->appendChild( $new_img_element );

				$dom_element->parentNode->replaceChild( $picture_element, $dom_element );

				$found_urls = array_merge( $this->replace_attributes( $new_img_element ), $found_urls );
			} else {
				$found_urls = array_merge( $this->replace_attributes( $dom_element ), $found_urls );
			}
		}
		return $found_urls;
	}

	private function get_files_for_urls( array $old_url_ids ) :array {
		if ( empty( $old_url_ids ) ) {
			return array();
		}
		global $wpdb;
		$new_urls = array();
		$results = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT * FROM ' . URLSLAB_FILES_TABLE . ' WHERE fileid in (' . trim( str_repeat( '%s,', count( $old_url_ids ) ), ',' ) . ')', // phpcs:ignore
				$old_url_ids
			),
			'ARRAY_A'
		);

		$arr_file_with_alternatives = array();

		foreach ( $results as $file_array ) {
			$file_obj = new Urlslab_File_Data( $file_array );
			$new_urls[ $file_obj->get_fileid() ] = $file_obj;
			if ( $file_obj->has_file_alternative() ) {
				$arr_file_with_alternatives[] = $file_obj->get_fileid();
			}
		}

		if ( ! empty( $arr_file_with_alternatives ) ) {
			$results = $wpdb->get_results(
				$wpdb->prepare(
					'SELECT f.*, a.fileid as parent_fileid FROM ' . URLSLAB_FILES_TABLE . ' as f INNER JOIN ' . URLSLAB_FILE_ALTERNATIVES_TABLE . ' as a ON (f.fileid = a.alternative_fileid) WHERE a.fileid in (' . trim( str_repeat( '%s,', count( $arr_file_with_alternatives ) ), ',' ) . ')', // phpcs:ignore
					$arr_file_with_alternatives
				),
				'ARRAY_A'
			);
			foreach ( $results as $file_array ) {
				$file_obj = new Urlslab_File_Data( $file_array );
				$new_urls[ $file_array['parent_fileid'] ]->add_alternative( $file_obj );
			}
		}
		return $new_urls;
	}

	private function schedule_missing_images( array $urls ) {
		$save_internal = get_option( self::SETTING_NAME_SAVE_INTERNAL, self::SETTING_DEFAULT_SAVE_INTERNAL );
		$save_external = get_option( self::SETTING_NAME_SAVE_EXTERNAL, self::SETTING_DEFAULT_SAVE_EXTERNAL );
		$default_driver = get_option( self::SETTING_NAME_NEW_FILE_DRIVER, self::SETTING_DEFAULT_NEW_FILE_DRIVER );
		if (
			! ( $save_internal || $save_external ) ) {
			return;
		}

		$placeholders = array();
		$values = array();

		foreach ( $urls as $fileid => $url ) {
			if ( ( urlslab_is_same_domain_url( $url ) && $save_internal ) || $save_external ) {
				$placeholders[] = '(%s, %s, %s, %s)';
				array_push( $values, $fileid, $url, Urlslab_Driver::STATUS_NEW, $default_driver );
			}
		}
		if ( ! empty( $placeholders ) ) {
			global $wpdb;
			$query = 'INSERT IGNORE INTO ' . URLSLAB_FILES_TABLE . ' (
                   fileid,
                   url,
                   filestatus,
                   driver) VALUES ' . implode( ', ', $placeholders );

			$wpdb->query( $wpdb->prepare( $query, $values ) ); // phpcs:ignore
		}
	}


	public function output_content() {
		global $_SERVER;
		if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
			return 'Path to file not detected.';
		}
		$path = pathinfo( $_SERVER['REQUEST_URI'] );
		$dirs = explode( '/', $path['dirname'] );
		$fileid = array_pop( $dirs );
		global $wpdb;

		$row = $wpdb->get_row( $wpdb->prepare( 'SELECT * from ' . URLSLAB_FILES_TABLE . ' WHERE fileid=%s', $fileid ), ARRAY_A ); // phpcs:ignore
		if ( empty( $row ) ) {
			status_header( 404 );
			exit( 'File not found' );
		}

		@set_time_limit( 0 );
		$file = new Urlslab_File_Data( $row );
		status_header( 200 );
		header( 'Content-Type: ' . $file->get_filetype() );
		header( 'Content-Disposition: inline; filename="' . $file->get_filename() . '"' );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Pragma: public' );

		//TODO define how long should be files cached (maybe each mime type should have own settings)
		$expires_offset = 9000;
		header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + $expires_offset ) . ' GMT' );
		header( "Cache-Control: public, max-age=$expires_offset" );
		header( 'Content-length: ' . $file->get_filesize() );

		$driver = Urlslab_Driver::get_driver( $file );
		$driver->output_file_content( $file );
	}

	private function update_last_seen_date( array $found_urls ) {
		if ( ! empty( $found_urls ) ) {
			global $wpdb;
			$query = 'UPDATE ' . URLSLAB_FILES_TABLE . ' SET last_seen = %s WHERE fileid IN (' . implode( ',', array_fill( 0, count( $found_urls ), '%s' ) ) . ')'; // phpcs:ignore
			array_unshift( $found_urls, gmdate( 'Y-m-d H:i:s' ) );
			$wpdb->query( $wpdb->prepare( $query, $found_urls ) ); // phpcs:ignore
		}
	}

	public function get_parent_page(): Urlslab_Admin_Page {
		return Urlslab_Page_Factory::get_instance()->get_page( 'urlslab-media-offloader' );
	}

	public function get_widget_tab(): string {
		return '';
	}

	public function render_widget_overview() {
		// TODO: Implement render_widget_overview() method.
	}

	public function get_thumbnail_demo_url(): string {
		return '';
	}

	public static function add_option() {
		add_option( self::SETTING_NAME_IMPORT_POST_ATTACHMENTS_ON_BACKGROUND, self::SETTING_DEFAULT_IMPORT_POST_ATTACHMENTS_ON_BACKGROUND, '', false );
		add_option( self::SETTING_NAME_SAVE_EXTERNAL, self::SETTING_DEFAULT_SAVE_EXTERNAL, '', true );
		add_option( self::SETTING_NAME_SAVE_INTERNAL, self::SETTING_DEFAULT_SAVE_INTERNAL, '', true );
		add_option( self::SETTING_NAME_NEW_FILE_DRIVER, self::SETTING_DEFAULT_NEW_FILE_DRIVER, '', true );
		add_option( self::SETTING_NAME_TRANSFER_FROM_DRIVER_LOCAL_FILES, self::SETTING_DEFAULT_TRANSFER_FROM_DRIVER_LOCAL_FILES, '', true );
		add_option( self::SETTING_NAME_TRANSFER_FROM_DRIVER_S3, self::SETTING_DEFAULT_TRANSFER_FROM_DRIVER_S3, '', true );
		add_option( self::SETTING_NAME_TRANSFER_FROM_DRIVER_DB, self::SETTING_DEFAULT_TRANSFER_FROM_DRIVER_DB, '', true );

		add_option( self::SETTING_NAME_USE_AVIF_ALTERNATIVE, false, '', true );
		add_option( self::SETTING_NAME_USE_WEBP_ALTERNATIVE, false, '', true );
		add_option( self::SETTING_NAME_WEPB_QUALITY, self::SETTING_DEFAULT_WEPB_QUALITY, '', true );
		add_option( self::SETTING_NAME_AVIF_QUALITY, self::SETTING_DEFAULT_AVIF_QUALITY, '', true );
		add_option( self::SETTING_NAME_AVIF_SPEED, self::SETTING_DEFAULT_AVIF_SPEED, '', true );
		add_option( self::SETTING_NAME_AVIF_TYPES_TO_CONVERT, self::SETTING_DEFAULT_AVIF_TYPES_TO_CONVERT, '', true );
		add_option( self::SETTING_NAME_WEBP_TYPES_TO_CONVERT, self::SETTING_DEFAULT_WEBP_TYPES_TO_CONVERT, '', true );

		add_option( self::SETTING_NAME_IMG_LAZY_LOADING, false, '', true );
		add_option( self::SETTING_NAME_VIDEO_LAZY_LOADING, false, '', true );
		add_option( self::SETTING_NAME_YOUTUBE_LAZY_LOADING, false, '', true );
		add_option( self::SETTING_NAME_YOUTUBE_API_KEY, '', '', true );
	}

	public static function update_settings( array $new_settings ) {
		if ( isset( $new_settings[ self::SETTING_NAME_IMPORT_POST_ATTACHMENTS_ON_BACKGROUND ] ) &&
			! empty( $new_settings[ self::SETTING_NAME_IMPORT_POST_ATTACHMENTS_ON_BACKGROUND ] ) ) {
			update_option(
				self::SETTING_NAME_IMPORT_POST_ATTACHMENTS_ON_BACKGROUND,
				$new_settings[ self::SETTING_NAME_IMPORT_POST_ATTACHMENTS_ON_BACKGROUND ]
			);
		} else {
			update_option(
				self::SETTING_NAME_IMPORT_POST_ATTACHMENTS_ON_BACKGROUND,
				false
			);
		}

		if ( isset( $new_settings[ self::SETTING_NAME_SAVE_EXTERNAL ] ) &&
			 ! empty( $new_settings[ self::SETTING_NAME_SAVE_EXTERNAL ] ) ) {
			update_option(
				self::SETTING_NAME_SAVE_EXTERNAL,
				$new_settings[ self::SETTING_NAME_SAVE_EXTERNAL ]
			);
		} else {
			update_option(
				self::SETTING_NAME_SAVE_EXTERNAL,
				false
			);
		}

		if ( isset( $new_settings[ self::SETTING_NAME_SAVE_INTERNAL ] ) &&
			 ! empty( $new_settings[ self::SETTING_NAME_SAVE_INTERNAL ] ) ) {
			update_option(
				self::SETTING_NAME_SAVE_INTERNAL,
				$new_settings[ self::SETTING_NAME_SAVE_INTERNAL ]
			);
		} else {
			update_option(
				self::SETTING_NAME_SAVE_INTERNAL,
				false
			);
		}

		if ( isset( $new_settings[ self::SETTING_NAME_NEW_FILE_DRIVER ] ) &&
			 ! empty( $new_settings[ self::SETTING_NAME_NEW_FILE_DRIVER ] ) ) {
			update_option(
				self::SETTING_NAME_NEW_FILE_DRIVER,
				$new_settings[ self::SETTING_NAME_NEW_FILE_DRIVER ]
			);
		}

		if ( isset( $new_settings[ self::SETTING_NAME_TRANSFER_FROM_DRIVER_LOCAL_FILES ] ) &&
			 ! empty( $new_settings[ self::SETTING_NAME_TRANSFER_FROM_DRIVER_LOCAL_FILES ] ) ) {
			update_option(
				self::SETTING_NAME_TRANSFER_FROM_DRIVER_LOCAL_FILES,
				$new_settings[ self::SETTING_NAME_TRANSFER_FROM_DRIVER_LOCAL_FILES ]
			);
		} else {
			update_option(
				self::SETTING_NAME_TRANSFER_FROM_DRIVER_LOCAL_FILES,
				false
			);
		}

		if ( isset( $new_settings[ self::SETTING_NAME_TRANSFER_FROM_DRIVER_S3 ] ) &&
			 ! empty( $new_settings[ self::SETTING_NAME_TRANSFER_FROM_DRIVER_S3 ] ) ) {
			update_option(
				self::SETTING_NAME_TRANSFER_FROM_DRIVER_S3,
				$new_settings[ self::SETTING_NAME_TRANSFER_FROM_DRIVER_S3 ]
			);
		} else {
			update_option(
				self::SETTING_NAME_TRANSFER_FROM_DRIVER_S3,
				false
			);
		}

		if ( isset( $new_settings[ self::SETTING_NAME_TRANSFER_FROM_DRIVER_DB ] ) &&
			 ! empty( $new_settings[ self::SETTING_NAME_TRANSFER_FROM_DRIVER_DB ] ) ) {
			update_option(
				self::SETTING_NAME_TRANSFER_FROM_DRIVER_DB,
				$new_settings[ self::SETTING_NAME_TRANSFER_FROM_DRIVER_DB ]
			);
		} else {
			update_option(
				self::SETTING_NAME_TRANSFER_FROM_DRIVER_DB,
				false
			);
		}
	}

	public static function update_option_image_optimisation( array $new_settings ) {
		if ( isset( $new_settings[ self::SETTING_NAME_USE_WEBP_ALTERNATIVE ] ) &&
			 ! empty( $new_settings[ self::SETTING_NAME_USE_WEBP_ALTERNATIVE ] ) ) {
			update_option(
				self::SETTING_NAME_USE_WEBP_ALTERNATIVE,
				$new_settings[ self::SETTING_NAME_USE_WEBP_ALTERNATIVE ]
			);
		} else {
			update_option(
				self::SETTING_NAME_USE_WEBP_ALTERNATIVE,
				false
			);
		}

		if ( isset( $new_settings[ self::SETTING_NAME_WEBP_TYPES_TO_CONVERT ] ) &&
			 ! empty( $new_settings[ self::SETTING_NAME_WEBP_TYPES_TO_CONVERT ] ) ) {
			update_option(
				self::SETTING_NAME_WEBP_TYPES_TO_CONVERT,
				$new_settings[ self::SETTING_NAME_WEBP_TYPES_TO_CONVERT ]
			);
		} else {
			update_option(
				self::SETTING_NAME_WEBP_TYPES_TO_CONVERT,
				array()
			);
		}

		if ( isset( $new_settings[ self::SETTING_NAME_USE_AVIF_ALTERNATIVE ] ) &&
			 ! empty( $new_settings[ self::SETTING_NAME_USE_AVIF_ALTERNATIVE ] ) ) {
			update_option(
				self::SETTING_NAME_USE_AVIF_ALTERNATIVE,
				$new_settings[ self::SETTING_NAME_USE_AVIF_ALTERNATIVE ]
			);
		} else {
			update_option(
				self::SETTING_NAME_USE_AVIF_ALTERNATIVE,
				false
			);
		}

		if ( isset( $new_settings[ self::SETTING_NAME_AVIF_TYPES_TO_CONVERT ] ) &&
			 ! empty( $new_settings[ self::SETTING_NAME_AVIF_TYPES_TO_CONVERT ] ) ) {
			update_option(
				self::SETTING_NAME_AVIF_TYPES_TO_CONVERT,
				$new_settings[ self::SETTING_NAME_AVIF_TYPES_TO_CONVERT ]
			);
		} else {
			update_option(
				self::SETTING_NAME_AVIF_TYPES_TO_CONVERT,
				array()
			);
		}

		if ( isset( $new_settings[ self::SETTING_NAME_WEPB_QUALITY ] ) &&
			 ! empty( $new_settings[ self::SETTING_NAME_WEPB_QUALITY ] ) ) {
			update_option(
				self::SETTING_NAME_WEPB_QUALITY,
				$new_settings[ self::SETTING_NAME_WEPB_QUALITY ]
			);
		}

		if ( isset( $new_settings[ self::SETTING_NAME_AVIF_QUALITY ] ) &&
			 ! empty( $new_settings[ self::SETTING_NAME_AVIF_QUALITY ] ) ) {
			update_option(
				self::SETTING_NAME_AVIF_QUALITY,
				$new_settings[ self::SETTING_NAME_AVIF_QUALITY ]
			);
		}

		if ( isset( $new_settings[ self::SETTING_NAME_AVIF_SPEED ] ) &&
			 ! empty( $new_settings[ self::SETTING_NAME_AVIF_SPEED ] ) ) {
			update_option(
				self::SETTING_NAME_AVIF_SPEED,
				$new_settings[ self::SETTING_NAME_AVIF_SPEED ]
			);
		}
	}

	public static function update_option_lazy_loading( array $new_settings ) {
		if ( isset( $new_settings[ self::SETTING_NAME_IMG_LAZY_LOADING ] ) &&
			 ! empty( $new_settings[ self::SETTING_NAME_IMG_LAZY_LOADING ] ) ) {
			update_option(
				self::SETTING_NAME_IMG_LAZY_LOADING,
				$new_settings[ self::SETTING_NAME_IMG_LAZY_LOADING ]
			);
		} else {
			update_option(
				self::SETTING_NAME_IMG_LAZY_LOADING,
				false
			);
		}

		if ( isset( $new_settings[ self::SETTING_NAME_VIDEO_LAZY_LOADING ] ) &&
			 ! empty( $new_settings[ self::SETTING_NAME_VIDEO_LAZY_LOADING ] ) ) {
			update_option(
				self::SETTING_NAME_VIDEO_LAZY_LOADING,
				$new_settings[ self::SETTING_NAME_VIDEO_LAZY_LOADING ]
			);
		} else {
			update_option(
				self::SETTING_NAME_VIDEO_LAZY_LOADING,
				false
			);
		}

		if ( isset( $new_settings[ self::SETTING_NAME_YOUTUBE_LAZY_LOADING ] ) &&
			 ! empty( $new_settings[ self::SETTING_NAME_YOUTUBE_LAZY_LOADING ] ) ) {
			update_option(
				self::SETTING_NAME_YOUTUBE_LAZY_LOADING,
				$new_settings[ self::SETTING_NAME_YOUTUBE_LAZY_LOADING ]
			);
		} else {
			update_option(
				self::SETTING_NAME_YOUTUBE_LAZY_LOADING,
				false
			);
		}

		if ( isset( $new_settings[ self::SETTING_NAME_YOUTUBE_API_KEY ] ) &&
			! empty( $new_settings[ self::SETTING_NAME_YOUTUBE_API_KEY ] ) ) {
			update_option(
				self::SETTING_NAME_YOUTUBE_API_KEY,
				$new_settings[ self::SETTING_NAME_YOUTUBE_API_KEY ]
			);
		}

	}

	/**
	 * @param $attributes
	 * @param $dom_element
	 * @param array $found_urls
	 * @param array $url_fileids
	 */
	private function replace_attributes( $dom_element ): array {
		$found_urls = array();
		foreach ( $this->media_tags_attributes[ $dom_element->tagName ] as $attribute ) {
			/** @noinspection SlowArrayOperationsInLoopInspection */
			$found_urls = array_merge_recursive( $this->replace_attribute( $dom_element, $attribute ), $found_urls );
		}
		return $found_urls;
	}

	private function replace_style_attribute( $dom_element ): array {
		$found_urls = array();
		/** @noinspection SlowArrayOperationsInLoopInspection */
		$found_urls = array_merge_recursive( $this->replace_attribute( $dom_element, 'style' ), $found_urls );
		return $found_urls;
	}

	/**
	 * @param $dom_element
	 * @param $attribute
	 * @param array $found_urls
	 * @return array
	 */
	private function replace_attribute( $dom_element, $attribute ): array {
		$found_urls = array();
		if ( $dom_element->hasAttribute( $attribute ) && strlen( $dom_element->getAttribute( $attribute ) ) > 0 ) {
			switch ( $attribute ) {
				case 'srcset':
					$urlvalues = explode( ',', $dom_element->getAttribute( $attribute ) );
					foreach ( $urlvalues as $url_value ) {
						$url_val = explode( ' ', trim( $url_value ) );
						$old_file_obj = new Urlslab_File_Data( array( 'url' => $url_val[0] ) );
						if ( isset( $this->files[ $old_file_obj->get_fileid() ] ) && Urlslab_Driver::STATUS_ACTIVE === $this->files[ $old_file_obj->get_fileid() ]->get_filestatus() ) {
							$source_url = Urlslab_Driver::get_driver( $this->files[ $old_file_obj->get_fileid() ] )->get_url( $this->files[ $old_file_obj->get_fileid() ] );
							$dom_element->setAttribute( $attribute, str_replace( $url_val[0], $source_url, $dom_element->getAttribute( $attribute ) ) );
							$found_urls[ $old_file_obj->get_fileid() ] = 1;
						}
					}
					break;
				case 'style':
					if ( preg_match_all( '/url\((.*?)\)/', $dom_element->getAttribute( $attribute ), $matches ) ) {
						foreach ( $matches[1] as $matched_url ) {
							$old_file_obj = new Urlslab_File_Data( array( 'url' => $matched_url ) );
							if ( isset( $this->files[ $old_file_obj->get_fileid() ] ) && Urlslab_Driver::STATUS_ACTIVE === $this->files[ $old_file_obj->get_fileid() ]->get_filestatus() ) {
								$source_url = Urlslab_Driver::get_driver( $this->files[ $old_file_obj->get_fileid() ] )->get_url( $this->files[ $old_file_obj->get_fileid() ] );
								$dom_element->setAttribute( $attribute, str_replace( $matched_url, $source_url, $dom_element->getAttribute( $attribute ) ) );
								$found_urls[ $old_file_obj->get_fileid() ] = 1;
							}
						}
					}
					break;
				default:
					$url = $dom_element->getAttribute( $attribute );
					$old_file_obj = new Urlslab_File_Data( array( 'url' => $url ) );
					if ( isset( $this->files[ $old_file_obj->get_fileid() ] ) && Urlslab_Driver::STATUS_ACTIVE === $this->files[ $old_file_obj->get_fileid() ]->get_filestatus() ) {
						$source_url = Urlslab_Driver::get_driver( $this->files[ $old_file_obj->get_fileid() ] )->get_url( $this->files[ $old_file_obj->get_fileid() ] );
						$dom_element->setAttribute( $attribute, $source_url );
						$found_urls[ $old_file_obj->get_fileid() ] = 1;
					}
			}
		}
		return $found_urls;
	}

	private function picture_has_source_for_type( DOMElement $picture_element, $filetype, $media = false ): bool {
		foreach ( $picture_element->childNodes as $node ) {
			if ( property_exists( $node, 'tagName' ) && 'source' == $node->tagName && $node->getAttribute( 'type' ) == $filetype && ( false === $media || $node->getAttribute( 'media' ) === $media ) ) {
				return true;
			}
		}
		return false;
	}

	private function add_img_lazy_loading( DOMElement $dom_element ) {
		if ( $dom_element->hasAttribute( 'src' ) ) {
			$dom_element->setAttribute( 'data-src', $dom_element->getAttribute( 'src' ) );
			$dom_element->removeAttribute( 'src' );
		}

		if ( $dom_element->hasAttribute( 'srcset' ) ) {
			$dom_element->setAttribute( 'data-srcset', $dom_element->getAttribute( 'srcset' ) );
			$dom_element->removeAttribute( 'srcset' );
		}

		if ( $dom_element->hasAttribute( 'style' ) ) {
			$dom_element->setAttribute( 'data-urlslabstyle', $dom_element->getAttribute( 'style' ) );
		}
		$dom_element->setAttribute( 'style', 'opacity: 0; transition: opacity .5s;' );

		if ( ! $dom_element->hasAttribute( 'loading' ) ) {
			$dom_element->setAttribute( 'loading', 'lazy' );
		}
		$dom_element->setAttribute( 'urlslab-lazy', 'yes' );
	}

	private function add_source_lazy_loading( DOMElement $dom_element ) {
		if ( $this->has_parent_node( $dom_element, 'picture' ) ) {
			if ( $dom_element->hasAttribute( 'srcset' ) ) {
				$dom_element->setAttribute( 'data-srcset', $dom_element->getAttribute( 'srcset' ) );
				$dom_element->removeAttribute( 'srcset' );
			}
			$dom_element->setAttribute( 'urlslab-lazy', 'yes' );
		}
	}

	private function add_youtube_lazy_loading( DOMDocument $document ) {
		$youtube_ids = array();

		//find all youtube iframes
		$iframe_elements = $document->getElementsByTagName( 'iframe' );
		foreach ( $iframe_elements as $element ) {
			if ( $element->hasAttribute( 'src' ) ) {
				$ytid = $this->get_youtube_videoid( $element->getAttribute( 'src' ) );
				if ( $ytid ) {
					$youtube_ids[ $ytid ] = $ytid;
				}
			}
		}

		//find elementor blocks
		$xpath         = new DOMXPath( $document );
		$elementor_divs = $xpath->query( "//div[contains(@class, 'elementor-widget-video')]" );
		foreach ( $elementor_divs as $element ) {
			if ( $element->hasAttribute( 'data-settings' ) ) {
				$json = json_decode( $element->getAttribute( 'data-settings' ) );
				if ( is_object( $json ) && property_exists( $json, 'youtube_url' ) ) {
					$ytid = $this->get_youtube_videoid( $json->youtube_url );
					if ( $ytid ) {
						$youtube_ids[ $ytid ] = $ytid;
					}
				}
			}
		}

		//find all elements with data-ytid parameter
		$xpath         = new DOMXPath( $document );
		$yt_elements = $xpath->query( '//*[@data-ytid]' );
		foreach ( $yt_elements as $yt_element ) {
			$ytid = $yt_element->getAttribute( 'data-ytid' );
			$youtube_ids[ $ytid ] = $ytid;
		}


		if ( empty( $youtube_ids ) ) {
			return; //no yt videos in page
		}

		$video_objects = $this->get_youtube_videos( array_keys( $youtube_ids ) );

		//replace iframe with placeholder
		foreach ( $iframe_elements as $element ) {
			if ( $element->hasAttribute( 'src' ) ) {
				$ytid = $this->get_youtube_videoid( $element->getAttribute( 'src' ) );
				if ( $ytid ) {
					$this->replace_youtube_element_with_placeholder( $document, $element, $video_objects, $ytid );
				}
			}
		}

		//replace elementor objects with placeholder
		foreach ( $elementor_divs as $element ) {
			if ( $element->hasAttribute( 'data-settings' ) ) {
				$json = json_decode( $element->getAttribute( 'data-settings' ) );
				if ( is_object( $json ) && property_exists( $json, 'youtube_url' ) ) {
					$ytid = $this->get_youtube_videoid( $json->youtube_url );
					if ( $ytid ) {
						$this->emhance_elementor_element_with_placeholder( $document, $element, $video_objects, $ytid );
					}
				}
			}
		}

		//add schema to all elements with attribute data-ytid
		$xpath         = new DOMXPath( $document );
		$yt_elements = $xpath->query( '//*[@data-ytid]' );
		foreach ( $yt_elements as $yt_element ) {
			$ytid = $yt_element->getAttribute( 'data-ytid' );
			if ( isset( $video_objects[ $ytid ] ) && Urlslab_Youtube_Data::YOUTUBE_AVAILABLE === $video_objects[ $ytid ]->get_status() ) {
				$this->append_video_schema( $document, $yt_element, $video_objects[ $ytid ] );
			}
		}

	}

	private function emhance_elementor_element_with_placeholder( DOMDocument $document, DOMElement $element, $video_objects, $ytid ):bool {
		$youtube_loader = $document->createElement( 'div' );
		$youtube_loader->setAttribute( 'class', 'youtube_urlslab_loader youtube_urlslab_loader--elementor' );
		$youtube_loader->setAttribute( 'data-ytid', $ytid );

		$youtube_img = $document->createElement( 'img' );
		$youtube_img->setAttribute( 'class', 'youtube_urlslab_loader--img' );
		$youtube_img->setAttribute( 'data-src', 'https://i.ytimg.com/vi/' . $ytid . '/hqdefault.jpg' );
		$youtube_img->setAttribute( 'style', 'opacity: 0; transition: opacity .5s;' );
		if ( isset( $video_objects[ $ytid ] ) ) {
			$youtube_img->setAttribute( 'alt', 'Youtube video: ' . $video_objects[ $ytid ]->get_title() );
		}
		$youtube_img->setAttribute( 'urlslab-lazy', 'yes' );
		$youtube_loader->appendChild( $youtube_img );

		$xpath         = new DOMXPath( $document );
		$child    = $xpath->query( "//div[@data-id='" . $element->getAttribute( 'data-id' ) . "']//div[contains(@class, 'elementor-video')]" );
		if ( $child->length ) {
			$child->item( 0 )->appendChild( $youtube_loader );
		}

		return true;
	}




	private function replace_youtube_element_with_placeholder( DOMDocument $document, DOMElement $element, $video_objects, $ytid ):bool {
		$youtube_loader = $document->createElement( 'div' );
		$youtube_loader->setAttribute( 'class', 'youtube_urlslab_loader' );
		$youtube_loader->setAttribute( 'data-ytid', $ytid );
		if ( $element->hasAttribute( 'width' ) ) {
			$youtube_loader->setAttribute( 'width', $element->getAttribute( 'width' ) );
		}
		if ( $element->hasAttribute( 'height' ) ) {
			$youtube_loader->setAttribute( 'height', $element->getAttribute( 'height' ) );
		}

		$youtube_img = $document->createElement( 'img' );
		$youtube_img->setAttribute( 'class', 'youtube_urlslab_loader--img' );
		$youtube_img->setAttribute( 'data-src', 'https://i.ytimg.com/vi/' . $ytid . '/hqdefault.jpg' );
		$youtube_img->setAttribute( 'style', 'opacity: 0; transition: opacity .5s;' );
		if ( isset( $video_objects[ $ytid ] ) ) {
			$youtube_img->setAttribute( 'alt', 'Youtube video: ' . $video_objects[ $ytid ]->get_title() );
		}
		$youtube_img->setAttribute( 'urlslab-lazy', 'yes' );
		$youtube_loader->appendChild( $youtube_img );
		$element->parentNode->replaceChild( $youtube_loader, $element );

		return true;
	}

	private function append_video_schema( DOMDocument $document, DOMElement $youtube_loader, Urlslab_Youtube_Data $youtube_obj ) {
		if ( ! empty( $youtube_obj->get_microdata() ) ) {
			$schema = $document->createElement( 'div' );
			$schema->setAttribute( 'itemscope', null );
			$schema->setAttribute( 'itemtype', 'https://schema.org/VideoObject' );
			$schema->setAttribute( 'itemprop', 'video' );
			$this->append_meta_attribute( $document, $schema, 'name', $youtube_obj->get_title() );
			$this->append_meta_attribute( $document, $schema, 'description', $youtube_obj->get_description() );
			$this->append_meta_attribute( $document, $schema, 'thumbnailUrl', $youtube_obj->get_thumbnail_url(), 'link' );
			$this->append_meta_attribute( $document, $schema, 'contentUrl', 'https://www.youtube.com/watch?v=' . $youtube_obj->get_videoid(), 'link' );
			$this->append_meta_attribute( $document, $schema, 'embedUrl', 'https://www.youtube.com/embed/' . $youtube_obj->get_videoid(), 'link' );
			$this->append_meta_attribute( $document, $schema, 'duration', $youtube_obj->get_duration() );
			$this->append_meta_attribute( $document, $schema, 'uploadDate', $youtube_obj->get_published_at() );
			$youtube_loader->appendChild( $schema );
		}
	}

	private function append_meta_attribute( DOMDocument $document, DOMElement $schema, $name, $content, $element_type = 'meta' ) {
		$meta = $document->createElement( $element_type );
		$meta->setAttribute( 'itemprop', $name );
		$meta->setAttribute( 'content', $content );
		$schema->appendChild( $meta );
	}

	private function get_youtube_videoid( $url ) {
		if ( preg_match( "/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user|shorts)\/))([^\?&\"'>]+)/", $url, $matches ) ) {
			return $matches[1];
		}
		return false;
	}

	private function get_youtube_videos( array $youtube_ids ):array {
		if ( empty( $youtube_ids ) ) {
			return array();
		}
		global $wpdb;
		$videos = array();
		$results = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT * FROM ' . URLSLAB_YOUTUBE_CACHE_TABLE . ' WHERE videoid in (' . trim( str_repeat( '%s,', count( $youtube_ids ) ), ',' ) . ')', // phpcs:ignore
				$youtube_ids
			),
			'ARRAY_A'
		);

		foreach ( $results as $row ) {
			$video_obj = new Urlslab_Youtube_Data( $row );
			$videos[ $video_obj->get_videoid() ] = $video_obj;
		}


		//schedule missing videos
		$placeholders = array();
		$values = array();
		foreach ( $youtube_ids as $videoid ) {
			if ( ! isset( $videos[ $videoid ] ) ) {
				$placeholders[] = '(%s, %s)';
				array_push( $values, $videoid, Urlslab_Youtube_Data::YOUTUBE_NEW );
			}
		}
		if ( ! empty( $placeholders ) ) {
			global $wpdb;
			$query = 'INSERT IGNORE INTO ' . URLSLAB_YOUTUBE_CACHE_TABLE . ' (
                   videoid,
                   status) VALUES ' . implode( ', ', $placeholders );
			$wpdb->query( $wpdb->prepare( $query, $values ) ); // phpcs:ignore
		}

		return $videos;
	}

	private function add_video_lazy_loading( DOMElement $dom_element ) {

		if ( $dom_element->hasAttribute( 'style' ) ) {
			$dom_element->setAttribute( 'data-urlslabstyle', $dom_element->getAttribute( 'style' ) );
		}
		$dom_element->setAttribute( 'style', 'opacity: 0;' );

		if ( $dom_element->hasAttribute( 'src' ) ) {
			$dom_element->setAttribute( 'data-src', $dom_element->getAttribute( 'src' ) );
			$dom_element->removeAttribute( 'src' );
		}

		$dom_element->setAttribute( 'urlslab-lazy', 'yes' );
	}

	private function process_lazy_loading( DOMDocument $document ) {
		if ( get_option( self::SETTING_NAME_YOUTUBE_LAZY_LOADING, false ) ) {
			$this->add_youtube_lazy_loading( $document );
		}

		if ( get_option( self::SETTING_NAME_IMG_LAZY_LOADING, false ) ) {
			$dom_elements = $document->getElementsByTagName( 'img' );
			foreach ( $dom_elements as $element ) {
				$this->add_img_lazy_loading( $element );
			}
			$dom_elements = $document->getElementsByTagName( 'source' );
			foreach ( $dom_elements as $element ) {
				$this->add_source_lazy_loading( $element );
			}
		}
		if ( get_option( self::SETTING_NAME_VIDEO_LAZY_LOADING, false ) ) {
			$dom_elements = $document->getElementsByTagName( 'video' );
			foreach ( $dom_elements as $element ) {
				$this->add_video_lazy_loading( $element );
			}
		}

	}
}
