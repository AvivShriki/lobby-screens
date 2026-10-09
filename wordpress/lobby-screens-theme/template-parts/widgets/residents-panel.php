<?php
/**
 * ResidentsPanel — brief §8, and the one card whose content is the client's own.
 *
 * v8.2 rebuilds this against Aviv's mockup (mockups/v8-2-notices.png). The card
 * was a column of four small notices in a 470px track; it is now the widest
 * element on the screen (946px) showing ONE notice at a time, large enough to
 * read without walking up to it.
 *
 * WHY ONE AT A TIME. Four notices in a narrow card meant four 19px titles and
 * four two-line summaries clamped to within an inch of their lives — a resident
 * walking past read none of them. One notice at 56px with its date is read in
 * the two seconds someone spends waiting for a lift, and the next one is along
 * shortly. The brief's "notices may rotate" finally earns its keep: before, the
 * card sat still whenever a building had four or fewer.
 *
 * The chevrons are DECORATION, not controls. There is no input device on a
 * lobby wall (brief §11) — they are in the mockup as the familiar sign that
 * this panel advances, and they are aria-hidden so a screen reader is not
 * offered a button that does not exist.
 *
 * The glyph is still chosen per notice in $lobby, so the client's future admin
 * screen picks one from a list rather than us guessing from the text.
 */
$notices = $args['notices'] ?? array();
if ( empty( $notices ) ) {
	return;
}
$multiple = count( $notices ) > 1;

$glyphs = array(
	// Crossed wrench and screwdriver, as in the mockup. The first attempt was
	// the wrench plus its own mirror image, which is tidy arithmetic and reads
	// as a bow tie at 54px — two closed shapes overlapping in the middle. Two
	// different tools crossing is what actually looks like the mockup.
	'wrench'   => '<path d="M16.1 3.3a4.2 4.2 0 0 0-5 5.3L3.6 16.1a2.2 2.2 0 0 0 3.1 3.1l7.5-7.5a4.2 4.2 0 0 0 5.3-5l-2.6 2.6-2.4-.6-.6-2.4Z"/>'
		. '<path d="M7.9 3.3 4.4 6.8l2.5 2.5"/>'
		. '<path d="m14.6 14.6 5 5a1.9 1.9 0 0 0 2.7-2.7l-5-5"/>',
	'car'      => '<path d="M4 16.5v2.2M20 16.5v2.2"/><path d="M3.4 12.6 5 7.8A2 2 0 0 1 6.9 6.4h10.2A2 2 0 0 1 19 7.8l1.6 4.8v3.2a1 1 0 0 1-1 1h-15a1 1 0 0 1-1-1Z"/><path d="M6.6 14.4h.01M17.4 14.4h.01"/>',
	'water'    => '<path d="M12 3.2s5.6 5.9 5.6 9.5a5.6 5.6 0 1 1-11.2 0C6.4 9.1 12 3.2 12 3.2Z"/>',
	'people'   => '<circle cx="9" cy="8.4" r="3"/><path d="M3.6 19.4a5.6 5.6 0 0 1 10.8 0"/><path d="M16.2 6a3 3 0 0 1 0 5.6M17.4 14.4a5.6 5.6 0 0 1 3 5"/>',
	'bell'     => '<path d="M18 8.6a6 6 0 1 0-12 0c0 6-2.2 7.4-2.2 7.4h16.4S18 14.6 18 8.6Z"/><path d="M13.7 19.5a2 2 0 0 1-3.4 0"/>',
);
$chevron = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="%s"/></svg>';
?>
<section class="card w-notices reveal">

  <header class="w-notices-head">
    <svg class="w-notices-head-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?php echo $glyphs['bell']; // phpcs:ignore ?></svg>
    <h2 class="w-notices-head-title">הודעות לדיירים</h2>
    <span class="w-notices-head-rule" aria-hidden="true"></span>
    <span class="w-notices-head-status">עדכונים מהבניין</span>
  </header>

  <div class="w-notices-stage">

    <?php if ( $multiple ) : ?>
      <?php /* RTL: this one lands on the right */ ?>
      <span class="w-notices-arrow"><?php printf( $chevron, 'm14.5 5-6 7 6 7' ); // phpcs:ignore ?></span>
    <?php endif; ?>

    <div class="w-notices-panel">
      <?php /* the mockup's gold sweep across the lower corner — pure decoration,
              drawn rather than imported so it scales with the panel */ ?>
      <svg class="w-notices-flourish" viewBox="0 0 800 420" preserveAspectRatio="none" aria-hidden="true">
        <defs>
          <linearGradient id="noticeSweep" x1="0" y1="1" x2="1" y2="0">
            <stop offset="0%" stop-color="rgba(201,188,156,0)"/>
            <stop offset="42%" stop-color="rgba(201,188,156,.55)"/>
            <stop offset="100%" stop-color="rgba(201,188,156,0)"/>
          </linearGradient>
        </defs>
        <path d="M-20 430 C 150 330, 420 300, 820 322" fill="none" stroke="url(#noticeSweep)" stroke-width="1.4"/>
        <path d="M-20 414 C 170 300, 440 268, 820 286" fill="none" stroke="url(#noticeSweep)" stroke-width="1"/>
        <path d="M-20 398 C 190 272, 460 238, 820 252" fill="none" stroke="url(#noticeSweep)" stroke-width=".7"/>
      </svg>

      <div class="w-notices-deck" data-rotator="<?php echo $multiple ? 10000 : 0; ?>" data-rotator-dots="noticesDots">
        <?php foreach ( $notices as $n => $notice ) : ?>
          <?php $glyph = $glyphs[ $notice['icon'] ?? '' ] ?? $glyphs['bell']; ?>
          <article class="w-notice<?php echo 0 === $n ? ' is-active' : ''; ?><?php echo ! empty( $notice['priority'] ) ? ' is-priority' : ''; ?>">
            <svg class="w-notice-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?php echo $glyph; // phpcs:ignore ?></svg>
            <h3 class="w-notice-title"><?php echo esc_html( $notice['title'] ); ?></h3>
            <span class="w-notice-rule" aria-hidden="true"></span>
            <p class="w-notice-detail"><?php echo esc_html( $notice['detail'] ); ?></p>
            <?php if ( ! empty( $notice['date'] ) ) : ?>
              <div class="w-notice-date">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <rect x="3.4" y="5.2" width="17.2" height="15.4" rx="2.4"/>
                  <path d="M3.4 9.8h17.2M8.2 3.4v3.6M15.8 3.4v3.6"/>
                </svg>
                <span><?php echo esc_html( $notice['date'] ); ?></span>
              </div>
            <?php endif; ?>
          </article>
        <?php endforeach; ?>
      </div>
    </div>

    <?php if ( $multiple ) : ?>
      <span class="w-notices-arrow"><?php printf( $chevron, 'm9.5 5 6 7-6 7' ); // phpcs:ignore ?></span>
    <?php endif; ?>

  </div>

  <?php if ( $multiple ) : ?>
    <div class="w-notices-dots" id="noticesDots" aria-hidden="true">
      <?php foreach ( $notices as $n => $notice ) : ?>
        <span<?php echo 0 === $n ? ' class="is-active"' : ''; ?>></span>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</section>
