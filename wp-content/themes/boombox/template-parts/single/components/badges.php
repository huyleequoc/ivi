<?php
/**
 * Part to render badges for single post
 * @since 2.5.0
 * @version 2.5.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$badges_list = boombox_get_post_badge_list( array(
	'post_id'          => Boombox_Template::get_clean( 'post_id' ),
	'badges_before'    => '<div class="bb-badge-list s-post-badge-list">',
	'badges_after'     => '</div>',
	'badges_count'     => 4,
    'post_type_badges' => false
) );

if( $badges_list[ 'badges' ] ) {
	echo Boombox_Template::get_clean( 'before' ) . $badges_list[ 'badges' ] . Boombox_Template::get_clean( 'after' );
}