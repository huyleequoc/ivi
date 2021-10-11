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

$html = '';
if( Boombox_Template::get_clean( 'comments' ) ) {
	$html .= boombox_get_post_comments_count_html( array( 'class' => 'post-comments' ) );
}

if( Boombox_Template::get_clean( 'views' ) ) {
	$html .= boombox_get_post_views_count_html( array( 'class' => 's-post-views size-lg' ) );
}

if( $html ) {
	echo Boombox_Template::get_clean( 'before' ); ?>
	<div class="s-post-meta bb-post-meta size-lg"><?php echo $html; ?></div>
	<?php echo Boombox_Template::get_clean( 'after' );
}