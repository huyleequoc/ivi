<?php
/**
 * Template part to render mixed featured media
 * @since   2.5.0
 * @version 2.5.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$protect_content= Boombox_Template::get_clean( 'protect_content' );
$media = Boombox_Template::get_clean( 'media' );
$caption = Boombox_Template::get_clean( 'caption' );
$media = apply_filters( 'boombox/single/component_content/media_mix', $media );

if( $protect_content || ! $media ) {
	return;
} ?>
<figure class="s-post-thumbnail post-thumbnail bb-mb-el"><?php echo $media . $caption; ?></figure>