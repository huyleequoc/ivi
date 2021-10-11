<?php
/**
 * The template for displaying the single post
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.5.0
 * @var $helper Boombox_Single_Post_Template_Helper Template helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/***** header */
get_header();

$helper = Boombox_Template::init( 'post' );
$options = $helper->get_options( 'boombox_image768' );

/***** Featured strip */
if ( $options[ 'featured_strip' ] ) {
	boombox_get_template_part( 'template-parts/featured', 'strip' );
}

/***** Ad: location - After Header */
boombox_the_advertisement( 'boombox-after-header', array(
	'class' => 'container large bb-after-header',
) );

/***** Include chosen single template */
boombox_get_template_part( 'template-parts/single/layouts/' . $options['template'] );

/***** footer */
get_footer();