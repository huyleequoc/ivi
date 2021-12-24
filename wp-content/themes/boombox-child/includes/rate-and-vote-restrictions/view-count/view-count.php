<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

// restrictions
add_filter( 'boombox_vote_restrictions', 'boombox_init_view_count_restrictions' );
/**
 * @param array $vote_restrictions of Boombox_Vote_Restriction objects
 * @return array of Boombox_Vote_Restriction objects
 */
function boombox_init_view_count_restrictions( $vote_restrictions ) {
	$settings    = new Boombox_Vote_Settings( Boombox_Vote_Settings::IP_DAILY | Boombox_Vote_Settings::SESSION_TOTAL, 1, 1, 1, 1, 5 );
	$db_settings = new Boombox_Vote_Db_Settings();
	$db_settings->set_table_name( Boombox_View_Count_Helper::get_table_name() );

	$vote_restrictions[] = new Boombox_Vote_Restriction( Boombox_View_Count_Helper::get_restriction_name(), $settings, $db_settings );

	return $vote_restrictions;
}

// rate
add_filter( 'boombox_rate_criterias', 'boombox_init_view_count_rate_criteria' );
/**
 * @param array $rate_criteria_configs of Boombox_Rate_Criteria objects
 * @return array of Boombox_Rate_Criteria objects
 */
function boombox_init_view_count_rate_criteria( $rate_criteria_configs ) {
	$rate_criteria_config    = new Boombox_Rate_Criteria( 'most_viewed', esc_html__( 'Most Viewed', 'boombox' ), Boombox_View_Count_Helper::get_table_name() );
	$rate_criteria_config->set_count_column_name('point');
	$rate_criteria_configs[] = $rate_criteria_config;

	return $rate_criteria_configs;
}