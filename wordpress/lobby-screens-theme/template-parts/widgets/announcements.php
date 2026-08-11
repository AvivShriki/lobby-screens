<?php
/**
 * AnnouncementWidget — the hero zone: one notice at a time, huge type,
 * slow crossfade, inside a speech "bubble" (Aviv's idea) that cycles
 * through every notice. The bubble also gives the headline its own
 * surface, so legibility no longer depends on what the background photo
 * happens to be doing behind it (see README).
 * JS: rotate('.w-ann-item','.w-ann-dot',11000).
 * Args: notices = array of ['title' => ..., 'detail' => ...].
 */
$a = wp_parse_args( $args ?? array(), array( 'notices' => array() ) );
if ( empty( $a['notices'] ) ) {
	return;
}
?>
<div class="w-ann">
  <div class="w-ann-label"><span class="w-ann-label-dot"></span>הודעות לדיירים</div>
  <div class="w-ann-bubble">
    <div class="w-ann-viewport">
      <?php foreach ( $a['notices'] as $i => $n ) : ?>
      <div class="w-ann-item<?php echo 0 === $i ? ' is-active' : ''; ?>">
        <div class="w-ann-title"><?php echo esc_html( $n['title'] ); ?></div>
        <?php if ( ! empty( $n['detail'] ) ) : ?>
          <div class="w-ann-detail"><?php echo esc_html( $n['detail'] ); ?></div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="w-ann-dots">
      <?php foreach ( $a['notices'] as $i => $n ) : ?>
        <span class="w-ann-dot<?php echo 0 === $i ? ' is-active' : ''; ?>"></span>
      <?php endforeach; ?>
    </div>
  </div>
</div>
