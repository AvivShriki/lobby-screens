<?php
/**
 * ResidentsPanel — brief §8, and the one card whose content is the client's own.
 *
 * v8.5 rebuilds it on the same concept as the Shabbat card: the card IS Aviv's
 * image (mockups/v8-2-notices.png) with the variable strings erased out of it,
 * and live spans positioned back into the holes. He asked for the two cards to
 * be built the same way so they read as a pair, and this is that.
 *
 * v8.6 — Aviv struck the chevrons for good (a control shape on a screen with
 * nothing to press it with), struck the rule beside "עדכונים מהבניין", and
 * asked for the card title centred. All three are changes to the CHROME, so
 * all three were erased out of the image: the chevrons and the rule for good,
 * the title and its bell so they can be redrawn in the middle.
 *
 * Baked, because it never changes: the frame and its glow, the inner panel and
 * its top highlight, the gold sweep, the pip and "עדכונים מהבניין", and the
 * rule under the notice title.
 *
 * Live, because it does: the notice glyph, the title, the body, the date, and
 * the position dots. The dots had to come out of the image — there are five in
 * it and a building has however many notices it has — so they are redrawn from
 * the image's own measurements: 9px at a 14px gap, active #FFD064, the rest
 * #555554.
 *
 * NOTE ON THE CHEVRONS. Aviv struck these in v8.2.1 as controls on a screen
 * with nothing to press them with, and they are back only because they are in
 * the image he sent with "leave the design as it is". They are part of the
 * picture, not elements, so there is nothing to make accessible or to click.
 * Say the word and they come out of the chrome the same way the text did.
 */
$notices = $args['notices'] ?? array();
if ( empty( $notices ) ) {
	return;
}
$theme_uri = get_stylesheet_directory_uri();
$multiple  = count( $notices ) > 1;

$glyphs = array(
	'wrench' => '<path d="M16.1 3.3a4.2 4.2 0 0 0-5 5.3L3.6 16.1a2.2 2.2 0 0 0 3.1 3.1l7.5-7.5a4.2 4.2 0 0 0 5.3-5l-2.6 2.6-2.4-.6-.6-2.4Z"/>'
		. '<path d="M7.9 3.3 4.4 6.8l2.5 2.5"/><path d="m14.6 14.6 5 5a1.9 1.9 0 0 0 2.7-2.7l-5-5"/>',
	'car'    => '<path d="M4 16.5v2.2M20 16.5v2.2"/><path d="M3.4 12.6 5 7.8A2 2 0 0 1 6.9 6.4h10.2A2 2 0 0 1 19 7.8l1.6 4.8v3.2a1 1 0 0 1-1 1h-15a1 1 0 0 1-1-1Z"/><path d="M6.6 14.4h.01M17.4 14.4h.01"/>',
	'water'  => '<path d="M12 3.2s5.6 5.9 5.6 9.5a5.6 5.6 0 1 1-11.2 0C6.4 9.1 12 3.2 12 3.2Z"/>',
	'people' => '<circle cx="9" cy="8.4" r="3"/><path d="M3.6 19.4a5.6 5.6 0 0 1 10.8 0"/><path d="M16.2 6a3 3 0 0 1 0 5.6M17.4 14.4a5.6 5.6 0 0 1 3 5"/>',
	'bell'   => '<path d="M18 8.6a6 6 0 1 0-12 0c0 6-2.2 7.4-2.2 7.4h16.4S18 14.6 18 8.6Z"/><path d="M13.7 19.5a2 2 0 0 1-3.4 0"/>',
);
?>
<section class="card w-notices reveal">
  <img class="w-notices-art" src="<?php echo esc_url( $theme_uri . '/assets/images/notices-card.jpg' ); ?>" alt="">

  <?php /* v8.6 — the title and its bell come out of the chrome and are drawn
          live, because Aviv asked for them centred and they were baked to the
          right. Everything else in the header is still his pixels. */ ?>
  <h2 class="w-notices-head">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?php echo $glyphs['bell']; // phpcs:ignore ?></svg>
    <span>הודעות לדיירים</span>
  </h2>

  <div class="w-notices-deck" data-rotator="<?php echo $multiple ? 10000 : 0; ?>" data-rotator-dots="noticesDots">
    <?php foreach ( $notices as $n => $notice ) : ?>
      <?php $glyph = $glyphs[ $notice['icon'] ?? '' ] ?? $glyphs['bell']; ?>
      <article class="w-notice<?php echo 0 === $n ? ' is-active' : ''; ?>">
        <svg class="w-notice-f f-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?php echo $glyph; // phpcs:ignore ?></svg>
        <h3 class="w-notice-f f-title"><?php echo esc_html( $notice['title'] ); ?></h3>
        <p class="w-notice-f f-body"><?php echo esc_html( $notice['detail'] ); ?></p>
        <?php if ( ! empty( $notice['date'] ) ) : ?>
          <span class="w-notice-f f-date">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <rect x="3.4" y="5.2" width="17.2" height="15.4" rx="2.4"/>
              <path d="M3.4 9.8h17.2M8.2 3.4v3.6M15.8 3.4v3.6"/>
            </svg>
            <span><?php echo esc_html( $notice['date'] ); ?></span>
          </span>
        <?php endif; ?>
      </article>
    <?php endforeach; ?>
  </div>

  <?php if ( $multiple ) : ?>
    <div class="w-notices-dots" id="noticesDots" aria-hidden="true">
      <?php foreach ( $notices as $n => $notice ) : ?>
        <span<?php echo 0 === $n ? ' class="is-active"' : ''; ?>></span>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
