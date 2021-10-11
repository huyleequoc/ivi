<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if ( ! class_exists( 'ZF_Converter' ) ){

    final class ZF_Converter extends ZF_Converter_Cloudconvert{

        /*
         * Singleton Instance
         */
        public static function Instance(){

            static $instance = null;
            if ($instance === null) {
                $instance = new ZF_Converter();
            }
            return $instance;

        }

        private function __construct(){ }

    }

}