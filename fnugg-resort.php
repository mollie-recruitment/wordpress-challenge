<?php
/**
 * Plugin Name:       Fnugg Resort
 * Description:       A block to display up to date weather information for resorts within the Fnugg organization.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       fnugg-resort
 *
 * @package           fnugg-resort
 */

namespace FnuggResort;

define( 'FNUGG_RESORT_BASE_FILE', __DIR__ );

require_once __DIR__ . '/includes/class-rest-api.php';

function fnugg_resort_fnugg_resort_block_init() {
	register_block_type_from_metadata( __DIR__ );

	$fnugg_resort = array(
		'pluginBaseUrl' => untrailingslashit( plugin_dir_url( FNUGG_RESORT_BASE_FILE ) ),
		'restBase'      => untrailingslashit( rest_url( '' ) ),
	);

	wp_add_inline_script(
		'fnugg-resort-fnugg-resort-editor-script', // Handle of the script to localize.
		'const fnuggResort = JSON.parse(\'' . json_encode( $fnugg_resort ) . '\');', // Name for the object. Passed directly, so it can be anything.
		'before'
	);
}

add_actions( 'init', __NAMESPACE__ . '\\fnugg_resort_fnugg_resort_block_init' );
