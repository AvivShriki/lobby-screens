<?php
/**
 * ShabbatPanel — brief §7. Live Hebcal data for this building's city.
 *
 * v7.2: the card is now short and wide (bottom of the left column), so the
 * candle photo and the times sit side by side instead of stacked. The photo
 * keeps the occasion overlaid on it, which is what stops the light panel from
 * being the only thing in the card.
 *
 * NOTE ON THE BRIEF: §7 and the mockup both list four values — הדלקת נרות /
 * מוצאי שבת / כניסת שבת / יציאת שבת — but those are two pairs of synonyms, and
 * the mockup gives each pair a different time (candle lighting 18:57 arriving
 * AFTER Shabbat starts at 18:32). There are two real times in the data, so
 * four rows would mean inventing two of them.
 *
 * The labels are not hardcoded either: on a yom tov week the API returns the
 * chag instead of a parasha and the card says "המועד הקרוב" / "צאת החג" /
 * "חג שמח" to match. See lobby_screens_get_shabbat_times().
 */
$shabbat = lobby_screens_get_shabbat_times( $args['shabbat_city'] ?? 'beersheva' );
if ( empty( $shabbat['primary']['candle'] ) ) {
	return;
}
$theme_uri = get_stylesheet_directory_uri();
// Hebcal returns e.g. "פרשת עקב"; the card carries its own label above the
// name, so the repeated word comes off.
$occasion = trim( preg_replace( '/^פרשת\s+/u', '', $shabbat['occasion'] ) );

$icon_candle = '<svg viewBox="0 0 24 24" fill="none" stroke="#8C7F5F" stroke-width="1.6" stroke-linecap="round" aria-hidden="true">'
	. '<path d="M12 2c1.6 1.9 2.4 3.3 2.4 4.4a2.4 2.4 0 0 1-4.8 0C9.6 5.3 10.4 3.9 12 2Z" fill="#C9A227" stroke="none"/>'
	. '<path d="M12 9.5v11M8.5 20.5h7"/></svg>';
$icon_moon = '<svg viewBox="0 0 24 24" fill="none" stroke="#8C7F5F" stroke-width="1.6" stroke-linejoin="round" aria-hidden="true">'
	. '<path d="M20 14.5A8.2 8.2 0 0 1 9.5 4 8.4 8.4 0 1 0 20 14.5Z"/></svg>';
?>
<section class="card w-shabbat reveal">

  <?php get_template_part( 'template-parts/parts/card-label', null, array(
    'text' => 'זמני שבת',
    'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true">'
      . '<path d="M9 3.4c1.1 1.3 1.7 2.3 1.7 3.1a1.7 1.7 0 0 1-3.4 0c0-.8.6-1.8 1.7-3.1Z" fill="currentColor" stroke="none"/>'
      . '<path d="M15 3.4c1.1 1.3 1.7 2.3 1.7 3.1a1.7 1.7 0 0 1-3.4 0c0-.8.6-1.8 1.7-3.1Z" fill="currentColor" stroke="none"/>'
      . '<path d="M9 9v10M15 9v10M5.5 20.6h13"/></svg>',
  ) ); ?>

  <div class="w-shabbat-body">

    <div class="w-shabbat-media">
      <img class="kb" src="<?php echo esc_url( $theme_uri . '/assets/images/candles-pixabay.jpg' ); ?>" alt="נרות שבת">
      <?php if ( $occasion ) : ?>
        <div class="w-shabbat-parasha">
          <div class="w-shabbat-parasha-label"><?php echo esc_html( $shabbat['occasion_label'] ); ?></div>
          <div class="w-shabbat-parasha-name"><?php echo esc_html( $occasion ); ?></div>
        </div>
      <?php endif; ?>
    </div>

    <div class="w-shabbat-side">
      <div class="w-shabbat-times">
        <div class="w-shabbat-t">
          <span class="w-shabbat-t-label"><?php echo $icon_candle; // phpcs:ignore ?>הדלקת נרות</span>
          <span class="w-shabbat-t-val"><?php echo esc_html( $shabbat['primary']['candle'] ); ?></span>
        </div>
        <?php if ( ! empty( $shabbat['primary']['havdalah'] ) ) : ?>
          <div class="w-shabbat-t">
            <span class="w-shabbat-t-label"><?php echo $icon_moon; // phpcs:ignore ?><?php echo esc_html( $shabbat['end_label'] ); ?></span>
            <span class="w-shabbat-t-val"><?php echo esc_html( $shabbat['primary']['havdalah'] ); ?></span>
          </div>
        <?php endif; ?>
      </div>
      <div class="w-shabbat-foot"><?php echo esc_html( $shabbat['greeting'] ); ?></div>
    </div>

  </div>

</section>
