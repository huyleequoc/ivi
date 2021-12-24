<?php

/**
 * Library public function
 *
 * @package "All In One Meta" library
 * @since   1.0.0
 * @version 1.0.0
 */

// Prevent direct script access.
if ( !defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Get post meta data
 * @param int     $object_id  Post ID
 * @param string  $key        Meta key: can be omitted to get all data
 *
 * @return mixed
 * @since 1.0.0
 * @version 1.0.0
 */
function aiom_get_post_meta( $object_id, $key = '' ) {

	$special_key = AIOM_Config::get_post_meta_key();
	$aiom = get_post_meta( $object_id, $special_key, true );

	if ( $key ) {
		if ( $key == $special_key ) {
			$value = $aiom;
		} else if ( isset( $aiom[ $key ] ) ) {
			$value = $aiom[ $key ];
		} else {
			$value = get_post_meta( $object_id, $key, true );
		}
	} else {
		$value = array();
		$all_metas = get_post_meta( $object_id, '', true );
		foreach( $all_metas as $key => $value_array ) {
			$value[ $key ] = $value_array[0];
		}
	}

	return $value;
}

/**
 * Get term meta data
 * @param int     $object_id  Term ID
 * @param string  $key        Meta key: can be omitted to get all data
 *
 * @return mixed
 * @since 1.0.0
 * @version 1.0.0
 */
function aiom_get_term_meta( $object_id, $key = '' ) {

	$special_key = AIOM_Config::get_tax_meta_key();
	$aiom = get_term_meta( $object_id, $special_key, true );

	if ( $key ) {
		if ( $key == $special_key ) {
			$value = $aiom;
		} else if ( isset( $aiom[ $key ] ) ) {
			$value = $aiom[ $key ];
		} else {
			$value = get_term_meta( $object_id, $key, true );
		}
	} else {
		$value = array();
		$all_metas = get_term_meta( $object_id, '', true );
		foreach( $all_metas as $key => $value_array ) {
			$value[ $key ] = $value_array[0];
		}
	}

	return $value;
}

/**
 * Get term meta data
 * @param int     $object_id  Term ID
 * @param string  $key        Meta key: can be omitted to get all data
 *
 * @return mixed
 * @since 1.0.0
 * @version 1.0.0
 */
function aiom_get_user_meta( $object_id, $key = '' ) {
	
	$special_key = AIOM_Config::get_user_meta_key();
	$aiom = get_user_meta( $object_id, $special_key, true );
	
	if ( $key ) {
		if ( $key == $special_key ) {
			$value = $aiom;
		} else if ( isset( $aiom[ $key ] ) ) {
			$value = $aiom[ $key ];
		} else {
			$value = get_user_meta( $object_id, $key, true );
		}
	} else {
		$value = array();
		$all_metas = get_user_meta( $object_id, '', true );
		foreach( $all_metas as $key => $value_array ) {
			$value[ $key ] = $value_array[0];
		}
	}
	
	return $value;
}