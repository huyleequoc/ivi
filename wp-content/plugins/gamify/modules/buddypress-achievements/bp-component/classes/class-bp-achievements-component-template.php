<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

final class GFY_BP_Achievements_Component_Template {

	/**
	 * Holds current instance
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * Get instance
	 * @return GFY_BP_Achievements_Component_Template|null
	 */
	public static function get_instance() {

		if ( null == static::$_instance ) {
			static::$_instance = new GFY_BP_Achievements_Component_Template();
		}

		return static::$_instance;

	}

	/**
	 * GFY_BP_Achievements_Component_Template constructor.
	 */
	private function __construct() {
		$this->setup_actions();
	}

	/**
	 * A dummy magic method to prevent GFY_BP_Achievements_Component_Template from being cloned.
	 *
	 */
	public function __clone() {
		throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
	}

	/**
	 * Setup actions
	 */
	private function setup_actions() {
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 10, 1 );
	}

	/**
	 * Get component template default data
	 * @return array
	 */
	private function get_template_default_data() {
		return array(
			'component_id'      => strtr( GFY_BP_Achievements_BP_Component_Helper::get_id(), array( '_' => '-' ) ),
			'component_classes' => sprintf( 'gfy-bp-component %s', strtr( GFY_BP_Achievements_BP_Component_Helper::get_id(), array( '_' => '-' ) ) ),
		);
	}

	/**
	 * Setup component "unearned" tab data
	 */
	public function configure_unearned() {
		$bp = buddypress();
		$component_id = GFY_BP_Achievements_BP_Component_Helper::get_id();

		$slug = GFY_BP_Achievements_BP_Component_Helper::get_subpage_slug( 'unearned' );
		$bp->{$component_id}->current_page_slug = $slug;
		$bp->{$component_id}->current_page_url = GFY_BP_Achievements_BP_Component_Helper::get_page_url( $slug );

		do_action( 'gfy/' . $component_id .'/render_' . $slug );

		add_action( 'bp_template_content', array( $this, 'render_unearned' ) );

		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	/**
	 * Render "unearned" tab content
	 */
	public function render_unearned() {
		$user_id = bp_displayed_user_id();
		$paged = GFY_BP_Achievements_BP_Component_Helper::get_paged();
		$per_page = GFY_BP_Achievements_BP_Component_Helper::get_posts_per_page();

		/**
		 * @var $module GFY_BP_Achievements_Module
		 */
		$module = mycred_get_module( GFY_BP_Achievements_Module::MODULE_NAME );
		$badges_data = $module->get_user_unearned_badges( $user_id, $paged, $per_page );

		$template_name = 'modules/buddypress-achievements/bp-component/templates/unearned.php';
		$template_data = array_merge( $this->get_template_default_data(), array(
			'badges'        => $badges_data['badges'],
			'pagination'    => $this->get_pagination( $paged, $per_page, $badges_data['total'] )
		) );
		gfy_get_template_part( $template_name, $template_data );

	}

	/**
	 * Setup component "earned" tab data
	 */
	public function configure_earned() {
		$bp = buddypress();
		$component_id = GFY_BP_Achievements_BP_Component_Helper::get_id();

		$slug = GFY_BP_Achievements_BP_Component_Helper::get_subpage_slug( 'earned' );
		$bp->{$component_id}->current_page_slug = $slug;
		$bp->{$component_id}->current_page_url = GFY_BP_Achievements_BP_Component_Helper::get_page_url( $slug );

		do_action( 'gfy/' . $component_id .'/render_' . $slug );

		add_action( 'bp_template_content', array( $this, 'render_earned' ) );

		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	/**
	 * Render "earned" tab content
	 */
	public function render_earned() {
		$user_id = bp_displayed_user_id();
		$paged = GFY_BP_Achievements_BP_Component_Helper::get_paged();
		$per_page = GFY_BP_Achievements_BP_Component_Helper::get_posts_per_page();

		/**
		 * @var $module GFY_BP_Achievements_Module
		 */
		$module = mycred_get_module( GFY_BP_Achievements_Module::MODULE_NAME );
		$badges_data = $module->get_user_earned_badges( $user_id, $paged, $per_page );

		$template_name = 'modules/buddypress-achievements/bp-component/templates/earned.php';
		$template_data = array_merge( $this->get_template_default_data(), array(
			'badges'        => $badges_data['badges'],
			'pagination'    => $this->get_pagination( $paged, $per_page, $badges_data['total'] )
		) );

		gfy_get_template_part( $template_name, $template_data );

	}

	/**
	 * Setup component "all" tab data
	 */
	public function configure_all() {
		$bp = buddypress();
		$component_id = GFY_BP_Achievements_BP_Component_Helper::get_id();

		$slug = GFY_BP_Achievements_BP_Component_Helper::get_subpage_slug( 'all' );
		$bp->{$component_id}->current_page_slug = $slug;
		$bp->{$component_id}->current_page_url = GFY_BP_Achievements_BP_Component_Helper::get_page_url( $slug );

		do_action( 'gfy/' . $component_id .'/render_' . $slug );

		add_action( 'bp_template_content', array( $this, 'render_all' ) );

		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	/**
	 * Render "all" tab content
	 */
	public function render_all() {
		$user_id = bp_displayed_user_id();
		$paged = GFY_BP_Achievements_BP_Component_Helper::get_paged();
		$per_page = GFY_BP_Achievements_BP_Component_Helper::get_posts_per_page();

		/**
		 * @var $module GFY_BP_Achievements_Module
		 */
		$module = mycred_get_module( GFY_BP_Achievements_Module::MODULE_NAME );
		$badges_data = $module->get_user_all_badges( $user_id, $paged, $per_page );

		$template_name = 'modules/buddypress-achievements/bp-component/templates/all.php';
		$template_data = array_merge( $this->get_template_default_data(), array(
			'badges'        => $badges_data['badges'],
			'pagination'    => $this->get_pagination( $paged, $per_page, $badges_data['total'] )
		) );

		gfy_get_template_part( $template_name, $template_data );
	}

	/**
	 * Add required query vars
	 *
	 * @param array     $query_vars     Existing query vars
	 * @return array
	 */
	public function add_query_vars( $query_vars ) {
		$query_vars[] = GFY_BP_Achievements_BP_Component_Helper::get_pagination_query_param();

		return $query_vars;
	}

	/**
	 * Get pagination markup
	 * @param $current      int Current page
	 * @param $per_page     int Items per page
	 * @param $total        int Total pages
	 *
	 * @return array|string String of page links or array of page links.
	 */
	private function get_pagination( $current, $per_page, $total ) {
		$pagination_query_args = array(
			'base' =>  @add_query_arg( GFY_BP_Achievements_BP_Component_Helper::get_pagination_query_param(), '%#%' ),
			'format' => '',
			'current' => max( 1, $current ),
			'total' => ceil( $total / $per_page )
		);

		return paginate_links( $pagination_query_args );
	}

}