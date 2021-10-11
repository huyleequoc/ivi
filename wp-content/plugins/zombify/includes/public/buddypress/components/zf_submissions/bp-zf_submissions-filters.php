<?php
/**
 * BuddyPress Zombify Submissions Component filters.
 *
 *
 * @package Zombify
 * @subpackage Buddypress Submissions
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Add required query vars
 *
 * @param $query_vars
 * @return array
 */
add_filter( 'query_vars', 'zf_submissions_add_query_vars' , 10, 1 );
function zf_submissions_add_query_vars( $query_vars ) {
    $query_vars[] = zf_submissions_get_pagination_query_param();

    return $query_vars;
}