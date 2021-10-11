<?php
/**
 * "FBInstant Articles" plugin functions
 *
 * @package Zombify
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if( is_plugin_active( 'fb-instant-articles/facebook-instant-articles.php' ) ) {
	
	if ( ! class_exists( 'Zombify_FB_Instant_Articles' ) ) {
		
		class Zombify_FB_Instant_Articles {
			
			/**
			 * Singleton.
			 */
			static function get_instance() {
				static $Inst = null;
				if ( $Inst == null ) {
					$Inst = new self();
				}
				
				return $Inst;
			}
			
			/**
			 * Constructor
			 */
			function __construct() {
				$this->hooks();
			}
			
			/**
			 * Setup Hooks
			 */
			private function hooks() {
				add_filter( 'instant_articles_transformer_custom_rules_loaded', array( $this, 'custom_rules' ), 100, 1 );
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
			
		}
		
	}
	
	Zombify_FB_Instant_Articles::get_instance();
	
}