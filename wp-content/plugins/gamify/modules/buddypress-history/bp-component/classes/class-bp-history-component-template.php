<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

final class GFY_BP_History_Component_Template {

	/**
	 * Holds current instance
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * Get instance
	 * @return GFY_BP_History_Component_Template|null
	 */
	public static function get_instance() {

		if ( null == static::$_instance ) {
			static::$_instance = new GFY_BP_History_Component_Template();
		}

		return static::$_instance;

	}

	/**
	 * GFY_BP_History_Component_Template constructor.
	 */
	private function __construct() {
		$this->setup_actions();
	}

	/**
	 * A dummy magic method to prevent GFY_BP_History_Component_Template from being cloned.
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
		add_filter( 'mycred_log_column_headers', array( $this, 'edit_mycred_log_column_headers' ), 10, 3 );
		add_filter( 'mycred_query_log_args', array( $this, 'edit_mycred_query_log_args' ), 10, 1 );
	}

	/**
	 * Get component template default data
	 * @return array
	 */
	private function get_template_default_data() {
		return array(
			'component_id'      => strtr( GFY_BP_History_BP_Component_Helper::get_id(), array( '_' => '-' ) ),
			'component_classes' => sprintf( 'gfy-bp-component %s', strtr( GFY_BP_History_BP_Component_Helper::get_id(), array( '_' => '-' ) ) )
		);
	}

	/**
	 * Setup component "today" tab data
	 */
	public function configure_today() {
		$bp = buddypress();
		$component_id = GFY_BP_History_BP_Component_Helper::get_id();

		$slug = GFY_BP_History_BP_Component_Helper::get_subpage_slug( 'today' );
		$bp->{$component_id}->current_page_slug = $slug;
		$bp->{$component_id}->current_page_url = GFY_BP_History_BP_Component_Helper::get_page_url( $slug );

		do_action( 'gfy/' . $component_id .'/render_' . $slug );

		add_action( 'bp_member_plugin_options_nav', array( $this, 'render_filtering_dropdown' ) );
		add_action( 'bp_template_content', array( $this, 'render_today' ) );

		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	/**
	 * Render "today" tab content
	 */
	public function render_today() {
		$shortcode_config = array(
			'user_id'   => bp_displayed_user_id(),
			'time'      => 'today',
			'type'      => GFY_BP_History_BP_Component_Helper::get_current_filter()
		);

		$template_name = 'modules/buddypress-history/bp-component/templates/today.php';
		$template_data = array_merge( $this->get_template_default_data(), array(
			'shortcode_config' => $shortcode_config
		) );

		gfy_get_template_part( $template_name, $template_data );
	}

	/**
	 * Setup component "week" tab data
	 */
	public function configure_week() {
		$bp = buddypress();
		$component_id = GFY_BP_History_BP_Component_Helper::get_id();

		$slug = GFY_BP_History_BP_Component_Helper::get_subpage_slug( 'week' );
		$bp->{$component_id}->current_page_slug = $slug;
		$bp->{$component_id}->current_page_url = GFY_BP_History_BP_Component_Helper::get_page_url( $slug );

		do_action( 'gfy/' . $component_id .'/render_' . $slug );

		add_action( 'bp_member_plugin_options_nav', array( $this, 'render_filtering_dropdown' ) );
		add_action( 'bp_template_content', array( $this, 'render_week' ) );

		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	/**
	 * Render "week" tab content
	 */
	public function render_week() {
		$shortcode_config = array(
			'user_id'   => bp_displayed_user_id(),
			'time'      => 'thisweek',
			'type'      => GFY_BP_History_BP_Component_Helper::get_current_filter()
		);

		$template_name = 'modules/buddypress-history/bp-component/templates/week.php';
		$template_data = array_merge( $this->get_template_default_data(), array(
			'shortcode_config' => $shortcode_config
		) );

		gfy_get_template_part( $template_name, $template_data );
	}

	/**
	 * Setup component "month" tab data
	 */
	public function configure_month() {
		$bp = buddypress();
		$component_id = GFY_BP_History_BP_Component_Helper::get_id();

		$slug = GFY_BP_History_BP_Component_Helper::get_subpage_slug( 'month' );
		$bp->{$component_id}->current_page_slug = $slug;
		$bp->{$component_id}->current_page_url = GFY_BP_History_BP_Component_Helper::get_page_url( $slug );

		do_action( 'gfy/' . $component_id .'/render_' . $slug );

		add_action( 'bp_member_plugin_options_nav', array( $this, 'render_filtering_dropdown' ) );
		add_action( 'bp_template_content', array( $this, 'render_month' ) );

		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	/**
	 * Render "month" tab content
	 */
	public function render_month() {
		$shortcode_config = array(
			'user_id'   => bp_displayed_user_id(),
			'time'      => 'thismonth',
			'type'      => GFY_BP_History_BP_Component_Helper::get_current_filter()
		);

		$template_name = 'modules/buddypress-history/bp-component/templates/month.php';
		$template_data = array_merge( $this->get_template_default_data(), array(
			'shortcode_config' => $shortcode_config
		) );

		gfy_get_template_part( $template_name, $template_data );
	}

	/**
	 * Render filtering dropdown
	 */
	public function render_filtering_dropdown() {

		$point_types = mycred_get_types();
		if( count( $point_types ) <= 1 ) {
			return;
		}

		$template_name = 'modules/buddypress-history/bp-component/templates/filter.php';
		$template_data = array(
			'filter_name'       => GFY_BP_History_BP_Component_Helper::get_filtering_query_param(),
			'current_filter'    => GFY_BP_History_BP_Component_Helper::get_current_filter(),
			'filter_choices'    => $point_types
		);
		gfy_get_template_part( $template_name, $template_data );

	}

	/**
	 * Add required query vars
	 *
	 * @param $query_vars
	 * @return array
	 */
	public function add_query_vars( $query_vars ) {
		$query_vars[] = GFY_BP_History_BP_Component_Helper::get_pagination_query_param();

		return $query_vars;
	}
	
	/**
	 * Edit columns headers
	 * @param $headers      array               Table columns
	 * @param $instance     myCRED_Query_Log    Query log instance
	 * @param $is_admin     bool                Is admin
	 *
	 * @return mixed
	 */
	public function edit_mycred_log_column_headers( $headers, $instance, $is_admin ) {
		if( ! $is_admin ) {
			unset( $headers['username'] );
		}
		return $headers;
	}

	/**
	 * Edit history log query arguments
	 *
	 * @param $args array<string,mixed> Current arguments
	 * @return array<string,mixed>      Updated arguments
	 */
	public function edit_mycred_query_log_args( $args ) {
		$order = isset( $args[ 'order' ] ) ? $args[ 'order' ] : 'DESC';
		$args[ 'orderby' ] = array( 'time' => $order, 'id' => $order );

		$args[ 'page_arg' ] = GFY_BP_History_BP_Component_Helper::get_pagination_query_param();

		return $args;
	}

}