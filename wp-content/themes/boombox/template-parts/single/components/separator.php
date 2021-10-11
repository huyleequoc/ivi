<?php
/**
 * Template part to render single post separator
 * @since 2.5.0
 * @version 2.5.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$class = 'bb-separator';
$other_classes = Boombox_Template::get_clean( 'class' );
$class .= $other_classes ? ' ' . $other_classes : ''; ?>

<hr class="<?php echo esc_attr( $class ); ?>" />