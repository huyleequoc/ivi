<?php
/**
 * Register Settings
 *
 * @package     QUADS
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2016, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
add_filter( 'quads_advanced_settings', 'quads_advanced_settings', 10, 2 );

function quads_advanced_settings( $content, $id ) {
   global $quads_options, $quads;

   // Current version of wp quads is not supported and needs to be updated to at least 1.5.2
   if( !quads_pro_allow_execution() ) {
      return false;
   }


   if( function_exists( quads_is_extra() ) && !quads_is_extra() ) {
      return sprintf( __( '<strong><a href="%s">Enter license key</a> to activate WP QUADS PRO <br>and to use all premium features.</strong>', 'quick-adsense-reloaded' ), admin_url() . 'admin.php?page=quads-settings&tab=licenses' );
   }
   //   $lic = get_option( 'quads_wp_quads_pro_license_active' );
//   if( !$lic || (is_object( $lic ) && $lic->success !== true) ) {
//      return sprintf( __( '<strong><a href="%s">Enter license key</a> to activate WP QUADS PRO <br>and to use all premium features.</strong>', 'quick-adsense-reloaded' ), admin_url() . 'admin.php?page=quads-settings&tab=licenses' );
//   }

   $html = '<div class="quads-advanced-ad-box">';
   $html .= '<h3>Advanced Options</h3>';
   $html .= '<div>' . __( '<i>Auto</i> creates a responsive ad with automatic size detection. You can overwrite the size on specific devices with manual selecting a fixed size value. <br><strong>Responsive Ad not shown?</strong>  Switch Layout to <i>Default</i> or select a fixed ad size here.<br><br>', 'quick-adsense-reloaded' ) . '</div>';
   $html .= '<div class="quads-left-box">';
   $html .= '<div class="quads-advanced-description"><label for="quads_settings[ads][' . $id . '][desktop]">' . __( 'Disable on Desktop ', 'quick-adsense-reloaded' ) . '</label></div>' . $quads->html->checkbox( array('name' => 'quads_settings[ads][' . $id . '][desktop]', 'current' => !empty( $quads_options['ads'][$id]['desktop'] ) ? $quads_options['ads'][$id]['desktop'] : null, 'class' => 'quads-checkbox') );
   $html .= '<div class="quads-advanced-description"><label for="quads_settings[ads][' . $id . '][tablet_landscape]">' . __( 'Disable on Tablet Landscape ', 'quick-adsense-reloaded' ) . '</label></div>' . $quads->html->checkbox( array('name' => 'quads_settings[ads][' . $id . '][tablet_landscape]', 'current' => !empty( $quads_options['ads'][$id]['tablet_landscape'] ) ? $quads_options['ads'][$id]['tablet_landscape'] : null, 'class' => 'quads-checkbox') );
   $html .= '<div class="quads-advanced-description"><label for="quads_settings[ads][' . $id . '][tablet_portrait]">' . __( 'Disable on Tablet Portrait ', 'quick-adsense-reloaded' ) . '</label></div>' . $quads->html->checkbox( array('name' => 'quads_settings[ads][' . $id . '][tablet_portrait]', 'current' => !empty( $quads_options['ads'][$id]['tablet_portrait'] ) ? $quads_options['ads'][$id]['tablet_portrait'] : null, 'class' => 'quads-checkbox') );
   $html .= '<div class="quads-advanced-description"><label for="quads_settings[ads][' . $id . '][phone]">' . __( 'Disable on Phone  ', 'quick-adsense-reloaded' ) . '</label></div>' . $quads->html->checkbox( array('name' => 'quads_settings[ads][' . $id . '][phone]', 'current' => !empty( $quads_options['ads'][$id]['phone'] ) ? $quads_options['ads'][$id]['phone'] : null, 'class' => 'quads-checkbox') );
   $html .= '<div class="quads-advanced-description quads-amp"><label for="quads_settings[ads][' . $id . '][amp]">' . __( 'Activate on AMP  ', 'quick-adsense-reloaded' ) . '<a class="quads-helper" href="#"></a><div class="quads-message">Activate this advert on AMP pages. Automattic AMP plugin is required. To test if the AMP ad is working it\'s required to open your site on mobile device. Ads are not shown on other devices! </div></label></div>' . $quads->html->checkbox( array('name' => 'quads_settings[ads][' . $id . '][amp]', 'current' => !empty( $quads_options['ads'][$id]['amp'] ) ? $quads_options['ads'][$id]['amp'] : null, 'class' => 'quads-checkbox quads-activate-amp') );
   $html .= '</div>';
   $html .= '<div class="quads-sizes-container">';
   $html .= '<div class="quads-sizes">';
   $html .= '<span class="adsense-size-title">' . __( 'Desktop Size: ', 'quick-adsense-reloaded' ) . '</span>' . quads_render_size_option( array('id' => $id, 'type' => 'desktop_size') );
   $html .= '<span class="adsense-size-title">' . __( 'Tablet Size: ', 'quick-adsense-reloaded' ) . '</span>' . quads_render_size_option( array('id' => $id, 'type' => 'tbl_lands_size') );
   $html .= '<span class="adsense-size-title">' . __( 'Tablet Size: ', 'quick-adsense-reloaded' ) . '</span>' . quads_render_size_option( array('id' => $id, 'type' => 'tbl_portr_size') );
   $html .= '<span class="adsense-size-title">' . __( 'Phone Size: ', 'quick-adsense-reloaded' ) . '</span>' . quads_render_size_option( array('id' => $id, 'type' => 'phone_size') );
   $html .= '</div>';
   $html .= '</div>';
   $html .= '<div class="quads-advanced-description quads-amp">' . $quads->html->textarea( array('id' => 'quads_settings[ads][' . $id . '][amp_code]', 'name' => 'quads_settings[ads][' . $id . '][amp_code]', 'class' => 'quads-amp-code', 'value' => !empty( $quads_options['ads'][$id]['amp_code'] ) ? $quads_options['ads'][$id]['amp_code'] : '', 'placeholder' => 'Optional: Add any custom AMP ad code here. Must not be exclusive AdSense - If it\'s left empty, an AdSense AMP ad with size 300x250 is automatically generated.') ) . '</div>';
   $html .='</div>';
   $html .='<div>';
   $html .='<a href="#" class="quads-delete-ad">' . __( 'Delete Ad', 'quick-adsense-reloaded' ) . '</a><br>';
   $html .='</div>';

   return $html;
}

/**
 * Add custom ad sizes to the list of available ad sizes
 * 
 * @param type $content
 * @return type
 */
function quads_custom_banner_formats( $sizes ) {
   global $quads_options;

   $content = !empty( $quads_options['custom_ad_sizes'] ) ? $quads_options['custom_ad_sizes'] : '';

   if( empty( $content ) ) {
      return $sizes;
   }

   $custom_ad_sizes = explode( ',', $content );

   if( is_array( $custom_ad_sizes ) && !empty( $custom_ad_sizes ) ) {
      foreach ( $custom_ad_sizes as $value ) {
         $ad_sizes[$value] = $value . ' Custom Size';
      }
      return array_merge( $sizes, $ad_sizes );
   }
}

add_filter( 'quads_adsense_size_formats', 'quads_custom_banner_formats' );

/**
 * Add more paragraph options 
 * 
 * @param type $content
 * @return string
 */
function quads_add_more_paragraph_settings( $content ) {
   global $quads, $quads_options;
   // Extra position 
   $html = $quads->html->checkbox( array('name' => 'quads_settings[extra1][ParAds]', 'current' => !empty( $quads_options['extra1']['ParAds'] ) ? $quads_options['extra1']['ParAds'] : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'Assign', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->select( array('options' => quads_get_ads(), 'id' => 'quads_settings[extra1][ParRnd]', 'name' => 'quads_settings[extra1][ParRnd]', 'selected' => !empty( $quads_options['extra1']['ParRnd'] ) ? $quads_options['extra1']['ParRnd'] : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' ' . __( '<strong>After Paragraph</strong>', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->select( array('options' => quads_get_values(), 'id' => 'quads_settings[extra1][ParNup]', 'name' => 'quads_settings[extra1][ParNup]', 'selected' => !empty( $quads_options['extra1']['ParNup'] ) ? $quads_options['extra1']['ParNup'] : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' ' . __( '→', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[extra1][ParCon]', 'current' => !empty( $quads_options['extra1']['ParCon'] ) ? $quads_options['extra1']['ParCon'] : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'to <strong>End of Post</strong> if fewer paragraphs are found.', 'quick-adsense-reloaded' ) . ' </br>';
   // Extra position 
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[extra2][ParAds]', 'current' => !empty( $quads_options['extra2']['ParAds'] ) ? $quads_options['extra2']['ParAds'] : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'Assign', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->select( array('options' => quads_get_ads(), 'id' => 'quads_settings[extra2][ParRnd]', 'name' => 'quads_settings[extra2][ParRnd]', 'selected' => !empty( $quads_options['extra2']['ParRnd'] ) ? $quads_options['extra2']['ParRnd'] : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' ' . __( '<strong>After Paragraph</strong>', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->select( array('options' => quads_get_values(), 'id' => 'quads_settings[extra2][ParNup]', 'name' => 'quads_settings[extra2][ParNup]', 'selected' => !empty( $quads_options['extra2']['ParNup'] ) ? $quads_options['extra2']['ParNup'] : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' ' . __( '→', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[extra2][ParCon]', 'current' => !empty( $quads_options['extra2']['ParCon'] ) ? $quads_options['extra2']['ParCon'] : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'to <strong>End of Post</strong> if fewer paragraphs are found.', 'quick-adsense-reloaded' ) . ' </br>';
   // Extra position 
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[extra3][ParAds]', 'current' => !empty( $quads_options['extra3']['ParAds'] ) ? $quads_options['extra3']['ParAds'] : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'Assign', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->select( array('options' => quads_get_ads(), 'id' => 'quads_settings[extra3][ParRnd]', 'name' => 'quads_settings[extra3][ParRnd]', 'selected' => !empty( $quads_options['extra3']['ParRnd'] ) ? $quads_options['extra3']['ParRnd'] : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' ' . __( '<strong>After Paragraph</strong>', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->select( array('options' => quads_get_values(), 'id' => 'quads_settings[extra3][ParNup]', 'name' => 'quads_settings[extra3][ParNup]', 'selected' => !empty( $quads_options['extra3']['ParNup'] ) ? $quads_options['extra3']['ParNup'] : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' ' . __( '→', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[extra3][ParCon]', 'current' => !empty( $quads_options['extra3']['ParCon'] ) ? $quads_options['extra3']['ParCon'] : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'to <strong>End of Post</strong> if fewer paragraphs are found.', 'quick-adsense-reloaded' ) . ' </br>';

   // Extra position 
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[extra4][ParAds]', 'current' => !empty( $quads_options['extra4']['ParAds'] ) ? $quads_options['extra4']['ParAds'] : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'Assign', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->select( array('options' => quads_get_ads(), 'id' => 'quads_settings[extra4][ParRnd]', 'name' => 'quads_settings[extra4][ParRnd]', 'selected' => !empty( $quads_options['extra4']['ParRnd'] ) ? $quads_options['extra4']['ParRnd'] : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' ' . __( '<strong>After Paragraph</strong>', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->select( array('options' => quads_get_values(), 'id' => 'quads_settings[extra4][ParNup]', 'name' => 'quads_settings[extra4][ParNup]', 'selected' => !empty( $quads_options['extra4']['ParNup'] ) ? $quads_options['extra4']['ParNup'] : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' ' . __( '→', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[extra4][ParCon]', 'current' => !empty( $quads_options['extra4']['ParCon'] ) ? $quads_options['extra4']['ParCon'] : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'to <strong>End of Post</strong> if fewer paragraphs are found.', 'quick-adsense-reloaded' ) . ' </br>';
   // Extra position 
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[extra5][ParAds]', 'current' => !empty( $quads_options['extra5']['ParAds'] ) ? $quads_options['extra5']['ParAds'] : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'Assign', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->select( array('options' => quads_get_ads(), 'id' => 'quads_settings[extra5][ParRnd]', 'name' => 'quads_settings[extra5][ParRnd]', 'selected' => !empty( $quads_options['extra5']['ParRnd'] ) ? $quads_options['extra5']['ParRnd'] : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' ' . __( '<strong>After Paragraph</strong>', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->select( array('options' => quads_get_values(), 'id' => 'quads_settings[extra5][ParNup]', 'name' => 'quads_settings[extra5][ParNup]', 'selected' => !empty( $quads_options['extra5']['ParNup'] ) ? $quads_options['extra5']['ParNup'] : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' ' . __( '→', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[extra5][ParCon]', 'current' => !empty( $quads_options['extra5']['ParCon'] ) ? $quads_options['extra5']['ParCon'] : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'to <strong>End of Post</strong> if fewer paragraphs are found.', 'quick-adsense-reloaded' ) . ' </br>';
   // Extra position 
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[extra6][ParAds]', 'current' => !empty( $quads_options['extra3']['ParAds'] ) ? $quads_options['extra6']['ParAds'] : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'Assign', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->select( array('options' => quads_get_ads(), 'id' => 'quads_settings[extra6][ParRnd]', 'name' => 'quads_settings[extra6][ParRnd]', 'selected' => !empty( $quads_options['extra6']['ParRnd'] ) ? $quads_options['extra6']['ParRnd'] : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' ' . __( '<strong>After Paragraph</strong>', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->select( array('options' => quads_get_values(), 'id' => 'quads_settings[extra6][ParNup]', 'name' => 'quads_settings[extra6][ParNup]', 'selected' => !empty( $quads_options['extra6']['ParNup'] ) ? $quads_options['extra6']['ParNup'] : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' ' . __( '→', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[extra6][ParCon]', 'current' => !empty( $quads_options['extra6']['ParCon'] ) ? $quads_options['extra6']['ParCon'] : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'to <strong>End of Post</strong> if fewer paragraphs are found.', 'quick-adsense-reloaded' ) . ' </br>';
   // Extra position 
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[extra7][ParAds]', 'current' => !empty( $quads_options['extra7']['ParAds'] ) ? $quads_options['extra7']['ParAds'] : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'Assign', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->select( array('options' => quads_get_ads(), 'id' => 'quads_settings[extra7][ParRnd]', 'name' => 'quads_settings[extra7][ParRnd]', 'selected' => !empty( $quads_options['extra7']['ParRnd'] ) ? $quads_options['extra7']['ParRnd'] : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' ' . __( '<strong>After Paragraph</strong>', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->select( array('options' => quads_get_values(), 'id' => 'quads_settings[extra7][ParNup]', 'name' => 'quads_settings[extra7][ParNup]', 'selected' => !empty( $quads_options['extra7']['ParNup'] ) ? $quads_options['extra7']['ParNup'] : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' ' . __( '→', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[extra7][ParCon]', 'current' => !empty( $quads_options['extra7']['ParCon'] ) ? $quads_options['extra7']['ParCon'] : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'to <strong>End of Post</strong> if fewer paragraphs are found.', 'quick-adsense-reloaded' ) . ' </br>';
   // Extra position 
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[extra8][ParAds]', 'current' => !empty( $quads_options['extra8']['ParAds'] ) ? $quads_options['extra8']['ParAds'] : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'Assign', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->select( array('options' => quads_get_ads(), 'id' => 'quads_settings[extra8][ParRnd]', 'name' => 'quads_settings[extra8][ParRnd]', 'selected' => !empty( $quads_options['extra8']['ParRnd'] ) ? $quads_options['extra8']['ParRnd'] : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' ' . __( '<strong>After Paragraph</strong>', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->select( array('options' => quads_get_values(), 'id' => 'quads_settings[extra8][ParNup]', 'name' => 'quads_settings[extra8][ParNup]', 'selected' => !empty( $quads_options['extra8']['ParNup'] ) ? $quads_options['extra8']['ParNup'] : null, 'show_option_all' => false, 'show_option_none' => false) );
   $html .= ' ' . __( '→', 'quick-adsense-reloaded' ) . ' ';
   $html .= $quads->html->checkbox( array('name' => 'quads_settings[extra8][ParCon]', 'current' => !empty( $quads_options['extra8']['ParCon'] ) ? $quads_options['extra8']['ParCon'] : null, 'class' => 'quads-checkbox quads-assign') );
   $html .= ' ' . __( 'to <strong>End of Post</strong> if fewer paragraphs are found.', 'quick-adsense-reloaded' ) . ' </br>';
   return $html;
}

add_filter( 'quads_extra_paragraph', 'quads_add_more_paragraph_settings' );

/**
 * Add more advanced settings
 * 
 * @param array $settings
 * @return array
 */
function quads_add_advanced_settings( $settings ) {
   global $quads_options;

   $more_settings = array(
       'excluded_id' => array(
           "id" => "excluded_id",
           "name" => __( "Hide Ads for Post ID's", "quick-adsense-reloaded" ),
           "desc" => __( "", "quick-adsense-reloaded" ),
           "helper-desc" => __( "Enter post id's separated by comma that can not see any ads, e.g. 0,1,5,6", "quick-adsense-reloaded" ),
           "type" => "text",
       ),
       'user_roles' => array(
           "id" => "user_roles",
           "name" => __( "Hide Ads for User Roles", "quick-adsense-reloaded" ),
           "desc" => __( "Select user roles that can not see any ads. If nothing is set ads are visible for all user roles including public visitors.", "quick-adsense-reloaded" ),
           "helper-desc" => __( "Select user roles that can not see any ads. If nothing is set ads are visible for all user roles including public visitors.", "quick-adsense-reloaded" ),
           "type" => "multiselect",
           "options" => quads_get_user_roles(),
           "placeholder" => __( "Select User Roles", "quick-adsense-reloaded" ),
           "std" => __( "All Roles", "quick-adsense-reloaded" )
       ),
       'tags' => array(
           "id" => "tags",
           "name" => __( "Hide Ads for Tags", "quick-adsense-reloaded" ),
           "desc" => __( "Select tags where ads are not shown. If nothing is set ads are shown for all post tags.", "quick-adsense-reloaded" ),
           "helper-desc" => __( "Select tags where ads are not shown. If nothing is set ads are shown for all post tags.", "quick-adsense-reloaded" ),
           "type" => "multiselect_ajax",
           //"options" => quads_get_tags(),
           "options" => array(),
           "placeholder" => __( "Select Post Tags", "quick-adsense-reloaded" ),
           "std" => __( "All Tags", "quick-adsense-reloaded" )
       ),
       'plugins' => array(
           "id" => "plugins",
           "name" => __( "Hide Ads for plugins", "quick-adsense-reloaded" ),
           "desc" => __( "", "quick-adsense-reloaded" ),
           "helper-desc" => __( "Hide ads on plugin specific pages", "quick-adsense-reloaded" ),
           "type" => "multiselect",
           "options" => array('buddypress' => 'buddypress', 'woocommerce' => 'woocommerce'),
       ),
       'custom_ad_sizes' => array(
           'id' => 'custom_ad_sizes',
           'name' => __( 'Custom Banner Sizes', 'quick-adsense-reloaded' ),
           'desc' => '<br>' . __( 'Add more banner formats separated by comma. e.g. 600 x 100, 400 x 50', 'quick-adsense-reloaded' ),
           'type' => 'textarea',
           'size' => 3
       ),
       'adlabel' => array(
           'id' => 'adlabel',
           'name' => __( 'Ad label', 'quick-adsense-reloaded' ),
           'desc' => __( 'Add Label <i>Advertisement</i> above or below ads', 'quick-adsense-reloaded' ),
           'type' => 'select',
           'options' => array(
               'none' => 'No Label',
               'above' => 'Above Ads',
               'below' => 'Below Ads',
           )
       ),
       array(
           'id' => 'ignoreShortcodeCond',
           'name' => 'Ignore Conditions for Shortcodes',
           'helper-desc' => 'Activate this to ignore above display conditions for post shortcodes like [quads]. Using a shortcode will result in showing of the ad, no matter if there is any display condition which usually would prevent this.',
           'type' => 'checkbox'
       )
   );

   // Put them in position 5
   quads_array_insert( $settings, $more_settings, 5 );
   return $settings;
}

add_filter( 'quads_settings_general', 'quads_add_advanced_settings', 1000 );

/**
 * Add more settings under tab Plugin Settings
 * 
 * @param array $settings
 * @return array
 */
function quads_add_plugin_settings( $settings ) {
   global $quads_options;

   $more_settings = array(
       'analytics' => array(
           'id' => 'analytics',
           'name' => __( 'Google Analytics Integration', 'quick-adsense-reloaded' ),
           'desc' => __( 'Enable', 'quick-adsense-reloaded' ),
           "helper-desc" => __( "Check how many visitors are using ad blockers in your Google Analytics account from the event tracking in <i>Google Analytics->Behavior->Events</i>. This only works if your visitors are using regular ad blockers like 'adBlock'. There are browser plugins which block all external requests like the  software uBlock origin. This also block google analytics and as a result you do get any analytics data at all.", "quick-adsense-reloaded" ),
           'type' => 'checkbox'
       ),
       'ad_blocker_message' => array(
           'id' => 'ad_blocker_message',
           'name' => __( 'Ask user to deactivate ad blocker', 'quick-adsense-reloaded' ),
           'desc' => __( 'Enable', 'quick-adsense-reloaded' ),
           "helper-desc" => sprintf( __( "If visitor is using an ad blocker he will see a message instead of an ad, asking him to deactivate the ad blocker. <a href='%s' target='_blank'>Read here</a> how to customize colors and text.", "quick-adsense-reloaded" ), 'http://wpquads.com/docs/customize-ad-blocker-notice/' ),
           'type' => 'checkbox'
       )
   );


   // Put them in position 100
   quads_array_insert( $settings, $more_settings, 100 );
   return $settings;
}

add_filter( 'quads_settings_general', 'quads_add_plugin_settings', 1000 );

/**
 * Put array into specific position in another array
 * 
 * 
 * @param array      $array
 * @param int|string $position
 * @param mixed      $insert
 */
function quads_array_insert( &$array, $insert, $position ) {
   settype( $array, "array" );
   settype( $insert, "array" );
   settype( $position, "int" );

//if pos is start, just merge them
   if( $position == 0 ) {
      $array = array_merge( $insert, $array );
   } else {

      //if pos is end just merge them
      if( $position >= (count( $array ) - 1) ) {
         $array = array_merge( $array, $insert );
      } else {
         //split into head and tail, then merge head+inserted bit+tail
         $head = array_slice( $array, 0, $position );
         $tail = array_slice( $array, $position );
         $array = array_merge( $head, $insert, $tail );
      }
   }
}

/**
 * Render extra margin fields
 * 
 * @global array $quads_options
 * @param string $content
 * @param int $id
 */
function quads_render_margins( $content, $id ) {
   global $quads_options;

   if( function_exists( 'quads_is_extra' ) && !quads_is_extra() ) {
      return '';
   }

//   if(  !quadsIsActivated()){
//      return '';
//   }
   // Current version of wp quads is not supported and needs to be updated to at least 1.5.3
   if( !quads_pro_allow_execution() ) {
      return false;
   }

   // One margin value rules the world. This is the default option since releasing of the free version
   if( empty( $quads_options['ads'][$id]['margin-left'] ) &&
           empty( $quads_options['ads'][$id]['margin-top'] ) &&
           empty( $quads_options['ads'][$id]['margin-right'] ) &&
           empty( $quads_options['ads'][$id]['margin-bottom'] ) &&
           !empty( $quads_options['ads'][$id]['margin'] ) ) {
      // Get old margin values depending on the alignment option
      $align = isset( $quads_options['ads'][$id]['align'] ) ? $quads_options['ads'][$id]['align'] : '3'; // 3 is default

      switch ( $align ) {
         case '0':
            $top = $quads_options['ads'][$id]['margin'];
            $right = $quads_options['ads'][$id]['margin'];
            $bottom = $quads_options['ads'][$id]['margin'];
            $left = '0';
            break;
         case '1':
            $top = $quads_options['ads'][$id]['margin'];
            $right = '0';
            $bottom = $quads_options['ads'][$id]['margin'];
            $left = '0';
            break;
         case '2':
            $top = $quads_options['ads'][$id]['margin'];
            $right = '0';
            $bottom = $quads_options['ads'][$id]['margin'];
            $left = $quads_options['ads'][$id]['margin'];
            break;
         case '3':
            $top = '0';
            $right = '0';
            $bottom = '0';
            $left = '0';
            break;
      }
   } else {
      // New margin setting allows control of all four positions. Since WP QUADS PRO 1.2.7
      $top = isset( $quads_options['ads'][$id]['margin-top'] ) ? $quads_options['ads'][$id]['margin-top'] : '';
      $right = isset( $quads_options['ads'][$id]['margin-right'] ) ? $quads_options['ads'][$id]['margin-right'] : '';
      $bottom = isset( $quads_options['ads'][$id]['margin-bottom'] ) ? $quads_options['ads'][$id]['margin-bottom'] : '';
      $left = isset( $quads_options['ads'][$id]['margin-left'] ) ? $quads_options['ads'][$id]['margin-left'] : '';
   }
   ?>
   <br />
   <label><?php _e( 'Margin', 'quick-adsense-reloaded' ); ?> &nbsp;&nbsp;&nbsp;&nbsp; <?php _e( 'Top:', 'quick-adsense-reloaded' ); ?> </label><input type="number" step="1" max="" min="" class="small-text" id="quads_settings[ads][<?php echo $id; ?>][margin-top]" name="quads_settings[ads][<?php echo $id; ?>][margin-top]" value="<?php echo esc_attr( stripslashes( $top ) ); ?>"/>px
   <label style="margin-left:10px;"><?php _e( 'Right:', 'quick-adsense-reloaded' ); ?> </label><input type="number" step="1" max="" min="" class="small-text" id="quads_settings[ads][<?php echo $id; ?>][margin-right]" name="quads_settings[ads][<?php echo $id; ?>][margin-right]" value="<?php echo esc_attr( stripslashes( $right ) ); ?>"/>px
   <label style="margin-left:10px;"><?php _e( 'Bottom:', 'quick-adsense-reloaded' ); ?> </label> <input type="number" step="1" max="" min="" class="small-text" id="quads_settings[ads][<?php echo $id; ?>][margin-bottom]" name="quads_settings[ads][<?php echo $id; ?>][margin-bottom]" value="<?php echo esc_attr( stripslashes( $bottom ) ); ?>"/>px
   <label style="margin-left:10px;"><?php _e( 'Left:', 'quick-adsense-reloaded' ); ?> </label> <input type="number" step="1" max="" min="" class="small-text" id="quads_settings[ads][<?php echo $id; ?>][margin-left]" name="quads_settings[ads][<?php echo $id; ?>][margin-left]" value="<?php echo esc_attr( stripslashes( $left ) ); ?>"/>px

   <?php
}

add_filter( 'quads_render_margin', 'quads_render_margins', 2, 1000 );

/**
 * 
 * Get all user roles
 * 
 * @global array $wp_roles
 * @return array
 */
function quads_get_user_roles() {
   global $wp_roles;
   $roles = array();

   foreach ( $wp_roles->roles as $role ) {
      //if( isset( $role["capabilities"]["edit_posts"] ) && $role["capabilities"]["edit_posts"] === true ) {
      $value = str_replace( ' ', null, strtolower( $role["name"] ) );
      $roles[$value] = $role["name"];
      //}
   }
   return $roles;
}

/**
 * Add more post_types to default ones
 * @param array $default_post_types
 * @return array
 */
function quads_add_more_post_types( $default_post_types ) {
   $post_types = get_post_types();
   return $post_types;
}

add_filter( 'quads_post_types', 'quads_add_more_post_types' );

/**
 * 
 * Get all available tags
 * 
 * @global array $wp_roles
 * @return array
 */
function quads_get_tags() {
   $tags = get_tags();
   $new_tags = array();
   //wp_die(var_dump($tags));
   foreach ( $tags as $key => $value ) {
      //$new_tags[$key]['term_id'] = $value->term_id;
      $new_tags[$key][$value->slug] = $value->name;
   }
   $new_tags = quads_flatten( $new_tags );
   //wp_die(var_dump($new_tags));
   return $new_tags;
   //wp_die(var_dump($new_tags));
}

/**
 * Several conditions to check if wp quads pro is allowed to be executed
 * @return boolean
 */
function quads_pro_allow_execution() {

   // wp quads not installed
   if( !defined( 'QUADS_VERSION' ) ) {
      return false;
   }

   // wp quads lower than 1.5.3
   if( version_compare( QUADS_VERSION, '1.5.3', '<' ) && QUADS_VERSION !== '{{ version }}' ) {
      return false;
   }

   // default allow activation
   return true;
}

function quadsIsActivated() {
   $lic = get_option( 'quads_wp_quads_pro_license_active' );
   if( !$lic || (is_object( $lic ) && $lic->success !== true) ) {
      return false;
   }
   return true;
}