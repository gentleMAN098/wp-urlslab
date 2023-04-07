<?php

class Urlslab_Screenshot_Widget extends Urlslab_Widget {
	const SLUG = 'urlslab-screenshot';


	const SETTING_NAME_SCHEDULE_SCREENSHOTS = 'urlslab-scr-sched-scr';


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


	/**
	 * @return string
	 */
	public function get_widget_slug(): string {
		return Urlslab_Screenshot_Widget::SLUG;
	}

	/**
	 * @return string
	 */
	public function get_widget_title(): string {
		return __( 'Automated Screenshots' );
	}

	/**
	 * @return string
	 */
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

		$urlslab_atts = shortcode_atts(
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

		return $urlslab_atts;
	}

	public function get_shortcode_content(
		$atts = array(),
		$content = null,
		$tag = ''
	): string {
		if (
			( isset( $_REQUEST['action'] )
				&& false !== strpos(
					$_REQUEST['action'],
					'elementor'
				) )
			|| in_array(
				get_post_status(),
				array( 'trash', 'auto-draft', 'inherit' )
			)
			|| ( class_exists( '\Elementor\Plugin' )
				&& \Elementor\Plugin::$instance->editor->is_edit_mode() )
		) {
			return '<div style="padding: 20px; background-color: #f5f5f5; border: 1px solid #ccc;text-align: center">Screenshot Placeholder</div>';
		}
		$urlslab_atts = $this->get_attribute_values( $atts, $content, $tag );

		try {
			if ( ! empty( $urlslab_atts['url'] ) ) {
				$url_data = Urlslab_Url_Data_Fetcher::get_instance()
													->load_and_schedule_url(
														new Urlslab_Url(
															$urlslab_atts['url']
														)
													);

				if ( ! empty( $url_data ) && $url_data->has_screenshot() ) {
					if ( $this->get_option(
						self::SETTING_NAME_SCHEDULE_SCREENSHOTS
					)
					) {
						$url_data->request_url_schedule(
							Urlslab_Url_Row::URL_SCHEDULE_SCREENSHOT_REQUIRED
						);
					}
					$alt_text = $url_data->get_summary_text(
						Urlslab_Link_Enhancer::DESC_TEXT_SUMMARY
					);
					if ( empty( $alt_text ) ) {
						$alt_text = $urlslab_atts['alt'];
					}

					$screenshot_url = $url_data->get_screenshot_url(
						$urlslab_atts['screenshot-type']
					);
					if ( empty( $screenshot_url ) ) {
						$screenshot_url = $urlslab_atts['default-image'];
					}

					if ( empty( $screenshot_url ) ) {
						return ' <!-- URLSLAB processing '
							. $urlslab_atts['url'] . ' -->';
					}

					//track screenshot usage
					$scr_url = new Urlslab_Screenshot_Url_Row();
					$scr_url->set_src_url_id(
						$this->get_current_page_url()->get_url_id()
					);
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

	public function has_shortcode(): bool {
		return true;
	}

	public function is_api_key_required(): bool {
		return true;
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
			self::SETTING_NAME_SCHEDULE_SCREENSHOTS,
			false,
			false,
			__( 'Screenshots Scheduling (paid)' ),
			__(
				'Automatically schedule new URLs to take screenshots with URLsLab service. It will be executed just once.'
			),
			self::OPTION_TYPE_CHECKBOX,
			false,
			null,
			'schedule'
		);
	}

	public function init_wp_widget() {
		require_once URLSLAB_PLUGIN_DIR
			. 'includes/wp-widgets/class-urlslab-wp-widget-screenshot.php';
		register_widget( 'Urlslab_Wp_Widget_Screenshot' );
	}
}
