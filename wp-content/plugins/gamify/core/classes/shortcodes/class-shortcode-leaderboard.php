<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

final class GFY_Shortcode_Leaderboard {

	/**
	 * Holds class single instance
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * Get instance
	 * @return GFY_Shortcode_Leaderboard|null
	 */
	public static function get_instance() {

		if( null == static::$_instance ) {
			static::$_instance = new self();
		}

		return static::$_instance;

	}

	/**
	 * Holds shortcode name
	 * @var string
	 */
	private $shortcode = 'gfy_leaderboard';

	/**
	 * Holds original shortcode name
	 * @var string
	 */
	private $original_shortcode = 'mycred_leaderboard';

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
	 * Holds final version of shortcode attributes
	 * @var array
	 */
	private $attributes = array();

	/**
	 * GFY_Shortcode_Leaderboard constructor.
	 */
	private function __construct() {
		$this->setup_actions();
	}

	/**
	 * A dummy magic method to prevent GFY_Shortcode_Leaderboard from being cloned.
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
		add_shortcode( $this->shortcode, array( $this, 'render' ) );
	}

	/**
	 * Get pagination query var
	 * @return string
	 */
	public function get_pagination_query_var() {
		return apply_filters( 'gfy/shortcode/' . $this->shortcode . '/pagination_query_var', 'gfy_lp' );
	}

	/**
	 * Get filtering query var
	 * @return string
	 */
	public function get_filtering_query_var() {
		return apply_filters( 'gfy/shortcode/' . $this->shortcode . '/pagination_query_var', 'gfy_lf' );
	}

	/**
	 * Get default filter
	 * @return string
	 */
	public function get_default_filter() {
		return MYCRED_DEFAULT_TYPE_KEY;
	}

	/**
	 * Get current page number
	 * @return int
	 */
	public function get_paged() {
		return max( absint( get_query_var( $this->get_pagination_query_var(), 1 ) ), 1 );
	}

	/**
	 * Get current filter
	 * @return string
	 */
	public function get_current_filter( $default = false ) {

		$filtering_query_param = $this->get_filtering_query_var();
		if( ! $default ) {
			$default = $this->get_default_filter();
		}
		$filter = isset( $_REQUEST[ $filtering_query_param ] ) && $_REQUEST[ $filtering_query_param ] ?
			$_REQUEST[ $filtering_query_param ] : $default;

		return array_key_exists( $filter, mycred_get_types() ) ? $filter : $default;
	}

	/**
	 * Add required query vars
	 *
	 * @param $query_vars
	 * @return array
	 */
	public function add_query_vars( $query_vars ) {
		$query_vars[] = $this->get_pagination_query_var();

		return $query_vars;
	}

	/**
	 * Sanitize shortcode attributes
	 *
	 * @param array $attributes Current attributes
	 * @return array
	 */
	public function sanitize_attributes( $attributes ) {

		$paged                  = $this->get_paged();
		$attributes['number']   = isset( $attributes['number'] ) ? max( absint( $attributes['number'] ), 1 ) : 25;
		$attributes['offset']   = ( $paged - 1 ) * $attributes['number'];
		$attributes['current']  = 0;
		$attributes['total']    = 1;
		$attributes['forced'] = true;

		$this->attributes = $attributes;

		return $attributes;
	}

	/**
	 * Edit leader board results to add possibility for pagination
	 *
	 * @param array[] $results
	 * @param bool $append_current_user
	 * @param myCRED_Query_Leaderboard $instance
	 * @return array|bool
	 */
	public function edit_leaderboard_results( $results, $append_current_user, $instance ) {

		$this->total_count = 0;
		if( (bool)$results ) {

			remove_filter( 'mycred_get_leaderboard_results', array( $this, 'edit_leaderboard_results' ), 10 );

			$query = new myCRED_Query_Leaderboard( $this->attributes );
			$query->get_leaderboard_results( false );

			add_filter( 'mycred_get_leaderboard_results', array( $this, 'edit_leaderboard_results' ), 10, 3 );

			if( $query->leaderboard ) {
				$this->total_count = count( $query->leaderboard );
			}

		}

		return $results;
	}

	/**
	 * Get pagination for current request
	 *
	 * @return string String of page links or array of page links.
	 */
	public function get_pagination() {

		$pagination_query_args = array(
			'base' =>  @add_query_arg( $this->get_pagination_query_var(), '%#%' ),
			'format' => '',
			'current' => $this->get_paged(),
			'total' => ceil( $this->get_total() / $this->attributes['number'] )
		);

		return paginate_links( $pagination_query_args );
	}

	/**
	 * Render leaderboard
	 *
	 * @param array $attributes Shortcode attributes
	 * @return string
	 */
	public function render( $attributes ) {

		$default_attributes = array(
			'nothing'           => __( 'Leaderboard is empty', 'gamify' ),
			'type'              => false,
			'timeframe'         => '',
			'timeframe_choices' => 1,
			'number'            => 25
		);
		$attributes = shortcode_atts( $default_attributes, $attributes, $this->shortcode );
		$attributes[ 'type' ] = $this->get_current_filter( $attributes[ 'type' ] );

		if( $attributes[ 'number' ] < 0 ) {
			$attributes[ 'number' ] = 99999;
		}

		$filter_choices = mycred_get_types();
		if( count( $filter_choices ) <= 1 ) {
			$filter_choices = array();
		}
		
		
		$current_url = boombox_get_current_url( true );
		$timeframe_choices = array();
		
		if( $attributes[ 'timeframe_choices' ] ) {
			if( isset( $_REQUEST[ 'timeframe' ] ) && $_REQUEST[ 'timeframe' ] ) {
				$attributes[ 'timeframe' ] = sanitize_text_field( $_REQUEST[ 'timeframe' ] );
			}
			if( ! in_array( $attributes[ 'timeframe' ], array( '', 'all', 'today', 'this-week', 'this-month' ) ) ) {
				$attributes[ 'timeframe' ] = '';
			}
			
			$timeframe_choices = array(
				''      => array(
					'label'  => __( 'All Time', 'gamify' ),
					'url'    => in_array( $attributes[ 'timeframe' ], array(
						'',
						'all'
					) ) ? false : add_query_arg( 'timeframe', 'all', $current_url ),
					'active' => in_array( $attributes[ 'timeframe' ], array(
						'',
						'all'
					) )
				),
				'today' => array(
					'label'  => __( 'Today', 'gamify' ),
					'url'    => ( $attributes[ 'timeframe' ] == 'today' ) ? false : add_query_arg( 'timeframe', 'today', $current_url ),
					'active' => ( $attributes[ 'timeframe' ] == 'today' )
				),
				'this-week'  => array(
					'label'  => __( 'Week', 'gamify' ),
					'url'    => ( $attributes[ 'timeframe' ] == 'this-week' ) ? false : add_query_arg( 'timeframe', 'this-week', $current_url ),
					'active' => ( $attributes[ 'timeframe' ] == 'this-week' )
				),
				'this-month' => array(
					'label'  => __( 'Month', 'gamify' ),
					'url'    => ( $attributes[ 'timeframe' ] == 'this-month' ) ? false : add_query_arg( 'timeframe', 'this-month', $current_url ),
					'active' => ( $attributes[ 'timeframe' ] == 'this-month' )
				),
			);
		} else {
			if( ! in_array( $attributes[ 'timeframe' ], array( '', 'all', 'today', 'this-week', 'this-month' ) ) ) {
				$attributes[ 'timeframe' ] = '';
			}
		}
		
		add_filter( 'mycred_get_leaderboard_results', array( $this, 'edit_leaderboard_results' ), 10, 3 );
		add_filter( 'gfy/shortcode/' . $this->original_shortcode . '/attributes', array( $this, 'sanitize_attributes' ), 10, 1 );

		ob_start();
			gfy_get_template_part( 'core/templates/shortcodes/leaderboard.php', array(
				'instance'              => $this,
				'shortcode_attributes'  => $attributes,
				'filter_choices'        => $filter_choices,
				'timeframe_choices'     => $timeframe_choices,
				'current_url'           => $current_url
			) );
		$html = ob_get_clean();

		remove_filter( 'mycred_get_leaderboard_results', array( $this, 'edit_leaderboard_results' ), 10 );
		remove_filter( 'gfy/shortcode/' . $this->original_shortcode . '/attributes', array( $this, 'sanitize_attributes' ), 10 );

		return $html;
	}

}