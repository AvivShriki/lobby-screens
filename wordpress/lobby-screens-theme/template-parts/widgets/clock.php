<?php
/**
 * ClockWidget — big thin digital clock + Hebrew date.
 * JS: tick() in front-page.php updates #clockTime every second.
 * (An analog variant can be added here later without touching the page.)
 */
?>
<div class="w-clock">
  <div class="w-clock-time" id="clockTime">--:--</div>
  <div class="w-clock-date"><?php echo esc_html( lobby_screens_hebrew_date() ); ?></div>
</div>
