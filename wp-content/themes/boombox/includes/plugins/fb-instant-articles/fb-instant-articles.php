<?php
/**
 * "FBInstant Articles" plugin functions
 *
 * @package BoomBox_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if( boombox_plugin_management_service()->is_plugin_active( 'fb-instant-articles/facebook-instant-articles.php' ) ) {
	
	if ( ! class_exists( 'Boombox_FB_Instant_Articles' ) ) {
		
		class Boombox_FB_Instant_Articles {

			/**
			 * Holds class single instance
			 * @var null
			 */
			private static $_instance = NULL;

			/**
			 * Singleton.
			 */
			static function get_instance() {
				if ( NULL == static::$_instance ) {
					static::$_instance = new self();
				}

				return static::$_instance;
			}
			
			/**
			 * Constructor
			 */
			function __construct() {
				$this->hooks();

				do_action( 'boombox/fbia/wakeup', $this );
			}
			
			/**
			 * Setup Hooks
			 */
			private function hooks() {
				add_filter( 'instant_articles_transformer_custom_rules_loaded', array( $this, 'custom_rules' ), 100, 1 );
				add_filter( 'instant_articles_subtitle', array( $this, 'edit_subtitle' ), 10, 2 );
			}
			
			/**
			 * Set custom rules via json configuration file to handle theme requirements
			 * @param $transformer
			 *
			 * @return mixed
			 */
			public function custom_rules( $transformer ) {
				
				$rules_file_path = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'custom-rules.json';
				if( file_exists( $rules_file_path ) ) {
				
					$rules = @file_get_contents( $rules_file_path );
					if( $rules ) {
						$transformer->loadRules( $rules );
					}
					
				}
				
				return $transformer;
			}
			
			/**
			 * Edit instant article subtitle
			 * @param $subtitle
			 * @param $instance
			 *
			 * @return mixed
			 */
			public function edit_subtitle( $subtitle, $instance ) {
				
				if( ! $subtitle ) {
					$subtitle = $instance->get_the_excerpt();
				}
				
				return $subtitle;
			}
		}
		
	}
	
	Boombox_FB_Instant_Articles::get_instance();
	
}