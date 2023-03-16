<?php

class Urlslab_Screenshot_Widget extends Urlslab_Widget {
	const SLUG = 'urlslab-screenshot';


	const SETTING_NAME_UPDATE_FREQ = 'urlslab-scr-update-freq';
	const SETTING_NAME_SCREENSHOT_SCHEDULING = 'urlslab-scr-scheduling';


	public function init_widget() {
		Urlslab_Loader::get_instance()->add_action( 'init', $this, 'hook_callback', 10, 0 );
	}

	public function hook_callback() {
		add_shortcode( $this->get_widget_slug(), array( $this, 'get_shortcode_content' ) );
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
		return __( '(Images) Embed screenshot of any external or internal URL using simple wordpress shortcode.' );
	}

	public function get_shortcode_content( $atts = array(), $content = null, $tag = '' ): string {
		$atts = array_change_key_case( (array) $atts );

		$urlslab_atts = shortcode_atts(
			array(
				'width'           => '100%',
				'height'          => '100%',
				'alt'             => 'Screenshot taken by URLSLAB.com',
				'default-image'   => '',
				'url'             => 'https://www.urlslab.com',
				'screenshot-type' => Urlslab_Url_Row::SCREENSHOT_TYPE_CAROUSEL,
			),
			$atts,
			$tag
		);


		try {
			if ( ! empty( $urlslab_atts['url'] ) ) {
				$url_data = Urlslab_Url_Data_Fetcher::get_instance()->fetch_schedule_url( new Urlslab_Url( $urlslab_atts['url'] ) );

				if ( ! empty( $url_data ) && ! $url_data->is_http_valid() ) {
					$alt_text = $url_data->get_summary_text( Urlslab_Link_Enhancer::DESC_TEXT_SUMMARY );
					if ( empty( $alt_text ) ) {
						$alt_text = $urlslab_atts['alt'];
					}

					$screenshot_url = $url_data->get_screenshot_url( $urlslab_atts['screenshot-type'] );
					if ( empty( $screenshot_url ) ) {
						$screenshot_url = $urlslab_atts['default-image'];
					}

					if ( empty( $screenshot_url ) ) {
						return '';
					}

					return $this->render_shortcode( $urlslab_atts['url'], $screenshot_url, $alt_text, $urlslab_atts['width'], $urlslab_atts['height'] );
				}
			}
		} catch ( Exception $e ) {
		}

		return '';
	}

	private function render_shortcode( string $url, string $src, string $alt, string $width, string $height ): string {
		if ( empty( $src ) ) {
			return ' <!-- URLSLAB processing ' . $url . ' -->';
		}

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

	public function is_api_key_required() {
		return true;
	}

	protected function add_options() {
		$this->add_option_definition(
			self::SETTING_NAME_UPDATE_FREQ,
			2419200,
			false,
			__( 'Update frequency' ),
			__( 'Define how often should be updated screenshot image from www.urlslab.com into your wordpress installation. Urlslab can take screenshot in different frequency as you update it in your wordpress installation. It is defined by schedule defined for specific domain.' ),
			self::OPTION_TYPE_LISTBOX,
			array(
				86400     => __( 'Daily' ),
				604800    => __( 'Weekly' ),
				2419200   => __( 'Monthly' ),
				7257600   => __( 'Quarterly' ),
				31536000  => __( 'Yearly' ),
				999999999 => __( 'Never' ),
			),
			function( $value ) {
				return is_numeric( $value ) && 0 < $value;
			},
		);
		$this->add_option_definition(
			self::SETTING_NAME_SCREENSHOT_SCHEDULING,
			'shortcode',
			false,
			__( 'Screenshot scheduling' ),
			__( 'Choose how will be screenshotting process started in urlslab.com. Each screenshot cost something, so sometimes is better to request screenshot just for urls, where you need it. Another option is to make a screenshot of every url. This could help you to track visual changes of your website over the time.' ),
			self::OPTION_TYPE_LISTBOX,
			array(
				'all'       => __( 'Request screenshot of all URLs' ),
				'shortcode' => __( 'Request screenshot only for URLs displayed by screenshot shortcode' ),
			),
		);
	}
}
