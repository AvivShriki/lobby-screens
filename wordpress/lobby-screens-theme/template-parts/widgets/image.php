<?php
/**
 * ImageWidget — a building-supplied photo with slow Ken Burns. STUB:
 * registered in the architecture, disabled in the demo config. Enable by
 * adding 'image' to a zone with an 'src' arg.
 */
$a = wp_parse_args( $args ?? array(), array( 'src' => '', 'alt' => '' ) );
if ( empty( $a['src'] ) ) {
	return;
}
?>
<div class="card w-image">
  <img class="w-image-img" src="<?php echo esc_url( $a['src'] ); ?>" alt="<?php echo esc_attr( $a['alt'] ); ?>">
</div>
