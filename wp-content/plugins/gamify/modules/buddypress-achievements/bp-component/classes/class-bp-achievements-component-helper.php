<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

class GFY_BP_Achievements_BP_Component_Helper {
	
	/**
	 * Hold component ID
	 * @var string
	 */
	private static $id = 'gfy_bp_achievements';
	
	/**
	 * Get component ID
	 *
	 * @return string
	 */
	public static function get_id() {
		return static::$id;
	}

	/**
	 * Get component title
	 *
	 * @return string
	 */
	public static function get_title() {
		return apply_filters( 'gfy/' . static::get_id() . '/component_title', __( 'Achievements', 'gamify' ) );
	}

	/**
	 * Get component description
	 *
	 * @return string
	 */
	public static function get_description() {
		return apply_filters( 'gfy/' . static::get_id() . '/component_description', __( 'Show all, earned & unearned badges ( myCRED ).', 'gamify' ) );
	}

	/**
	 * Get component menu order
	 * @return int
	 */
	public static function get_menu_order() {
		return absint( apply_filters( 'gfy/' . static::get_id() . '/component_menu_order', 22 ) );
	}

	/**
	 * Get component page slug
	 *
	 * @return mixed
	 */
	public static function get_slug() {
		$slug = apply_filters( 'gfy/' . static::get_id() . '/page_slug', 'achievements' );

		return $slug;
	}

	/**
	 * Get component sub-page slug
	 *
	 * @param $sub_page
	 * @return mixed
	 */
	public static function get_subpage_slug( $sub_page ) {
		switch( $sub_page ) {
			case 'all':
				$slug = 'all';
				break;
			case 'earned':
				$slug = 'earned';
				break;
			case 'unearned':
				$slug = 'unearned';
				break;
			default:
				$slug = '';
		}

		return apply_filters( 'gfy/' . static::get_id() . '/subpage_slug', $slug, $sub_page );
	}

	/**
	 * Get component page/sub-page URL
	 *
	 * @param string    $sub_page   Sub page slug key
	 * @param bool      $logged_in  Should the page be shown for current or displayed user
	 * @return string
	 */
	public static function get_page_url( $sub_page = '', $logged_in = false ) {
		$sub_page = $sub_page ? $sub_page : '';
		$url = trailingslashit( ( $logged_in ? bp_loggedin_user_domain() : bp_displayed_user_domain() ) . static::get_slug() ) . $sub_page;

		return trailingslashit( $url );
	}

	/**
	 * Get pagination query param
	 *
	 * @return string
	 */
	public static function get_pagination_query_param() {
		return apply_filters( 'gfy/' . static::get_id() . '/pagination_query_param', 'achievements_page' );
	}

	/**
	 * Get posts per page
	 *
	 * @return integer
	 */
	public static function get_posts_per_page() {
		return absint( apply_filters( 'gfy/' . static::get_id() . '/per_page', get_option( 'posts_per_page' ) ) );
	}

	/**
	 * Get paged param from query
	 * @see GFY_BP_Achievements_BP_Component_Helper::get_pagination_query_param()
	 *
	 * @return integer
	 */
	public static function get_paged() {
		return get_query_var( static::get_pagination_query_param(), 1 );
	}

	/**
	 * Get notifications status
	 * @return bool
	 */
	public static function is_notification_disabled() {
		return (bool)apply_filters( 'gfy/' . static::get_id() . '/disable_notifications', false );
	}

	/**
	 * Render pagination
	 */
	public static function pagination() {

		global $wp_query;

		$html = apply_filters( 'gfy/' . static::get_id() . '/pagination_html', '<nav class="navigation pagination" role="navigation"><div class="nav-links">%s</div></nav>' );
		if( $html ) {

			$pagination_args = array(
				'base'    => @add_query_arg( static::get_pagination_query_param(), '%#%' ),
				'format'  => '',
				'current' => max( 1, $wp_query->get( 'paged' ) ),
				'total'   => $wp_query->max_num_pages
			);
			$pagination_args = wp_parse_args( apply_filters( 'gfy/' . static::get_id() . '/pagination_args', $pagination_args ), $pagination_args );

			printf( $html, paginate_links( $pagination_args ) );
		}
	}

	/**
	 * Register BP component
	 * @param array $components
	 *
	 * @return array
	 */
	public static function register_component( $components ) {

		$components[] = array(
			'id'            => static::get_id(),
			'class'         => 'GFY_BP_Achievements_BP_Component',
			'path'          => trailingslashit( __DIR__ ) . 'class-bp-achievements-component.php',
			'title'         => static::get_title(),
			'description'   => static::get_description()
		);

		return $components;
	}

	/**
	 * Enqueue admin styles
	 */
	public static function admin_styles() {
		wp_add_inline_style( 'bp-admin-common-css', '
			.settings_page_bp-components tr.' . static::get_id() . ' td.plugin-title span:before {
				content: "\f313";
		    }
		' );
	}

}