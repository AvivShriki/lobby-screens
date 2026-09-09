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
 * $args: label, logo (url), logo_alt, stories (array), hero (int), hold (ms), modifier
 */
$stories  = $args['stories'] ?? array();
$hero_n   = $args['hero'] ?? 3;
$modifier = $args['modifier'] ?? '';
// hold is per-card on purpose: two cards crossfading on the same beat reads
// as the whole screen twitching, so the panels are given different periods.
$hold     = $args['hold'] ?? 13000;

if ( empty( $stories ) ) {
	return;
}

$hero = array_slice( $stories, 0, $hero_n );
$rest = array_slice( $stories, $hero_n );
?>
<section class="card w-feed reveal <?php echo esc_attr( $modifier ); ?>">

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
        <?php if ( ! empty( $story['image'] ) ) : ?>
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
