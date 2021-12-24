<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

class migration_08022017 {

	/**
	 * Update share bars
	 * @return true
	 */
	private static function update_share_bars() {

		$share_bar = boombox_get_theme_option( 'single_post_general_top_sharebar' );
		$share_bar_elements = boombox_get_theme_option( 'single_post_general_share_box_elements' );

		// process customizer
		if ( ! class_exists( 'Boombox_Customizer' ) ) {
			require_once( BOOMBOX_INCLUDES_PATH . 'customizer' . DIRECTORY_SEPARATOR . 'class-boombox-customizer.php' );
		}

		$config = get_option( Boombox_Customizer::OPTION_NAME, array() );
		if ( ! empty( $config ) ) {

			if( ! $share_bar ) {
				// home settings
				if ( isset( $config[ 'home_main_posts_hide_elements' ] ) ) {
					$config[ 'home_main_posts_hide_elements' ] = (array)$config[ 'home_main_posts_hide_elements' ];
					$config[ 'home_main_posts_hide_elements' ][] = 'share_bar';
				}

				// archive settings
				if ( isset( $config[ 'archive_main_posts_hide_elements' ] ) ) {
					$config[ 'archive_main_posts_hide_elements' ] = (array)$config[ 'archive_main_posts_hide_elements' ];
					$config[ 'archive_main_posts_hide_elements' ][] = 'share_bar';
				}
			}

			// share bar elements
			$config[ 'home_main_posts_share_bar_elements' ] = $share_bar_elements;
			$config[ 'archive_main_posts_share_bar_elements' ] = $share_bar_elements;

			update_option( Boombox_Customizer::OPTION_NAME, $config );
		}

		// process pages
		require_once( BOOMBOX_ADMIN_PATH . 'metaboxes' . DIRECTORY_SEPARATOR . 'class-boombox-page-metaboxes.php' );
		$pages = get_posts( array( 'post_type' => 'page', 'posts_per_page' => -1 ) );

		foreach ( $pages as $page ) {

			// hide elements
			$meta_data = boombox_get_post_meta( $page->ID, AIOM_Config::get_post_meta_key() );
			if( ! $share_bar ) {
				if ( ! isset( $meta_data[ 'boombox_listing_hide_elements' ] ) ) {
					$meta_data[ 'boombox_listing_hide_elements' ] = array( 'share_bar' );
				} else {
					$meta_data[ 'boombox_listing_hide_elements' ] = (array)$meta_data[ 'boombox_listing_hide_elements' ];
					$meta_data[ 'boombox_listing_hide_elements' ][] = 'share_bar';
				}
			}

			// share bar elements
			$meta_data[ 'boombox_listing_share_bar_elements' ] = $share_bar_elements;

			// update meta data
			update_post_meta( $page->ID, AIOM_Config::get_post_meta_key(), $meta_data );
		}

		return true;
	}

	/**
	 * Organize migration tasks
	 * @return bool
	 */
	public static function up() {
		return self::update_share_bars();
	}

}