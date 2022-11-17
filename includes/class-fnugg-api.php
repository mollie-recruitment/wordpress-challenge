<?php

declare( strict_types=1 );

namespace FnuggResort;

class FnuggApi {

	private $cache_prefix = 'fnugg_resort_';

	private $sourceFields = 'id,name,images,conditions.combined.top,last_updated';

	public function __construct() {

	}

	public function get_resort( $resort_id ) {
		$cache_key = $this->cache_prefix . 'resort';
		$cache     = get_transient( $cache_key );
		if ( false !== $cache ) {
			return $cache;
		}

		$url = add_query_arg(
			array(
				'sourceFields' => $this->sourceFields,
			),
			'https://api.fnugg.no/get/resort/' . $resort_id
		);

		$response = wp_remote_get( $url );
		if ( is_wp_error( $response ) ) {
			return (object) array();
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body );
		if ( ! is_object( $data ) ) {
			return (object) array();
		}

		// Prepare the data into something much easier to work with in the front-end.
		$data = $this->parse_resort_data( $data );

		// Cache resort data for a short period, this is weather data that updates quite frequently.
		set_transient( $cache_key, $data, 5 * MINUTE_IN_SECONDS );

		return $data;
	}

	public function search( $query ) {
		$cache_key = $this->cache_prefix . 'search_' . md5( $query );
		$cache     = get_transient( $cache_key );
		if ( false !== $cache ) {
			return $cache;
		}

		$url      = add_query_arg(
			array(
				'q'            => $query,
				'sourceFields' => $this->sourceFields,
			),
			'https://api.fnugg.no/search'
		);
		$response = wp_remote_get( $url );
		if ( is_wp_error( $response ) ) {
			return (object) array();
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body );
		if ( ! is_object( $data ) ) {
			return (object) array();
		}

		// Cache resort search results for 1 day, this contains non-dynamic data.
		set_transient( $cache_key, $data, DAY_IN_SECONDS );

		return $data;
	}

	private function parse_resort_data( $original_data ) {
		$original_data = $original_data->_source;

		$data = array(
			'id'          => $original_data->id,
			'name'        => $original_data->name,
			'lastUpdated' => $original_data->last_updated,
			'image'       => ( $original_data->images->image_16_9_m ?? $original_data->images->image_full ),
			'weather'     => array(
				'symbolId'    => $original_data->conditions->combined->top->symbol->fnugg_id,
				'description' => $original_data->conditions->combined->top->condition_description,
				'temperature' => array(
					'degrees' => $original_data->conditions->combined->top->temperature->value,
					'unit'    => $original_data->conditions->combined->top->temperature->unit,
				),
				'wind'        => array(
					'speed'       => $original_data->conditions->combined->top->wind->mps,
					'direction'   => $original_data->conditions->combined->top->wind->degree,
					'description' => $original_data->conditions->combined->top->wind->speed,
				)
			),
		);

		return $data;
	}
}
