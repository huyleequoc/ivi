<?php

/**
 * Extend the meta box
 *
 * @package     QUADS_PRO\Widgets
 * @since       1.2.8
 * @copyright   Copyright (c) 2016, René Hermenau
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
   exit;

add_filter( 'quads_meta_box_post_types', 'quadspro_add_extra_post_types' );
add_filter( 'quads_quicktag_list', 'quads_add_quicktags', 100 );

/**
 * Show meta settings on all available post_types
 * 
 * @param array $content
 * @return array
 */
function quadspro_add_extra_post_types( $content ) {
   return apply_filters( 'quadspro_meta_box_post_types', get_post_types() );
}

/**
 * Add some extra options into the post edit meta box settings
 * 
 * @param array $content
 * @return array
 */
function quads_add_quicktags( $content ) {

   $quicktags = array('OffAMP' => __( 'Hide all AMP ads', 'quick-adsense-reloaded' ));

   return array_merge( $content, $quicktags );
}
