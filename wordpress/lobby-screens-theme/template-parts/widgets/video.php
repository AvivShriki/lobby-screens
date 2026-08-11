<?php
/**
 * VideoWidget — embedded video player. STUB: registered in the
 * architecture, disabled in the demo config. Note: YouTube embeds only
 * work when the uploader allows embedding (see README — the previously
 * requested video returned YouTube error 153, embedding disabled by its
 * owner). Enable by adding 'video' to a zone with an 'embed_url' arg.
 */
$a = wp_parse_args( $args ?? array(), array( 'embed_url' => '' ) );
if ( empty( $a['embed_url'] ) ) {
	return;
}
?>
<div class="card w-video">
  <iframe class="w-video-frame" src="<?php echo esc_url( $a['embed_url'] ); ?>"
    title="וידאו" allow="autoplay; encrypted-media" allowfullscreen></iframe>
</div>
