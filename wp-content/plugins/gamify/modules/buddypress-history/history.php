<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'GFY_BP_History_Module' ) && class_exists( 'myCRED_Module' ) ) {

	/**
	 * Class GFY_BP_History_Module
	 */
	final class GFY_BP_History_Module extends myCRED_Module {

		const MODULE_ID = 'GFY_BP_History_Module';
		const MODULE_NAME = 'gfy_bp_history';

		/**
		 * GFY_BP_History_Module constructor.
		 */
		public function __construct() {
			parent::__construct(self::MODULE_ID, array(
				'module_name' => self::MODULE_NAME,
				'register' => false,
				'add_to_core' => true,
			));
		}

	}

	if( GFY_Plugin_Management_Service::get_instance()->is_plugin_active( 'buddypress/bp-loader.php' ) ) {
		GFY_myCRED_Module_Loader::get_instance()->register(
			GFY_BP_History_Module::MODULE_NAME,
			'GFY_BP_History_Module',
			array(
				'includes' => array(
					__DIR__ . DIRECTORY_SEPARATOR . 'bp-component' . DIRECTORY_SEPARATOR . 'bootstrap.php'
				)
			)
		);
	}

}