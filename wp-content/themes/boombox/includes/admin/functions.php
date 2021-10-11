<?php
/**
 * Boombox admin panel global functions
 *
 * @package BoomBox_Theme
 */

/************************ Posts list table ************************/

/**
 * Modify columns in posts list table
 */
function boombox_add_posts_custom_columns( $columns ) {
	
	$columns = array_merge( $columns, array(
		'total_views' => esc_html__( 'Real Views', 'boombox' )
	) );
	
	return $columns;
}
add_filter( 'manage_posts_columns' , 'boombox_add_posts_custom_columns', 10, 1 );

/**
 * Modify sortable columns in posts list table
 */
function boombox_add_posts_sortable_custom_columns( $sortable_columns ) {
	
	$sortable_columns = array_merge( $sortable_columns, array(
		'total_views' => 'total_views'
	) );
	
	return $sortable_columns;
}
add_filter( 'manage_edit-post_sortable_columns', 'boombox_add_posts_sortable_custom_columns', 10, 1 );

/**
 * Render custom column content in posts list table
 */
function boombox_render_posts_custom_columns( $column, $post_id ) {
	
	switch ( $column ) {
		case 'total_views':
			$total_views = absint( boombox_get_post_meta( $post_id, 'total_views' ) );
			echo $total_views ? boombox_numerical_word( $total_views ) : $total_views;
			
			break;
	}
	
}
add_action( 'manage_posts_custom_column' , 'boombox_render_posts_custom_columns', 10, 2 );

/**
 * Change posts list order based on defined custom keys
 */
function boombox_posts_list_table_set_order( $query ) {
	$orderby = $query->get( 'orderby');
	
	switch ( $orderby ) {
		case 'total_views':
			$query->set( 'meta_key', 'total_views' );
			$query->set( 'orderby', 'meta_value_num' );
			
			break;
	}
}
add_action( 'pre_get_posts', 'boombox_posts_list_table_set_order', 10, 1 );

/**
 * Trigger dynamic actions for plugins install / update
 */
function boombox_upgrader_process_complete( $instance, $data ) {
	if( isset( $data['plugins'] ) || isset( $data['plugin'] ) ) {
	
			if ( ! isset( $data['bulk'] ) ) {
			$data['plugins'] = isset( $data['plugins'] ) ? (array) $data['plugins'] : (array) $data['plugin'];
		}
		
		foreach ( $data['plugins'] as $plugin_main_file ) {
			$action = 'boombox/tgma/' . $data['action'] . '/' . $plugin_main_file;
			
			do_action( $action, $plugin_main_file );
		}
		
	}
}
add_action( 'upgrader_process_complete', 'boombox_upgrader_process_complete', 100, 2 );

/**
 * Allow custom mime types
 */
function boombox_allowed_mime_types( $mimes ) {
	if( ! isset( $mimes['svg'] ) ) {
		$mimes['svg'] = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';
	}

	return $mimes;
}
add_filter( 'upload_mimes', 'boombox_allowed_mime_types', 10, 1 );