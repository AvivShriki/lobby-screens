<?php
/**
 * Fallback template required by WordPress. The real page is front-page.php —
 * this theme has exactly one screen, there's nothing else to route to.
 */
get_header();
?>
<p style="padding:2rem;font-family:sans-serif;">
	This theme only defines the lobby screen (see <code>front-page.php</code>).
</p>
<?php
get_footer();
