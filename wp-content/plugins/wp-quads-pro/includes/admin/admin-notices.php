<?php
/**
 * Admin Notices
 *
 * @package     QUADS
 * @subpackage  Admin/Notices
 * @copyright   Copyright (c) 2017, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3.6
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;

/**
 * Admin Messages
 *
 * @since 1.3.6
 * @global $quads_options Array of all the WPQUADS Options
 * @return void
 */
function quads_pro_admin_messages() {
    global $quads_options;

    if( !current_user_can( 'update_plugins' ) ) {
        return;
    }
    
    quads_upgrade_notice();
    //quadsLicenseExpired();

}

add_action( 'admin_notices', 'quads_pro_admin_messages' );



/**
 * Show upgrade notice if wp quads pro is lower than 1.3.6
 * @return boolean
 */
function quads_upgrade_notice() {
   if (!defined( 'QUADS_VERSION' )){
      return false;
   }

   if( (version_compare( QUADS_VERSION, '1.5.3', '<' ) ) ) {
      $message = sprintf( __( 'You need to update <strong>WP QUADS</strong> to version 1.5.3 or higher. Your version of <strong>WP QUADS Pro</strong> is not supported by WP QUADS ' . QUADS_VERSION . '. Get  the latest version of WP QUADS on <a href="%1s" target="_new">wordpress.org</a> or update directly from <a href="%2s" target="_self">Installed Plugins</a>.', 'quick-adsense-reloaded' ), 'https://wordpress.org/plugins/quick-adsense-reloaded/', admin_url() . 'plugins.php' );
?>
              <div class="notice notice-error">
                  <p><?php echo $message; ?></p>
              </div> <?php
   }
}

function quadsLicenseExpired() {
   global $quads_options;

   $licensekey = empty( $quads_options['quads_wp_quads_pro_license_key'] ) ? '' : $quads_options['quads_wp_quads_pro_license_key'];

   $lic = get_option( 'quads_wp_quads_pro_license_active' );
   if( $lic && (is_object( $lic ) && $lic->success !== true) ) {
      $message = sprintf( __( 'WP QUADS Pro License has been expired or is disabled. <a href="%1s" target="_new">Renew your license key</a>.', 'quick-adsense-reloaded' ), 'https://wpquads.com/checkout/?edd_license_key=' . $licensekey . '&download_id=11' );
?>
              <div class="notice notice-error">
                  <p>//<?php echo $message; ?></p>
              </div> //<?php
   }

}
