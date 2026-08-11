<?php
/**
 * NewsWidget — Ynet ticker as a floating glass bar.
 * Data: lobby_screens_get_ynet_headlines() (RSS, cached + blocklist-filtered).
 */
$lines = lobby_screens_get_ynet_headlines( 6 );
if ( empty( $lines ) ) {
	return;
}
$track = '';
foreach ( array_merge( $lines, $lines ) as $line ) {
	$track .= '<span><b>מבזק</b>' . esc_html( $line ) . '</span>';
}
$theme_uri = get_stylesheet_directory_uri();
?>
<div class="w-news">
  <span class="w-news-chip"><img src="<?php echo esc_url( $theme_uri . '/assets/images/ynet-logo.png' ); ?>" alt="Ynet"></span>
  <div class="w-news-flow">
    <div class="w-news-track"><?php echo $track; ?></div>
  </div>
  <div class="w-news-brand">עוצמה · ניהול ואחזקת מבנים</div>
</div>
