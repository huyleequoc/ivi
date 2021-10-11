<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_action( 'plugins_loaded', 'gfy_load_core_hooks' );
function gfy_load_core_hooks() {
	include GFY_CORE_DIR . 'hooks/class-hook-publishing-in-category.php';
	include GFY_CORE_DIR . 'hooks/class-hook-bonuses-for-publishing.php';
	include GFY_CORE_DIR . 'hooks/class-hook-publishing-firstly-in-category.php';
}
