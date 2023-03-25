<?php

class Urlslab_Api_Schedules extends Urlslab_Api_Base {
	public function register_routes() {
		$base = '/schedule';

		register_rest_route(
			self::NAMESPACE,
			$base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'args'                => array(),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
				),
			)
		);
		register_rest_route(
			self::NAMESPACE,
			$base,
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'args'                => array(
						'urls'                  => array(
							'required'          => true,
							'validate_callback' => function( $param ) {
								if ( ! is_array( $param ) ) {
									return false;
								}
								try {
									foreach ( $param as $url_row ) {
										$url_obj = new Urlslab_Url( $url_row );
										if ( ! $url_obj->is_url_valid() ) {
											return false;
										}
									}

									return true;
								} catch ( Exception $e ) {
									return false;
								}
							},
						),
						'process_all_sitemaps'  => array(
							'required'          => false,
							'default'           => false,
							'validate_callback' => function( $param ) {
								return is_bool( $param );
							},
						),
						'custom_sitemaps'       => array(
							'required'          => false,
							'default'           => array(),
							'validate_callback' => function( $param ) {
								return is_array( $param );
							},
						),
						'follow_links'          => array(
							'required'          => false,
							'default'           => \OpenAPI\Client\Model\DomainScheduleScheduleConf::LINK_FOLLOWING_STRATEGY_NO_LINK,
							'validate_callback' => function( $param ) {
								$conf = new \OpenAPI\Client\Model\DomainScheduleScheduleConf();

								return in_array( $param, array_keys( $conf->getLinkFollowingStrategyAllowableValues() ) );
							},
						),
						'take_screenshot'       => array(
							'required'          => false,
							'default'           => false,
							'validate_callback' => function( $param ) {
								return is_bool( $param );
							},
						),
						'analyze_text'          => array(
							'required'          => false,
							'default'           => false,
							'validate_callback' => function( $param ) {
								return is_bool( $param );
							},
						),
						'scan_speed_per_minute' => array(
							'required'          => false,
							'default'           => 20,
							'validate_callback' => function( $param ) {
								return is_int( $param );
							},
						),
						'scan_frequency'        => array(
							'required'          => false,
							'default'           => \OpenAPI\Client\Model\DomainScheduleScheduleConf::SCAN_FREQUENCY_ONE_TIME,
							'validate_callback' => function( $param ) {
								$schedule       = new \OpenAPI\Client\Model\DomainScheduleScheduleConf();
								$allowed_values = $schedule->getScanFrequencyAllowableValues();

								return in_array( $param, $allowed_values );
							},
						),
					),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
				),
			)
		);


		register_rest_route(
			self::NAMESPACE,
			$base . '/(?P<schedule_id>[0-9a-zA-Z\\-]+)',
			array(
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
					'args'                => array(),
				),
			)
		);
	}


	private function get_client() {
		if ( ! strlen( get_option( Urlslab_General::SETTING_NAME_URLSLAB_API_KEY ) ) ) {
			throw new Exception( 'Urlslab API key not defined' );
		}

		return new \OpenAPI\Client\Urlslab\ScheduleApi( new GuzzleHttp\Client(), \OpenAPI\Client\Configuration::getDefaultConfiguration()->setApiKey( 'X-URLSLAB-KEY', get_option( Urlslab_General::SETTING_NAME_URLSLAB_API_KEY ) ) );
	}

	public function get_items( $request ) {
		try {
			$result = array();
			foreach ( $this->get_client()->listSchedules() as $schedule ) {
				$result[] = (object) array(
					'schedule_id'           => $schedule->getProcessId(),
					'urls'                  => $schedule->getScheduleConf()->getUrls(),
					'process_all_sitemaps'  => $schedule->getScheduleConf()->getAllSitemaps(),
					'custom_sitemaps'       => $schedule->getScheduleConf()->getSitemaps(),
					'follow_links'          => $schedule->getScheduleConf()->getLinkFollowingStrategy(),
					'take_screenshot'       => $schedule->getScheduleConf()->getTakeScreenshot(),
					'analyze_text'          => $schedule->getScheduleConf()->getFetchText(),
					'scan_speed_per_minute' => $schedule->getScheduleConf()->getScanSpeedPerMinute(),
					'scan_frequency'        => $schedule->getScheduleConf()->getScanFrequency(),
				);
			}

			return new WP_REST_Response( $result, 200 );
		} catch ( Throwable $e ) {
			return new WP_Error( 'error', $e->getMessage() );
		}
	}

	public function create_item( $request ) {
		try {
			$schedule = new \OpenAPI\Client\Model\DomainScheduleScheduleConf();

			if ( $request->has_param( 'urls' ) ) {
				$schedule->setUrls( $request->get_param( 'urls' ) );
			} else {
				throw new Exception( 'URLs not defined' );
			}


			if ( $request->has_param( 'follow_links' ) ) {
				$schedule->setLinkFollowingStrategy( $request->get_param( 'follow_links' ) );
			} else {
				$schedule->setLinkFollowingStrategy( \OpenAPI\Client\Model\DomainScheduleScheduleConf::LINK_FOLLOWING_STRATEGY_NO_LINK );
			}

			if ( $request->has_param( 'custom_sitemaps' ) ) {
				$schedule->setSitemaps( $request->get_param( 'custom_sitemaps' ) );
			} else {
				$schedule->setSitemaps( array() );
			}

			if ( $request->has_param( 'process_all_sitemaps' ) ) {
				$schedule->setAllSitemaps( $request->get_param( 'process_all_sitemaps' ) );
			} else {
				$schedule->setAllSitemaps( false );
			}

			if ( $request->has_param( 'take_screenshot' ) ) {
				$schedule->setTakeScreenshot( $request->get_param( 'take_screenshot' ) );
			} else {
				$schedule->setTakeScreenshot( false );
			}

			if ( $request->has_param( 'analyze_text' ) ) {
				$schedule->setFetchText( $request->get_param( 'analyze_text' ) );
			} else {
				$schedule->setFetchText( false );
			}

			if ( $request->has_param( 'scan_speed_per_minute' ) ) {
				$schedule->setScanSpeedPerMinute( $request->get_param( 'scan_speed_per_minute' ) );
			} else {
				$schedule->setScanSpeedPerMinute( 20 );
			}

			if ( $request->has_param( 'scan_frequency' ) ) {
				$schedule->setScanFrequency( $request->get_param( 'scan_frequency' ) );
			} else {
				$schedule->setScanFrequency( \OpenAPI\Client\Model\DomainScheduleScheduleConf::SCAN_FREQUENCY_ONE_TIME );
			}

			return new WP_REST_Response( $this->get_client()->createSchedule( $schedule ), 200 );
		} catch ( Throwable $e ) {
			return new WP_REST_Response( $e->getMessage(), 500 );
		}
	}

	public function delete_item( $request ) {
		try {
			$this->get_client()->deleteSchedule( $request->get_param( 'schedule_id' ) );

			return new WP_REST_Response( 'Deleted', 200 );
		} catch ( Throwable $e ) {
			return new WP_Error( 'error', $e->getMessage() );
		}
	}

}
