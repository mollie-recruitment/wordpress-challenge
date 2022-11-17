<?php

use FnuggResort\FnuggApi;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( ! isset( $attributes['resortId'] ) || empty( $attributes['resortId'] ) ) {
	return;
}

$fnugg_api = new FnuggApi();
$resort_data = $fnugg_api->get_resort( $attributes['resortId'] );
?>
<div class="fnugg-resort">
	<div class="fnugg-resort-title">
		<?php echo esc_html( $resort_data['name'] ); ?>
	</div>

	<div class="fnugg-resort-image">
		<img src="<?php echo esc_url( plugins_url( 'assets/placeholder.jpg', FNUGG_RESORT_BASE_FILE ) ); ?>" alt=""/>

		<div class="fnugg-resort-image-overlay">
			<div class="fnugg-resort-image-overlay-text">
				<div class="fnugg-resort-sub-title">
					<?php esc_html_e( 'Todays conditions', 'fnugg-resort' ); ?>
				</div>

				<div class="fnugg-resort-last-update">
					<?php printf( esc_html__( 'Last updated: %s', 'fnugg-resort' ), $resort_data['lastUpdated'] ); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="fnugg-resort-details">
		<div class="fnugg-resort-weather">
			<img src="<?php echo esc_url( plugins_url( 'assets/icons/resort-weather-blue-' . $resort_data['weather']['symbolId'] . '.svg', FNUGG_RESORT_BASE_FILE ) ); ?>" alt="" />
			<div>
				<?php echo esc_html( $resort_data['weather']['description'] ); ?>
			</div>
		</div>
		<div class="fnugg-resort-temperature">
			<?php echo esc_html( $resort_data['weather']['temperature']['degrees'] ); ?>°
		</div>

		<div class="fnugg-resort-wind">
			<div>
				<img src="<?php echo esc_url( plugins_url( 'assets/icons/wind-direction.svg', FNUGG_RESORT_BASE_FILE ) ); ?>" style="transform: rotate(<?php echo esc_attr( $resort_data['weather']['wind']['direction'] ); ?>deg);" alt="" />
				<span class="fnugg-resort-wind-speed"><?php echo esc_html( $resort_data['weather']['wind']['speed'] ); ?></span> <span class="fnugg-resort-wind-unit">m/s</span>
			</div>

			<div>
				<?php echo esc_html( $resort_data['weather']['wind']['description'] ); ?>
			</div>
		</div>
		<div class="fnugg-resort-conditions">
			<img src="<?php echo esc_url( plugins_url( 'assets/icons/slope.svg', FNUGG_RESORT_BASE_FILE ) ); ?>" alt="" />
			<?php echo esc_html( $resort_data['weather']['description'] ); ?>
		</div>
	</div>
</div>
