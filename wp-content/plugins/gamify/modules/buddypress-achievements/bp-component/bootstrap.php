<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

require_once 'classes/class-bp-achievements-component-helper.php';

add_action( 'bp_admin_enqueue_scripts', array( 'GFY_BP_Achievements_BP_Component_Helper', 'admin_styles' ), 11 );
add_filter( 'gfy/register_bp_components', array( 'GFY_BP_Achievements_BP_Component_Helper', 'register_component' ), 10, 1 );