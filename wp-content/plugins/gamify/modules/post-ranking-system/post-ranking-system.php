<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'GFY_PRS_Module' ) && class_exists( 'myCRED_Module' ) ) {

	/**
	 * Class GFY_PRS_Module
	 */
	final class GFY_PRS_Module extends myCRED_Module {

		const MODULE_ID = 'GFY_PRS_Module';
		const MODULE_NAME = 'gfy_prs';

		/**
		 * GFY_PRS_Module constructor.
		 */
		public function __construct() {
			parent::__construct(self::MODULE_ID, array(
				'module_name' => self::MODULE_NAME,
				'register' => false,
				'add_to_core' => true,
			));
		}

	}

	GFY_myCRED_Module_Loader::get_instance()->register(
        GFY_PRS_Module::MODULE_NAME,
        'GFY_PRS_Module',
        array(
        	'includes' => array(
		        __DIR__ . DIRECTORY_SEPARATOR . 'hooks' . DIRECTORY_SEPARATOR .  'class-hook-single-post-views-milestones.php',
		        __DIR__ . DIRECTORY_SEPARATOR . 'hooks' . DIRECTORY_SEPARATOR .  'class-hook-view-up.php',
		        __DIR__ . DIRECTORY_SEPARATOR . 'hooks' . DIRECTORY_SEPARATOR .  'class-hook-vote-up-down.php',
		        __DIR__ . DIRECTORY_SEPARATOR . 'hooks' . DIRECTORY_SEPARATOR .  'class-hook-featured-post.php',
		        __DIR__ . DIRECTORY_SEPARATOR . 'hooks' . DIRECTORY_SEPARATOR .  'class-hook-featured-post-in-category.php',
		        __DIR__ . DIRECTORY_SEPARATOR . 'hooks' . DIRECTORY_SEPARATOR .  'class-hook-featured-on-frontpage-post.php',
		        __DIR__ . DIRECTORY_SEPARATOR . 'hooks' . DIRECTORY_SEPARATOR .  'class-hook-featured-on-frontpage-post-in-category.php',
		        __DIR__ . DIRECTORY_SEPARATOR . 'hooks' . DIRECTORY_SEPARATOR .  'class-hook-recurrent-post-views.php',
		        __DIR__ . DIRECTORY_SEPARATOR . 'hooks' . DIRECTORY_SEPARATOR .  'class-hook-recurrent-post-views-in-category.php',
		        __DIR__ . DIRECTORY_SEPARATOR . 'hooks' . DIRECTORY_SEPARATOR .  'class-hook-recurrent-post-votes.php',
		        __DIR__ . DIRECTORY_SEPARATOR . 'hooks' . DIRECTORY_SEPARATOR .  'class-hook-trending-post.php'
	        )
        )
    );

}