<?php

class Urlslab_Api_Urls extends Urlslab_Api_Table {

	protected $base = '/url';

	public function register_routes() {
		register_rest_route(
			self::NAMESPACE, $this->base . '/', $this->get_route_get_items()
		);
		register_rest_route(
			self::NAMESPACE, $this->base . '/count',
			$this->get_count_route( $this->get_route_get_items() )
		);

		register_rest_route(
			self::NAMESPACE,
			$this->base . '/(?P<url_id>[0-9]+)',
			array(
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array(
						$this, 'update_item_permissions_check',
					),
					'args'                => array(
						'scr_status'           => array(
							'required'          => false,
							'validate_callback' => function ( $param ) {
								switch ( $param ) {
								case Urlslab_Url_Row::SCR_STATUS_ERROR:
								case Urlslab_Url_Row::SCR_STATUS_NEW:
								case Urlslab_Url_Row::SCR_STATUS_ACTIVE:
									return true;
								default:
									return false;
								}
							},
						),
						'sum_status'           => array(
							'required'          => false,
							'validate_callback' => function ( $param ) {
								switch ( $param ) {
								case Urlslab_Url_Row::SUM_STATUS_ERROR:
								case Urlslab_Url_Row::SUM_STATUS_NEW:
								case Urlslab_Url_Row::SUM_STATUS_ACTIVE:
									return true;
								default:
									return false;
								}
							},
						),
						'http_status'          => array(
							'required'          => false,
							'validate_callback' => function ( $param ) {
								return is_numeric( $param );
							},
						),
						'visibility'           => array(
							'required'          => false,
							'validate_callback' => function ( $param ) {
								switch ( $param ) {
								case Urlslab_Url_Row::VISIBILITY_VISIBLE:
								case Urlslab_Url_Row::VISIBILITY_HIDDEN:
									return true;
								default:
									return false;
								}
							},
						),
						'url_title'            => array(
							'required'          => false,
							'validate_callback' => function ( $param ) {
								return is_string( $param );
							},
						),
						'url_meta_description' => array(
							'required'          => false,
							'validate_callback' => function ( $param ) {
								return is_string( $param );
							},
						),
						'url_summary'          => array(
							'required'          => false,
							'validate_callback' => function ( $param ) {
								return is_string( $param );
							},
						),
					),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			$this->base . '/delete-all',
			array(
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_all_items' ),
					'permission_callback' => array(
						$this, 'delete_item_permissions_check',
					),
					'args'                => array(),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			$this->base . '/(?P<url_id>[0-9]+)',
			array(
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array(
						$this, 'delete_item_permissions_check',
					),
					'args'                => array(),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			$this->base . '/(?P<dest_url_id>[0-9]+)/linked-from',
			$this->get_route_get_url_usage()
		);
		register_rest_route(
			self::NAMESPACE,
			$this->base . '/(?P<dest_url_id>[0-9]+)/linked-from/count',
			$this->get_count_route( $this->get_route_get_url_usage() )
		);
		register_rest_route(
			self::NAMESPACE, $this->base . '/(?P<src_url_id>[0-9]+)/links',
			$this->get_route_get_url_usage()
		);
		register_rest_route(
			self::NAMESPACE,
			$this->base . '/(?P<src_url_id>[0-9]+)/links/count',
			$this->get_count_route( $this->get_route_get_url_usage() )
		);
	}


	public function get_items( $request ) {
		$rows = $this->get_items_sql( $request )->get_results();

		if ( ! is_array( $rows ) ) {
			return new WP_Error(
				'error', __( 'Failed to get items', 'urlslab' ),
				array( 'status' => 400 )
			);
		}

		$recordset = array();

		foreach ( $rows as $row ) {
			$url = new Urlslab_Url_Row( (array) $row );
			$row = (object) array_replace(
				(array) $row, $url->get_object_values_as_array()
			);

			$row->screenshot_url = $url->get_screenshot_url();
			$row->url_name = $url->get_url()->get_url_with_protocol();
			if ( in_array( 'url_usage_count', $this->get_custom_columns() ) ) {
				$row->url_usage_count = (int) $row->url_usage_count;
			}
			if ( in_array(
				'screenshot_usage_count', $this->get_custom_columns()
			)
			) {
				$row->screenshot_usage_count
					= (int) $row->screenshot_usage_count;
			}
			if ( in_array( 'url_links_count', $this->get_custom_columns() ) ) {
				$row->url_links_count = (int) $row->url_links_count;
			}
			$row->urlslab_scr_timestamp = (int) $row->urlslab_scr_timestamp;
			$row->urlslab_sum_timestamp = (int) $row->urlslab_sum_timestamp;
			$row->url_id = (int) $row->url_id;

			$recordset[] = $row;
		}

		return new WP_REST_Response( $recordset, 200 );
	}


	public function get_url_usage( $request ) {
		$rows = $this->get_url_usage_sql( $request )->get_results();
		if ( ! is_array( $rows ) ) {
			return new WP_Error(
				'error', __( 'Failed to get items', 'urlslab' ),
				array( 'status' => 400 )
			);
		}

		foreach ( $rows as $row ) {
			$row->src_url_id = (int) $row->src_url_id;
			$row->dest_url_id = (int) $row->dest_url_id;
			try {
				$url = new Urlslab_Url( $row->src_url_name, true );
				$row->src_url_name = $url->get_url_with_protocol();
			} catch ( Exception $e ) {
			}
			try {
				$url = new Urlslab_Url( $row->dest_url_name, true );
				$row->dest_url_name = $url->get_url_with_protocol();
			} catch ( Exception $e ) {
			}
		}

		return new WP_REST_Response( $rows, 200 );
	}

	public function get_url_usage_count( $request ) {
		return new WP_REST_Response(
			$this->get_url_usage_sql( $request )->get_count(), 200
		);
	}

	public function get_screenshot_usage( $request ) {
		$rows = $this->get_screenshot_usage_sql( $request )->get_results();
		if ( ! is_array( $rows ) ) {
			return new WP_Error(
				'error', __( 'Failed to get items', 'urlslab' ),
				array( 'status' => 400 )
			);
		}

		foreach ( $rows as $row ) {
			$row->src_url_id = (int) $row->src_url_id;
			$row->screenshot_url_id = (int) $row->screenshot_url_id;
			try {
				$url = new Urlslab_Url( $row->src_url_name, true );
				$row->src_url_name = $url->get_url_with_protocol();
			} catch ( Exception $e ) {
			}
		}

		return new WP_REST_Response( $rows, 200 );
	}

	public function get_screenshot_usage_count( $request ) {
		return new WP_REST_Response(
			$this->get_screenshot_usage_sql( $request )->get_count(), 200
		);
	}

	public function delete_item( $request ) {
		global $wpdb;

		$delete_params = array();
		$delete_params['url_id'] = $request->get_param( 'url_id' );

		if ( false === $wpdb->delete( URLSLAB_URLS_TABLE, $delete_params ) ) {
			return new WP_Error(
				'error', __( 'Failed to delete', 'urlslab' ),
				array( 'status' => 500 )
			);
		}

		$delete_params = array();
		$delete_params['url_id'] = $request->get_param( 'url_id' );
		if ( false === $wpdb->delete(
				URLSLAB_FILE_URLS_TABLE, $delete_params
			)
		) {
			return new WP_Error(
				'error', __( 'Failed to delete', 'urlslab' ),
				array( 'status' => 500 )
			);
		}
		if ( false === $wpdb->delete(
				URLSLAB_KEYWORDS_MAP_TABLE, $delete_params
			)
		) {
			return new WP_Error(
				'error', __( 'Failed to delete', 'urlslab' ),
				array( 'status' => 500 )
			);
		}

		$delete_params = array();
		$delete_params['src_url_id'] = $request->get_param( 'url_id' );
		if ( false === $wpdb->delete(
				URLSLAB_URLS_MAP_TABLE, $delete_params
			)
		) {
			return new WP_Error(
				'error', __( 'Failed to delete', 'urlslab' ),
				array( 'status' => 500 )
			);
		}
		if ( false === $wpdb->delete(
				URLSLAB_RELATED_RESOURCE_TABLE, $delete_params
			)
		) {
			return new WP_Error(
				'error', __( 'Failed to delete', 'urlslab' ),
				array( 'status' => 500 )
			);
		}
		if ( false === $wpdb->delete(
				URLSLAB_SCREENSHOT_URLS_TABLE, $delete_params
			)
		) {
			return new WP_Error(
				'error', __( 'Failed to delete', 'urlslab' ),
				array( 'status' => 500 )
			);
		}

		$delete_params = array();
		$delete_params['dest_url_id'] = $request->get_param( 'url_id' );
		if ( false === $wpdb->delete(
				URLSLAB_URLS_MAP_TABLE, $delete_params
			)
		) {
			return new WP_Error(
				'error', __( 'Failed to delete', 'urlslab' ),
				array( 'status' => 500 )
			);
		}
		if ( false === $wpdb->delete(
				URLSLAB_RELATED_RESOURCE_TABLE, $delete_params
			)
		) {
			return new WP_Error(
				'error', __( 'Failed to delete', 'urlslab' ),
				array( 'status' => 500 )
			);
		}

		$delete_params = array();
		$delete_params['screenshot_url_id'] = $request->get_param( 'url_id' );
		if ( false === $wpdb->delete(
				URLSLAB_SCREENSHOT_URLS_TABLE, $delete_params
			)
		) {
			return new WP_Error(
				'error', __( 'Failed to delete', 'urlslab' ),
				array( 'status' => 500 )
			);
		}
		$this->on_items_updated();

		return new WP_REST_Response( __( 'Deleted' ), 200 );
	}

	public function delete_all_items( $request ) {
		global $wpdb;

		if ( false === $wpdb->query(
				$wpdb->prepare( 'TRUNCATE ' . URLSLAB_URLS_TABLE )
			)
		) { // phpcs:ignore
			return new WP_Error(
				'error', __( 'Failed to delete', 'urlslab' ),
				array( 'status' => 500 )
			);
		}

		if ( false === $wpdb->query(
				$wpdb->prepare( 'TRUNCATE ' . URLSLAB_URLS_MAP_TABLE )
			)
		) { // phpcs:ignore
			return new WP_Error(
				'error', __( 'Failed to delete', 'urlslab' ),
				array( 'status' => 500 )
			);
		}
		$this->on_items_updated();

		return new WP_REST_Response( __( 'Deleted' ), 200 );
	}

	function get_row_object( $params = array() ): Urlslab_Data {
		return new Urlslab_Url_Row( $params );
	}

	function get_editable_columns(): array {
		return array(
			'scr_status',
			'sum_status',
			'http_status',
			'visibility',
			'url_title',
			'url_meta_description',
			'url_summary',
		);
	}

	/**
	 * @return array[]
	 */
	public function get_route_get_url_usage(): array {
		return array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_url_usage' ),
				'args'                => array(
					'rows_per_page' => array(
						'required'          => true,
						'default'           => self::ROWS_PER_PAGE,
						'validate_callback' => function ( $param ) {
							return is_numeric( $param ) && 0 < $param
								&& 200 > $param;
						},
					),
					'from_url_id'   => array(
						'required'          => false,
						'validate_callback' => function ( $param ) {
							return empty( $param ) || is_numeric( $param );
						},
					),
				),
				'permission_callback' => array(
					$this, 'get_items_permissions_check',
				),
			),
		);
	}

	/**
	 * @return array[]
	 */
	public function get_route_get_screenshot_usage(): array {
		return array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_screenshot_usage' ),
				'args'                => array(
					'rows_per_page'          => array(
						'required'          => true,
						'default'           => self::ROWS_PER_PAGE,
						'validate_callback' => function ( $param ) {
							return is_numeric( $param ) && 0 < $param
								&& 200 > $param;
						},
					),
					'from_screenshot_url_id' => array(
						'required'          => false,
						'validate_callback' => function ( $param ) {
							return is_numeric( $param );
						},
					),
				),
				'permission_callback' => array(
					$this, 'get_items_permissions_check',
				),
			),
		);
	}

	/**
	 * @param $request
	 *
	 * @return Urlslab_Api_Table_Sql
	 */
	public function get_url_usage_sql( $request ): Urlslab_Api_Table_Sql {
		$sql = new Urlslab_Api_Table_Sql( $request );
		$sql->add_select_column( 'src_url_id' );
		$sql->add_select_column( 'dest_url_id' );
		$sql->add_select_column( 'url_name', 'u_src', 'src_url_name' );
		$sql->add_select_column( 'url_name', 'u_dest', 'dest_url_name' );
		$sql->add_from(
			URLSLAB_URLS_MAP_TABLE . ' m LEFT JOIN ' . URLSLAB_URLS_TABLE
			. ' u_src ON m.src_url_id = u_src.url_id LEFT JOIN '
			. URLSLAB_URLS_TABLE . ' u_dest ON m.dest_url_id = u_dest.url_id'
		); // phpcs:ignore
		$sql->add_filter( 'dest_url_id' );
		$sql->add_filter( 'src_url_id' );
		$sql->add_filter( 'from_dest_url_id', '%d' );
		$sql->add_filter( 'from_src_url_id', '%d' );
		$sql->add_order( 'src_url_id' );
		$sql->add_order( 'dest_url_id' );

		return $sql;
	}

	/**
	 * @param $request
	 *
	 * @return Urlslab_Api_Table_Sql
	 */
	public function get_screenshot_usage_sql( $request
	): Urlslab_Api_Table_Sql {
		$sql = new Urlslab_Api_Table_Sql( $request );
		$sql->add_select_column( 'screenshot_url_id' );
		$sql->add_select_column( 'src_url_id' );
		$sql->add_select_column( 'url_name', 'u', 'src_url_name' );

		$sql->add_from(
			URLSLAB_SCREENSHOT_URLS_TABLE . ' s LEFT JOIN ' . URLSLAB_URLS_TABLE
			. ' u ON s.src_url_id = u.url_id'
		); // phpcs:ignore
		$sql->add_filter( 'screenshot_url_id' );
		$sql->add_filter( 'src_url_id' );
		$sql->add_filter( 'from_screenshot_url_id', '%d' );
		$sql->add_filter( 'from_src_url_id', '%d' );
		$sql->add_order( 'screenshot_url_id' );
		$sql->add_order( 'src_url_id' );

		return $sql;
	}

	/**
	 * @return array[]
	 */
	public function get_route_get_items(): array {
		return array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_items' ),
				'args'                => $this->get_table_arguments(
					array(
						'filter_url_id'                => array(
							'required'          => false,
							'validate_callback' => function ( $param ) {
								return Urlslab_Api_Table::validate_numeric_filter_value(
									$param
								);
							},
						),
						'filter_url_name'              => array(
							'required'          => false,
							'validate_callback' => function ( $param ) {
								return Urlslab_Api_Table::validate_string_filter_value(
									$param
								);
							},
						),
						'filter_scr_status'            => array(
							'required'          => false,
							'validate_callback' => function ( $param ) {
								return Urlslab_Api_Table::validate_string_filter_value(
									$param
								);
							},
						),
						'filter_sum_status'            => array(
							'required'          => false,
							'validate_callback' => function ( $param ) {
								return Urlslab_Api_Table::validate_string_filter_value(
									$param
								);
							},
						),
						'filter_http_status'           => array(
							'required'          => false,
							'validate_callback' => function ( $param ) {
								return Urlslab_Api_Table::validate_numeric_filter_value(
									$param
								);
							},
						),
						'filter_urlslab_domain_id'     => array(
							'required'          => false,
							'validate_callback' => function ( $param ) {
								return Urlslab_Api_Table::validate_string_filter_value(
									$param
								);
							},
						),
						'filter_urlslab_url_id'        => array(
							'required'          => false,
							'validate_callback' => function ( $param ) {
								return Urlslab_Api_Table::validate_string_filter_value(
									$param
								);
							},
						),
						'filter_urlslab_scr_timestamp' => array(
							'required'          => false,
							'validate_callback' => function ( $param ) {
								return Urlslab_Api_Table::validate_numeric_filter_value(
									$param
								);
							},
						),
						'filter_urlslab_sum_timestamp' => array(
							'required'          => false,
							'validate_callback' => function ( $param ) {
								return Urlslab_Api_Table::validate_numeric_filter_value(
									$param
								);
							},
						),
						'filter_update_scr_date'       => array(
							'required'          => false,
							'validate_callback' => function ( $param ) {
								return Urlslab_Api_Table::validate_string_filter_value(
									$param
								);
							},
						),
						'filter_update_sum_date'       => array(
							'required'          => false,
							'validate_callback' => function ( $param ) {
								return Urlslab_Api_Table::validate_string_filter_value(
									$param
								);
							},
						),
						'filter_update_http_date'      => array(
							'required'          => false,
							'validate_callback' => function ( $param ) {
								return Urlslab_Api_Table::validate_string_filter_value(
									$param
								);
							},
						),
						'filter_url_title'             => array(
							'required'          => false,
							'validate_callback' => function ( $param ) {
								return Urlslab_Api_Table::validate_string_filter_value(
									$param
								);
							},
						),
						'filter_url_meta_description'  => array(
							'required'          => false,
							'validate_callback' => function ( $param ) {
								return Urlslab_Api_Table::validate_string_filter_value(
									$param
								);
							},
						),
						'filter_url_summary'           => array(
							'required'          => false,
							'validate_callback' => function ( $param ) {
								return Urlslab_Api_Table::validate_string_filter_value(
									$param
								);
							},
						),
						'filter_visibility'            => array(
							'required'          => false,
							'validate_callback' => function ( $param ) {
								return Urlslab_Api_Table::validate_string_filter_value(
									$param
								);
							},
						),
						'filter_url_type'              => array(
							'required'          => false,
							'validate_callback' => function ( $param ) {
								return Urlslab_Api_Table::validate_string_filter_value(
									$param
								);
							},
						),
					)
				),
				'permission_callback' => array(
					$this, 'get_items_permissions_check',
				),
			),
		);
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return Urlslab_Api_Table_Sql
	 */
	protected function get_items_sql( WP_REST_Request $request
	): Urlslab_Api_Table_Sql {
		$sql = new Urlslab_Api_Table_Sql( $request );
		foreach (
			array_keys( $this->get_row_object()->get_columns() ) as $column
		) {
			$sql->add_select_column( $column, 'u' );
		}
		$sql->add_from( URLSLAB_URLS_TABLE . ' u' );

		if ( in_array( 'url_usage_count', $this->get_custom_columns() ) ) {
			$sql->add_select_column( 'url_usage_count' );
			$sql->add_from(
				'LEFT JOIN ((SELECT dest_url_id, COUNT(src_url_id) as url_usage_count FROM '
				. URLSLAB_URLS_MAP_TABLE
				. ' GROUP BY dest_url_id)) m_used ON u.url_id = m_used.dest_url_id '
			);
		}
		if ( in_array(
			'screenshot_usage_count', $this->get_custom_columns()
		)
		) {
			$sql->add_select_column( 'screenshot_usage_count' );
			$sql->add_from(
				'LEFT JOIN (SELECT screenshot_url_id, COUNT(src_url_id) as screenshot_usage_count FROM '
				. URLSLAB_SCREENSHOT_URLS_TABLE
				. ' GROUP BY screenshot_url_id) m_links ON u.url_id = m_links.screenshot_url_id '
			);
		}
		if ( in_array( 'url_links_count', $this->get_custom_columns() ) ) {
			$sql->add_select_column( 'url_links_count' );
			$sql->add_from(
				'LEFT JOIN (SELECT src_url_id, COUNT(dest_url_id) as url_links_count FROM '
				. URLSLAB_URLS_MAP_TABLE
				. ' GROUP BY src_url_id) m_links ON u.url_id = m_links.src_url_id '
			);
		}

		$this->add_filter_table_fields( $sql );

		$sql->add_filter( 'filter_url_id' );
		$sql->add_filter( 'filter_url_name' );

		$sql->add_filter( 'filter_scr_status' );
		$sql->add_filter( 'filter_scr_schedule' );
		$sql->add_filter( 'filter_rel_schedule' );
		$sql->add_filter( 'filter_rel_updated' );
		$sql->add_filter( 'filter_sum_status' );
		$sql->add_filter( 'filter_http_status', '%d' );

		$sql->add_filter( 'filter_urlslab_domain_id' );
		$sql->add_filter( 'filter_urlslab_url_id' );
		$sql->add_filter( 'filter_urlslab_scr_timestamp', '%d' );

		$sql->add_filter( 'filter_update_scr_date' );
		$sql->add_filter( 'filter_update_sum_date' );
		$sql->add_filter( 'filter_update_http_date' );

		$sql->add_filter( 'filter_url_title' );
		$sql->add_filter( 'filter_url_meta_description' );
		$sql->add_filter( 'filter_url_summary' );

		$sql->add_filter( 'filter_visibility' );
		$sql->add_filter( 'filter_url_type' );

		if ( in_array( 'url_usage_count', $this->get_custom_columns() ) ) {
			$sql->add_having_filter( 'filter_url_usage_count', '%d' );
		}
		if ( in_array(
			'screenshot_usage_count', $this->get_custom_columns()
		)
		) {
			$sql->add_having_filter( 'filter_screenshot_usage_count', '%d' );
		}
		if ( in_array( 'url_links_count', $this->get_custom_columns() ) ) {
			$sql->add_having_filter( 'filter_url_links_count', '%d' );
		}

		$sql->add_group_by( 'url_id', 'u' );

		if ( $request->get_param( 'sort_column' ) ) {
			$sql->add_order(
				$request->get_param( 'sort_column' ),
				$request->get_param( 'sort_direction' )
			);
		}
		$sql->add_order( 'u.url_id' );

		return $sql;
	}

	protected function get_custom_columns() {
		$columns = array();
		$columns[] = 'url_usage_count';
		$columns[] = 'url_links_count';

		return $columns;
	}

}
