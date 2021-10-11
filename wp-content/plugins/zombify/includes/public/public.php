<?php
/**
 * Main Zombify Public Class
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Zombify_Public' ) ) :

    /**
     * Zombify Public class
     */
    final class Zombify_Public
    {

        /**
         * Zombify Public instance
         *
         * @var Zombify_Public
         */
        private static $instance;

        /**
         * Return the only existing instance of object
         *
         * @return Zombify_Public
         */
        public static function get_instance()
        {
            if (!isset(self::$instance)) {
                self::$instance = new Zombify_Public();
            }

            return self::$instance;
        }

        /**
         * Prevent object cloning
         */
        private function __clone() {}

        /**
         * Private constructor to prevent creating a new instance
         * via the 'new' operator from outside of this class.
         */
        private function __construct() {

            $this->setup_hooks();

        }

        /**
         * Setup the default actions and filters
         */
        public function setup_hooks()
        {
            do_action("zombify_public_frontend_page");
        }

    }

    /**
     * The main function responsible for returning the Zombify_Public instance.
     *
     * @return Zombify_Public
     */
    function zombify_public()
    {
        return Zombify_Public::get_instance();
    }

    zombify_public();

endif;