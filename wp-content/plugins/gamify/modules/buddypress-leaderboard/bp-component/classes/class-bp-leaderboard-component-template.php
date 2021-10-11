<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

final class GFY_BP_Leaderboard_Component_Template {

	/**
	 * Holds current instance
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * Get instance
	 * @return GFY_BP_Leaderboard_Component_Template|null
	 */
	public static function get_instance() {

		if ( null == static::$_instance ) {
			static::$_instance = new GFY_BP_Leaderboard_Component_Template();
		}

		return static::$_instance;

	}

	/**
	 * Holds total results count
	 * @var int
	 */
	private $total_count = 0;

	/**
	 * Get total count
	 * @return int
	 */
	public function get_total() {
		return $this->total_count;
	}
	
	/**
	 * GFY_BP_Leaderboard_Component_Template constructor.
	 */
	private function __construct() {
		$this->setup_actions();
	}
	
	/**
	 * A dummy magic method to prevent GFY_BP_Leaderboard_Component_Template from being cloned.
	 *
	 */
	public function __clone() {
		throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
	}

	/**
	 * Setup actions
	 */
	private function setup_actions() {
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) , 10, 1 );
		add_filter( 'mycred_ranking_row', array( $this, 'edit_ranking_row' ), 10, 5 );
	}

	/**
	 * Get component template default data
	 * @return array
	 */
	private function get_template_default_data() {
		return array(
			'component_id'      => strtr( GFY_BP_Leaderboard_BP_Component_Helper::get_id(), array( '_' => '-' ) ),
			'component_classes' => sprintf( 'gfy-bp-component %s', strtr( GFY_BP_Leaderboard_BP_Component_Helper::get_id(), array( '_' => '-' ) ) )
		);
	}

	/**
	 * Get leaderboard results by provided criteria
	 * @param array $args   Leaderboard args
	 *                      @see myCRED_Query_Leaderboard
	 * @param bool $append_current_user Should current user be appended to results
	 * @return bool|array  Returns the leaderboard data in an array form or false if the query results in no data.
	 */
	private function get_leaderboard_query_results( $args, $append_current_user = false ) {
		$query = new myCRED_Query_Leaderboard( $args );
		$query->get_leaderboard_results( false );

		return $query->leaderboard;
	}

	/**
	 * Get provided user position in leader results array
	 *
	 * @param array[]       $results    Leaderboard results array
	 * @param $user_id      int         User ID
	 * @param $is_appended  bool        Is provided user appended to leaderboard results array
	 * @return int User position
	 */
	private function get_user_position_in_leaderboard_results( $results, $user_id, $is_appended ) {
		$positions = wp_list_filter( $results, array( 'ID' => $user_id ) );
		$position_keys = array_keys( $positions );
		$position_key = $position_keys[0];

		if( $is_appended && isset( $positions[ $position_key ][ 'position' ] ) ) {
			$position_key = $positions[ $position_key ][ 'position' ];
		}
		return $position_key;
	}
	
	/**
	 * Setup component "today" tab data
	 */
	public function configure_today() {
		$bp = buddypress();
		$component_id = GFY_BP_Leaderboard_BP_Component_Helper::get_id();
		
		$slug = GFY_BP_Leaderboard_BP_Component_Helper::get_subpage_slug( 'earned' );
		$bp->{$component_id}->current_page_slug = $slug;
		$bp->{$component_id}->current_page_url = GFY_BP_Leaderboard_BP_Component_Helper::get_page_url( $slug );
		
		do_action( 'gfy/' . $component_id .'/render_' . $slug );

		add_filter( 'mycred_get_leaderboard_results', array( $this, 'edit_leaderboard_results' ), 10, 3 );
		add_action( 'bp_member_plugin_options_nav', array( $this, 'render_filtering_dropdown' ) );
		add_action( 'bp_template_content', array( $this, 'render_today' ) );
		
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	/**
	 * Render "today" tab content
	 */
	public function render_today() {
		$shortcode_config = array(
			'timeframe' => 'today',
			'nothing'   => __( 'Leaderboard is empty', 'gamify' ),
			'type'      => GFY_BP_Leaderboard_BP_Component_Helper::get_current_filter(),
			'total'     => 1
		);

		$template_name = 'modules/buddypress-leaderboard/bp-component/templates/today.php';
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
		$component_id = GFY_BP_Leaderboard_BP_Component_Helper::get_id();
		
		$slug = GFY_BP_Leaderboard_BP_Component_Helper::get_subpage_slug( 'earned' );
		$bp->{$component_id}->current_page_slug = $slug;
		$bp->{$component_id}->current_page_url = GFY_BP_Leaderboard_BP_Component_Helper::get_page_url( $slug );
		
		do_action( 'gfy/' . $component_id .'/render_' . $slug );

		add_filter( 'mycred_get_leaderboard_results', array( $this, 'edit_leaderboard_results' ), 10, 3 );
		add_action( 'bp_member_plugin_options_nav', array( $this, 'render_filtering_dropdown' ) );
		add_action( 'bp_template_content', array( $this, 'render_week' ) );
		
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	/**
	 * Render "week" tab content
	 */
	public function render_week() {
		$shortcode_config = array(
			'timeframe' => 'this-week',
			'nothing'   => __( 'Leaderboard is empty', 'gamify' ),
			'type'      => GFY_BP_Leaderboard_BP_Component_Helper::get_current_filter(),
			'total'     => 1
		);

		$template_name = 'modules/buddypress-leaderboard/bp-component/templates/week.php';
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
		$component_id = GFY_BP_Leaderboard_BP_Component_Helper::get_id();

		$slug = GFY_BP_Leaderboard_BP_Component_Helper::get_subpage_slug( 'earned' );
		$bp->{$component_id}->current_page_slug = $slug;
		$bp->{$component_id}->current_page_url = GFY_BP_Leaderboard_BP_Component_Helper::get_page_url( $slug );

		do_action( 'gfy/' . $component_id .'/render_' . $slug );

		add_filter( 'mycred_get_leaderboard_results', array( $this, 'edit_leaderboard_results' ), 10, 3 );
		add_action( 'bp_member_plugin_options_nav', array( $this, 'render_filtering_dropdown' ) );
		add_action( 'bp_template_content', array( $this, 'render_month' ) );

		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	/**
	 * Render "month" tab content
	 */
	public function render_month() {

		$shortcode_config = array(
			'timeframe' => 'this-month',
			'nothing'   => __( 'Leaderboard is empty', 'gamify' ),
			'type'      => GFY_BP_Leaderboard_BP_Component_Helper::get_current_filter(),
			'total'     => 1
		);

		$template_name = 'modules/buddypress-leaderboard/bp-component/templates/month.php';
		$template_data = array_merge( $this->get_template_default_data(), array(
			'shortcode_config' => $shortcode_config
		) );

		gfy_get_template_part( $template_name, $template_data );
	}

	/**
	 * Add required query vars
	 *
	 * @param $query_vars
	 * @return array
	 */
	public function add_query_vars( $query_vars ) {
		$query_vars[] = GFY_BP_Leaderboard_BP_Component_Helper::get_pagination_query_param();

		return $query_vars;
	}

	/**
	 * Edit ranking row template layout
	 * @param   string  $layout     Current template layout
	 * @param   string  $template   Configured template
	 * @param   array   $user   {
	 *      Array describing user leaderboard data
	 * @type    string|int      $ID     User ID
	 * @type    string|int      $creds  User creds count
	 * }
	 * @param $position int Current position
	 * @param $instance myCRED_Query_Leaderboard    Query Log
	 * @return          string                      Updated template layout
	 */
	public function edit_ranking_row( $layout, $template, $user, $position, $instance ) {

		$replace_pairs = array();

		/***** User avatar */
		if( strpos( $layout, '%user_avatar%' ) !== false ) {
			$avatar = get_avatar( $user['ID'], apply_filters( 'gfy/mycred/rankiing_row/avatar_size', 50 ) );
			$avatar = $avatar ? $avatar : '';

			$replace_pairs[ '%user_avatar%' ] = $avatar;
		}

		/***** Rank logo */
		if( strpos( $layout, '%user_rank_logo%' ) !== false ) {
			$rank_logo = '';
			if( mycred_get_module( 'ranks' ) && ( $rank_id = mycred_get_users_rank_id( $user['ID'] ) ) ) {
				$rank_logo = mycred_get_rank_logo( $rank_id, 'user_rank_logo', array( 'title' => get_the_title( $rank_id ) ) );
			}

			$replace_pairs[ '%user_rank_logo%' ] = $rank_logo;
		}

		if( strpos( $layout, '%user_current_balance%' ) !== false ) {
			//todo uncomment after myCred filter fixing
			//$replace_pairs[ '%user_current_balance%' ] = mycred_format_creds( mycred_get_users_balance( $user['ID'] ), $instance->args['type'] );
			$replace_pairs[ '%user_current_balance%' ] = mycred_format_creds( mycred_get_users_balance( $user['ID'] ) );
		}

		if( ! empty( $replace_pairs ) ) {
			$layout = strtr( $layout, $replace_pairs );
		}

		return $layout;
	}

	/**
	 * @param array[] $results
	 * @param bool $append_current_user
	 * @param myCRED_Query_Leaderboard $instance
	 * @return array|bool
	 */
	public function edit_leaderboard_results( $results, $append_current_user, $instance ) {

		$user_id = get_current_user_id();
		$args = $instance->args;

		if( (bool)$results ) {

			if ( $args['number'] == 1 ) {

				if ($append_current_user) {
					$position = $instance->get_users_current_position($user_id, 1) - 1;
					$results = array(
						$position => array(
							'ID' => $user_id,
							'cred' => $instance->get_users_current_value($user_id)
						)
					);
				}

			} else {

				remove_filter( 'mycred_get_leaderboard_results', array( $this, 'edit_leaderboard_results' ), 10 );

				/**
				 * Let get displayed user position in leader board
				 */
				if ( $append_current_user ) {
					$user_position = $this->get_user_position_in_leaderboard_results( $results, $user_id, true );
				} else {
					$user_position = $instance->get_users_current_position($user_id, 0);
				}

				$args['offset'] = max(0, floor( ( $user_position - ( $args['number'] / 2 ) ) ) );
				$results = $this->get_leaderboard_query_results( $args, false );

				if ( count( $results ) < $args['number'] ) {
					$args['offset'] -= ($args['number'] - count($results));
					$args['offset'] = max(0, $args['offset']);

					$results = $this->get_leaderboard_query_results( $args, false );
				}

				if( (bool)$results ) {
					$results = array_combine(
						range( $args['offset'], ( count( $results ) + $args['offset'] - 1 ) ),
						array_values( $results )
					);
				}

				add_filter( 'mycred_get_leaderboard_results', array( $this, 'edit_leaderboard_results' ), 10, 3 );

			}

		}

		$this->total_count = $results ? count( $results ) : 0;

		return $results;
	}

	/**
	 * Render filtering dropdown
	 */
	public function render_filtering_dropdown() {

		$point_types = mycred_get_types();
		if( count( $point_types ) <= 1 ) {
			return;
		}

		$template_name = 'modules/buddypress-leaderboard/bp-component/templates/filter.php';
		$template_data = array(
			'filter_name'       => GFY_BP_Leaderboard_BP_Component_Helper::get_filtering_query_param(),
			'current_filter'    => GFY_BP_Leaderboard_BP_Component_Helper::get_current_filter(),
			'filter_choices'    => $point_types
		);
		gfy_get_template_part( $template_name, $template_data );

	}

}