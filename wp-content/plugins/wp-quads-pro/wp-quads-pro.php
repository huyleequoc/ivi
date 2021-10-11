<?php
namespace WPQuadsPro;

use WPQuadsPro\includes\Autoloader;
use WPQuadsPro\vendor\google\AutoAds;

/**
 * Plugin Name: AdSense Integration WP QUADS PRO
 * Plugin URI: http://wpquads.com
 * Description: This add-on extends the WP QUADS plugin with more features
 * Author: Rene Hermenau
 * Author URI: http://wpquads.com
 * Version: 1.4.2
 * Text Domain: wp-quads-pro
 * Domain Path: languages
 *
 *
 * @package QUADS_PRO
 * @category Core
 * @author René Hermenau
 * @version 0.9.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
   exit;

// Plugin version
if( !defined( 'QUADS_PRO_VERSION' ) ) {
   define( 'QUADS_PRO_VERSION', '1.4.2' );
}

// Plugin name
if( !defined( 'QUADS_PRO_NAME' ) ) {
   define( 'QUADS_PRO_NAME', 'WP QUADS PRO' );
}

// Debug
if( !defined( 'QUADS_PRO_DEBUG' ) ) {
   define( 'QUADS_PRO_DEBUG', false );
}


if( !class_exists( 'WPQUADS_PRO' ) ) :

   /**
    * Main WPQUADS_PRO Class
    *
    * @since 1.0.0
    */
   final class WPQUADS_PRO {
      /** Singleton ************************************************************ */

      /**
       * @var WPQUADS_PRO The one and only WPQUADS_PRO
       * @since 1.0
       */
      private static $instance;

      /**
       * QUADS HTML Element Helper Object
       *
       * @var object
       * @since 2.0.0
       */
      //public $html;

      /* QUADS LOGGER Class
       * 
       */
      public $logger;

      /**
       * Main WPQUADS_PRO Instance
       *
       * Insures that only one instance of WPQUADS_PRO exists in memory at any one
       * time. Also prevents needing to define globals all over the place.
       *
       * @since 1.0
       * @static
       * @static var array $instance
       * @uses WPQUADS_PRO::setup_constants() Setup the constants needed
       * @uses WPQUADS_PRO::includes() Include the required files
       * @uses WPQUADS_PRO::load_textdomain() load the language files
       * @see QUADS()
       * @return The one true WPQUADS_PRO
       */
      public static function instance() {
         if( !isset( self::$instance ) && !( self::$instance instanceof WPQUADS_PRO ) ) {
            self::$instance = new WPQUADS_PRO;
            self::$instance->setup_constants();
            self::$instance->includes();
            self::$instance->load_textdomain();
            self::$instance->hooks();
            self::$instance->registerNamespaces();
         }
         return self::$instance;
      }
     

      /**
       * Throw error on object clone
       *
       * The whole idea of the singleton design pattern is that there is a single
       * object therefore, we don't want the object to be cloned.
       *
       * @since 1.0
       * @access protected
       * @return void
       */
      public function __clone() {
         // Cloning instances of the class is forbidden
         _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'QUADS' ), '1.0' );
      }

      /**
       * Disable unserializing of the class
       *
       * @since 1.0
       * @access protected
       * @return void
       */
      public function __wakeup() {
         // Unserializing instances of the class is forbidden
         _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'QUADS' ), '1.0' );
      }

      /**
       * Setup plugin constants
       *
       * @access private
       * @since 1.0
       * @return void
       */
      private function setup_constants() {
         global $wpdb;

         // Plugin Folder Path
         if( !defined( 'QUADS_PRO_PLUGIN_DIR' ) ) {
            define( 'QUADS_PRO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
         }

         // Plugin Folder URL
         if( !defined( 'QUADS_PRO_PLUGIN_URL' ) ) {
            define( 'QUADS_PRO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
         }

         // Plugin Root File
         if( !defined( 'QUADS_PRO_PLUGIN_FILE' ) ) {
            define( 'QUADS_PRO_PLUGIN_FILE', __FILE__ );
         }
      }

      /**
       * Include required files
       *
       * @access private
       * @since 1.0
       * @return void
       */
      private function includes() {
         global $quads_options;

         require_once QUADS_PRO_PLUGIN_DIR . 'includes/template-functions.php';
         require_once QUADS_PRO_PLUGIN_DIR . 'includes/conditions.php';
         require_once QUADS_PRO_PLUGIN_DIR . 'includes/analytics.php';
         require_once QUADS_PRO_PLUGIN_DIR . 'includes/Autoloader.php';
         $this->registerNamespaces();
         //print_r(get_declared_classes());
         $this->start_auto_ads();

         if( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
            require_once QUADS_PRO_PLUGIN_DIR . 'includes/admin/plugins.php';
            require_once QUADS_PRO_PLUGIN_DIR . 'includes/admin/settings/advanced-settings.php';
            require_once QUADS_PRO_PLUGIN_DIR . 'includes/admin/settings/auto-ads.php';
            require_once QUADS_PRO_PLUGIN_DIR . 'includes/admin/class.extension-activation.php';
            require_once QUADS_PRO_PLUGIN_DIR . 'includes/admin/meta-box.php';
            require_once QUADS_PRO_PLUGIN_DIR . 'includes/admin/scripts.php';
            require_once QUADS_PRO_PLUGIN_DIR . 'includes/admin/admin-notices.php';
            require_once QUADS_PRO_PLUGIN_DIR . 'includes/admin/ajax.php';
         }
      }
      
      private function start_auto_ads(){
         $init = new AutoAds();
      }
      

      /**
       * Register used namespaces
       */
      private function registerNamespaces() {

         $autoloader = new Autoloader();
         //$this->set( "autoloader", $autoloader );

         // Autoloader
         $autoloader->registerNamespaces( array(
             "WPQuadsPro" => array(
                 plugin_dir_path( __FILE__ ) . 'includes' . DIRECTORY_SEPARATOR,
                 //plugin_dir_path( __FILE__ ) . 'includes' . DIRECTORY_SEPARATOR.'admin/Forms' . DIRECTORY_SEPARATOR,
                 //plugin_dir_path( __FILE__ ) . 'includes' . DIRECTORY_SEPARATOR.'vendor/google' . DIRECTORY_SEPARATOR,
             )
         ) );

         // Register namespaces
         $autoloader->register();
      }

      /**
       * Run action and filter hooks
       *
       * @access      private
       * @since       1.0.0
       * @return      void
       *
       */
      private function hooks() {

         /* Instantiate class QUADS_licence 
          * Create 
          * @since 1.0.0
          * @return apply_filter mashsb_settings_licenses and create licence key input field in core mashsb
          */
         if( class_exists( 'QUADS_License' ) ) {
            $quads_sl_license = new \QUADS_License( __FILE__, 'WP QUADS PRO', QUADS_PRO_VERSION, 'Rene Hermenau', 'edd_sl_license_key' );
         }
      }

      /**
       * Loads the plugin language files
       *
       * @access public
       * @since 1.0
       * @return void
       */
      public function load_textdomain() {
         // Set filter for plugin's languages directory
         $quads_lang_dir = dirname( plugin_basename( QUADS_PRO_PLUGIN_FILE ) ) . '/languages/';
         $quads_lang_dir = apply_filters( 'quads_languages_directory', $quads_lang_dir );

         // Traditional WordPress plugin locale filter
         $locale = apply_filters( 'plugin_locale', get_locale(), 'wp-quads-pro' );
         $mofile = sprintf( '%1$s-%2$s.mo', 'wp-quads-pro', $locale );

         // Setup paths to current locale file
         $mofile_local = $quads_lang_dir . $mofile;
         $mofile_global = WP_LANG_DIR . '/wp-quads-pro/' . $mofile;
         //echo $mofile_local;
         if( file_exists( $mofile_global ) ) {
            // Look in global /wp-content/languages/quads folder
            load_textdomain( 'wp-quads-pro', $mofile_global );
         } elseif( file_exists( $mofile_local ) ) {
            // Look in local /wp-content/plugins/wp-quads-pro/languages/ folder
            load_textdomain( 'wp-quads-pro', $mofile_local );
         } else {
            // Load the default language files
            load_plugin_textdomain( 'wp-quads-pro', false, $quads_lang_dir );
         }
      }

   }

   endif; // End if class_exists check

/**
 * Check if php version is minimum 5.3
 * @return boolean
 */
function is_valid_php_version() {
   if( version_compare( PHP_VERSION, '5.3.0', '>=' ) ) {
      return true;
   }
   return false;
}

/**
 * Show notice if php version is not supported
 * @return boolean
 */
function quads_php_invalid_notice() {
   if( !is_valid_php_version() ) {
      ?>
      <div class="notice notice-error" style="background-color:#ffebeb;">
          <p><?php echo "WP QUADS Pro " . QUADS_PRO_VERSION . " plugin does not run with PHP " . PHP_VERSION . ". Minimum requirement is php 5.3"; ?></p>
      </div>
      <?php
      return false;
   }
}

add_action( 'admin_notices', '\\WPQuadsPro\\quads_php_invalid_notice' );

/**
 * Populate the $quads global with an instance of the WPQUADS_PRO class and return it.
 *
 * @return $quads a global instance class of the WPQUADS_PRO class.
 */
function wpquads_pro_loaded() {

   // PHP version not supported
   if( !is_valid_php_version() ) {
      return false;
   }


   if( !class_exists( 'QuickAdsenseReloaded' ) ) {
      if( !class_exists( 'WPQUADS_PRO_Extension_Activation' ) ) {
         require_once 'includes/admin/class.extension-activation.php';
      }
      $activation = new WPQUADS_PRO_Extension_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
      $activation = $activation->run();
   } else {
      return WPQUADS_PRO::instance();
   }
}

add_action( 'plugins_loaded', '\\WPQuadsPro\\wpquads_pro_loaded' );

/**
 * Check if wp quads pro is active and installed
 * 
 * @return boolean
 * @deprecated since version 1.3.3
 * @todo get_plugins() is very slow!!!
 */
function quads_is_active_pro() {
   return true;
}
