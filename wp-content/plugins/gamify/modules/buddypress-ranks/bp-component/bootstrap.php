<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

require_once 'classes' . DIRECTORY_SEPARATOR . 'class-bp-ranks-component-helper.php';

add_filter( 'gfy/register_bp_components', array( 'GFY_BP_Ranks_BP_Component_Helper', 'register_component' ), 10, 1 );