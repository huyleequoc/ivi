<?php
/**
 * Boombox Social functions
 *
 * @package BoomBox_Theme_Extensions
 *
 */

// Prevent direct script access
if ( ! defined( 'ABSPATH' ) ) {
	die ( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Social' ) ) {
	include_once( BBTE_PLUGIN_PATH .'/boombox-social/class-boombox-social.php' );
}

/**
 * Show social links on front
 *
 * @param array  $args    Additional arguments
 * @param string $exclude Socials to exclude
 *
 * @return string
 * @version    1.0.0
 * @deprecated 2.0.0 boombox_get_social_links()
 * @see        boombox_get_social_links();
 */
function boombox_social_links( $exclude = '', $args = array() ) {
	_deprecated_function( __FUNCTION__, '2.0.0', "boombox_get_social_links" );

	return boombox_get_social_links( array_merge( $args, array( 'exclude' => $exclude ) ) );
}

/**
 * Get social links
 *
 * @param array $args
 *
 * @return string
 * @since 1.0.0
 * @verison 2.0.0
 */
function boombox_get_social_links( $args = array() ) {

	$r = wp_parse_args( $args, apply_filters( 'bbte/social_links_args', array(
		'link_classes' => '',
		'exclude'      => array(),
	) ) );

	$boombox_social_items = get_option( 'boombox_social_items', array() );
	if ( ! empty( $boombox_social_items ) ) {
		if ( ! empty( $r[ 'exclude' ] ) ) {
			$boombox_social_items = array_diff_key( $boombox_social_items, array_flip( $r[ 'exclude' ] ) );
		}
		$boombox_social_items = wp_list_filter( $boombox_social_items, array( 'link' => '' ), 'NOT' );
	}

	$html = '';
	foreach ( $boombox_social_items as $item_key => $boombox_social_item ) {
		/***** URL */
		$url = esc_url( $boombox_social_item[ 'link' ] );
		if ( is_email( $boombox_social_item[ 'link' ] ) ) {
			$url = sprintf( 'mailto:%s', $boombox_social_item[ 'link' ] );
		}

		/***** Class */
		$class = sprintf( boombox_is_amp() ? 'icon icon-%s %s' : 'bb-icon bb-ui-icon-%s %s', esc_attr( $boombox_social_item[ 'icon' ] ), $r[ 'link_classes' ] );

		/***** Title */
		$title = esc_html( $boombox_social_item[ 'title' ] );
		$html .= sprintf( '<li><a href="%s" class="%s" title="%s" target="_blank" rel="nofollow noopener"></a></li>',
			$url, $class, $title );
	}

	if ( $html ) {
		$html = sprintf( '<ul>%s</ul>', $html );
	}

	return $html;
}