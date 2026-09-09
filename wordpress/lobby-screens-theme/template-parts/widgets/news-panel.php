<?php
/**
 * NewsPanel — Ynet card (brief §5). Data comes from the RSS feed, curated
 * through the shared lobby blocklist (see functions.php); the panel filters
 * on the standfirst as well as the headline, since it prints both.
 *
 * ynet-logo-light.png is Ynet's own dark-background lockup: the same file with
 * the black wordmark recoloured to ivory so it reads on the card. The roundel
 * is untouched.
 */
$stories = lobby_screens_get_ynet_stories( 6 );
if ( empty( $stories ) ) {
	return;
}
get_template_part( 'template-parts/parts/feed-card', null, array(
	'label'    => 'עדכוני חדשות',
	'logo'     => get_stylesheet_directory_uri() . '/assets/images/ynet-logo-light.png',
	'logo_alt' => 'Ynet',
	'stories'  => $stories,
	'hero'     => 3,
	'hold'     => 13000,
) );
