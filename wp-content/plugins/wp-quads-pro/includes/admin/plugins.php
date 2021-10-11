<?php

/**
 * Admin Plugins
 *
 * @package     QUADS PRO
 * @subpackage  Admin/Plugins
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
   exit;

/**
 * Plugins row action links
 *
 * @author Michael Cannon <mc@aihr.us>
 * @since 2.0
 * @param array $links already defined action links
 * @param string $file plugin file path and name being processed
 * @return array $links
 */
function quads_pro_plugin_action_links( $links, $file ) {
   $settings_link = '<a href="' . admin_url( 'options-general.php?page=quads-settings' ) . '">' . esc_html__( 'General Settings', 'wp-quads-pro' ) . '</a>';

   if( $file == 'wp-quads-pro/wp-quads-pro.php' ) {
      array_unshift( $links, $settings_link );
   }

   return $links;
}

add_filter( 'plugin_action_links', 'quads_pro_plugin_action_links', 10, 2 );
