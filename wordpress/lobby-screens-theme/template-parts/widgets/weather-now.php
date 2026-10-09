<?php
/**
 * WeatherNowWidget — the weather cell of the header rail (v8.1).
 *
 * Split out of clock-weather.php. In the old two-group header the temperature
 * was a 24px line tucked under the clock, together with the Hebrew date and the
 * city — three different kinds of information stacked in one corner while the
 * middle of the screen sat empty. As its own cell it gets the rail's two bands:
 * a 46px reading, and the condition in words beneath it.
 *
 * The condition word is not new data — it names the icon we already draw. That
 * also means the state is never carried by a shape or a colour alone.
 *
 * v8.1.1: the city joins it. It used to sit beside the temperature, moved to
 * the building cell when the rail was built, and would have left the screen
 * altogether when that cell was cut — leaving a temperature that belongs to
 * nowhere in particular.
 */
$city    = $args['city'] ?? '';
$weather = lobby_screens_get_current_weather();
if ( ! $weather ) {
	// brief §18: no cell at all beats an error message or a stale-looking
	// number. The rail closes the gap on its own (.hdr-cell:empty).
	return;
}
$conditions = array(
	'sun'   => 'בהיר',
	'cloud' => 'מעונן',
	'rain'  => 'גשום',
);
?>
<div class="w-wx-now">
  <div class="w-wx-now-main">
    <svg class="w-wx-now-icon"><use href="#wx<?php echo esc_attr( ucfirst( $weather['kind'] ) ); ?>"></use></svg>
    <?php /* Same face and same size as the clock (Aviv, 9.10.2026), which
             means the same fixed cells — a 30° that becomes 9° must not drag
             the whole rail sideways. */ ?>
    <span class="w-wx-now-temp num"><?php echo lobby_screens_numeral_cells( $weather['temp'] . '°' ); // phpcs:ignore ?></span>
  </div>
  <div class="w-wx-now-cond">
    <?php if ( '' !== $city ) : ?><span class="w-wx-now-city"><?php echo esc_html( $city ); ?></span><?php endif; ?>
    <span><?php echo esc_html( $conditions[ $weather['kind'] ] ?? '' ); ?></span>
  </div>
</div>
