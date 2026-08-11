<?php
/**
 * ShabbatWidget — parasha + candle/havdalah times, floating glass card.
 * Data: lobby_screens_get_shabbat_times() (Hebcal, cached).
 */
$shabbat = lobby_screens_get_shabbat_times();
if ( empty( $shabbat ) ) {
	return;
}
?>
<div class="card w-shabbat">
  <div class="card-label">שבת · <?php echo esc_html( $shabbat['parasha'] ); ?></div>
  <div class="w-shabbat-times">
    <div class="w-shabbat-t">
      <span class="w-shabbat-t-label">כניסה</span>
      <span class="w-shabbat-t-val"><?php echo esc_html( $shabbat['primary']['candle'] ?? '--:--' ); ?></span>
    </div>
    <div class="w-shabbat-sep"></div>
    <div class="w-shabbat-t">
      <span class="w-shabbat-t-label">יציאה</span>
      <span class="w-shabbat-t-val"><?php echo esc_html( $shabbat['primary']['havdalah'] ?? '--:--' ); ?></span>
    </div>
  </div>
</div>
