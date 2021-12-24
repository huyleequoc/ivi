<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Hooks
 */
add_filter( 'boombox/module_management_service/is_active_prs', function(){ return true; } );
add_action( 'wp_enqueue_scripts', 'boombox_rate_and_vote_scripts' );
apply_filters( 'boombox_reactions_is_enabled', 'boombox_reactions_is_enabled' );
add_action( 'boombox/after_migration', 'boombox_clear_rate_schedule' );

/**
 * Enqueue scripts
 */
function boombox_rate_and_vote_scripts() {
	$restriction = Boombox_Vote_Restriction::get_restriction_by_name(Boombox_View_Count_Helper::get_restriction_name());
	$settings = $restriction->get_settings();
	$track_view_request_cache = 0;
	if( $settings->need_to_check_session_total() ){
		$track_view_request_cache = intval($settings->get_session_total());
	}

	$min = boombox_get_minified_asset_suffix();
	wp_enqueue_script(
		'boombox-ajax',
		BOOMBOX_RATE_VOTE_RESTRICTIONS_URL . 'js/ajax' . $min . '.js',
		array( 'boombox-scripts-min' ),
		boombox_get_assets_version(),
		true
	);
	$ajax_array = array(
		'ajax_url' 		            => admin_url( 'admin-ajax.php' ),
		'track_view'	            => ( is_single() && boombox_get_theme_option( 'extras_post_ranking_system_enable_view_track' ) ) ? 1 : 0,
		'track_view_request_cache'  => $track_view_request_cache
	);
	wp_localize_script( 'boombox-ajax', 'boombox_ajax_params', $ajax_array );
}

/**
 * Delete all schedules on theme activation to prevent conflicts
 * @since 1.0.0
 * @version 2.1.2
 */
function boombox_clear_rate_schedule() {
	global $wpdb;
	$rate_schedule_table_name  = $wpdb->prefix . 'rate_schedule';
	$sql = "DELETE FROM `{$rate_schedule_table_name}`";
	
	return $wpdb->query( $sql );
}


/**
 * Truncate prefix_views table
 *
 * @since   2.7.9
 * @version 2.7.9
 * @return bool|int
 */
function boombox_reset_views() {
	global $wpdb;
	$views_table_name = Boombox_View_Count_Helper::get_table_name();
	$result           = $wpdb->query( 'TRUNCATE TABLE ' . $views_table_name );

	return $result;
}

/**
 * Truncate prefix_points table
 *
 * @since   2.7.9
 * @version 2.7.9
 *
 * @return bool|int
 */
function boombox_reset_points() {
	global $wpdb;
	$point_table_name = Boombox_Point_Count_Helper::get_table_name();
	$result           = $wpdb->query( 'TRUNCATE TABLE ' . $point_table_name );

	return $result;
}

/**
 * Rate and Vote Restriction Modules
 */
require_once( BOOMBOX_RATE_VOTE_RESTRICTIONS_PATH . 'class-boombox-exception-helper.php');
require_once( BOOMBOX_RATE_VOTE_RESTRICTIONS_PATH . 'vote/class-boombox-vote-restriction-trait.php');
require_once( BOOMBOX_RATE_VOTE_RESTRICTIONS_PATH . 'vote/class-boombox-vote-db-settings.php');
require_once( BOOMBOX_RATE_VOTE_RESTRICTIONS_PATH . 'vote/class-boombox-vote-settings.php');
require_once( BOOMBOX_RATE_VOTE_RESTRICTIONS_PATH . 'vote/class-boombox-vote-restriction.php');
require_once( BOOMBOX_RATE_VOTE_RESTRICTIONS_PATH . 'point-count/class-boombox-point-count-helper.php');
require_once( BOOMBOX_RATE_VOTE_RESTRICTIONS_PATH . 'view-count/class-boombox-view-count-helper.php');
require_once( BOOMBOX_RATE_VOTE_RESTRICTIONS_PATH . 'reaction/class-boombox-reaction-helper.php');
require_once( BOOMBOX_RATE_VOTE_RESTRICTIONS_PATH . 'rate/class-boombox-rate-job.php');
require_once( BOOMBOX_RATE_VOTE_RESTRICTIONS_PATH . 'rate/class-boombox-rate-time-range.php');
require_once( BOOMBOX_RATE_VOTE_RESTRICTIONS_PATH . 'rate/class-boombox-rate-criteria.php');
require_once( BOOMBOX_RATE_VOTE_RESTRICTIONS_PATH . 'rate/class-boombox-rate-query.php');
require_once( BOOMBOX_RATE_VOTE_RESTRICTIONS_PATH . 'rate/class-boombox-rate-cron.php');
require_once( BOOMBOX_RATE_VOTE_RESTRICTIONS_PATH . 'rate/class-boombox-rate-schedule.php');


require_once( BOOMBOX_RATE_VOTE_RESTRICTIONS_PATH . 'point-count/point-count.php');
require_once( BOOMBOX_RATE_VOTE_RESTRICTIONS_PATH . 'view-count/view-count.php');
require_once( BOOMBOX_RATE_VOTE_RESTRICTIONS_PATH . 'comment-count/comment-count.php');
require_once( BOOMBOX_RATE_VOTE_RESTRICTIONS_PATH . 'reaction/reaction.php');
require_once( BOOMBOX_RATE_VOTE_RESTRICTIONS_PATH . 'rate/rate.php');

/**
 * Require Ajax
 */
require_once( BOOMBOX_RATE_VOTE_RESTRICTIONS_PATH . 'ajax.php');