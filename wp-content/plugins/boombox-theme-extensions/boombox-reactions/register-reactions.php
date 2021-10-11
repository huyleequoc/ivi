<?php
/**
 * Register Reactions
 *
 * @package BoomBox_Theme_Extensions
 */

// Prevent direct script access
if ( ! defined( 'ABSPATH' ) ) {
	die ( 'No direct script access allowed' );
}

// Register Reactions
add_action( 'init', 'boombox_reactions', 1 );
function boombox_reactions() {
	$labels = array(
		'name'                       => _x( 'Reactions', 'Taxonomy General Name', 'boombox-theme-extensions' ),
		'singular_name'              => _x( 'Reaction', 'Taxonomy Singular Name', 'boombox-theme-extensions' ),
		'menu_name'                  => __( 'Reactions', 'boombox-theme-extensions' ),
		'all_items'                  => __( 'All Reactions', 'boombox-theme-extensions' ),
		'parent_item'                => __( 'Parent Reaction', 'boombox-theme-extensions' ),
		'parent_item_colon'          => __( 'Parent Reaction:', 'boombox-theme-extensions' ),
		'new_item_name'              => __( 'New Reaction Name', 'boombox-theme-extensions' ),
		'add_new_item'               => __( 'Add New Reaction', 'boombox-theme-extensions' ),
		'edit_item'                  => __( 'Edit Reaction', 'boombox-theme-extensions' ),
		'update_item'                => __( 'Update Reaction', 'boombox-theme-extensions' ),
		'view_item'                  => __( 'View Reaction', 'boombox-theme-extensions' ),
		'separate_items_with_commas' => __( 'Separate Reactions with commas', 'boombox-theme-extensions' ),
		'add_or_remove_items'        => __( 'Add or Remove Reactions', 'boombox-theme-extensions' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'boombox-theme-extensions' ),
		'popular_items'              => __( 'Popular Reactions', 'boombox-theme-extensions' ),
		'search_items'               => __( 'Search Reactions', 'boombox-theme-extensions' ),
		'not_found'                  => __( 'Not Found', 'boombox-theme-extensions' ),
		'no_terms'                   => __( 'No Reactions', 'boombox-theme-extensions' ),
		'items_list'                 => __( 'Reactions list', 'boombox-theme-extensions' ),
		'items_list_navigation'      => __( 'Reactions list navigation', 'boombox-theme-extensions' )
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => false,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => false
	);
	register_taxonomy( 'reaction', array( 'post' ), $args );
}