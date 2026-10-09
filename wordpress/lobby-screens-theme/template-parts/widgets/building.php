<?php
/**
 * BuildingWidget — the identity cell of the header rail (v8.1).
 *
 * WHY THIS EXISTS: the building's own name lived only in $lobby and in the
 * browser title. A resident standing in the lobby could not see which building
 * the screen belonged to — on a product that will run in several buildings off
 * one theme, that is the one line that should never be missing.
 *
 * It is also the honest content for the middle of the rail: the greeting to its
 * right reads "ברוכים הבאים / לבניין שלנו", and this finishes the sentence
 * instead of filling the gap with decoration.
 *
 * Args: building, city — passed in, not read from a constant, so a second
 * building carries its own (see $lobby in front-page.php).
 */
$a = wp_parse_args( $args ?? array(), array( 'building' => '', 'city' => '' ) );
if ( '' === $a['building'] ) {
	return;   // empty cell removes itself and its separator — see .hdr-cell:empty
}
?>
<div class="w-building">
  <div class="w-building-name"><?php echo esc_html( $a['building'] ); ?></div>
  <?php if ( '' !== $a['city'] ) : ?>
    <div class="w-building-city">
      <svg viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.5" stroke-linejoin="round" aria-hidden="true">
        <path d="M12 21.2s6.4-5.6 6.4-10.4A6.4 6.4 0 0 0 5.6 10.8c0 4.8 6.4 10.4 6.4 10.4Z"/>
        <circle cx="12" cy="10.5" r="2.3"/>
      </svg>
      <span><?php echo esc_html( $a['city'] ); ?></span>
    </div>
  <?php endif; ?>
</div>
