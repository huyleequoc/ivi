<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

require_once GFY_CORE_DIR . 'classes/class-plugin-management-service.php';
require_once GFY_CORE_DIR . 'classes/class-shortcode-management-service.php';
require_once GFY_CORE_DIR . 'classes/class-mycred-hook-management-service.php';
require_once GFY_CORE_DIR . 'classes/class-mycred-module-loader.php';
require_once GFY_CORE_DIR . 'classes/class-bp-component-loader.php';
require_once GFY_CORE_DIR . 'classes/class-actions-helper.php';
require_once GFY_CORE_DIR . 'hooks/bootstrap.php';
require_once GFY_CORE_DIR . 'functions.php';
require_once GFY_CORE_DIR . 'shortcodes.php';
require_once GFY_CORE_DIR . 'widgets.php';
if( is_admin() ) {
	require_once GFY_ADMIN_DIR . 'bootstrap.php';
}
GFY_Actions_Helper::get_instance();
GFY_BP_Component_Loader::get_instance();