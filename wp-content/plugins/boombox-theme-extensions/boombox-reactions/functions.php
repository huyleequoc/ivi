<?php
/**
 * Reactions
 *
 * @package BoomBox_Theme_Extensions
 */

// Prevent direct script access
if ( ! defined( 'ABSPATH' ) ) {
	die ( 'No direct script access allowed' );
}

if ( ! defined( 'BBTE_REACTIONS_DIR' ) ) {
	define( 'BBTE_REACTIONS_PATH', BBTE_PLUGIN_PATH . '/boombox-reactions/' );
}
if ( ! defined( 'BBTE_REACTIONS_URL' ) ) {
	define( 'BBTE_REACTIONS_URL', BBTE_PLUGIN_URL . '/boombox-reactions/' );
}
if ( ! defined( 'BBTE_REACTIONS_ICON_DIR' ) ) {
	define( 'BBTE_REACTIONS_ICON_DIR', BBTE_REACTIONS_PATH . 'svg/' );
}
if ( ! defined( 'BBTE_REACTIONS_ICON_URL' ) ) {
	define( 'BBTE_REACTIONS_ICON_URL', BBTE_REACTIONS_URL . 'svg/' );
}

/**
 * Register Reactions
 */
include_once( BBTE_REACTIONS_PATH . 'register-reactions.php' );

/**
 * Add metabox to Reactions
 */
include_once( BBTE_REACTIONS_PATH . 'lib/class-boombox-reactions-metabox.php' );

/**
 * Add Reactions metabox to post single
 */
include_once( BBTE_REACTIONS_PATH . 'lib/class-boombox-post-reactions-metabox.php' );

/**
 * Get Reaction taxonomy name
 *
 * @return string
 */
function boombox_get_reaction_taxonomy_name(){
	return 'reaction';
}

/**
 * Get All Reactions
 *
 * @return array|bool|int|WP_Error
 */
function boombox_get_all_reactions(){
	$taxonomy = boombox_get_reaction_taxonomy_name();

	$terms_order_by = 'meta_value';
	if( function_exists( 'boombox_plugin_management_service' ) ) {
		if( boombox_plugin_management_service()->is_plugin_active( 'wp-term-order/wp-term-order.php' ) ) {
			$terms_order_by = 'order';
		}
	}

	$args = array(
		'taxonomy'      => $taxonomy,
		'hide_empty'    => false,
		'meta_query' => array(
			'relation' => 'OR',
			array(
				'key' => 'reaction_disable_vote',
				'value' => 1,
				'compare' => '!='
			),
			array(
				'key' => 'reaction_disable_vote',
				'compare' => 'NOT EXISTS'
			)
		),
		'orderby' => $terms_order_by,
		'order'	  => 'ASC'
	);

	$reactions = get_terms( $args );

	if ( is_wp_error( $reactions ) ) {
		return false;
	}
	return $reactions;
}

/**
 * Get Reaction icon url
 *
 * @param $reaction_id
 * @param $reaction_icon_file_name
 *
 * @return string
 */
function boombox_get_reaction_icon_url( $reaction_id, $reaction_icon_file_name = '' ){
	$reaction_icon_file_name = $reaction_icon_file_name ? $reaction_icon_file_name : boombox_get_term_meta( $reaction_id, 'reaction_icon_file_name' );
	
	$url = '';
	if( $reaction_icon_file_name ){

		$theme_folder_name = boombox_theme_reactions_folder_name();
		$theme_folder_uri = get_stylesheet_directory_uri() . '/' . $theme_folder_name . '/';
		$theme_folder_path = trailingslashit( get_stylesheet_directory() ) . $theme_folder_name . '/';

		if( file_exists( $theme_folder_path . $reaction_icon_file_name ) ){
			$url = $theme_folder_uri . $reaction_icon_file_name;
		} elseif( file_exists( BBTE_REACTIONS_ICON_DIR . $reaction_icon_file_name ) ) {
			$url = BBTE_REACTIONS_ICON_URL . $reaction_icon_file_name;
		}
	}
	return $url;
}