<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

class migration_28122017 {

	/**
	 * Customizer data migrate up
	 */
	private static function customizer_migrate_up() {
		if ( ! class_exists( 'Boombox_Customizer' ) ) {
			require_once( BOOMBOX_INCLUDES_PATH . 'customizer' . DIRECTORY_SEPARATOR . 'class-boombox-customizer.php' );
		}

		$status = true;
		$config = get_option( Boombox_Customizer::OPTION_NAME, array() );

		if ( ! empty( $config ) ) {
			if( isset( $config['archive_main_posts_hide_elements'] ) ) {
				$config['home_main_posts_hide_elements'][] = 'tags';
			}

			if( isset( $config['archive_main_posts_hide_elements'] ) ) {
				$config['archive_main_posts_hide_elements'][] = 'tags';
			}

			update_option( Boombox_Customizer::OPTION_NAME, $config );
		}

		return $status;
	}

	/**
	 * Migrate pages metadata to single meta
	 * @return bool
	 */
	private static function pages_metadata_migrate_up() {

		require_once( BOOMBOX_ADMIN_PATH . 'metaboxes' . DIRECTORY_SEPARATOR . 'class-boombox-page-metaboxes.php' );
		$pages = get_posts( array( 'post_type' => 'page', 'posts_per_page' => -1 ) );

		$status = true;
		foreach( $pages as $page ) {

			$need_update = false;
			$metadata = boombox_get_post_meta( $page->ID, AIOM_Config::get_post_meta_key() );

			if( isset( $metadata['boombox_listing_hide_elements'] ) ) {
				$need_update = true;
				if( ! is_array( $metadata['boombox_listing_hide_elements'] ) ) {
					$metadata['boombox_listing_hide_elements'] = (array)$metadata['boombox_listing_hide_elements'];
				}
				$metadata['boombox_listing_hide_elements'][] = 'tags';
			}

			if( $need_update ) {
				$status = ( $status && update_post_meta( $page->ID, AIOM_Config::get_post_meta_key(), $metadata ) );
			}
		}

		return $status;
	}

	/**
	 * Organize migration tasks
	 * @return false|int
	 */
	public static function up() {
		return (
			self::customizer_migrate_up()
			&& self::pages_metadata_migrate_up()
		);
	}
	
}