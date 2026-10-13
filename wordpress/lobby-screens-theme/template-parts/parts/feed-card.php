<?php
/**
 * FeedCard — the shared body of the two news cards (brief §5 and §6).
 *
 * A rotating hero (image, category tag, headline, standfirst) above a static
 * list of secondary updates. Only the partner logo, the label and the data
 * source differ per feed, so the markup and CSS live here once. Adding a feed
 * back means one more thin widget file, not a second copy of this.
 *
 * Two layouts, set by the caller through `modifier`: the default stacks the
 * hero image over its headline (for a tall narrow card), and `is-wide` sets
 * them side by side (for the short wide card in the v7.2 two-column layout).
 *
 * NOTE: only the ONE card uses this now. Aviv dropped the Ynet card on
 * 9.9.2026 — the ticker along the bottom already carries Ynet — but the
 * component stays general, and lobby_screens_get_ynet_stories() stays with it.
 *
 * v9.5 — the card can now be kept current by the browser as well as by the
 * server. `live` names a feed the page knows how to fetch for itself; with it
 * set the card is rendered even when the server came back with nothing, as an
 * empty shell that hides itself until something fills it. Without it the old
 * behaviour stands: no stories, no card.
 *
 * $args: label, logo (url), logo_alt, stories (array), hero (int), hold (ms),
 * modifier, list_thumbs (bool, default true), live (feed key), count (int)
 */
$stories  = $args['stories'] ?? array();
$hero_n   = $args['hero'] ?? 3;
$modifier = $args['modifier'] ?? '';
// hold is per-card on purpose: two cards crossfading on the same beat reads
// as the whole screen twitching, so the panels are given different periods.
$hold     = $args['hold'] ?? 13000;
// Whether the secondary rows carry a thumbnail. A tall card has room for them;
// the short wide one does not, and an image small enough to fit there is too
// small to tell you anything. Off means the <img> is never emitted, so the
// screen does not fetch pictures it has decided not to show.
$list_thumbs = $args['list_thumbs'] ?? true;

// A live card keeps its shell so the browser has somewhere to put the feed;
// a server-only card still disappears whole rather than leaving a frame with
// nothing in it (brief §18).
$live  = $args['live'] ?? '';
$count = (int) ( $args['count'] ?? count( $stories ) );
if ( empty( $stories ) && ! $live ) {
	return;
}

$hero = array_slice( $stories, 0, $hero_n );
$rest = array_slice( $stories, $hero_n );
?>
<section class="card w-feed reveal <?php echo esc_attr( $modifier ); ?><?php echo empty( $stories ) ? ' is-empty' : ''; ?>"
  <?php if ( $live ) : ?>
  data-live="<?php echo esc_attr( $live ); ?>"
  data-count="<?php echo $count; ?>"
  <?php /* how many the SERVER got — 0 means the page shipped a shell and the
           browser filled it. Nothing depends on this; it is here because it
           is the only way to tell those two apart by looking at a published
           page, and that is exactly the question asked the day ONE stopped
           answering the build. The class list cannot answer it: a shell
           carries the same modifiers a full card does. */ ?>
  data-served="<?php echo count( $stories ); ?>"
  data-hero="<?php echo (int) $hero_n; ?>"
  data-thumbs="<?php echo $list_thumbs ? '1' : '0'; ?>"
  <?php endif; ?>>

  <?php get_template_part( 'template-parts/parts/card-label', null, array(
    'text'     => $args['label'] ?? '',
    'logo'     => $args['logo'] ?? '',
    'logo_alt' => $args['logo_alt'] ?? '',
  ) ); ?>

  <div class="w-feed-hero" data-rotator="<?php echo esc_attr( $hold ); ?>">
    <?php foreach ( $hero as $i => $story ) : ?>
      <article class="w-feed-hero-item<?php echo 0 === $i ? ' is-active' : ''; ?>">
        <div class="w-feed-media">
          <?php if ( ! empty( $story['category'] ) ) : ?>
            <span class="w-feed-tag"><?php echo esc_html( $story['category'] ); ?></span>
          <?php endif; ?>
          <?php if ( ! empty( $story['image'] ) ) : ?>
            <img class="kb" src="<?php echo esc_url( $story['image'] ); ?>" alt="" loading="lazy">
          <?php endif; ?>
        </div>
        <div class="w-feed-copy">
          <h2 class="w-feed-title"><?php echo esc_html( $story['title'] ); ?></h2>
          <?php if ( ! empty( $story['summary'] ) ) : ?>
            <p class="w-feed-standfirst"><?php echo esc_html( $story['summary'] ); ?></p>
          <?php endif; ?>
        </div>
      </article>
    <?php endforeach; ?>
  </div>

  <div class="w-feed-list">
    <?php foreach ( $rest as $story ) : ?>
      <article class="w-feed-item">
        <?php if ( $list_thumbs && ! empty( $story['image'] ) ) : ?>
          <div class="w-feed-thumb">
            <img src="<?php echo esc_url( $story['image'] ); ?>" alt="" loading="lazy">
          </div>
        <?php endif; ?>
        <div class="w-feed-item-body">
          <?php if ( ! empty( $story['time'] ) ) : ?>
            <div class="w-feed-time"><?php echo esc_html( $story['time'] ); ?></div>
          <?php endif; ?>
          <div class="w-feed-item-title"><?php echo esc_html( $story['title'] ); ?></div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>

</section>
