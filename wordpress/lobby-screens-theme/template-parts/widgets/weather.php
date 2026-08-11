<?php
/**
 * WeatherWidget — 7-day forecast as a floating glass card.
 * Data: lobby_screens_get_weekly_weather() (Open-Meteo, cached).
 */
$weather = lobby_screens_get_weekly_weather();
if ( empty( $weather ) ) {
	return;
}
?>
<div class="card w-weather">
  <div class="card-label">מזג אוויר · שבוע קדימה</div>
  <div class="w-weather-grid">
    <?php foreach ( $weather as $day ) :
      $kind    = lobby_screens_weather_icon_kind( $day['code'] );
      $icon_id = 'sun' === $kind ? 'wxSun' : ( 'rain' === $kind ? 'wxRain' : 'wxCloud' );
    ?>
    <div class="w-weather-col">
      <span class="w-weather-day"><?php echo esc_html( $day['label'] ); ?></span>
      <svg class="w-weather-icon"><use href="#<?php echo esc_attr( $icon_id ); ?>"/></svg>
      <span class="w-weather-max"><?php echo esc_html( $day['max'] ); ?>°</span>
      <span class="w-weather-min"><?php echo esc_html( $day['min'] ); ?>°</span>
    </div>
    <?php endforeach; ?>
  </div>
</div>
