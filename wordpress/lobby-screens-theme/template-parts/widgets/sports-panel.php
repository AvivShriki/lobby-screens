<?php
/**
 * SportsPanel — ONE card (brief §6). ONE's feed carries a real image and
 * summary per item, so no extra parsing.
 *
 * NO CATEGORY TAG. ONE has no section taxonomy, so every story used to be
 * stamped with the same constant word — and the card's own header already
 * says it. Measured on the hero: the badge was 86x41 over a 179x92 image,
 * covering 48% of its width and 21% of its area, to repeat a word printed two
 * centimetres above it. The shared component only draws a tag when a story
 * actually carries one, so simply not inventing one here gives the picture
 * back and keeps the tag working for any feed with real per-story sections.
 *
 * v7.2: this card is now short and wide (top of the left column), so it runs
 * the shared component's `is-wide` layout — hero image beside its headline
 * rather than above it — and carries two hero stories over two list rows
 * instead of three over three.
 */
/**
 * v9.5: no early return. ONE's RSS sends CORS headers, so the screen itself
 * can fetch it — measured against the Pages origin on 13.10.2026, 200 in
 * 312ms. That matters twice over: the card refills itself between rebuilds
 * instead of going stale, and it comes back on its own when the build server
 * could not reach ONE at all, which is what emptied it that morning while
 * Ynet, fetched from the same job seconds apart, kept answering.
 */
$stories = lobby_screens_get_one_stories( 4 );
get_template_part( 'template-parts/parts/feed-card', null, array(
	'live'     => 'one',
	'count'    => 4,
	'label'    => 'ספורט',
	'logo'     => lobby_screens_asset( 'assets/images/one-logo.png' ),
	'logo_alt' => 'ONE',
	'stories'  => $stories,
	'hero'     => 2,
	// Aviv: the thumbnails on the secondary rows were hurting the card. At the
	// size this layout can give them they carried no information anyway — a
	// 74x38 crop of a football photo is a smudge — while taking 86px of width
	// from the headline, which is the part anyone actually reads.
	'list_thumbs' => false,
	'hold'     => 15500,
	'modifier' => 'is-sport is-wide',
) );
