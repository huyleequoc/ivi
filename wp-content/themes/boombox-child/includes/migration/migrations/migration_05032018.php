<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

class migration_05032018 {
	
	private static function migrate_alnp_data() {
		if( get_option( 'auto_load_next_post_content_container' ) ) {
			update_option( 'auto_load_next_post_content_container', 'div#bb-alnp-content-container' );
			update_option( 'auto_load_next_post_navigation_container', 'div.bb-alnp-urls:last' );
			update_option( 'auto_load_next_post_comments_container', 'section#boombox_comments' );
		}
		
		return true;
	}
	
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
			$sql = $wpdb->prepare( "SELECT `term_id`,`meta_key`,`meta_value` FROM {$wpdb->termmeta} WHERE `meta_key` IN (
					'cat_icon_name',
					'term_image_icon_id',
					'hide_featured_area',
					'hide_attached_posts_featured_media',
					'term_icon_background_color',
					'post_tag_icon_name',
					'reaction_icon_file_name',
					'reaction_disable_vote',
					'term_icon_color_scheme',
					'title_area_style',
					'title_area_background_container',
					'title_area_text_color',
					'title_area_bg_color',
					'title_area_gradient_color',
					'title_area_bg_gradient_direction',
					'title_area_background_image',
					'title_area_background_image_size',
					'title_area_background_image_position',
					'title_area_background_image_repeat',
					'boombox_skip_posts_amp'
				) ORDER BY `term_id` ASC LIMIT %d,%d", $offset, $limit );

			
			$metadata = $wpdb->get_results( $sql );
			if( ! empty( $metadata ) ) {
				$new_metadata = array();
				$term_ids = array();
				
				foreach ( $metadata as $data ) {
					if( ! isset( $new_metadata[ $data->term_id ] ) ) {
						$new_metadata[ $data->term_id ] = array();
					}
					if( $tax_meta_key != $data->meta_key ) {
						$new_metadata[ $data->term_id ][ $data->meta_key ] = $data->meta_value;
						$term_ids[ $data->term_id ] = $data->term_id;
					}
				}
				
				if( count( $metadata ) >= $limit ) {
					end( $new_metadata );
					$key = key( $new_metadata );
					
					unset( $term_ids[ $key ], $new_metadata[ $key ] );
				}
				
				// setup new meta data
				foreach( $new_metadata as $term_id => $new_data ) {
					update_term_meta( $term_id, $tax_meta_key, $new_data );
				}
				
				// delete records
				$sql = "DELETE FROM `{$wpdb->termmeta}` 
							WHERE `meta_key` IN (
								'cat_icon_name',
								'term_image_icon_id',
								'hide_featured_area',
								'post_tag_icon_name',
								'reaction_icon_file_name',
								'reaction_disable_vote',
								'term_icon_color_scheme',
								'title_area_style',
								'title_area_background_container',
								'title_area_text_color',
								'title_area_bg_color',
								'title_area_gradient_color',
								'title_area_bg_gradient_direction',
								'title_area_background_image',
								'title_area_background_image_size',
								'title_area_background_image_position',
								'title_area_background_image_repeat',
							) 
							AND `term_id` IN (" . implode( ',', $term_ids ) . ");";
				$wpdb->query( $sql );
				
			} else {
				break;
			}
			
			$i++;
		}
		
		return true;
	}

	/**
	 * Purge icon fonts cache
	 */
	private static function purge_icon_fonts_cache() {
		delete_transient( 'boombox-icons' );
		delete_site_transient( 'boombox-icons' );

		return true;
	}

	/**
	 * Migrate customizer options
	 */
	private static function migrate_customizer() {
		if ( ! class_exists( 'Boombox_Customizer' ) ) {
			require_once( BOOMBOX_INCLUDES_PATH . 'customizer' . DIRECTORY_SEPARATOR . 'class-boombox-customizer.php' );
		}

		$options = get_option( Boombox_Customizer::OPTION_NAME, array() );
		if( ! empty( $options ) ) {

			// Floating Navbar
			if ( isset( $options['single_post_general_floating_navbar'] ) ) {
				if ( $options['single_post_general_floating_navbar'] ) {
					$options['single_post_general_floating_navbar'] = 'post_title';
				} else {
					$options['single_post_general_floating_navbar'] = 'none';
				}
			} else {
				$options['single_post_general_floating_navbar'] = 'post_title';
			}

			update_option( Boombox_Customizer::OPTION_NAME, $options );
		}

		return true;
	}
	
	/**
	 * Organize migration tasks
	 * @return bool
	 */
	public static function up() {
		return (
			self::migrate_alnp_data() &&
			self::migrate_term_metas() &&
			self::purge_icon_fonts_cache() &&
			self::migrate_customizer()
		);
	}
}