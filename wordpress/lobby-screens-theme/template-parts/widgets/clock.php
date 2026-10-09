<?php
/**
 * ClockWidget — big thin digital clock + Hebrew date.
 *
 * Back in service as the last cell of the v8.1 header rail: when the header was
 * split into cells, time + date was already exactly one cell, so this is used
 * instead of writing a fourth near-copy of it (clock-weather.php, which bundled
 * the weather in too, is retired).
 * JS: tick() in front-page.php updates #clockTime every second.
 * (An analog variant can be added here later without touching the page.)
 */
?>
<div class="w-clock">
  <div class="w-clock-time" id="clockTime">--:--</div>
  <div class="w-clock-date"><?php echo esc_html( lobby_screens_hebrew_date() ); ?></div>
</div>
