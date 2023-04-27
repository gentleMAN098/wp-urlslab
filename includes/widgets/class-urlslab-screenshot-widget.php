<?php

use Elementor\Plugin;

class Urlslab_Screenshot_Widget extends Urlslab_Widget {
	public const SLUG = 'urlslab-screenshot';

	const SETTING_NAME_SCREENSHOT_REFRESH_INTERVAL = 'urlslab-scr-refresh';
	const SETTING_NAME_SHEDULE_SCRRENSHOT = 'urlslab-scr-schedule';
	public const SCHEDULE_SHORTCODE = 'S';
	public const SCHEDULE_ALL_INTERNALS = 'I';
	public const SCHEDULE_ALL = 'A';
	public const SCHEDULE_NEVER = 'N';

	public function get_widget_labels(): array {
		return array( self::LABEL_PAID );
	}


	public function init_widget() {
		Urlslab_Loader::get_instance()->add_action(
			'init',
			$this,
			'hook_callback',
			10,
			0
		);
		Urlslab_Loader::get_instance()->add_action(
			'widgets_init',
			$this,
			'init_wp_widget',
			10,
			0
		);
	}

	public function hook_callback() {
		add_shortcode(
			$this->get_widget_slug(),
			array( $this, 'get_shortcode_content' )
		);
	}

	public function get_widget_slug(): string {
		return Urlslab_Screenshot_Widget::SLUG;
	}

	public function get_widget_title(): string {
		return __( 'Screenshots' );
	}

	public function get_widget_description(): string {
		return __(
			'Improve the appeal of the content by creating automatically generated screenshots'
		);
	}

	public function get_attribute_values(
		$atts = array(),
		$content = null,
		$tag = ''
	) {
		$atts = array_change_key_case( (array) $atts );

		return shortcode_atts(
			array(
				'width'           => '100%',
				'height'          => '100%',
				'alt'             => 'Screenshot taken by URLsLab service',
				'default-image'   => '',
				'url'             => '',
				'screenshot-type' => Urlslab_Url_Row::SCREENSHOT_TYPE_CAROUSEL,
			),
			$atts,
			$tag
		);
	}

	public function get_shortcode_content( $atts = array(), $content = null, $tag = '' ): string {
		if (
			(
				isset( $_REQUEST['action'] )
				&& false !== strpos( $_REQUEST['action'], 'elementor' )
			)
			|| in_array(
				get_post_status(),
				array( 'trash', 'auto-draft', 'inherit' )
			)
			|| ( class_exists( '\Elementor\Plugin' )
				 && Plugin::$instance->editor->is_edit_mode() )
		) {
			$html_attributes = array();
			foreach ( $this->get_attribute_values( $atts, $content, $tag ) as $id => $value ) {
				$html_attributes[] = '<b>' . esc_html( $id ) . '</b>="<i>' . esc_html( $value ) . '</i>"';
			}

			return '<div style="padding: 20px; background-color: #f5f5f5; border: 1px solid #ccc;text-align: center">[<b>urlslab-screenshot</b> ' . implode( ', ', $html_attributes ) . ']</div>';
		}
		$urlslab_atts = $this->get_attribute_values( $atts, $content, $tag );

		try {
			if ( ! empty( $urlslab_atts['url'] ) ) {
				$url_data = Urlslab_Url_Data_Fetcher::get_instance()->load_and_schedule_url( new Urlslab_Url( $urlslab_atts['url'] ) );

				if ( ! empty( $url_data ) ) {
					if (
						empty( $url_data->get_scr_status() ) &&
						Urlslab_Screenshot_Widget::SCHEDULE_NEVER != Urlslab_User_Widget::get_instance()->get_widget( Urlslab_Screenshot_Widget::SLUG )->get_option( Urlslab_Screenshot_Widget::SETTING_NAME_SHEDULE_SCRRENSHOT )
					) {
						$url_data->set_scr_status( Urlslab_Url_Row::SCR_STATUS_NEW );
						$url_data->update();
					}
					$alt_text = $url_data->get_summary_text( Urlslab_Link_Enhancer::DESC_TEXT_SUMMARY );
					if ( empty( $alt_text ) ) {
						$alt_text = $urlslab_atts['alt'];
					}

					$screenshot_url = $url_data->get_screenshot_url( $urlslab_atts['screenshot-type'], true );
					if ( empty( $screenshot_url ) ) {
						$screenshot_url = $urlslab_atts['default-image'];
					}

					if ( empty( $screenshot_url ) ) {
						return ' <!-- URLSLAB processing '
							   . $urlslab_atts['url'] . ' -->';
					}

					// track screenshot usage
					$scr_url = new Urlslab_Screenshot_Url_Row();
					$scr_url->set_src_url_id( $this->get_current_page_url()->get_url_id() );
					$scr_url->set_screenshot_url_id( $url_data->get_url_id() );
					$scr_url->insert_all( array( $scr_url ), true );

					return $this->render_shortcode(
						$urlslab_atts['url'],
						$screenshot_url,
						$alt_text,
						$urlslab_atts['width'],
						$urlslab_atts['height']
					);
				}
			}
		} catch ( Exception $e ) {
		}

		return '';
	}

	public function has_shortcode(): bool {
		return true;
	}

	public function is_api_key_required(): bool {
		return true;
	}

	public function init_wp_widget() {
		require_once URLSLAB_PLUGIN_DIR
					 . 'includes/wp-widgets/class-urlslab-wp-widget-screenshot.php';
		register_widget( 'Urlslab_Wp_Widget_Screenshot' );
	}

	protected function add_options() {
		$this->add_options_form_section(
			'schedule',
			__( 'Scheduling Settings' ),
			__(
				'Screenshots are generated by the URLsLab service, a paid feature of the module. Buy credits on www.urlslab.com and start using it today!'
			)
		);
		$this->add_option_definition(
			self::SETTING_NAME_SHEDULE_SCRRENSHOT,
			self::SCHEDULE_SHORTCODE,
			false,
			__( 'Schedule Screenshot' ),
			__( 'Choose URL types for which we will process screenshots' ),
			self::OPTION_TYPE_LISTBOX,
			array(
				self::SCHEDULE_NEVER         => __( 'Never' ),
				self::SCHEDULE_SHORTCODE     => __( 'When URL (external or internal) is used in screenshot shortcode' ),
				self::SCHEDULE_ALL_INTERNALS => __( 'Every internal URL' ),
				self::SCHEDULE_ALL           => __( 'All URLs in database' ),
			),
			function( $value ) {
				return is_string( $value );
			},
			'schedule',
		);
		$this->add_option_definition(
			self::SETTING_NAME_SCREENSHOT_REFRESH_INTERVAL,
			\OpenAPI\Client\Model\DomainDataRetrievalDataRequest::RENEW_FREQUENCY_ONE_TIME,
			false,
			__( 'Synchronization Frequency of Screenshots with URLsLab service' ),
			__( 'Choose how often should URLsLab retake screenshot of url. Each screenshot costs some credits, so choose this value visely. To take the screenshot just once will fit to majority of cases.' ),
			self::OPTION_TYPE_LISTBOX,
			array(
				\OpenAPI\Client\Model\DomainDataRetrievalDataRequest::RENEW_FREQUENCY_NO_SCHEDULE => __( 'Never' ),
				\OpenAPI\Client\Model\DomainDataRetrievalDataRequest::RENEW_FREQUENCY_ONE_TIME    => __( 'Only once' ),
				\OpenAPI\Client\Model\DomainDataRetrievalDataRequest::RENEW_FREQUENCY_DAILY       => __( 'Daily' ),
				\OpenAPI\Client\Model\DomainDataRetrievalDataRequest::RENEW_FREQUENCY_WEEKLY      => __( 'Weekly' ),
				\OpenAPI\Client\Model\DomainDataRetrievalDataRequest::RENEW_FREQUENCY_MONTHLY     => __( 'Monthly' ),
				\OpenAPI\Client\Model\DomainDataRetrievalDataRequest::RENEW_FREQUENCY_YEARLY      => __( 'Yearly' ),
			),
			function( $value ) {
				return is_numeric( $value ) && 0 < $value;
			},
			'schedule',
		);


	}

	private function render_shortcode(
		string $url,
		string $src,
		string $alt,
		string $width,
		string $height
	): string {
		return sprintf(
			'<div class="urlslab-screenshot-container"><img src="%s" alt="%s" width="%s" height="%s"></div>',
			esc_url( $src ),
			esc_attr( $alt ),
			esc_attr( $width ),
			esc_attr( $height ),
		);
	}
}
