<?php

/**
 * Condition Functions
 *
 * @package     QUADS
 * @subpackage  Functions/Conditions
 * @copyright   Copyright (c) 2016, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
   exit;



/**
 * Check if ad is allowed on extra conditions like post tags
 * 
 * @global array $quads_options
 * @global array $post
 * @return boolean true if tag is allowed
 */
function quads_hide_on_tags($isActive) {
   global $quads_options, $post;

   // exit early. This condition can only be valid on post tags
   if( !isset( $post ) ) {
      return false;
   }
   // do not execute further
   if( !isset( $quads_options['tags'] ) || empty( $quads_options['tags'] ) ) {
      return false;
   }

   // Create array of slugs containing tag names
   $tagsObj = wp_get_post_tags( $post->ID );
   $tags = array();

   foreach ( $tagsObj as $key => $value ) {
      $tags[$key] = trim( $value->slug );
   }


   if( isset( $quads_options['tags'] ) && isset( $tags ) && count( array_intersect( $quads_options['tags'], $tags ) ) >= 1 ) {
      return true;
   }
   return $isActive;
}

add_filter( 'quads_hide_ads', 'quads_hide_on_tags', 5 );

/**
 * Hide ads per post id
 * 
 * @global array $quads_options
 * @global array $post
 * @return boolean
 */
function quads_hide_on_post_id($isActive){
    global $quads_options, $post;
    
   // exit early. This condition can only be valid on post ids
    if (!isset($post) || empty($quads_options['excluded_id'])) {
        return $isActive;
    }

    $excluded = !empty($quads_options['excluded_id']) ? $quads_options['excluded_id'] : null;
    
    if (strpos($excluded, ',') !== false) {
        $excluded = explode(',', $excluded);
        if (in_array($post->ID, $excluded)) {
            return true;
        }
    }
    if ($post->ID == $excluded) {
        return true;
    }
    
    // default condition
    return $isActive;
}

add_filter('quads_hide_ads', 'quads_hide_on_post_id', 6);

/**
 * Hide ads on buddypess pages
 * 
 * @global array $quads_options
 * @global array $post
 * @return boolean
 */
function quads_hide_buddypress($isActive){
    global $quads_options;
    
   // exit early. This condition can only be valid if buddypress plugin is installed
    if (!function_exists('is_buddypress')) {
        return $isActive;
    }


    // Hide ads on buddypress pages
    if ( isset($quads_options['plugins']) && is_array($quads_options['plugins']) && in_array('buddypress', $quads_options['plugins']) && is_buddypress() ){
        return true;
    }
    
    // default condition
    return $isActive;
}

add_filter('quads_hide_ads', 'quads_hide_buddypress', 7);
/**
 * Hide ads on woocommerce pages
 * 
 * @global array $quads_options
 * @global array $post
 * @return boolean
 */
function quads_hide_woocommerce($isActive){
    global $quads_options;

    // exit early. This condition can only be valid if woocommeerce plugin is installed
    if (!function_exists('is_woocommerce')) {
        return $isActive;
    }


    // Hide ads on buddypress pages
    if (isset($quads_options['plugins']) && is_array($quads_options['plugins']) && in_array('woocommerce', $quads_options['plugins']) && is_woocommerce() ){
        return true;
    }
    
    // default condition
    return $isActive;
}

add_filter('quads_hide_ads', 'quads_hide_woocommerce', 8);



/**
 * Check if display conditions should be running for shortcode generated ads like [quads]
 * Must be last filter
 */

//function quads_hide_on_shortcodes($isActive){
//    global $quads_options;
//    
//    if (isset($quads_options['ignoreShortcodeCond'])){
//        return false;
//    }
//    // default filtered condition
//    return $isActive;
//}
//add_filter('quads_hide_ads', 'quads_hide_on_shortcodes', 9);

/**
 * Overwrite post_type display conditions for shortcode generated ads like [quads]
 */

//function quads_is_post_type_allowed($isActive){
//    global $quads_options;
//    
//    if (isset($quads_options['ignoreShortcodeCond'])){
//        return true;
//    }
//    // default post_type filtered condition
//    return $isActive;
//}
//add_filter('quads_post_type_allowed', 'quads_is_post_type_allowed', 1);


/**
 * 
 * @param type $arrActivatedAds
 * @return array List of ad id's
 */
function quads_filter_ads( $args ) {
   global $quads_options;

   // Get all the paragraph values[]
   $paragraph = $args['paragraph'];

   //Add some extra paragraph positions
   $number = 11; // number of paragraph ads to loop
   for ( $i = 4; $i <= $number; $i++ ) {

      $key = ($i - 4) + 1; // 1,2,3

      $paragraph['status'][$i] = isset( $quads_options['extra' . $key]['ParAds'] ) ? $quads_options['extra' . $key]['ParAds'] : 0; // Status - active | inactive
      $paragraph['id'][$i] = isset( $quads_options['extra' . $key]['ParRnd'] ) ? $quads_options['extra' . $key]['ParRnd'] : 0; // Ad id	
      $paragraph['position'][$i] = isset( $quads_options['extra' . $key]['ParNup'] ) ? $quads_options['extra' . $key]['ParNup'] : 0; // Paragraph No	
      $paragraph['end_post'][$i] = isset( $quads_options['extra' . $key]['ParCon'] ) ? $quads_options['extra' . $key]['ParCon'] : 0; // End of post - yes | no                        
   }


   for ( $i = 1; $i <= $number; $i++ ) {
      if( $paragraph['id'][$i] == 0 ) {
         $paragraph[$i] = $args['cusrnd'];
      } else {
         $paragraph[$i] = $args['cusads'] . $paragraph['id'][$i];
         array_push( $args['AdsIdCus'], $paragraph['id'][$i] );
      };
   }
   // Convert all return values into one array()
   $args = array('paragraph' => $paragraph,
       'AdsIdCus' => $args['AdsIdCus']
   );

   return $args;
}

add_filter( 'quads_filter_paragraphs', 'quads_filter_ads' );
