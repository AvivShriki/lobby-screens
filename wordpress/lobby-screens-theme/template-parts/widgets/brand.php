<?php
/**
 * BrandWidget — the client's logo, header left (brief §3).
 *
 * Otzma's logo exists only as a vertical lockup (mark over wordmark over
 * tagline); there is no horizontal variant in their source files. The brief
 * forbids rebuilding it, so the header row is sized around the real file
 * rather than the file being cut down to fit a short header.
 */
$theme_uri = get_stylesheet_directory_uri();
?>
<div class="w-brand">
  <img src="<?php echo esc_url( $theme_uri . '/assets/images/otzma-logo-dark.png' ); ?>"
       alt="עוצמה · ניהול ואחזקת מבנים">
</div>
