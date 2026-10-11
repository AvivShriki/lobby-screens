<?php
/**
 * ShabbatPanel — brief §7.
 *
 * v8.5 — the haftarah is gone (Aviv: the numbers mean nothing to him and the
 * parasha name is enough), so the book icon and the divider that separated the
 * two bottom cells are erased from the chrome too, and the parasha moves to the
 * centre of that row at roughly double the size. An empty cell with an icon
 * still sitting in it would have been worse than either.
 *
 * v8.4 — Aviv asked for his design to be used as the design, with only the
 * values swapped. So that is literally what this is: the card is HIS image
 * (mockups/v8-3-shabbat.png) with the nine variable strings erased out of it,
 * and nine live spans positioned back over the holes.
 *
 * Nothing about the look is reimplemented in CSS any more. The frame, the gold
 * sweep, the photo, the panel, all five icons, both hairlines and the "זמני
 * שבת" title are his pixels. There is no longer any way for the card to drift
 * away from the reference, because there is nothing left to drift.
 *
 * HOW THE HOLES WERE MADE: each string's bounding box was measured in the
 * source, then filled by interpolating the panel's own gradient vertically
 * across the box. Vertically on purpose — the first attempt lerped left to
 * right and the padding on "צאת השבת" reached the moon icon beside it and
 * smeared it across the patch. The gradient runs top-to-bottom anyway, so the
 * vertical fill is both more accurate and unable to touch a neighbour.
 *
 * POSITIONS AND SIZES are percentages of the card, taken from the same
 * measurement, so they hold at any card width. Cap heights were measured too
 * and converted to font sizes; see .w-shabbat-f in style.css.
 *
 * STILL NOTHING SCHEDULED. Hebcal's /shabbat endpoint always answers with the
 * upcoming Shabbat — asked on a Sunday it already returns the coming Friday —
 * so the values roll over on their own within the hour after havdalah.
 */
$shabbat = lobby_screens_get_shabbat_times( $args['shabbat_city'] ?? 'beersheva' );
if ( empty( $shabbat['primary']['candle'] ) ) {
	return;
}
$theme_uri = get_stylesheet_directory_uri();
$occasion  = trim( preg_replace( '/^פרשת\s+/u', '', $shabbat['occasion'] ) );
// the building's own city, not Hebcal's "ב״ש" abbreviation
$city = $args['city'] ?? ( $shabbat['label'] ?? '' );
?>
<section class="card w-shabbat reveal">
  <img class="w-shabbat-art" src="<?php echo esc_url( lobby_screens_asset( 'assets/images/shabbat-card.jpg' ) ); ?>" alt="">

  <span class="w-shabbat-f f-city"><?php echo esc_html( $city ); ?></span>

  <span class="w-shabbat-f f-lbl-candle">הדלקת נרות</span>
  <span class="w-shabbat-f f-time-candle"><?php echo esc_html( $shabbat['primary']['candle'] ); ?></span>

  <?php /* the end label is live rather than baked so a yom tov week reads
          "צאת החג" instead of quietly lying */ ?>
  <span class="w-shabbat-f f-lbl-hav"><?php echo esc_html( $shabbat['end_label'] ); ?></span>
  <span class="w-shabbat-f f-time-hav"><?php echo esc_html( $shabbat['primary']['havdalah'] ); ?></span>

  <span class="w-shabbat-f f-lbl-parasha"><?php echo esc_html( $shabbat['parasha'] ? 'פרשת' : 'המועד' ); ?></span>
  <span class="w-shabbat-f f-val-parasha"><?php echo esc_html( $occasion ); ?></span>

</section>
