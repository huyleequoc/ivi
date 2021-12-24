<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_filter( 'boombox_rate_criterias', 'boombox_init_comment_count_rate_criteria' );
/**
 * @param array $rate_criteria_configs of Boombox_Rate_Criteria object
 * @return array of Boombox_Rate_Criteria object
 */
function boombox_init_comment_count_rate_criteria( $rate_criteria_configs ) {
	global $wpdb;
	$rate_criteria_config = new Boombox_Rate_Criteria( 'most_discussed', esc_html__( 'Most Discussed', 'boombox' ), $wpdb->comments );
	$rate_criteria_config->set_post_id_column_name( 'comment_post_ID' );
	$rate_criteria_config->set_date_column_name( 'comment_date' );
	$rate_criteria_configs[] = $rate_criteria_config;

	return $rate_criteria_configs;
}

add_filter( 'boombox_rate_where', 'boombox_filter_rate_where_comment_count', 10, 2 );
/**
 * @param string $where
 * @param Boombox_Rate_Job $job
 * @return string
 */
function boombox_filter_rate_where_comment_count( $where, Boombox_Rate_Job $job ) {
	if( 'most_discussed' === $job->get_criteria()->get_name() ) {
		$where .= ' AND `comment_approved` = 1 ';
	}

	return $where;
}