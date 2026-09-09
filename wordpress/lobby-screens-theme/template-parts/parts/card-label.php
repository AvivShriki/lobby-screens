<?php
/**
 * The label row every card wears: mark on the right, title beside it, a gold
 * hairline across the gap and the mockup's small chevron closing the line.
 * Kept as its own partial so all four cards can't drift apart.
 *
 * $args: text, logo (url) | icon (raw svg), logo_alt
 */
?>
<div class="card-label">
  <?php if ( ! empty( $args['logo'] ) ) : ?>
    <span class="card-label-mark">
      <img src="<?php echo esc_url( $args['logo'] ); ?>" alt="<?php echo esc_attr( $args['logo_alt'] ?? '' ); ?>">
    </span>
    <span class="card-label-rule"></span>
  <?php elseif ( ! empty( $args['icon'] ) ) : ?>
    <span class="card-label-mark"><?php echo $args['icon']; // phpcs:ignore — trusted inline SVG ?></span>
  <?php endif; ?>
  <span class="card-label-text"><?php echo esc_html( $args['text'] ?? '' ); ?></span>
  <span class="card-label-fill"></span>
  <svg class="card-label-chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="M15 5 8 12l7 7"/>
  </svg>
</div>
