<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Class GFY_BP_History_BP_Component
 */
class GFY_BP_History_BP_Component extends BP_Component {
	
	/**
	 * Start the component creation process
	 * GFY_BP_History_BP_Component constructor.
	 */
	function __construct() {
		parent::start(
			GFY_BP_History_BP_Component_Helper::get_id(),
			GFY_BP_History_BP_Component_Helper::get_title(),
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
			'classes' . DIRECTORY_SEPARATOR . 'class-bp-history-component-template.php',
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
			'slug'      => GFY_BP_History_BP_Component_Helper::get_slug(),
			'root_slug' => GFY_BP_History_BP_Component_Helper::get_slug()
		) );
	}
	
	/**
	 * Setup component navigation
	 *
	 * @param array $main_nav
	 * @param array $sub_nav
	 */
	public function setup_nav( $main_nav = array(), $sub_nav = array() ) {

		$default_filter = GFY_BP_History_BP_Component_Helper::get_default_filter();
		$current_filter = GFY_BP_History_BP_Component_Helper::get_current_filter();
		$parent_slug = GFY_BP_History_BP_Component_Helper::get_slug();
		$parent_url = GFY_BP_History_BP_Component_Helper::get_page_url();
		$component_template_instance = GFY_BP_History_Component_Template::get_instance();

		$query_args = array();
		if( $current_filter != $default_filter ) {
			$query_args[ GFY_BP_History_BP_Component_Helper::get_filtering_query_param() ] = $current_filter;
		}

		$main_nav = array(
			'name'                      => GFY_BP_History_BP_Component_Helper::get_title(),
			'slug'                      => $parent_slug,
			'show_for_displayed_user'   => false,
			'position'                  => GFY_BP_History_BP_Component_Helper::get_menu_order()
		);

		$sub_nav = array(
			array(
				'name'              => __( 'Today', 'gamify' ),
				'slug'              => GFY_BP_History_BP_Component_Helper::get_subpage_slug( 'today' ),
				'parent_slug'       => $parent_slug,
				'parent_url'        => $parent_url,
				'link'              => add_query_arg( $query_args, ( $parent_url . GFY_BP_History_BP_Component_Helper::get_subpage_slug( 'today' ) ) ),
				'position'          => 10,
				'screen_function'   => array( $component_template_instance, 'configure_today' )
			),
			array(
				'name'              => __( 'This Week', 'gamify' ),
				'slug'              => GFY_BP_History_BP_Component_Helper::get_subpage_slug( 'week' ),
				'parent_slug'       => $parent_slug,
				'parent_url'        => $parent_url,
				'link'              => add_query_arg( $query_args, ( $parent_url . GFY_BP_History_BP_Component_Helper::get_subpage_slug( 'week' ) ) ),
				'position'          => 20,
				'screen_function'   => array( $component_template_instance, 'configure_week' )
			),
			array(
				'name'              => __( 'This Month', 'gamify' ),
				'slug'              => GFY_BP_History_BP_Component_Helper::get_subpage_slug( 'month' ),
				'parent_slug'       => $parent_slug,
				'parent_url'        => $parent_url,
				'link'              => add_query_arg( $query_args, ( $parent_url . GFY_BP_History_BP_Component_Helper::get_subpage_slug( 'month' ) ) ),
				'position'          => 30,
				'screen_function'   => array( $component_template_instance, 'configure_month' )
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