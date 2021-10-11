<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Class GFY_BP_Achievements_BP_Component
 */
class GFY_BP_Achievements_BP_Component extends BP_Component {
	
	/**
	 * Start the component creation process
	 * GFY_BP_Achievements_BP_Component constructor.
	 */
	function __construct() {
		parent::start(
			GFY_BP_Achievements_BP_Component_Helper::get_id(),
			GFY_BP_Achievements_BP_Component_Helper::get_title(),
			dirname( __DIR__ ),
			array(
				'adminbar_myaccount_order'  => 90
			)
		);
	}
	
	/**
	 * Include files
	 *
	 * @param array $includes
	 */
	public function includes( $includes = array() ) {
		$includes = array(
			'classes' . DIRECTORY_SEPARATOR . 'class-bp-achievements-component-template.php',
			'classes' . DIRECTORY_SEPARATOR . 'class-bp-achievements-component-notification.php'
		);

		parent::includes( $includes );
	}
	
	/**
	 * Setup globals
	 *
	 * @param array $args
	 */
	public function setup_globals( $args = array() ) {
		parent::setup_globals( array(
			'slug'                  => GFY_BP_Achievements_BP_Component_Helper::get_slug(),
			'root_slug'             => GFY_BP_Achievements_BP_Component_Helper::get_slug(),
			'notification_callback' => class_exists( 'GFY_BP_Achievements_Component_Notification' ) ?
										array( GFY_BP_Achievements_Component_Notification::get_instance(), 'format_notification' ) : ''
		) );
	}
	
	/**
	 * Setup Buddypress navigation
	 *
	 * @param array $main_nav
	 * @param array $sub_nav
	 */
	public function setup_nav( $main_nav = array(), $sub_nav = array() ) {
		
		$parent_slug = GFY_BP_Achievements_BP_Component_Helper::get_slug();
		$parent_url = GFY_BP_Achievements_BP_Component_Helper::get_page_url();
		$component_template_instance = GFY_BP_Achievements_Component_Template::get_instance();
		
		$main_nav = array(
			'name'              => GFY_BP_Achievements_BP_Component_Helper::get_title(),
			'slug'              => $parent_slug,
			'position'          => GFY_BP_Achievements_BP_Component_Helper::get_menu_order()
		);
		
		$sub_nav = array(
			array(
				'name'              => __( 'All', 'gamify' ),
				'slug'              => GFY_BP_Achievements_BP_Component_Helper::get_subpage_slug( 'all' ),
				'parent_slug'       => $parent_slug,
				'parent_url'        => $parent_url,
				'position'          => 10,
				'screen_function'   => array( $component_template_instance, 'configure_all' )
			),
			array(
				'name'              => __( 'Earned', 'gamify' ),
				'slug'              => GFY_BP_Achievements_BP_Component_Helper::get_subpage_slug( 'earned' ),
				'parent_slug'       => $parent_slug,
				'parent_url'        => $parent_url,
				'position'          => 20,
				'screen_function'   => array( $component_template_instance, 'configure_earned' )
			),
			array(
				'name'              => __( 'Unearned', 'gamify' ),
				'slug'              => GFY_BP_Achievements_BP_Component_Helper::get_subpage_slug( 'unearned' ),
				'parent_slug'       => $parent_slug,
				'parent_url'        => $parent_url,
				'position'          => 30,
				'screen_function'   => array( $component_template_instance, 'configure_unearned' )
			)
		);
		
		usort( $sub_nav, function ( $a, $b ) {
			return $a['position'] - $b['position'];
		});
		
		$nav = array(
			'main_nav'  => $main_nav,
			'sub_nav'   => $sub_nav
		);
		
		$nav = apply_filters( 'gfy/' . $this->id . '/bp_nav', $nav );
		
		if ( ! empty ( $nav['sub_nav'] ) ) {
			$first_sub_nav_item = $nav['sub_nav'][0];
			
			$nav['main_nav']['default_subnav_slug'] = $first_sub_nav_item['slug'];
			$nav['main_nav']['screen_function'] = $first_sub_nav_item['screen_function'];
		}
		
		parent::setup_nav( $nav['main_nav'], $nav['sub_nav'] );
		
	}

}