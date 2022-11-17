<?php

declare( strict_types=1 );

namespace FnuggResort;

use WP_REST_Server;

class RestApi {

	private $namespace = 'fnugg-resort/v1';

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public function register_routes() {
		if ( ! class_exists( 'fnuggResorts\FnuggApi' ) ) {
			return;
		}

		register_rest_route(
			$this->namespace,
			'/search',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'search_resorts' ),
				'args'                => array(
					'q' => array(
						'validate_callback' => function ( $param, $request, $key ) {
							return is_numeric( $param );
						},
					),
				),
				'permission_callback' => function() {
					return current_user_can( 'edit_post' );
				},
			)
		);

		register_rest_route(
			$this->namespace,
			'/resort/(?P<id>\d+)',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_resort' ),
				'args'                => array(
					'id' => array(
						'validate_callback' => function ( $param, $request, $key ) {
							return is_numeric( $param );
						},
					),
				),
				'permission_callback' => function() {
					return current_user_can( 'edit_post' );
				},
			)
		);
	}

	public function get_resort( \WP_REST_Request $request ) {
		$resort_id = $request->get_param( 'id' );
		$api       = new FnuggApi();
		$resort    = $api->get_resort( $resort_id );

		return rest_ensure_response( $resort );
	}

	public function search_resorts( \WP_REST_Request $request ) {
		$query    = wp_strip_all_tags( $request->get_param( 'q' ) );
		$api      = new FnuggApi();
		$raw_data = $api->search( $query );

		$data = [];

		if ( $raw_data->hits->total > 0 ) {
			foreach ( $raw_data->hits->hits as $hit ) {
				$data[] = array(
					'id'   => $hit->_source->id,
					'name' => $hit->_source->name,
				);
			}
		}

		return rest_ensure_response( $data );
	}

}

new RestApi();
