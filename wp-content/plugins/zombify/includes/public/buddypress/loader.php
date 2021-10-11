<?php
/**
 * Zombify Buddypress functions
 *
 * @package Zombify
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}


if( ! class_exists( 'Zombify_Buddypress' ) ) {

    class Zombify_Buddypress {

	    /**
	     * Holds unique instance
	     * @var Zombify_Buddypress
	     */
    	private static $_instance;

        /**
         * Constructor.
         */
        public function __construct() {
        	if( is_plugin_active( 'buddypress/bp-loader.php' ) ) {
		        $this->setup_components();
	        }
        }

        /**
         * Singleton.
         */
        public static function get_instance() {
            if ( null == static::$_instance ) {
	            static::$_instance = new self();
            }
            return static::$_instance;
        }

        /**
         * Include required files
         */
        private function setup_components() {
         
	        if( ! defined( 'ZF_BP_COMPOMENTS_PATH' ) ) {
		        define( 'ZF_BP_COMPOMENTS_PATH', trailingslashit( __DIR__ ) . 'components' );
	        }

            require_once ZF_BP_COMPOMENTS_PATH . '/zf_submissions/zf_submissions-loader.php';
            require_once ZF_BP_COMPOMENTS_PATH . '/zf_open_list_notification/loader.php';
        }

    }
}

Zombify_Buddypress::get_instance();