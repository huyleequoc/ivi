<?php
/**
 * The template part for displaying post item for "mixed" listing type
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.5.8.1
 */

$listing_type = 'list';
if( ( boombox_get_paged() == 1 ) && ( Boombox_Loop_Helper::get_index() == 1 ) ) {
	if( apply_filters( 'boombox/loop-item/mixed/allow_first_as_visual', true ) ) {
		$listing_type = 'classic';
	}
} else {
	$featured_image_size = 'boombox_image768';
	$featured_video = boombox_get_post_featured_video( get_the_ID(), $featured_image_size, array( 'template' => 'listing', 'listing_type' => $listing_type ) );

	if( $featured_video ) {
		$listing_type = 'classic';
	} elseif( boombox_is_visual_post() ) {
		$listing_type = 'classic';
	} else {
		$boombox_post_thumbnail_html = boombox_get_post_thumbnail( null, $featured_image_size, array( 'play' => true, 'template' => 'listing', 'listing_type' => $listing_type ) );

		if( boombox_html_contains_gif_image( $boombox_post_thumbnail_html ) ) {
			$listing_type = 'classic';
		} elseif( boombox_html_contains_embed_or_static_video( $boombox_post_thumbnail_html ) ) {
			$listing_type = 'classic';
		}
	}
}

boombox_get_template_part( 'template-parts/listings/mixed/' . $listing_type, get_post_format() );