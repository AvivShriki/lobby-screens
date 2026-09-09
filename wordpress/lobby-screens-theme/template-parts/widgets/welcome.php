<?php
/**
 * WelcomeWidget — the greeting beside the logo (brief §3, mockup right group).
 * Two lines: the greeting in display gold, then the building line between two
 * gold hairlines. Text is passed in so a second building carries its own.
 */
$title = $args['title'] ?? 'ברוכים הבאים';
$sub   = $args['sub'] ?? '';
?>
<div class="w-welcome">
  <div class="w-welcome-title"><?php echo esc_html( $title ); ?></div>
  <?php if ( $sub ) : ?>
    <div class="w-welcome-sub"><?php echo esc_html( $sub ); ?></div>
  <?php endif; ?>
</div>
