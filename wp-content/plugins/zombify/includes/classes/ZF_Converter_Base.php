<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if ( ! class_exists( 'ZF_Converter_Base' ) ){

    abstract class ZF_Converter_Base{

        abstract function process( $file, $target_format, $source_format = null );

        protected function log($message){

            if ( true === WP_DEBUG ) {
                if ( is_array( $message ) || is_object( $message ) ) {
                    error_log( "Converter [".date("Y-m-d H:i:s")."]: ".print_r( $message, true ) );
                } else {
                    error_log( "Converter [".date("Y-m-d H:i:s")."]: ".$message );
                }
            }

        }

    }

}