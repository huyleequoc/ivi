<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

class migration_28042018 {

	/**
	 * Migrate term metas
	 */
	private static function migrate_term_metas() {
		global $wpdb;

		ini_set( 'max_execution_time', 0 );

		$limit = 200;
		$i = 0;
		$tax_meta_key = AIOM_Config::get_tax_meta_key();

		while ( true ) {

			$offset = $i * $limit;
			$sql = $wpdb->prepare( "SELECT `term_id`,`meta_value` FROM {$wpdb->termmeta} WHERE `meta_key`=%s ORDER BY `term_id` ASC LIMIT %d,%d", $tax_meta_key, $offset, $limit );

			$metadata = $wpdb->get_results( $sql, ARRAY_A );

			if( ! empty( $metadata ) ) {

				$to_migrate = array(
					'hide_attached_posts_featured_media',
					'term_icon_background_color'
				);
				foreach( $metadata as $term_meta_data ) {
					if( ! isset( $term_meta_data[ 'meta_value' ] ) ) {
						continue;
					}
					$metas = maybe_unserialize( $term_meta_data[ 'meta_value' ] );
					foreach ( $to_migrate as $meta_key ) {
						if( isset( $metas[ $meta_key ] ) ) {
							update_term_meta( $term_meta_data[ 'term_id' ], $meta_key, $metas[ $meta_key ] );
						}
					}
				}

			} else {
				break;
			}

			$i++;
		}

		return true;
	}

	/**
	 * Organize migration tasks
	 * @return bool
	 */
	public static function up() {
		return self::migrate_term_metas();
	}
}