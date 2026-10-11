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
  <?php /* Two number spans and a coloured colon — no per-digit cells. Heebo's
           digits are naturally monospaced, so the clock cannot shuffle
           sideways and the cells the old display face needed are gone.
           Dashes rather than the server's time on purpose: if JS never runs,
           a frozen wrong clock is worse than an obviously blank one. */ ?>
  <div class="w-clock-time num" id="clockTime"><span>––</span><span class="num-sep">:</span><span>––</span></div>
  <div class="w-clock-date"><?php echo esc_html( lobby_screens_hebrew_date() ); ?></div>
</div>
