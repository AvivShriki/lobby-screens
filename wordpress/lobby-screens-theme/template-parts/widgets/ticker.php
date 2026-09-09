<?php
/**
 * TickerWidget — brief §9. Full-bleed Ynet headline bar across the bottom
 * edge: partner lockup on the reading side, then a continuous marquee.
 *
 * Continuous, never stepping or fading (the brief is explicit): the track
 * holds two copies of the headline set and slides exactly -50%, so the second
 * copy is under the viewport the instant the first leaves it.
 *
 * The mockup closes the bar with a gold skyline illustration. Rather than
 * invent one, the client's own topographic ornament — the strip from their
 * printed material — fades in at that end.
 */
$lines = lobby_screens_get_ynet_headlines( 8 );
if ( empty( $lines ) ) {
	return;
}
$theme_uri = get_stylesheet_directory_uri();
?>
<div class="w-ticker reveal">

  <img class="w-ticker-logo" src="<?php echo esc_url( $theme_uri . '/assets/images/ynet-logo-light.png' ); ?>" alt="Ynet">
  <div class="w-ticker-sep"></div>
  <span class="w-ticker-kicker">עדכוני חדשות</span>
  <div class="w-ticker-sep"></div>

  <div class="w-ticker-flow">
    <div class="w-ticker-track"><?php
      foreach ( array_merge( $lines, $lines ) as $line ) {
        echo '<span>' . esc_html( $line ) . '</span><i></i>';
      }
    ?></div>
  </div>

  <img class="w-ticker-orn" src="<?php echo esc_url( $theme_uri . '/assets/images/otzma-ornament.png' ); ?>" alt="" aria-hidden="true">
</div>
