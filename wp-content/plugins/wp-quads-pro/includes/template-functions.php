<?php

/**
 * Template Functions
 *
 * @package     QUADS
 * @subpackage  Functions/Templates
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.9.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
   exit;

/**
 * Return the ad label
 * 
 * @return string
 */
function quads_render_ad_label( $adcode ) {
   global $quads_options;

   $position = isset( $quads_options['adlabel'] ) && $quads_options['adlabel'] !== 'none' ? $quads_options['adlabel'] : 'none';

   $label = apply_filters( 'quads_ad_label', 'Advertisements' );

   $html = '<div class="quads-ad-label">' . $label . '</div>';

   if( $position === 'none' ) {
      return $adcode;
   }
   if( $position === 'above' ) {
      return $html . $adcode;
   }
   if( $position === 'below' ) {
      return $adcode . $html;
   }
}

add_filter( 'quads_render_ad', 'quads_render_ad_label' );

/**
 * Overwrite custom advert code.
 * Can be used in functions.php to overwrite ads
 * 
 * @param string $id number of the ad
 * @return string ad code
 */
function quads_overwrite_ad( $id ) {
   // Overwrite an ad with custom one
   $custom_ad = apply_filters( 'quads_overwrite_ad', $id );

   if( isset( $custom_ad[$id] ) ) {
      return $custom_ad[$id];
   }

   if( !empty( $custom_ad ) && !is_array( $custom_ad ) ) {
      return $custom_ad;
   }

   return false;
}

/**
 * Add more margin positions
 * 
 * @global array $quads_options
 * @param string $margin
 * @param int $id
 * @return string
 */
function quads_pro_add_margin( $style, $id ) {
   global $quads_options;

   if( empty( $quads_options['ads'][$id]['margin-left'] ) &&
           empty( $quads_options['ads'][$id]['margin-top'] ) &&
           empty( $quads_options['ads'][$id]['margin-right'] ) &&
           empty( $quads_options['ads'][$id]['margin-bottom'] ) ) {
      return $style;
   }

   $top = isset( $quads_options['ads'][$id]['margin-top'] ) ? $quads_options['ads'][$id]['margin-top'] : '0';
   $right = isset( $quads_options['ads'][$id]['margin-right'] ) ? $quads_options['ads'][$id]['margin-right'] : '0';
   $bottom = isset( $quads_options['ads'][$id]['margin-bottom'] ) ? $quads_options['ads'][$id]['margin-bottom'] : '0';
   $left = isset( $quads_options['ads'][$id]['margin-left'] ) ? $quads_options['ads'][$id]['margin-left'] : '0';

   $arr = array(
       'float:left;margin:%1$dpx %2$dpx %3$dpx %4$dpx;',
       'float:none;margin:%1$dpx %2$dpx %3$dpx %4$dpx;text-align:center;',
       'float:right;margin:%1$dpx %2$dpx %3$dpx %4$dpx;',
       'float:none;margin:%1$dpx %2$dpx %3$dpx %4$dpx;');

   $align = isset( $quads_options['ads'][$id]['align'] ) ? $quads_options['ads'][$id]['align'] : '3'; // 3 is default
   $style = sprintf( $arr[( int ) $align], $top, $right, $bottom, $left );

   return $style;
}

add_filter( 'quads_filter_margins', 'quads_pro_add_margin', 2, 3 );

/**
 * Add more margin positions for widgets
 * 
 * @global array $quads_options
 * @param string $margin
 * @param int $id
 * @return string
 */
function quads_pro_add_widget_margin( $style, $id ) {
   global $quads_options;

   if( empty( $quads_options['ads'][$id]['margin-left'] ) &&
           empty( $quads_options['ads'][$id]['margin-top'] ) &&
           empty( $quads_options['ads'][$id]['margin-right'] ) &&
           empty( $quads_options['ads'][$id]['margin-bottom'] ) ) {
      return $style;
   }

   $top = isset( $quads_options['ads'][$id]['margin-top'] ) ? $quads_options['ads'][$id]['margin-top'] : '0';
   $right = isset( $quads_options['ads'][$id]['margin-right'] ) ? $quads_options['ads'][$id]['margin-right'] : '0';
   $bottom = isset( $quads_options['ads'][$id]['margin-bottom'] ) ? $quads_options['ads'][$id]['margin-bottom'] : '0';
   $left = isset( $quads_options['ads'][$id]['margin-left'] ) ? $quads_options['ads'][$id]['margin-left'] : '0';

   $arr = array(
       'float:left;margin:%1$dpx %2$dpx %3$dpx %4$dpx;',
       'float:none;margin:%1$dpx %2$dpx %3$dpx %4$dpx;text-align:center;',
       'float:right;margin:%1$dpx %2$dpx %3$dpx %4$dpx;',
       'float:none;margin:%1$dpx %2$dpx %3$dpx %4$dpx;');

   $align = isset( $quads_options['ads'][$id]['align'] ) ? $quads_options['ads'][$id]['align'] : '3'; // 3 is default
   $style = sprintf( $arr[( int ) $align], $top, $right, $bottom, $left );

   return $style;
}

add_filter( 'quads_filter_widget_margins', 'quads_pro_add_widget_margin', 2, 3 );

/**
 * Flattens an array
 * 
 * @param array $array
 * @return array
 */
function quads_flatten( array $array ) {
   $new = array();
   foreach ( $array as $value ) {
      $new += $value;
   }
   return $new;
}


/**
 * Check extra stuff
 * 
 * @return boolean
 */
function quads_extra() {
   return true;
}
