<?php
/**
 * Boombox Activation
 *
 * @package BoomBox_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Create 'NSFW' category
 */
function boombox_create_nsfw_category() {
	$nsfw_category_slugs = boombox_get_theme_option( 'extras_nsfw_categories' );
	if ( ! empty( $nsfw_category_slugs ) ) {
		foreach( $nsfw_category_slugs as $slug ) {
			if( ! term_exists( $slug, 'category' ) ) {
				wp_insert_term( $slug, 'category', array(
					'category_parent' => '',
					'slug'            => $slug,
				) );
			}
		}
	}
}

/**
 * Theme Activation
 */
add_action( 'after_switch_theme', 'boombox_setup_options' );
function boombox_setup_options() {
	if ( isset($_GET['activated']) && is_admin() ) {
		boombox_create_nsfw_category();
		boombox_flush_caches();
	}
}

/**
 * Create Trending Pages
 */
function boombox_create_trending_pages(){
	$meta = array(
		'_wp_page_template'              => 'page-trending-result.php',
		'boombox_hide_title_area'       => false,
		'boombox_pagination_type'        => 'load_more',
		'boombox_posts_per_page'         => get_option( 'posts_per_page' ),
		'boombox_page_ad'                => 'none'
	);
	$trending_pages = array(
		'trending' => array(
			'title' => 'Trending',
			'meta'  => $meta
		),
		'hot'      => array(
			'title' => 'Hot',
			'meta'  => $meta
		),
		'popular'  => array(
			'title' => 'Popular',
			'meta'  => $meta
		),
	);

	foreach($trending_pages as $slug => $trending_page) {
		$trending = get_page_by_path( $slug );
		if( null === $trending ){
			$page_settings = array(
				'post_type'    => 'page',
				'post_title'   => wp_strip_all_tags( $trending_page['title'] ),
				'post_name'    => $slug,
				'post_status'  => 'publish',
				'post_content' => ''
			);
			$trending_page_id = wp_insert_post( $page_settings );

			if( is_int( $trending_page_id ) && 0 < $trending_page_id ){
				foreach ( $trending_page['meta'] as $meta_name => $meta_value ){
					update_post_meta( $trending_page_id, $meta_name, $meta_value );
				}
			}
		}
	}
}