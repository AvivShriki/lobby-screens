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
  <?php /* Each digit gets its own fixed-width cell (see .w-clock-digit): the
           clock face has no tabular figures, so without this the time shifts
           sideways every time a 1 turns into a 0. The placeholder carries the
           same five cells, so nothing moves when JS fills them a moment later.
           Dashes rather than the server's time on purpose — if JS never runs,
           a frozen wrong clock is worse than an obviously blank one. */ ?>
  <div class="w-clock-time" id="clockTime"><span class="w-clock-digit">–</span><span class="w-clock-digit">–</span><span class="w-clock-colon">:</span><span class="w-clock-digit">–</span><span class="w-clock-digit">–</span></div>
  <div class="w-clock-date"><?php echo esc_html( lobby_screens_hebrew_date() ); ?></div>
</div>
