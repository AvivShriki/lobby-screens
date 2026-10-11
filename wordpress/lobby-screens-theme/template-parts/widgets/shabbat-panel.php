<?php
/**
 * ShabbatPanel — brief §7, rebuilt in v8.3 against Aviv's design
 * (mockups/v8-3-shabbat.png), which he asked to be followed exactly.
 *
 * Layout, right to left: a photo bleeding off the right edge with the title
 * over it, and a bordered panel holding two times above two facts.
 *
 * THREE PLACES THIS DEPARTS FROM THE REFERENCE, each on purpose:
 *
 *  1. The reference's lower-left cell prints "פרשת השבוע" as both its label
 *     AND its value — an artifact of how the image was generated. Printing the
 *     parasha name twice would be worse than either option, so that cell holds
 *     the haftarah: real data, from the same Hebcal response we already parse,
 *     and the natural companion to the parasha. One line to change if Aviv
 *     wants something else there.
 *
 *  2. The photo is the repo's own licensed candle photo, not the reference's
 *     candlesticks-and-challah still life, which is generated art with no
 *     licence behind it. Dropping a replacement into assets/images and
 *     changing this src is the whole swap.
 *
 *  3. No countdown band. v8 added a live count to candle lighting and the
 *     reference has no room for one. The data (candle_iso / havdalah_iso) and
 *     the JS that drives it are both still in place and simply find nothing to
 *     attach to, so putting it back is markup only.
 *
 * NOTHING HERE IS SCHEDULED. Hebcal's /shabbat endpoint always answers with
 * the UPCOMING Shabbat — asked on a Sunday it already returns the following
 * Friday's candle lighting and that week's parasha — so the card rolls over on
 * its own after havdalah. Verified against the live API on 11.10.2026: it
 * returned 16.10 17:48 / 17.10 18:43, Parashat Noach. The transient is an hour
 * and the page reloads every fifteen minutes, so the turnover lands within the
 * hour after Shabbat goes out with no cron and nothing to maintain.
 */
$shabbat = lobby_screens_get_shabbat_times( $args['shabbat_city'] ?? 'beersheva' );
if ( empty( $shabbat['primary']['candle'] ) ) {
	return;
}
$theme_uri = get_stylesheet_directory_uri();
// Hebcal returns "פרשת נח"; the cell carries its own label above the name, so
// the repeated word comes off.
$occasion = trim( preg_replace( '/^פרשת\s+/u', '', $shabbat['occasion'] ) );
// The building's own city, not the Hebcal label: those labels are
// abbreviations ("ב״ש") written for an older design that listed five cities in
// one narrow row. This card has room for the name, the reference spells it
// out, and the header rail already says "באר שבע" — two places on one screen
// should not disagree about where the building is.
$city = $args['city'] ?? ( $shabbat['label'] ?? '' );

$icon = function ( $paths, $class = '' ) {
	return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" '
		. 'stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths . '</svg>';
};
?>
<section class="card w-shabbat reveal">

  <div class="w-shabbat-photo" aria-hidden="true">
    <img class="kb" src="<?php echo esc_url( $theme_uri . '/assets/images/candles-pixabay.jpg' ); ?>" alt="">
  </div>

  <header class="w-shabbat-head">
    <span class="w-shabbat-brand">
      <?php // two candlesticks, as in the reference ?>
      <?php echo $icon( '<path d="M8.4 3.2c.7.9 1.1 1.6 1.1 2.1a1.1 1.1 0 0 1-2.2 0c0-.5.4-1.2 1.1-2.1Z" fill="currentColor" stroke="none"/>'
        . '<path d="M15.6 3.2c.7.9 1.1 1.6 1.1 2.1a1.1 1.1 0 0 1-2.2 0c0-.5.4-1.2 1.1-2.1Z" fill="currentColor" stroke="none"/>'
        . '<path d="M8.4 7.4v8.4M15.6 7.4v8.4M6.2 15.8h4.4M13.4 15.8h4.4M8.4 15.8v3.4M15.6 15.8v3.4M5.6 20.8h5.6M12.8 20.8h5.6"/>',
        'w-shabbat-candles' ); // phpcs:ignore ?>
      <span class="w-shabbat-title">זמני שבת</span>
    </span>
    <span class="w-shabbat-headrule" aria-hidden="true"></span>
    <span class="w-shabbat-city">
      <span><?php echo esc_html( $city ); ?></span>
      <?php echo $icon( '<path d="M12 21.2s6.4-5.6 6.4-10.4A6.4 6.4 0 0 0 5.6 10.8c0 4.8 6.4 10.4 6.4 10.4Z"/><circle cx="12" cy="10.5" r="2.3"/>' ); // phpcs:ignore ?>
    </span>
  </header>

  <div class="w-shabbat-panel">
    <?php /* the same gold sweep language as the notices card, so the two cards
            on screen read as one family rather than two treatments */ ?>
    <svg class="w-shabbat-sweep" viewBox="0 0 900 300" preserveAspectRatio="none" aria-hidden="true">
      <defs>
        <linearGradient id="shabSweep" x1="0" y1="1" x2="1" y2="0">
          <stop offset="0%"   stop-color="rgba(201,162,39,0)"/>
          <stop offset="20%"  stop-color="rgba(201,162,39,.50)"/>
          <stop offset="46%"  stop-color="rgba(248,238,206,.95)"/>
          <stop offset="72%"  stop-color="rgba(214,176,74,.65)"/>
          <stop offset="100%" stop-color="rgba(201,162,39,0)"/>
        </linearGradient>
        <filter id="shabBloom" x="-10%" y="-60%" width="120%" height="260%">
          <feGaussianBlur stdDeviation="7"/>
        </filter>
      </defs>
      <g fill="none" stroke-linecap="round">
        <path d="M-40 322 C 190 262, 520 232, 940 244" stroke="url(#shabSweep)" stroke-width="11" filter="url(#shabBloom)" opacity=".55"/>
        <path d="M-40 322 C 190 262, 520 232, 940 244" stroke="url(#shabSweep)" stroke-width="2"/>
        <path d="M-40 306 C 210 242, 540 210, 940 220" stroke="url(#shabSweep)" stroke-width="1" opacity=".6"/>
      </g>
    </svg>

    <div class="w-shabbat-times">
      <div class="w-shabbat-t">
        <span class="w-shabbat-t-label">
          <?php echo $icon( '<path d="M12 3.4c1.1 1.4 1.7 2.4 1.7 3.2a1.7 1.7 0 0 1-3.4 0c0-.8.6-1.8 1.7-3.2Z" fill="currentColor" stroke="none"/><path d="M12 9.6v9.6M8.6 20.6h6.8"/>' ); // phpcs:ignore ?>
          הדלקת נרות
        </span>
        <span class="w-shabbat-t-val"><?php echo esc_html( $shabbat['primary']['candle'] ); ?></span>
      </div>

      <span class="w-shabbat-vrule" aria-hidden="true"></span>

      <div class="w-shabbat-t">
        <span class="w-shabbat-t-label">
          <?php echo $icon( '<path d="M20 14.6A8.3 8.3 0 0 1 9.4 4 8.4 8.4 0 1 0 20 14.6Z"/>' ); // phpcs:ignore ?>
          <?php echo esc_html( $shabbat['end_label'] ); ?>
        </span>
        <span class="w-shabbat-t-val"><?php echo esc_html( $shabbat['primary']['havdalah'] ); ?></span>
      </div>
    </div>

    <span class="w-shabbat-hrule" aria-hidden="true"></span>

    <div class="w-shabbat-meta">
      <div class="w-shabbat-m">
        <span class="w-shabbat-m-label"><?php echo esc_html( $shabbat['parasha'] ? 'פרשת' : 'המועד' ); ?></span>
        <span class="w-shabbat-m-val"><?php echo esc_html( $occasion ); ?></span>
      </div>

      <?php if ( ! empty( $shabbat['haftarah'] ) ) : ?>
        <span class="w-shabbat-vrule" aria-hidden="true"></span>
        <div class="w-shabbat-m w-shabbat-m-wide">
          <div class="w-shabbat-m-text">
            <span class="w-shabbat-m-label">הפטרה</span>
            <span class="w-shabbat-m-val"><?php echo esc_html( $shabbat['haftarah'] ); ?></span>
          </div>
          <?php echo $icon( '<path d="M3.4 5.2h5.2a3 3 0 0 1 3 3v11a2.4 2.4 0 0 0-2.4-2.4H3.4Z"/><path d="M20.6 5.2h-5.2a3 3 0 0 0-3 3v11a2.4 2.4 0 0 1 2.4-2.4h5.8Z"/>', 'w-shabbat-book' ); // phpcs:ignore ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

</section>
