<?php
/**
 * Template part to render single post comments section
 * @since 2.5.0
 * @version 2.5.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
$class = 'bb-comments bb-card-item ';
$class .= Boombox_Template::get_clean( 'class' );
$class = rtrim( $class ); ?>
<section id="boombox_comments" class="<?php echo esc_attr( $class ); ?>"><?php comments_template(); ?></section>