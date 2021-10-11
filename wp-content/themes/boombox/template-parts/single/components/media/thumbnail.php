<?php
/**
 * Template part to render single post thumbnail
 * @since 2.5.0
 * @version 2.5.0
 * @var $helper Boombox_Single_Post_Template_Helper Template helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
$helper = Boombox_Template::init( 'post' );
$options = $helper->get_options();

$class = 's-post-media-img post-thumbnail';
if( $options[ 'featured_image' ] ) {
	$class .= ' hidden';
} ?>
<figure class="<?php echo esc_attr( $class ); ?>"><?php echo $options[ 'featured_image' ]; ?></figure>