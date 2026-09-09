<?php
/**
 * SportsPanel — ONE card (brief §6). ONE's feed carries a real image and
 * summary per item, so no extra parsing. It has no section taxonomy, so every
 * item is tagged "ספורט".
 *
 * v7.2: this card is now short and wide (top of the left column), so it runs
 * the shared component's `is-wide` layout — hero image beside its headline
 * rather than above it — and carries two hero stories over two list rows
 * instead of three over three.
 */
$stories = lobby_screens_get_one_stories( 4 );
if ( empty( $stories ) ) {
	return;
}
foreach ( $stories as &$story ) {
	$story['category'] = 'ספורט';
}
unset( $story );

get_template_part( 'template-parts/parts/feed-card', null, array(
	'label'    => 'ספורט',
	'logo'     => get_stylesheet_directory_uri() . '/assets/images/one-logo.png',
	'logo_alt' => 'ONE',
	'stories'  => $stories,
	'hero'     => 2,
	'hold'     => 15500,
	'modifier' => 'is-sport is-wide',
) );
