<?php
/**
 * ResidentsPanel — brief §8, and the one card whose content is the client's
 * own. No buttons, no "read more": the screen is passive (brief §11).
 *
 * Each notice carries a glyph, as in the mockup — that is what lets a resident
 * sort four notices at a glance instead of reading all four titles. The glyph
 * is chosen per notice in $lobby, so the client's future admin screen can pick
 * one from a list rather than us guessing from the text.
 *
 * Four notices fill the card, so a building with four sees no motion at all.
 * Beyond four the list pages in blocks of four with a slow crossfade — that is
 * what the brief's "notices may rotate" is for, rather than animating a list
 * that already fits.
 */
$notices  = $args['notices'] ?? array();
$per_page = 4;
if ( empty( $notices ) ) {
	return;
}
$pages = array_chunk( $notices, $per_page );

$glyphs = array(
	'wrench'   => '<path d="M14.7 6.3a4 4 0 0 0 5 5l-8.4 8.4a2.1 2.1 0 0 1-3-3Z"/><path d="M19.7 11.3 21 6.6 18.4 4 13.7 5.3"/>',
	'car'      => '<path d="M4 16.5v2.2M20 16.5v2.2"/><path d="M3.4 12.6 5 7.8A2 2 0 0 1 6.9 6.4h10.2A2 2 0 0 1 19 7.8l1.6 4.8v3.2a1 1 0 0 1-1 1h-15a1 1 0 0 1-1-1Z"/><path d="M6.6 14.4h.01M17.4 14.4h.01"/>',
	'water'    => '<path d="M12 3.2s5.6 5.9 5.6 9.5a5.6 5.6 0 1 1-11.2 0C6.4 9.1 12 3.2 12 3.2Z"/>',
	'people'   => '<circle cx="9" cy="8.4" r="3"/><path d="M3.6 19.4a5.6 5.6 0 0 1 10.8 0"/><path d="M16.2 6a3 3 0 0 1 0 5.6M17.4 14.4a5.6 5.6 0 0 1 3 5"/>',
	'bell'     => '<path d="M18 8.6a6 6 0 1 0-12 0c0 6-2.2 7.4-2.2 7.4h16.4S18 14.6 18 8.6Z"/><path d="M13.7 19.5a2 2 0 0 1-3.4 0"/>',
);
?>
<section class="card w-notices reveal">

  <?php get_template_part( 'template-parts/parts/card-label', null, array(
    'text' => 'הודעות לדיירים',
    'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
      . $glyphs['bell'] . '</svg>',
  ) ); ?>

  <div class="w-notices-list" data-rotator="10000">
    <?php foreach ( $pages as $p => $page ) : ?>
      <div class="w-notices-page<?php echo 0 === $p ? ' is-active' : ''; ?>">
        <?php foreach ( $page as $notice ) : ?>
          <?php $glyph = $glyphs[ $notice['icon'] ?? '' ] ?? $glyphs['bell']; ?>
          <article class="w-notice<?php echo ! empty( $notice['priority'] ) ? ' is-priority' : ''; ?>">
            <div class="w-notice-head">
              <svg class="w-notice-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?php echo $glyph; // phpcs:ignore ?></svg>
              <h3 class="w-notice-title"><?php echo esc_html( $notice['title'] ); ?></h3>
            </div>
            <p class="w-notice-detail"><?php echo esc_html( $notice['detail'] ); ?></p>
            <?php if ( ! empty( $notice['date'] ) ) : ?>
              <div class="w-notice-date"><?php echo esc_html( $notice['date'] ); ?></div>
            <?php endif; ?>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  </div>

</section>
