<?php

namespace WPQuadsPro\scripts;

/**
 * Scripts
 *
 * @package     QUADS
 * @subpackage  Functions
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3.6
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;


add_action( 'admin_enqueue_scripts', '\\WPQuadsPro\\scripts\\quads_pro_load_admin_scripts', 100 );


/**
 * Load Admin Scripts
 *
 * Enqueues the required admin scripts.
 *
 * @since 1.0
 * @global $post
 * @param string $hook Page hook
 * @return void
 */
function quads_pro_load_admin_scripts( $hook ) {
    if( !apply_filters( '\\WPQuadsPro\\scripts\\quads_pro_load_admin_scripts', quads_is_admin_page(), $hook ) ) {
        return;
    }
    global $wp_version;
    
    
    $js_dir = QUADS_PRO_PLUGIN_URL . 'assets/js/';

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( quadsIsDebugMode() ) ? '' : '.min';
   
    // These have to be global
    wp_enqueue_script( 'quads-admin-pro-scripts', $js_dir . 'quads-pro-admin' . $suffix . '.js', array('jquery'), QUADS_PRO_VERSION, false );
    wp_enqueue_script( 'quads-chosen-ajaxaddition', $js_dir . 'chosen.ajaxaddition.jquery.js', array('jquery'), QUADS_PRO_VERSION, false );
}