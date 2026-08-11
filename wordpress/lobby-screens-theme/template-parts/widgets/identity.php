<?php
/**
 * IdentityWidget — company mark + building name + address.
 * Args: building, city, company.
 */
$a = wp_parse_args( $args ?? array(), array( 'building' => '', 'city' => '', 'company' => '' ) );
$theme_uri = get_stylesheet_directory_uri();
?>
<div class="w-identity">
  <img class="w-identity-mark" src="<?php echo esc_url( $theme_uri . '/assets/images/otzma-mark.png' ); ?>" alt="">
  <div class="w-identity-text">
    <div class="w-identity-building"><?php echo esc_html( $a['building'] ); ?></div>
    <div class="w-identity-sub"><?php echo esc_html( $a['city'] ); ?> · <?php echo esc_html( $a['company'] ); ?></div>
  </div>
</div>
