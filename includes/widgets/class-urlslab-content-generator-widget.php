<?php

class Urlslab_Content_Generator_Widget extends Urlslab_Widget {
	const SLUG = 'urlslab-generator';
	const SETTING_NAME_SCHEDULE = 'urlslab-gen-sched';
	const SETTING_NAME_REFRESH_INTERVAL = 'urlslab-gen-refresh';
	const SETTING_NAME_AUTOAPPROVE = 'urlslab-gen-autoapprove';


	public function init_widget() {
		Urlslab_Loader::get_instance()->add_action(
			'init',
			$this,
			'hook_callback',
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
		return Urlslab_Content_Generator_Widget::SLUG;
	}

	/**
	 * @return string
	 */
	public function get_widget_title(): string {
		return __( 'AI Content Generator' );
	}

	/**
	 * @return string
	 */
	public function get_widget_description(): string {
		return __(
			'Enhance your site\'s content effortlessly with our AI-powered module for generating unique texts'
		);
	}


	public function get_attribute_values(
		$atts = array(),
		$content = null,
		$tag = ''
	) {
		$atts = array_change_key_case( (array) $atts );
		$current_url_obj = Urlslab_Url_Data_Fetcher::get_instance()
												   ->load_and_schedule_url(
													   $this->get_current_page_url(
													   )
												   );
		if ( ! empty( $current_url_obj ) ) {
			$title = $current_url_obj->get_summary_text(
				Urlslab_Link_Enhancer::DESC_TEXT_TITLE
			);
		} else {
			$title = get_the_title();
		}
		$urlslab_atts = shortcode_atts(
			array(
				'semantic_context' => $title,
				'command'          => 'Summarize information I gave you. Generate summarization in language |lang|.',
				'url_filter'       => str_replace(
						array(
							'www.',
							'https://',
							'http://',
						),
						'',
						$this->get_current_page_url()->get_url()
					) . '*',
				'template'         => 'templates/simple-result.php',
				'default_value'    => '',
				'lang'             => $this->get_current_language(),
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
		if ( isset( $_REQUEST['action'] )
			&& false !== strpos(
				$_REQUEST['action'],
				'elementor'
			)
			|| in_array(
				get_post_status(),
				array( 'trash', 'auto-draft', 'inherit' )
			)
			|| class_exists( '\Elementor\Plugin' )
			&& \Elementor\Plugin::$instance->editor->is_edit_mode()
		) {
			return '<div style="padding: 20px; background-color: #f5f5f5; border: 1px solid #ccc;text-align: center">Content Generator Placeholder</div>';
		}

		$atts = $this->get_attribute_values( $atts, $content, $tag );
		$obj = new Urlslab_Content_Generator_Row( $atts, false );
		$value = $atts['default_value'];
		if ( $obj->is_valid() ) {
			if ( $obj->load() ) {
				if ( $obj->is_active() ) {
					$value = $obj->get_result();
				}
			} else {
				$obj->set_status( Urlslab_Content_Generator_Row::STATUS_NEW );
				$obj->insert_all( array( $obj ), true );
			}
		}

		if ( ! empty( $value ) && isset( $atts['template'] ) ) {
			$template = locate_template(
				$atts['template'],
				false,
				false,
				$atts
			);
			if ( empty( $template ) ) {
				if ( file_exists(
					URLSLAB_PLUGIN_DIR . 'public/' . $atts['template']
				)
				) {
					$template = URLSLAB_PLUGIN_DIR . 'public/'
						. $atts['template'];
				} else {
					return $value;
				}
			}

			ob_start();
			$atts['result'] = $value;
			load_template( $template, true, $atts );

			return '' . ob_get_clean();
		}

		return '<!-- URLsLab Content Not Ready Yet -->';
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
				'Texts are generated by the URLsLab service, a paid feature of the module. Buy credits on www.urlslab.com and start using it today!'
			)
		);
		$this->add_option_definition(
			self::SETTING_NAME_SCHEDULE,
			false,
			false,
			__( 'Text Generating Scheduling (paid)' ),
			__(
				'Automatically schedule the query to server for seamless text generation with URLsLab service.'
			),
			self::OPTION_TYPE_CHECKBOX,
			false,
			null,
			'schedule'
		);
		$this->add_option_definition(
			self::SETTING_NAME_REFRESH_INTERVAL,
			self::FREQ_NEVER,
			3600,
			__( 'Content Refresh Interval (paid)' ),
			__(
				'Define how often we should generate refreshed content with the URLsLab service in the background. Be aware that renewal fees correspond to the initial content generation charges.'
			),
			self::OPTION_TYPE_LISTBOX,
			array(
				604800           => __( 'Weekly' ),
				2419200          => __( 'Monthly' ),
				7257600          => __( 'Quarterly' ),
				31536000         => __( 'Yearly' ),
				self::FREQ_NEVER => __( 'Never' ),
			),
			function ( $value ) {
				return is_numeric( $value ) && 0 < $value;
			},
			'schedule',
		);
		$this->add_options_form_section(
			'approval',
			__( 'Approval Settings' ),
			__(
				'AI may occasionally produce inaccurate content, requiring adjustments for optimal user experience. You can easily review, approve, or edit AI-generated content to ensure quality and accuracy.'
			)
		);

		$this->add_option_definition(
			self::SETTING_NAME_AUTOAPPROVE,
			false,
			false,
			__( 'Content Auto Approve' ),
			__(
				'Auto approve AI-generated results and show them instantly on your site.'
			),
			self::OPTION_TYPE_CHECKBOX,
			false,
			null,
			'approval'
		);
	}


}
