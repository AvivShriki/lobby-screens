<?php
/**
 * EventsWidget — upcoming building events. STUB: component registered in
 * the architecture but disabled in the demo config (no real events data
 * source yet). Enable by adding 'events' to a zone in $lobby['zones'] and
 * passing an events array.
 */
$a = wp_parse_args( $args ?? array(), array( 'events' => array() ) );
if ( empty( $a['events'] ) ) {
	return;
}
?>
<div class="card w-events">
  <div class="card-label">אירועים קרובים</div>
  <?php foreach ( $a['events'] as $event ) : ?>
    <div class="w-events-row">
      <span class="w-events-date"><?php echo esc_html( $event['date'] ); ?></span>
      <span class="w-events-title"><?php echo esc_html( $event['title'] ); ?></span>
    </div>
  <?php endforeach; ?>
</div>
