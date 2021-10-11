<?php
/**
 * Template part to render post meta data
 * @since 2.5.0
 * @version 2.5.0
 * @var $helper Boombox_Single_Post_Template_Helper Template helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$layout = Boombox_Template::get_clean( 'layout' );
$class = '';
if( 'page_xy' == $layout ) {
	$class .= 's-post-next-prev-pg mb-md bb-mb-el';
	$class .= Boombox_Template::get_clean( 'has_secondary_sidebar' ) ? ' pg-md' : ' pg-lg';
} elseif( 'next_xy' == $layout ) {
	$class .= 's-post-next-pg mb-md bb-mb-el';
}
wp_link_pages( array(
	'layout' => $layout,
	'class'  => $class,
	'next_prev_posts' => Boombox_Template::get_clean( 'next_prev_posts' )
) );

boombox_the_advertisement( 'boombox-single-after-next-prev-buttons', array(
	'class'         => 'large bb-after-next-prev-btns',
	'in_the_loop'   => true
) );