<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_filter( 'boombox_rate_time_range', 'boombox_init_rate_time_ranges' );
/**
 * @param array $time_ranges of Boombox_Rate_Time_Range
 * @return array of Boombox_Rate_Time_Range
 */
function boombox_init_rate_time_ranges( $time_ranges ) {

	$time_ranges[] = new Boombox_Rate_Time_Range( 'day', esc_html__( 'Last 24 hours', 'boombox' ), 1 );
	$time_ranges[] = new Boombox_Rate_Time_Range( 'week', esc_html__( 'Last 7 days', 'boombox' ), 7 );
	$time_ranges[] = new Boombox_Rate_Time_Range( 'month', esc_html__( 'Last 30 days', 'boombox' ), 30 );
	$time_ranges[] = new Boombox_Rate_Time_Range( 'all', esc_html__( 'All time', 'boombox' ), -1 );

	return $time_ranges;
}