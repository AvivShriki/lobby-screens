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
 * v8.2.1 — Aviv struck the chevrons and the "עדכונים מהבניין" line off the
 * mockup. The chevrons were the right call to lose: they are a control shape
 * on a screen with nothing to control it with (brief §11), and the dots
 * already say the panel advances. The header is now the bell and the title,
 * nothing else.
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
?>
<section class="card w-notices reveal">

  <header class="w-notices-head">
    <svg class="w-notices-head-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?php echo $glyphs['bell']; // phpcs:ignore ?></svg>
    <h2 class="w-notices-head-title">הודעות לדיירים</h2>
  </header>

  <div class="w-notices-stage">

    <div class="w-notices-panel">
      <?php /* The gold sweep across the lower corner — the thing Aviv pointed
              at in his reference. Drawn rather than imported so it tracks the
              panel at any size, and built in three passes because one stroke
              cannot look like light: a wide blurred pass for the bloom, a
              bright thin pass for the filament itself, then two hairlines
              trailing behind it. The viewBox is close to the panel's own
              aspect so preserveAspectRatio="none" barely distorts the strokes.

              Gradient stops rather than one colour, for the same reason the
              frame is a gradient: the sweep has to arrive, catch, and leave. */ ?>
      <svg class="w-notices-flourish" viewBox="0 0 900 520" preserveAspectRatio="none" aria-hidden="true">
        <defs>
          <linearGradient id="noticeSweep" x1="0" y1="1" x2="1" y2="0">
            <stop offset="0%"   stop-color="rgba(201,162,39,0)"/>
            <stop offset="18%"  stop-color="rgba(201,162,39,.55)"/>
            <stop offset="44%"  stop-color="rgba(248,238,206,1)"/>
            <stop offset="68%"  stop-color="rgba(214,176,74,.72)"/>
            <stop offset="100%" stop-color="rgba(201,162,39,0)"/>
          </linearGradient>
          <linearGradient id="noticeSweepSoft" x1="0" y1="1" x2="1" y2="0">
            <stop offset="0%"   stop-color="rgba(201,162,39,0)"/>
            <stop offset="40%"  stop-color="rgba(230,196,114,.40)"/>
            <stop offset="100%" stop-color="rgba(201,162,39,0)"/>
          </linearGradient>
          <filter id="noticeBloom" x="-10%" y="-40%" width="120%" height="200%">
            <feGaussianBlur stdDeviation="9"/>
          </filter>
        </defs>
        <g fill="none" stroke-linecap="round">
          <path d="M-40 556 C 190 480, 520 446, 940 458" stroke="url(#noticeSweepSoft)" stroke-width="13" filter="url(#noticeBloom)"/>
          <path d="M-40 556 C 190 480, 520 446, 940 458" stroke="url(#noticeSweep)" stroke-width="2.6"/>
          <path d="M-40 538 C 210 456, 540 420, 940 430" stroke="url(#noticeSweep)" stroke-width="1.2" opacity=".70"/>
          <path d="M-40 520 C 230 432, 560 394, 940 402" stroke="url(#noticeSweep)" stroke-width=".8" opacity=".45"/>
        </g>
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

  </div>

  <?php if ( $multiple ) : ?>
    <div class="w-notices-dots" id="noticesDots" aria-hidden="true">
      <?php foreach ( $notices as $n => $notice ) : ?>
        <span<?php echo 0 === $n ? ' class="is-active"' : ''; ?>></span>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</section>
