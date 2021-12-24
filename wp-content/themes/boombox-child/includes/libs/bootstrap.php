<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * All in one meta box
 */
require_once( 'all-in-one-meta' . DIRECTORY_SEPARATOR . 'loader.php' );
AIOM_Config::setup( array( 'post_meta_key' => 'boombox_meta', 'tax_meta_key' => 'boombox_meta', 'user_meta_key' => 'boombox_meta' ) );

/**
 * Callback to setup AIOM custom field types configuration
 * @param array $config Current configuration for field type
 * @param string $type Sanitized field type
 * @since 2.5.0
 * @version 2.5.0
 *
 * @return array
 */
function boombox_register_aiom_custom_field_type_config( $config, $type ) {
	
	$custom_field_types_path = BOOMBOX_ADMIN_PATH . 'metaboxes' . DIRECTORY_SEPARATOR . 'aiom-custom-fields' . DIRECTORY_SEPARATOR;
	switch( $type ) {
		case 'icons_dropdown':
			$config = array(
				'class' => 'Boombox_AIOM_Icons_Dropdown_Field',
				'path'  => $custom_field_types_path . 'icons-dropdown' . DIRECTORY_SEPARATOR . 'field.php'
			);
			break;
	}
	
	return $config;
}
add_filter( 'aiom/custom_field_type_config', 'boombox_register_aiom_custom_field_type_config', 10, 2 );

/**
 * Badges Navigation Walker Class
 */
require_once( 'walkers' . DIRECTORY_SEPARATOR . 'class-boombox-walker-badges-nav-menu.php' );

/**
 * Header Navigation Walker Class
 */
require_once( 'walkers' . DIRECTORY_SEPARATOR . 'class-boombox-walker-nav-menu-custom-fields.php' );

/**
 * Featured Labels Navigation Walker Class
 */
require_once( 'walkers' . DIRECTORY_SEPARATOR . 'class-boombox-walker-featured-labels-menu.php' );

/**
 * Side Navigation Walker Class
 */
require_once( 'walkers' . DIRECTORY_SEPARATOR . 'class-boombox-walker-side-nav-menu.php' );

/**
 * Loop Helper
 */
require_once( 'class-boombox-loop-helper.php' );