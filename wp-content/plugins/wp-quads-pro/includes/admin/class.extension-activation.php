<?php

namespace WPQuadsPro;

/**
 * Activation handler checks if QUADS_PRO is running before activating the Add-On
 *
 * @package     QUADS_PRO\ActivationHandler
 * @since       1.0.0
 * @version     1.0.1
 * @copyright   Copyright (c) 2017, René Hermenau
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
   exit;

/**
 * QUADSPRO Extension Activation Handler Class
 *
 * @since       1.0.0
 */
class WPQUADS_PRO_Extension_Activation {

   public $plugin_name, $plugin_path, $plugin_file, $has_wpquads;

   /**
    * Setup the activation class
    *
    * @access      public
    * @since       1.0.0
    * @return      void
    */
   public function __construct( $plugin_path, $plugin_file ) {
      // We need plugin.php!
      require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

      $plugins = get_plugins();

      // Set plugin directory
      $plugin_path = array_filter( explode( '/', $plugin_path ) );
      $this->plugin_path = end( $plugin_path );

      // Set plugin file
      $this->plugin_file = $plugin_file;

      // Set plugin name
      $this->plugin_name = str_replace( 'AdSense Integration ', '', $plugins[$this->plugin_path . '/' . $this->plugin_file]['Name'] );

      // Is QUADS installed?
      foreach ( $plugins as $plugin_path => $plugin ) {
         if( $plugin['Name'] == 'AdSense Integration WP QUADS' ) {
            $this->has_wpquads = true;
            break;
         }
      }
   }

   /**
    * Process plugin deactivation
    *
    * @access      public
    * @since       1.0.0
    * @return      void
    */
   public function run() {
      // We need plugin.php!
      require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

      // Display notice
      add_action( 'admin_notices', array($this, 'missing_notice') );
   }

   /**
    * Display notice if QUADS isn't installed
    *
    * @access      public
    * @since       1.0.0
    * @return      string The notice to display
    */
   public function missing_notice() {
      if( $this->has_wpquads ) {
         echo '<div class="error"><p>' . $this->plugin_name . __( ' requires <a href="https://wordpress.org/plugins/quick-adsense-reloaded/" target="_blank">the free plugin WP QUADS</a>! Install WP QUADS, than activate WP QUADS PRO to continue!', 'mashsb' ) . '</p></div>';
      } else {
         echo '<div class="error"><p>' . $this->plugin_name . __( ' requires <a href="https://wordpress.org/plugins/quick-adsense-reloaded/" target="_blank">the free plugin WP QUADS</a>! Install WP QUADS, than activate WP QUADS PRO to continue!', 'mashsb' ) . '</p></div>';
      }
   }

}
