<?php
/**
 * ClockWeatherWidget — header right (brief §3): live time, Hebrew date, and
 * the current temperature for this building's city.
 *
 * The time is the one value rendered client-side (front-page.php ticks it);
 * everything else is server data, so a lost connection leaves the last good
 * temperature on screen rather than an error (brief §18).
 */
$city    = $args['city'] ?? '';
$weather = lobby_screens_get_current_weather();
?>
<div class="w-clock reveal">
  <div class="w-clock-time" id="clockTime">--:--</div>
  <div class="w-clock-date"><?php echo esc_html( lobby_screens_hebrew_date() ); ?></div>
  <?php if ( $weather ) : ?>
    <div class="w-clock-weather">
      <svg><use href="#wx<?php echo esc_attr( ucfirst( $weather['kind'] ) ); ?>"></use></svg>
      <span class="w-clock-temp"><?php echo esc_html( $weather['temp'] ); ?>°</span>
      <span class="w-clock-city"><?php echo esc_html( $city ); ?></span>
    </div>
  <?php endif; ?>
</div>
