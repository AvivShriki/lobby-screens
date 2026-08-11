<?php
/**
 * SportsWidget — ONE.co.il rotating headline, floating glass card.
 * Data: lobby_screens_get_one_headlines() (RSS, cached).
 * JS: rotate('.w-sports-item',null,8000).
 */
$headlines = lobby_screens_get_one_headlines( 8 );
if ( empty( $headlines ) ) {
	return;
}
$theme_uri = get_stylesheet_directory_uri();
?>
<div class="card w-sports">
  <div class="card-label">
    ספורט
    <img class="w-sports-logo" src="<?php echo esc_url( $theme_uri . '/assets/images/one-logo.png' ); ?>" alt="ONE.CO.IL">
  </div>
  <div class="w-sports-viewport">
    <?php foreach ( $headlines as $i => $headline ) : ?>
      <div class="w-sports-item<?php echo 0 === $i ? ' is-active' : ''; ?>"><?php echo esc_html( $headline ); ?></div>
    <?php endforeach; ?>
  </div>
</div>
