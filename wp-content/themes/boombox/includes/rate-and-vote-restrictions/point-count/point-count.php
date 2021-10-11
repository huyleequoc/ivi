<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_filter( 'boombox_vote_restrictions', 'boombox_init_point_count_restrictions' );
/**
 * @param array $vote_restrictions of Boombox_Vote_Restriction objects
 * @return array of Boombox_Vote_Restriction objects
 */
function boombox_init_point_count_restrictions( $vote_restrictions ) {
	$signed_in_restrict = boombox_get_theme_option( 'extras_post_ranking_system_points_login_require' );
	if( $signed_in_restrict ){
		$vote_settings = new Boombox_Vote_Settings( Boombox_Vote_Settings::USER_TOTAL, 1, 1, 1, 1, 1 );
	} else {
		$vote_settings = new Boombox_Vote_Settings( Boombox_Vote_Settings::IP_DAILY, 1, 1, 1, 1, 1 );
	}

	$vote_db_settings = new Boombox_Vote_Db_Settings();
	$vote_db_settings->set_table_name( Boombox_Point_Count_Helper::get_table_name() );
	$vote_db_settings->set_key_column_names( array( 'post_id', 'point' ) );

	$vote_restrictions[] = new Boombox_Vote_Restriction( Boombox_Point_Count_Helper::get_restriction_name(), $vote_settings, $vote_db_settings );

	return $vote_restrictions;
}

add_filter( 'boombox_rate_criterias', 'boombox_init_point_count_rate_criteria' );
/**
 * @param array $rate_criteria_configs of Boombox_Rate_Criteria objects
 * @return array of Boombox_Rate_Criteria objects
 */
function boombox_init_point_count_rate_criteria( $rate_criteria_configs ) {
	$rate_criteria_config    = new Boombox_Rate_Criteria( 'most_voted', esc_html__( 'Most Voted', 'boombox' ), Boombox_Point_Count_Helper::get_table_name() );
	$rate_criteria_config->set_count_column_name('point');
	$rate_criteria_configs[] = $rate_criteria_config;

	return $rate_criteria_configs;
}