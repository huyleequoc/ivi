<?php
/**
 * Boombox customizer functions
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Get Theme Options
 *
 * @param string $name          Option name
 * @param bool   $force_default Force load default value
 *
 * @return mixed
 */
function boombox_get_theme_option( $name, $force_default = false ) {
	return Boombox_Customizer::get_instance()->get_option( $name, $force_default );
}

/**
 * Get theme options set values
 * @param array $options Options
 *
 * @return array
 */
function boombox_get_theme_options_set( $options = array() ) {
	return Boombox_Customizer::get_instance()->get_options_set( $options );
}

/**
 * Check if customizer value is changed
 *
 * @param $name
 *
 * @return bool
 */
function boombox_is_theme_option_changed( $name ) {
	return boombox_get_theme_option( $name ) != boombox_get_theme_option( $name, true );
}

/**
 * Get logo data
 * @return array
 */
function boombox_get_logo() {

	$key = 'logo_data';
	if( is_single() ) {
		$key .= '_single_post';
	} elseif( is_category() ) {
		$key .= '_taxonomy_category_' . get_queried_object_id();
	}
	$data = boombox_cache_get( $key );

	if ( ! $data ) {

		$set = boombox_get_theme_options_set( array(
			'branding_logo',
			'branding_logo_hdpi',
			'branding_logo_width',
			'branding_logo_height'
		) );

		if( is_single() ) {
			$category_ids = wp_get_post_categories( get_the_ID(), array(
				'fields'     => 'ids',
				'number'     => 1,
				'meta_query' => array(
					'relation' => 'OR',
					array(
						'key'       => 'boombox_term_logo',
						'value'     => '',
						'compare'   => '!='
					),
					array(
						'key'       => 'boombox_term_logo_hdpi',
						'value'     => '',
						'compare'   => '!='
					)
				)
			) );
			if( ! empty( $category_ids ) ) {
				$cat_id = $category_ids[ 0 ];
				$term_logo = boombox_get_term_meta( $cat_id, 'boombox_term_logo' );
				$term_logo_hdpi = boombox_get_term_meta( $cat_id, 'boombox_term_logo_hdpi' );
				$set = array(
					'branding_logo'        => $term_logo ? wp_get_attachment_url( $term_logo ) : '',
					'branding_logo_hdpi'   => $term_logo_hdpi ? wp_get_attachment_url( $term_logo_hdpi ) : '',
					'branding_logo_width'  => boombox_get_term_meta( $cat_id, 'boombox_term_logo_width' ),
					'branding_logo_height' => boombox_get_term_meta( $cat_id, 'boombox_term_logo_height' )
				);
			}
		} elseif( is_category() ) {
			$cat_id = get_queried_object_id();
			$term_logo = boombox_get_term_meta( $cat_id, 'boombox_term_logo' );
			$term_logo_hdpi = boombox_get_term_meta( $cat_id, 'boombox_term_logo_hdpi' );
			if( $term_logo || $term_logo_hdpi ) {
				$set = array(
					'branding_logo'        => $term_logo ? wp_get_attachment_url( $term_logo ) : '',
					'branding_logo_hdpi'   => $term_logo_hdpi ? wp_get_attachment_url( $term_logo_hdpi ) : '',
					'branding_logo_width'  => boombox_get_term_meta( $cat_id, 'boombox_term_logo_width' ),
					'branding_logo_height' => boombox_get_term_meta( $cat_id, 'boombox_term_logo_height' )
				);;
			}
		}

		if ( ! empty ( $set['branding_logo'] ) || ! empty ( $set['branding_logo_hdpi'] ) ) {

			$data[ 'width' ] = $set['branding_logo_width'];
			$data[ 'height' ] = $set['branding_logo_height'];
			$data[ 'src_2x' ] = array();

			if ( ! empty ( $set['branding_logo_hdpi'] ) ) {
				$data[ 'src_2x' ][] = $set['branding_logo_hdpi'] . ' 2x';
			}

			if ( ! empty ( $set['branding_logo'] ) ) {
				$data[ 'src' ] = $set['branding_logo'];
				$data[ 'src_2x' ][] = $set['branding_logo'] . ' 1x';
			} else {
				$data[ 'src' ] = $set['branding_logo_hdpi'];
			}

			$data[ 'src_2x' ] = implode( ',', $data[ 'src_2x' ] );

			boombox_cache_set( $key, $data );
		}
	}

	return $data;
}