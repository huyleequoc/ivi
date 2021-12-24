<?php
/**
 * Boombox contributor role permissions and settings
 *
 * @package BoomBox_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Setup permissions for user roles
 */
function boombox_edit_user_role_permissions() {

	$current_user = wp_get_current_user();

	if ( current_user_can( 'contributor' ) && in_array( 'contributor', $current_user->roles ) ) {

		if ( is_admin() ) {

			/**
			 * Removes from post and pages
			 */
			remove_post_type_support( 'post', 'comments' );
			remove_post_type_support( 'post', 'author' );

			/**
			 * Hooks
			 */
			add_action( 'admin_menu', 'boombox_remove_admin_menus' );
			add_action( 'wp_before_admin_bar_render', 'boombox_admin_bar_render' );
			add_action( 'admin_init', 'boombox_admin_menu_redirect' );

			/**
			 * Removes from admin menu
			 */
			function boombox_remove_admin_menus() {
				remove_menu_page( 'edit-comments.php' );
				remove_menu_page( 'tools.php' );
				remove_menu_page( 'index.php' );
				remove_menu_page( 'about.php' );
			}

			/**
			 * Removes from admin bar
			 */
			function boombox_admin_bar_render() {
				global $wp_admin_bar;
				$wp_admin_bar->remove_menu( 'comments' );
			}

			/**
			 * Redirect any user trying to access dashboard, comments or tools pages
			 */
			function boombox_admin_menu_redirect() {
				global $pagenow;
				$deprecated_pages = array(
					'index.php',
					'edit-comments.php',
					'tools.php',
					'about.php',
				);
				if ( in_array( $pagenow, $deprecated_pages ) ) {
					wp_redirect( admin_url( 'profile.php' ) );
					exit;
				}
			}
		}
	}
}

add_action( 'init', 'boombox_edit_user_role_permissions', 100 );

/**
 * Update single post 'total_views' meta
 *
 * @param int $scale   Selected view scale
 * @param int $post_id Post ID
 */
function boombox_update_post_total_view( $scale, $post_id ) {
	if ( absint( $scale ) > 0 ) {
		$total = intval( boombox_get_post_meta( $post_id, 'total_views' ) );
		$total += $scale;

		update_post_meta( $post_id, 'total_views', $total );
	}
}

add_action( 'boombox/view_total_updated', 'boombox_update_post_total_view', 10, 2 );

/**
 * Update post author 'total_views' meta
 *
 * @param $scale
 * @param $post_id
 */
function boombox_update_author_total_view( $scale, $post_id ) {

	$author_id = get_post_field( 'post_author', (int) $post_id );
	if ( ! $author_id ) {
		return;
	}

	$total_posts_view_count = get_user_meta( $author_id, 'total_posts_view_count', true );
	if ( ! $total_posts_view_count ) {
		$total_posts_view_count = $scale;
	} else {
		$total_posts_view_count += $scale;
	}
	update_user_meta( $author_id, 'total_posts_view_count', $total_posts_view_count );
}

add_action( 'boombox/view_total_updated', 'boombox_update_author_total_view', 10, 2 );

/**
 * Creating predefined rate jobs
 *
 * @param array $jobs
 *
 * @return array
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_create_predefined_jobs( array $jobs ) {
	$set = boombox_get_theme_options_set( array(
		'extras_post_ranking_system_trending_conditions',
		'extras_post_ranking_system_trending_enable',
		'extras_post_ranking_system_hot_enable',
		'extras_post_ranking_system_popular_enable',
		'extras_post_ranking_system_trending_posts_count',
		'extras_post_ranking_system_trending_minimal_score',
		'extras_post_ranking_system_hot_posts_count',
		'extras_post_ranking_system_hot_minimal_score',
		'extras_post_ranking_system_popular_posts_count',
		'extras_post_ranking_system_popular_minimal_score',
	) );

	$is_module_active = boombox_module_management_service()->is_module_active( 'prs' );
	if ( $is_module_active && Boombox_Rate_Criteria::get_criteria_by_name( $set['extras_post_ranking_system_trending_conditions'] ) ) {
		/***** Trending */
		if ( $set['extras_post_ranking_system_trending_enable'] && $set['extras_post_ranking_system_trending_posts_count'] ) {
			$job              = boombox_get_rate_job( $set['extras_post_ranking_system_trending_conditions'], 'post', 'day', intval( $set['extras_post_ranking_system_trending_posts_count'] ), max( absint( $set['extras_post_ranking_system_trending_minimal_score'] ), 1 ) );
			$jobs['trending'] = $job;
		}
		/***** Hot */
		if ( $set['extras_post_ranking_system_hot_enable'] && $set['extras_post_ranking_system_hot_posts_count'] ) {
			$job         = boombox_get_rate_job( $set['extras_post_ranking_system_trending_conditions'], 'post', 'week', intval( $set['extras_post_ranking_system_hot_posts_count'] ), max( absint( $set['extras_post_ranking_system_hot_minimal_score'] ), 1 ) );
			$jobs['hot'] = $job;
		}
		/***** Popular */
		if ( $set['extras_post_ranking_system_popular_enable'] && $set['extras_post_ranking_system_popular_posts_count'] ) {
			$job             = boombox_get_rate_job( $set['extras_post_ranking_system_trending_conditions'], 'post', 'month', intval( $set['extras_post_ranking_system_popular_posts_count'] ), max( absint( $set['extras_post_ranking_system_popular_minimal_score'] ), 1 ) );
			$jobs['popular'] = $job;
		}
	}

	return $jobs;
}

add_filter( 'boombox_rate_jobs', 'boombox_create_predefined_jobs', 10, 1 );

/**
 * Register predefined jobs
 *
 * @param array $jobs Current jobs
 */
function boombox_register_predefined_jobs( $jobs ) {
	if ( isset( $jobs['trending'] ) ) {
		Boombox_Rate_Cron::register_job( $jobs['trending'] );
	}

	if ( isset( $jobs['hot'] ) ) {
		Boombox_Rate_Cron::register_job( $jobs['hot'] );
	}

	if ( isset( $jobs['popular'] ) ) {
		Boombox_Rate_Cron::register_job( $jobs['popular'] );
	}
}

add_action( 'boombox_rate_jobs_register', 'boombox_register_predefined_jobs', 10, 1 );

/**
 * Check whether a reactions are enabled.
 *
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_reactions_is_enabled() {
	$is_module_active = boombox_module_management_service()->is_module_active( 'prs' );

	return ( $is_module_active && (bool) boombox_get_theme_option( 'extras_post_reaction_system_enable' ) );
}

/**
 * Get "paged" value for pages
 *
 * @return int
 */
function boombox_get_paged() {
	global $paged;

	if ( get_query_var( 'paged' ) ) {
		$paged = absint( get_query_var( 'paged' ) );
	} elseif ( get_query_var( 'page' ) ) {
		$paged = absint( get_query_var( 'page' ) );
	} else {
		$paged = 1;
	}

	return $paged;
}

/**
 * Get query for pages by condition
 *
 * @param $conditions
 * @param $time_range
 * @param $tax_conditions
 * @param $params
 *
 * @return null|WP_Query
 */
function boombox_get_posts_query( $conditions, $time_range, $tax_conditions, $params = array() ) {

	$categories = isset( $tax_conditions['category'] ) ? $tax_conditions['category'] : array();
	$tags       = isset( $tax_conditions['tag'] ) ? $tax_conditions['tag'] : array();
	$reactions  = array();
	if ( boombox_reactions_is_enabled() && taxonomy_exists( 'reaction' ) && isset( $tax_conditions['reaction'] ) ) {
		$reactions = $tax_conditions['reaction'];
	}

	$params = wp_parse_args( $params, array(
		'posts_per_page'      => - 1,
		'post_type'           => array( 'post' ),
		'paged'               => 1,
		'posts_count'         => - 1,
		'is_grid'             => false,
		'is_page_query'       => true,
		'excluded_categories' => array(),
		'is_live'             => false,
		'fake_meta_key'       => null,
		'ignore_sticky_posts' => true,
		'excluded_posts'      => array(),
		'offset'              => null,
	) );

	global $wpdb, $post;

	$query                 = null;
	$page_ad               = false;
	$page_newsletter       = false;
	$page_products         = false;
	$instead               = 0;
	$instead_newsletter    = 0;
	$page_product_position = 0;
	$page_product_count    = 0;

	if ( is_page() && $params['is_page_query'] ) {
		$page_ad = boombox_get_post_meta( $post->ID, 'boombox_page_ad' );
		$instead = boombox_get_post_meta( $post->ID, 'boombox_inject_ad_instead_post' );

		$page_newsletter    = boombox_get_post_meta( $post->ID, 'boombox_page_newsletter' );
		$instead_newsletter = boombox_get_post_meta( $post->ID, 'boombox_inject_newsletter_instead_post' );

		$page_product_position = boombox_get_post_meta( $post->ID, 'boombox_page_injected_products_position' );
		$page_product_count    = boombox_get_post_meta( $post->ID, 'boombox_page_injected_products_count' );
		$page_products         = boombox_get_post_meta( $post->ID, 'boombox_page_products_inject' );
	}

	$params['post_type'] = boombox_get_post_types_args( $params['post_type'] );
	$is_module_active    = boombox_module_management_service()->is_module_active( 'prs' );
	if ( $is_module_active && Boombox_Rate_Criteria::get_criteria_by_name( $conditions ) ) {
		$args = array(
			'ignore_sticky_posts' => $params['ignore_sticky_posts'],
		);

		$categories_args = boombox_categories_args( $categories );
		if ( $categories_args ) {
			$args['tax_query'][] = $categories_args;
		}
		$tags_args = boombox_tags_args( $tags );
		if ( $tags_args ) {
			$args['tax_query'][] = $tags_args;
		}
		$reaction_args = boombox_reactions_args( $reactions );
		if ( $reaction_args ) {
			$args['tax_query'][] = $reaction_args;
		}
		if ( isset( $args['tax_query'] ) && ( count( $args['tax_query'] ) > 1 ) ) {
			$args['tax_query']['relation'] = 'AND';
		}

		if ( - 1 != $params['posts_per_page'] ) {
			$args['posts_per_page'] = $params['posts_per_page'];
		} else {
			$args['nopaging'] = true;
		}

		if ( $params['paged'] ) {
			$args['paged'] = $params['paged'];
		}

		if ( $params['offset'] ) {
			$args['offset'] = $params['offset'];
		}

		if ( ! empty( $params['excluded_categories'] ) ) {
			$args['category__not_in'] = $params['excluded_categories'];
		}

		if ( ! empty( $params['excluded_posts'] ) ) {
			$args['post__not_in'] = $params['excluded_posts'];
		}

		$is_adv_enabled        = boombox_is_adv_enabled( $page_ad );
		$is_newsletter_enabled = boombox_is_newsletter_enabled( $page_newsletter );
		$is_product_enabled    = boombox_is_product_enabled( $page_products );
		if ( ( $is_adv_enabled || $is_newsletter_enabled || $is_product_enabled ) && $params['is_page_query'] ) {
			Boombox_Loop_Helper::init( array(
				'is_adv_enabled'        => $is_adv_enabled,
				'instead_adv'           => $instead,
				'is_newsletter_enabled' => $is_newsletter_enabled,
				'instead_newsletter'    => $instead_newsletter,
				'is_product_enabled'    => $is_product_enabled,
				'page_product_position' => $page_product_position,
				'page_product_count'    => $page_product_count,
				'skip'                  => $params['is_grid'],
				'posts_per_page'        => $params['posts_per_page'],
				'paged'                 => $params['paged'],
			) );
		}

		if ( 'all' == $time_range ) {
			$params['is_live'] = true;
		}

		$job        = boombox_get_rate_job( $conditions, $params['post_type'], $time_range, $params['posts_count'], 0, $params['is_live'] );
		$args       = apply_filters( 'boombox/query_args/' . $conditions, $args );
		$rate_query = new Boombox_Rate_Query( $args, $job, $params['fake_meta_key'] );
		$query      = $rate_query->get_wp_query();

	} else {
		switch ( $conditions ) {

			case 'recent':
				$args = array(
					'post_status'         => 'publish',
					'post_type'           => $params['post_type'],
					'orderby'             => 'date',
					'order'               => 'DESC',
					'posts_per_page'      => $params['posts_per_page'],
					'offset'              => $params['offset'],
					'ignore_sticky_posts' => $params['ignore_sticky_posts'],
				);

				$categories_args = boombox_categories_args( $categories );
				if ( $categories_args ) {
					$args['tax_query'][] = $categories_args;
				}
				$tags_args = boombox_tags_args( $tags );
				if ( $tags_args ) {
					$args['tax_query'][] = $tags_args;
				}
				$reaction_args = boombox_reactions_args( $reactions );
				if ( $reaction_args ) {
					$args['tax_query'][] = $reaction_args;
				}
				if ( isset( $args['tax_query'] ) && ( count( $args['tax_query'] ) > 1 ) ) {
					$args['tax_query']['relation'] = 'AND';
				}

				if ( $params['paged'] ) {
					$args['paged'] = $params['paged'];
				}

				if ( ! empty( $params['excluded_categories'] ) ) {
					$args['category__not_in'] = $params['excluded_categories'];
				}

				if ( ! empty( $params['excluded_posts'] ) ) {
					$args['post__not_in'] = $params['excluded_posts'];
				}

				$is_adv_enabled        = boombox_is_adv_enabled( $page_ad );
				$is_newsletter_enabled = boombox_is_newsletter_enabled( $page_newsletter );
				$is_product_enabled    = boombox_is_product_enabled( $page_products );
				if ( ( $is_adv_enabled || $is_newsletter_enabled || $is_product_enabled ) && $params['is_page_query'] ) {
					Boombox_Loop_Helper::init( array(
						'is_adv_enabled'        => $is_adv_enabled,
						'instead_adv'           => $instead,
						'is_newsletter_enabled' => $is_newsletter_enabled,
						'instead_newsletter'    => $instead_newsletter,
						'is_product_enabled'    => $is_product_enabled,
						'page_product_position' => $page_product_position,
						'page_product_count'    => $page_product_count,
						'skip'                  => $params['is_grid'],
						'posts_per_page'        => $params['posts_per_page'],
						'paged'                 => $params['paged'],
					) );
				}

				$args  = apply_filters( 'boombox/query_args/recent', $args );
				$query = new WP_Query( $args );

				break;

			case 'most_shared':
				// get a most shared posts ids
				$args = array(
					'post_status'         => 'publish',
					'post_type'           => $params['post_type'],
					'posts_per_page'      => - 1,
					'ignore_sticky_posts' => $params['ignore_sticky_posts'],
					'fields'              => 'ids',
					'orderby'             => 'meta_value',
					'order'               => 'DESC',
					'meta_query'          => array(
						array(
							'key'     => boombox_get_shares_meta_key(),
							'value'   => 0,
							'compare' => '>',
						),
					),
				);

				$categories_args = boombox_categories_args( $categories );
				if ( $categories_args ) {
					$args['tax_query'][] = $categories_args;
				}
				$tags_args = boombox_tags_args( $tags );
				if ( $tags_args ) {
					$args['tax_query'][] = $tags_args;
				}
				$reaction_args = boombox_reactions_args( $reactions );
				if ( $reaction_args ) {
					$args['tax_query'][] = $reaction_args;
				}
				if ( isset( $args['tax_query'] ) && ( count( $args['tax_query'] ) > 1 ) ) {
					$args['tax_query']['relation'] = 'AND';
				}

				$time_range_args = boombox_time_range_args( $time_range );
				if ( $time_range_args ) {
					$args['date_query'][] = $time_range_args;
				}

				if ( ! empty( $params['excluded_categories'] ) ) {
					$args['category__not_in'] = $params['excluded_categories'];
				}

				if ( ! empty( $params['excluded_posts'] ) ) {
					$args['post__not_in'] = $params['excluded_posts'];
				}

				$most_shared_ids = array();
				$main_query      = new WP_Query( $args );
				if ( $main_query->have_posts() ) {
					$most_shared_ids = $main_query->posts;
				}

				// get a fake posts ids ( for trending pages )
				if ( null != $params['fake_meta_key'] ) {
					$args = array(
						'post_status'         => 'publish',
						'post_type'           => $params['post_type'],
						'posts_per_page'      => - 1,
						'ignore_sticky_posts' => $params['ignore_sticky_posts'],
						'fields'              => 'ids',
						'meta_query'          => array(
							array(
								'key'     => $params['fake_meta_key'],
								'value'   => 0,
								'compare' => '>',
							),
						),
					);

					$fake_posts_query = new WP_Query( $args );
					if ( $fake_posts_query->have_posts() ) {
						$most_shared_ids = array_merge( $most_shared_ids, $fake_posts_query->posts );
					}
				}

				if ( empty( $most_shared_ids ) ) {
					// Passing an empty array to post__in will return all posts.
					// to prevent this we set into array fake post id
					$most_shared_ids = array( 0 );
				}

				if ( ! empty( $params['excluded_posts'] ) ) {
					$most_shared_ids = array_diff( $most_shared_ids, $params['excluded_posts'] );
				}

				$args = array(
					'post_status'         => 'publish',
					'post_type'           => $params['post_type'],
					'posts_per_page'      => $params['posts_per_page'],
					'offset'              => $params['offset'],
					'meta_key'            => boombox_get_shares_meta_key(),
					'orderby'             => 'meta_value_num',
					'order'               => 'DESC',
					'ignore_sticky_posts' => $params['ignore_sticky_posts'],
					'post__in'            => $most_shared_ids,
				);

				if ( $params['paged'] ) {
					$args['paged'] = $params['paged'];
				}

				$is_adv_enabled        = boombox_is_adv_enabled( $page_ad );
				$is_newsletter_enabled = boombox_is_newsletter_enabled( $page_newsletter );
				$is_product_enabled    = boombox_is_product_enabled( $page_products );
				if ( ( $is_adv_enabled || $is_newsletter_enabled || $is_product_enabled ) && $params['is_page_query'] ) {
					Boombox_Loop_Helper::init( array(
						'is_adv_enabled'        => $is_adv_enabled,
						'instead_adv'           => $instead,
						'is_newsletter_enabled' => $is_newsletter_enabled,
						'instead_newsletter'    => $instead_newsletter,
						'is_product_enabled'    => $is_product_enabled,
						'page_product_position' => $page_product_position,
						'page_product_count'    => $page_product_count,
						'skip'                  => $params['is_grid'],
						'posts_per_page'        => $params['posts_per_page'],
						'paged'                 => $params['paged'],
					) );
				}

				$args  = apply_filters( 'boombox/query_args/most_shared', $args );
				$query = new WP_Query( $args );

				break;

			case 'featured':
				$args = array(
					'post_status'         => 'publish',
					'post_type'           => $params['post_type'],
					'posts_per_page'      => $params['posts_per_page'],
					'offset'              => $params['offset'],
					'orderby'             => 'date',
					'order'               => 'DESC',
					'meta_query'          => array(
						array(
							'key'     => 'boombox_is_featured',
							'compare' => '=',
							'value'   => 1,
						),
					),
					'ignore_sticky_posts' => $params['ignore_sticky_posts'],
				);

				$categories_args = boombox_categories_args( $categories );
				if ( $categories_args ) {
					$args['tax_query'][] = $categories_args;
				}
				$tags_args = boombox_tags_args( $tags );
				if ( $tags_args ) {
					$args['tax_query'][] = $tags_args;
				}
				$reaction_args = boombox_reactions_args( $reactions );
				if ( $reaction_args ) {
					$args['tax_query'][] = $reaction_args;
				}
				if ( isset( $args['tax_query'] ) && ( count( $args['tax_query'] ) > 1 ) ) {
					$args['tax_query']['relation'] = 'AND';
				}

				if ( $params['paged'] ) {
					$args['paged'] = $params['paged'];
				}

				if ( ! empty( $params['excluded_categories'] ) ) {
					$args['category__not_in'] = $params['excluded_categories'];
				}

				if ( ! empty( $params['excluded_posts'] ) ) {
					$args['post__not_in'] = $params['excluded_posts'];
				}

				$is_adv_enabled        = boombox_is_adv_enabled( $page_ad );
				$is_newsletter_enabled = boombox_is_newsletter_enabled( $page_newsletter );
				$is_product_enabled    = boombox_is_product_enabled( $page_products );
				if ( ( $is_adv_enabled || $is_newsletter_enabled || $is_product_enabled ) && $params['is_page_query'] ) {
					Boombox_Loop_Helper::init( array(
						'is_adv_enabled'        => $is_adv_enabled,
						'instead_adv'           => $instead,
						'is_newsletter_enabled' => $is_newsletter_enabled,
						'instead_newsletter'    => $instead_newsletter,
						'is_product_enabled'    => $is_product_enabled,
						'page_product_position' => $page_product_position,
						'page_product_count'    => $page_product_count,
						'skip'                  => $params['is_grid'],
						'posts_per_page'        => $params['posts_per_page'],
						'paged'                 => $params['paged'],
					) );
				}

				$args  = apply_filters( 'boombox/query_args/featured', $args );
				$query = new WP_Query( $args );

				break;

			case 'featured_frontpage':
				$args = array(
					'post_status'         => 'publish',
					'post_type'           => $params['post_type'],
					'posts_per_page'      => $params['posts_per_page'],
					'offset'              => $params['offset'],
					'orderby'             => array(
						'meta_value_num' => 'DESC',
						'date'           => 'DESC',
					),
					'meta_query'          => array(
						'relation' => 'OR',
						array(
							'key'     => 'boombox_is_featured',
							'compare' => '>',
							'value'   => 0,
						),
						array(
							'key'     => 'boombox_is_featured_frontpage',
							'compare' => '>',
							'value'   => 0,
						),
					),
					'ignore_sticky_posts' => $params['ignore_sticky_posts'],
				);

				$categories_args = boombox_categories_args( $categories );
				if ( $categories_args ) {
					$args['tax_query'][] = $categories_args;
				}
				$tags_args = boombox_tags_args( $tags );
				if ( $tags_args ) {
					$args['tax_query'][] = $tags_args;
				}
				$reaction_args = boombox_reactions_args( $reactions );
				if ( $reaction_args ) {
					$args['tax_query'][] = $reaction_args;
				}
				if ( isset( $args['tax_query'] ) && ( count( $args['tax_query'] ) > 1 ) ) {
					$args['tax_query']['relation'] = 'AND';
				}

				if ( $params['paged'] ) {
					$args['paged'] = $params['paged'];
				}

				if ( ! empty( $params['excluded_categories'] ) ) {
					$args['category__not_in'] = $params['excluded_categories'];
				}

				if ( ! empty( $params['excluded_posts'] ) ) {
					$args['post__not_in'] = $params['excluded_posts'];
				}

				$is_adv_enabled        = boombox_is_adv_enabled( $page_ad );
				$is_newsletter_enabled = boombox_is_newsletter_enabled( $page_newsletter );
				$is_product_enabled    = boombox_is_product_enabled( $page_products );
				if ( ( $is_adv_enabled || $is_newsletter_enabled || $is_product_enabled ) && $params['is_page_query'] ) {
					Boombox_Loop_Helper::init( array(
						'is_adv_enabled'        => $is_adv_enabled,
						'instead_adv'           => $instead,
						'is_newsletter_enabled' => $is_newsletter_enabled,
						'instead_newsletter'    => $instead_newsletter,
						'is_product_enabled'    => $is_product_enabled,
						'page_product_position' => $page_product_position,
						'page_product_count'    => $page_product_count,
						'skip'                  => $params['is_grid'],
						'posts_per_page'        => $params['posts_per_page'],
						'paged'                 => $params['paged'],
					) );
				}

				$args  = apply_filters( 'boombox/query_args/featured_frontpage', $args );
				$query = new WP_Query( $args );

				break;

			case 'related':
				if ( $post ) {

					$related_ids = array();

					// Exclude current post.
					$params['excluded_posts'][] = $post->ID;

					// get posts id's filtered by current post tags
					$related_args = array(
						'posts_per_page'      => $params['posts_per_page'],
						'post_type'           => $params['post_type'],
						'post_status'         => 'publish',
						'ignore_sticky_posts' => $params['ignore_sticky_posts'],
						'post__not_in'        => array_unique( $params['excluded_posts'] ),
						'fields'              => 'ids',
						'orderby'             => 'date',
					);

					if ( ! empty( $params['excluded_categories'] ) ) {
						$related_args['category__not_in'] = $params['excluded_categories'];
					}

					$categories_args = boombox_categories_args( $categories );
					if ( $categories_args ) {
						$related_args['tax_query'][] = $categories_args;
					}
					$tags_args = boombox_tags_args( $tags );
					if ( $tags_args ) {
						$related_args['tax_query'][] = $tags_args;
					}
					$reaction_args = boombox_reactions_args( $reactions );
					if ( $reaction_args ) {
						$related_args['tax_query'][] = $reaction_args;
					}
					if ( isset( $related_args['tax_query'] ) && ( count( $related_args['tax_query'] ) > 1 ) ) {
						$related_args['tax_query']['relation'] = 'AND';
					}

					$related_args  = apply_filters( 'boombox/query_args/related_args', $related_args );
					$related_query = new WP_Query( $related_args );
					if ( $related_query->have_posts() ) {
						$related_ids = $related_query->posts;
					}

					// if related posts smaller than necessary, add ids from recent posts
					if ( $related_query->found_posts < $params['posts_per_page'] && ! apply_filters( 'boombox/query_args/related/disable_fill_with_recent_posts', false ) ) {
						$exclude_ids   = $related_ids;
						$exclude_ids[] = (int) $post->ID;
						$add_count     = $params['posts_per_page'] - $related_query->found_posts;
						$recent_args   = array(
							'posts_per_page'      => $add_count,
							'post_type'           => $params['post_type'],
							'post_status'         => 'publish',
							'ignore_sticky_posts' => $params['ignore_sticky_posts'],
							'fields'              => 'ids',
						);
						if ( 0 != $add_count ) {
							$recent_args['post__not_in'] = $exclude_ids;
						}
						if ( ! empty( $params['excluded_categories'] ) ) {
							$recent_args['category__not_in'] = $params['excluded_categories'];
						}
						if ( isset( $related_args['tax_query'] ) && ! empty( $related_args['tax_query'] ) ) {
							$recent_args['tax_query'] = $related_args['tax_query'];
						}

						$add_query = new WP_Query( $recent_args );
						if ( $add_query->have_posts() ) {
							$related_ids = array_merge( $related_ids, $add_query->posts );
						}
					}

					if ( ! empty( $params['excluded_posts'] ) ) {
						$related_ids = array_diff( $related_ids, $params['excluded_posts'] );
					}

					// get related posts by ids
					$args  = array(
						'post_type'           => $params['post_type'],
						'post_status'         => 'publish',
						'post__in'            => ! empty( $related_ids ) ? $related_ids : array( 0 ),
						'orderby'             => 'date',
						'posts_per_page'      => $params['posts_per_page'],
						'offset'              => $params['offset'],
						'ignore_sticky_posts' => $params['ignore_sticky_posts'],
					);
					$args  = apply_filters( 'boombox/query_args/related', $args );
					$query = new WP_Query( $args );
				}

				break;

			case 'more_from':
				$args = array(
					'post_type'           => $params['post_type'],
					'post_status'         => 'publish',
					'posts_per_page'      => $params['posts_per_page'],
					'offset'              => $params['offset'],
					'ignore_sticky_posts' => $params['ignore_sticky_posts'],
				);

				if ( is_single() ) {
					$params['excluded_posts'][] = $post->ID;
				}

				$categories_args = boombox_categories_args( $categories );
				if ( $categories_args ) {
					$args['tax_query'][] = $categories_args;
				}
				$tags_args = boombox_tags_args( $tags );
				if ( $tags_args ) {
					$args['tax_query'][] = $tags_args;
				}
				$reaction_args = boombox_reactions_args( $reactions );
				if ( $reaction_args ) {
					$args['tax_query'][] = $reaction_args;
				}
				if ( isset( $args['tax_query'] ) && ( count( $args['tax_query'] ) > 1 ) ) {
					$args['tax_query']['relation'] = 'AND';
				}

				if ( ! empty( $params['excluded_categories'] ) ) {
					$args['category__not_in'] = $params['excluded_categories'];
				}

				if ( ! empty( $params['excluded_posts'] ) ) {
					$args['post__not_in'] = $params['excluded_posts'];
				}

				$args  = apply_filters( 'boombox/query_args/more_from', $args );
				$query = new WP_Query( $args );

				break;

			case 'dont_miss':
				$args = array(
					'post_type'           => $params['post_type'],
					'post_status'         => 'publish',
					'posts_per_page'      => $params['posts_per_page'],
					'offset'              => $params['offset'],
					'ignore_sticky_posts' => $params['ignore_sticky_posts'],
				);

				if ( is_single() ) {
					$params['excluded_posts'][] = $post->ID;
				}

				$categories_args = boombox_categories_args( $categories );
				if ( $categories_args ) {
					$args['tax_query'][] = $categories_args;
				}
				$tags_args = boombox_tags_args( $tags );
				if ( $tags_args ) {
					$args['tax_query'][] = $tags_args;
				}
				$reaction_args = boombox_reactions_args( $reactions );
				if ( $reaction_args ) {
					$args['tax_query'][] = $reaction_args;
				}
				if ( isset( $args['tax_query'] ) && ( count( $args['tax_query'] ) > 1 ) ) {
					$args['tax_query']['relation'] = 'AND';
				}

				if ( ! empty( $params['excluded_categories'] ) ) {
					$args['category__not_in'] = $params['excluded_categories'];
				}

				if ( ! empty( $params['excluded_posts'] ) ) {
					$args['post__not_in'] = $params['excluded_posts'];
				}

				$args  = apply_filters( 'boombox/query_args/dont_miss', $args );
				$query = new WP_Query( $args );

				break;

			case 'random':
				$args = array(
					'post_status'         => 'publish',
					'post_type'           => $params['post_type'],
					'orderby'             => 'rand',
					'order'               => 'DESC',
					'posts_per_page'      => $params['posts_per_page'],
					'ignore_sticky_posts' => $params['ignore_sticky_posts'],
				);

				$categories_args = boombox_categories_args( $categories );
				if ( $categories_args ) {
					$args['tax_query'][] = $categories_args;
				}
				$tags_args = boombox_tags_args( $tags );
				if ( $tags_args ) {
					$args['tax_query'][] = $tags_args;
				}
				$reaction_args = boombox_reactions_args( $reactions );
				if ( $reaction_args ) {
					$args['tax_query'][] = $reaction_args;
				}
				if ( isset( $args['tax_query'] ) && ( count( $args['tax_query'] ) > 1 ) ) {
					$args['tax_query']['relation'] = 'AND';
				}

				$time_range_args = boombox_time_range_args( $time_range );
				if ( $time_range_args ) {
					$args['date_query'][] = $time_range_args;
				}

				if ( $params['paged'] ) {
					$args['paged'] = $params['paged'];
				}

				if ( ! empty( $params['excluded_categories'] ) ) {
					$args['category__not_in'] = $params['excluded_categories'];
				}

				if ( ! empty( $params['excluded_posts'] ) ) {
					$args['post__not_in'] = $params['excluded_posts'];
				}

				$is_adv_enabled        = boombox_is_adv_enabled( $page_ad );
				$is_newsletter_enabled = boombox_is_newsletter_enabled( $page_newsletter );
				$is_product_enabled    = boombox_is_product_enabled( $page_products );
				if ( ( $is_adv_enabled || $is_newsletter_enabled || $is_product_enabled ) && $params['is_page_query'] ) {
					Boombox_Loop_Helper::init( array(
						'is_adv_enabled'        => $is_adv_enabled,
						'instead_adv'           => $instead,
						'is_newsletter_enabled' => $is_newsletter_enabled,
						'instead_newsletter'    => $instead_newsletter,
						'is_product_enabled'    => $is_product_enabled,
						'page_product_position' => $page_product_position,
						'page_product_count'    => $page_product_count,
						'skip'                  => $params['is_grid'],
						'posts_per_page'        => $params['posts_per_page'],
						'paged'                 => $params['paged'],
					) );
				}

				$args  = apply_filters( 'boombox/query_args/random', $args );
				$query = new WP_Query( $args );

				break;
		}
	}

	return $query;
}

/**
 * Creates rate job
 *
 * @param $criteria_name
 * @param $post_type
 * @param $time_range
 * @param $posts_count
 * @param $min_count
 * @param $is_live
 *
 * @return Boombox_Rate_Job
 */
function boombox_get_rate_job( $criteria_name, $post_type, $time_range, $posts_count, $min_count = 0, $is_live = false ) {
	$job_name = md5( uniqid( rand(), true ) );
	$job      = new Boombox_Rate_Job( $job_name, $post_type, 'publish', $criteria_name, $time_range, $posts_count, $min_count, $is_live );

	return $job;
}

/**
 * Check against sidebar enabled feature
 *
 * @return bool
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_is_primary_sidebar_enabled( $sidebar_type = '' ) {

	if ( boombox_is_fragment_cache_enabled() ) {
		?>
		<!-- mfunc <?php echo W3TC_DYNAMIC_SECURITY; ?>
			$enabled = wp_is_mobile() ? boombox_get_theme_option( 'mobile_global_enable_sidebar' ) : true;
		-->
		<?php $enabled = wp_is_mobile() ? boombox_get_theme_option( 'mobile_global_enable_sidebar' ) : true; ?>
		<!-- /mfunc <?php echo W3TC_DYNAMIC_SECURITY; ?> -->
		<?php
	} else {
		if ( boombox_is_page_cache_enabled() ) {
			$enabled = true;
		} else {
			$enabled = wp_is_mobile() ? boombox_get_theme_option( 'mobile_global_enable_sidebar' ) : true;
		}
	}

	return ( $enabled && $sidebar_type && ( $sidebar_type != 'no-sidebar' ) );
}

/**
 * Check against secondary sidebar enabled feature
 *
 * @param $option
 *
 * @return bool
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_is_secondary_sidebar_enabled( $option ) {
	$secondary_sidebar_enable_choices = array(
		'2-sidebars-1_4-1_4',
		'2-sidebars-small-big',
	);

	return in_array( $option, $secondary_sidebar_enable_choices );
}

/**
 * Get related posts query
 *
 * @param $conditions
 * @param $posts_per_page
 *
 * @return WP_Query
 */
function boombox_get_related_posts_items( $conditions, $posts_per_page ) {
	global $post;

	$tax_conditions = array();
	$params         = array( 'posts_per_page' => $posts_per_page );

	$rel_tags = get_the_terms( $post->ID, 'post_tag' );
	if ( ! empty( $rel_tags ) ) {
		$tax_conditions['tag'] = wp_list_pluck( $rel_tags, 'slug' );
	}

	$query = boombox_get_posts_query( $conditions, '', $tax_conditions, $params );

	return $query;
}

/**
 * Get "More From" Section posts query
 *
 * @param $conditions
 * @param $post_first_category
 * @param $posts_per_page
 *
 * @return WP_Query
 */
function boombox_get_more_from_posts_items( $conditions, $post_first_category, $posts_per_page ) {

	$time_range = '';
	$categories = array();
	$tags       = array();

	if ( $post_first_category ) {
		$categories = array(
			$post_first_category->slug,
		);
	}

	$query = boombox_get_posts_query( $conditions, $time_range, array(
		'category' => $categories,
		'tag'      => $tags,
	), array(
		'posts_per_page' => $posts_per_page,
	) );

	return $query;
}

/**
 * Get "Don't Miss" Section posts query
 *
 * @param $conditions
 * @param $posts_per_page
 *
 * @return WP_Query
 */
function boombox_get_dont_miss_posts_items( $conditions, $posts_per_page ) {

	$query = boombox_get_posts_query( $conditions, 'all', array(), array(
		'posts_per_page' => $posts_per_page,
	) );

	return $query;
}

/**
 * Get Trending Posts
 *
 * @param       $type
 * @param       $posts_per_page
 * @param array $params
 *
 * @return bool|WP_Query
 */
function boombox_get_trending_posts( $type, $posts_per_page, $params = array() ) {

	$params = wp_parse_args( $params, array(
		'paged'                 => 1,
		'is_grid'               => false,
		'page_ad'               => 'none',
		'instead_ad'            => 1,
		'page_newsletter'       => 'none',
		'instead_newsletter'    => 1,
		'page_product'          => 'none',
		'page_product_position' => 1,
		'page_product_count'    => 1,
		'is_widget'             => false,
		'ignore_sticky_posts'   => true,
	) );

	static $boombox_trending_query;

	if ( $params['is_widget'] ) {
		unset( $boombox_trending_query[ $type ] );
	}

	if ( ! isset( $boombox_trending_query[ $type ] ) ) {
		$post_type     = 'post';
		$query         = null;
		$fake_meta_key = null;
		$criteria_name = boombox_get_theme_option( 'extras_post_ranking_system_trending_conditions' );

		if ( 'trending' == $type ) {
			$time_range    = 'day';
			$posts_count   = boombox_get_theme_option( 'extras_post_ranking_system_trending_posts_count' );
			$fake_meta_key = 'boombox_keep_trending';
		} elseif ( 'hot' == $type ) {
			$time_range    = 'week';
			$posts_count   = boombox_get_theme_option( 'extras_post_ranking_system_hot_posts_count' );
			$fake_meta_key = 'boombox_keep_hot';
		} elseif ( 'popular' == $type ) {
			$time_range    = 'month';
			$posts_count   = boombox_get_theme_option( 'extras_post_ranking_system_popular_posts_count' );
			$fake_meta_key = 'boombox_keep_popular';
		}

		$is_module_active = boombox_module_management_service()->is_module_active( 'prs' );
		if ( $is_module_active && Boombox_Rate_Criteria::get_criteria_by_name( $criteria_name ) && $job = Boombox_Rate_Job::get_job_by_name( $type ) ) {
			$args = array(
				'nopaging'            => false,
				'ignore_sticky_posts' => $params['ignore_sticky_posts'],
			);

			if ( - 1 != $posts_per_page ) {
				$args['posts_per_page'] = $posts_per_page;
			} else {
				$args['nopaging'] = true;
			}

			if ( $params['paged'] ) {
				$args['paged'] = $params['paged'];
			}

			$is_adv_enabled        = boombox_is_adv_enabled( $params['page_ad'] );
			$is_newsletter_enabled = boombox_is_newsletter_enabled( $params['page_newsletter'] );
			$is_product_enabled    = boombox_is_product_enabled( $params['page_product'] );

			if ( $is_adv_enabled || $is_newsletter_enabled || $is_product_enabled ) {
				Boombox_Loop_Helper::init( array(
					'is_adv_enabled'        => $is_adv_enabled,
					'instead_adv'           => $params['instead_ad'],
					'is_newsletter_enabled' => $is_newsletter_enabled,
					'instead_newsletter'    => $params['instead_newsletter'],
					'is_product_enabled'    => $is_product_enabled,
					'page_product_position' => $params['page_product_position'],
					'page_product_count'    => $params['page_product_count'],
					'skip'                  => $params['is_grid'],
					'posts_per_page'        => $posts_per_page,
					'paged'                 => $params['paged'],
				) );
			}

			$rate_query = new Boombox_Rate_Query( $args, $job, $fake_meta_key );

			$query = $rate_query->get_wp_query();

		} elseif ( 'most_shared' == $criteria_name || 'recent' === $criteria_name || 'featured' === $criteria_name ) {
			$categories          = array();
			$tags                = array();
			$is_page_query       = true;
			$excluded_categories = array();
			$is_live             = false;
			$query               = boombox_get_posts_query( $criteria_name, $time_range, array(
				'category' => $categories,
				'tag'      => $tags,
			), array(
				'posts_per_page'      => $posts_per_page,
				'post_type'           => $post_type,
				'paged'               => $params['paged'],
				'posts_count'         => $posts_count,
				'is_grid'             => $params['is_grid'],
				'is_page_query'       => $is_page_query,
				'excluded_categories' => $excluded_categories,
				'is_live'             => $is_live,
				'fake_meta_key'       => $fake_meta_key,
			) );
		}

		if ( $query ) {
			if ( 'trending' == $type ) {
				$boombox_trending_query['trending'] = $query;
			} elseif ( 'hot' == $type ) {
				$boombox_trending_query['hot'] = $query;
			} elseif ( 'popular' == $type ) {
				$boombox_trending_query['popular'] = $query;
			}

			return $boombox_trending_query[ $type ];
		}

	} else {
		return $boombox_trending_query[ $type ];
	}

	return false;
}

/**
 * Get trending page id by type
 *
 * @param $type 'trending' |'hot' |'popular'
 *
 * @return int|mixed
 */
function boombox_get_trending_page_id( $type ) {
	$customize_setting_slug = "extras_post_ranking_system_{$type}_page";
	$trending_page_id       = boombox_get_theme_option( $customize_setting_slug );

	return $trending_page_id;
}

/**
 * Return true if is trending page
 *
 * @param     $type 'trending' |'hot' |'popular'
 * @param int $post_id
 *
 * @return bool
 */
function boombox_is_trending_page( $type, $post_id = 0 ) {
	$is_trending = false;

	if ( 0 == $post_id ) {
		global $post;
		if ( is_object( $post ) ) {
			$post_id = $post->ID;
		}
	}

	if ( $post_id ) {
		$trending_page_id = boombox_get_trending_page_id( $type );
		$is_trending      = ( $trending_page_id == $post_id );
	}

	return $is_trending;
}

/**
 * Check, if post is trending
 *
 * @param string           $type trending,hot,popular
 * @param null|int|WP_Post $post Post
 *
 * @return bool
 */
function boombox_is_post_trending( $type, $post = null ) {
	$post = get_post( $post );

	$time_range  = false;
	$posts_count = false;
	$set         = boombox_get_theme_options_set( array(
		'extras_post_ranking_system_trending_conditions',
		'extras_post_ranking_system_trending_enable',
		'extras_post_ranking_system_hot_enable',
		'extras_post_ranking_system_popular_enable',
		'extras_post_ranking_system_trending_posts_count',
		'extras_post_ranking_system_hot_posts_count',
		'extras_post_ranking_system_popular_posts_count',
	) );

	if ( ( 'trending' === $type && ! $set['extras_post_ranking_system_trending_enable'] ) || ( 'hot' === $type && ! $set['extras_post_ranking_system_hot_enable'] ) || ( 'popular' === $type && ! $set['extras_post_ranking_system_popular_enable'] ) ) {
		return false;
	}

	$is_module_active = boombox_module_management_service()->is_module_active( 'prs' );
	if ( $is_module_active && Boombox_Rate_Criteria::get_criteria_by_name( $set['extras_post_ranking_system_trending_conditions'] ) ) {
		$keep_trending = ! ! ( boombox_get_post_meta( $post->ID, 'boombox_keep_' . $type ) );

		if ( ! $keep_trending && $job = Boombox_Rate_Job::get_job_by_name( $type ) ) {
			$keep_trending = Boombox_Rate_Cron::is_post_rated( $job, $post->ID );
		}

		return $keep_trending;
	}

	if ( 'trending' === $type && $set['extras_post_ranking_system_trending_enable'] && $set['extras_post_ranking_system_trending_posts_count'] ) {
		$time_range  = 'day';
		$posts_count = $set['extras_post_ranking_system_trending_posts_count'];
	} else {
		if ( 'hot' === $type && $set['extras_post_ranking_system_hot_enable'] && $set['extras_post_ranking_system_hot_posts_count'] ) {
			$time_range  = 'week';
			$posts_count = $set['extras_post_ranking_system_hot_posts_count'];
		} else {
			if ( 'popular' === $type && $set['extras_post_ranking_system_popular_enable'] && $set['extras_post_ranking_system_popular_posts_count'] ) {
				$time_range  = 'month';
				$posts_count = $set['extras_post_ranking_system_popular_posts_count'];
			}
		}
	}

	$is_trending = false;
	if ( $time_range && $posts_count && in_array( $set['extras_post_ranking_system_trending_conditions'], array(
			'most_shared',
			'recent',
		) ) ) {

		$cache_key    = 'trending_posts_' . md5( json_encode( array( 'type' => $type, 'count' => $posts_count ) ) );
		$trending_ids = boombox_cache_get( $cache_key );
		if ( false === $trending_ids ) {
			$trending_ids = array();
			$query        = boombox_get_trending_posts( $type, $posts_count );
			if ( is_object( $query ) && $query->have_posts() ) {
				$trending_ids = wp_list_pluck( $query->posts, 'ID' );
				boombox_cache_set( $cache_key, $trending_ids );
			}
		}
		$is_trending = in_array( $post->ID, $trending_ids );
	}

	return $is_trending;
}

/**
 * Return true, if is trending, hot or popular page
 *
 * @param int $post_id Page ID to check
 *
 * @return bool
 */
function boombox_is_trending( $post_id = 0 ) {

	$post_id = absint( $post_id );
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	$cache_key = 'is_trending_' . $post_id;

	$is_trending = boombox_cache_get( $cache_key );
	if ( ! $is_trending ) {

		$is_trending = false;

		if ( $post_id && ( boombox_is_trending_page( 'trending', $post_id ) || boombox_is_trending_page( 'hot', $post_id ) || boombox_is_trending_page( 'popular', $post_id ) ) ) {
			$is_trending = true;
		}

		boombox_cache_set( $cache_key, $is_trending );

	}

	return $is_trending;
}

/**
 * Get Trending Navigation Items
 *
 * @return array
 */
function boombox_get_trending_navigation_items() {
	$set = boombox_get_theme_options_set( array(
		'extras_badges_trending_icon',
		'extras_post_ranking_system_trending_enable',
		'extras_post_ranking_system_hot_enable',
		'extras_post_ranking_system_popular_enable',
	) );

	$trending_pages     = array(
		'trending' => array(
			'page'   => 'trending',
			'icon'   => $set['extras_badges_trending_icon'],
			'enable' => $set['extras_post_ranking_system_trending_enable'],
		),
		'hot'      => array(
			'page'   => 'hot',
			'icon'   => 'hot',
			'enable' => $set['extras_post_ranking_system_hot_enable'],
		),
		'popular'  => array(
			'page'   => 'popular',
			'icon'   => 'popular',
			'enable' => $set['extras_post_ranking_system_popular_enable'],
		),
	);
	$trending_pages_nav = array();
	foreach ( $trending_pages as $trending_page_key => $tr_page_options ) {
		$page_id = boombox_get_trending_page_id( $tr_page_options['page'] );
		if ( $page_id && $tr_page_options['enable'] ) {
			$trending_page = get_post( $page_id );
			if ( $trending_page ) {
				$trending_pages_nav[ $trending_page_key ]['id']   = $page_id;
				$trending_pages_nav[ $trending_page_key ]['key']  = $tr_page_options['page'];
				$trending_pages_nav[ $trending_page_key ]['href'] = get_permalink( $trending_page->ID );
				$trending_pages_nav[ $trending_page_key ]['name'] = esc_html( get_the_title( $trending_page ) );
				$trending_pages_nav[ $trending_page_key ]['icon'] = $tr_page_options['icon'];
			}

		}
	}

	return $trending_pages_nav;
}

/**
 * Get post reaction settings
 *
 * @param int $post_id
 *
 * @return array
 */
function boombox_get_post_reaction_settings( $post_id ) {

	$reaction_total          = array();
	$boombox_all_reactions   = array();
	$reaction_restrictions   = array();
	$reactions_login_require = (bool) boombox_get_theme_option( 'extras_post_reaction_system_login_require' );
	$reaction_item_class     = 'js-reaction-item';
	$authentication_url      = '#';
	$authentication_class    = '';

	if ( boombox_module_management_service()->is_module_active( 'prs' ) ) {
		$reaction_total        = Boombox_Reaction_Helper::get_reaction_total( $post_id );
		$boombox_all_reactions = function_exists( 'boombox_get_all_reactions' ) ? boombox_get_all_reactions() : false;
		$reaction_restrictions = Boombox_Reaction_Helper::get_post_reaction_restrictions( $post_id );


	}
	if ( $reactions_login_require == true && ! is_user_logged_in() ) {
		$authentication_class = 'js-authentication';
		$authentication_url   = '#sign-in';
		$reaction_item_class  = '';
	}

	return array(
		'reaction_total'          => $reaction_total,
		'all_reactions'           => $boombox_all_reactions,
		'reaction_restrictions'   => $reaction_restrictions,
		'reactions_login_require' => $reactions_login_require,
		'reaction_item_class'     => $reaction_item_class,
		'authentication_url'      => $authentication_url,
		'authentication_class'    => $authentication_class,
	);
}

/**
 * Get Time Range args for query argument
 *
 * @param $time_range
 *
 * @return array
 */
function boombox_get_time_range_args( $time_range ) {
	$args = array();

	if ( $time_range == 'all' || $time_range == '' ) {
		return $args;
	}

	$args['date_query'] = array(
		array(
			'after' => sprintf( esc_html__( "1 %s ago", 'boombox' ), $time_range ),
		),
	);

	return $args;
}

/**
 * Get categories args for query argument
 *
 * @param $categories
 *
 * @return array
 */
function boombox_categories_args( $categories ) {
	$args = array();
	if ( empty( $categories ) ) {
		return $args;
	}
	if ( ! is_array( $categories ) || 0 == count( $categories ) || '' == $categories[0] ) {
		return $args;
	}

	$args = array(
		'taxonomy'         => 'category',
		'field'            => 'slug',
		'terms'            => $categories,
		'operator'         => 'IN',
		'include_children' => false,
	);

	return apply_filters( 'boombox/categories-args', $args );
}

/**
 * Get tags args for query argument
 *
 * @param $tags
 *
 * @return array
 */
function boombox_tags_args( $tags ) {
	$args = array();
	if ( empty( $tags ) ) {
		return $args;
	}
	if ( is_string( $tags ) ) {
		$tags = explode( ',', preg_replace( '/\s+/', '', sanitize_text_field( $tags ) ) );
	}
	if ( 0 == count( $tags ) || '' == $tags[0] ) {
		return $args;
	}

	$args = array(
		'taxonomy'         => 'post_tag',
		'field'            => 'slug',
		'terms'            => $tags,
		'operator'         => 'IN',
		'include_children' => false,
	);

	return apply_filters( 'boombox/tags-args', $args );
}

/**
 * Get date args for query argument
 *
 * @param string $range The range key
 *
 * @return array
 */
function boombox_time_range_args( $range ) {
	switch ( $range ) {
		case 'day':
		case 'week':
		case 'month':
			$date = array(
				'after'     => date( 'Y-m-d H:i:s', strtotime( '-1 ' . $range ) ),
				'inclusive' => true
			);
			break;
		default:
			$date = array();
	}

	return $date;
}

/**
 * Get categories args for query argument
 *
 * @param $reactions
 *
 * @return array
 */
function boombox_reactions_args( $reactions ) {
	$args = array();
	if ( empty( $reactions ) ) {
		return $args;
	}
	if ( ! is_array( $reactions ) || 0 == count( $reactions ) || '' == $reactions[0] ) {
		return $args;
	}

	$args = array(
		'taxonomy'         => 'reaction',
		'field'            => 'slug',
		'terms'            => $reactions,
		'operator'         => 'IN',
		'include_children' => false,
	);

	return apply_filters( 'boombox/reactions-args', $args );
}

/**
 * Get post types args for query
 *
 * @param $post_types
 *
 * @return array
 */
function boombox_get_post_types_args( $post_types ) {

	if ( ! is_string( $post_types ) && ! is_array( $post_types ) ) {
		$post_types = array( 'post' );
	}

	if ( is_string( $post_types ) ) {
		$post_types = explode( ',', preg_replace( '/\s+/', '', $post_types ) );
	}

	if ( empty( $post_types ) ) {
		$post_types = array( 'post' );
	}

	return $post_types;
}

/**
 * Get post first category
 *
 * @param $post
 *
 * @return bool
 */
function boombox_get_post_first_category( $post ) {

	$post    = get_post( $post );
	$post_id = $post->ID;

	$terms = get_the_category( $post_id );
	if ( ! empty( $terms ) ) {
		return $terms[0];
	}

	return false;
}

/**
 * Get post first category
 *
 * @param $post
 *
 * @return bool
 */
function boombox_get_post_first_tag( $post ) {

	$post    = get_post( $post );
	$post_id = $post->ID;

	$terms = get_the_tags( $post_id );
	if ( ! empty( $terms ) ) {
		return $terms[0];
	}

	return false;
}

/**
 * Add additional classes to badges warpper element
 *
 * @param string $classes  Current classes
 * @param string $taxonomy Term Taxonomy
 * @param int    $term_id  Term ID
 *
 * @return string
 */
function boombox_add_additional_badge_classes( $classes, $taxonomy, $term_id ) {
	if ( in_array( $taxonomy, array(
		'reaction',
		'category',
		'post_tag',
	) ) ) {
		$classes .= sprintf( ' %1$s-%2$d', $taxonomy, $term_id );
	}

	return $classes;
}

add_filter( 'boombox_badge_wrapper_advanced_classes', 'boombox_add_additional_badge_classes', 10, 3 );

/**
 * Edit comment reply URL
 *
 * @param string $link Current URL
 * @param array  $args Args @see get_comment_reply_link()
 *
 * @return string
 */
function boombox_edit_comment_reply_url( $link, $args ) {
	if ( get_option( 'comment_registration' ) && ! is_user_logged_in() ) {
		$link = sprintf( '<a rel="nofollow" class="comment-reply-login js-authentication" href="#sign-in">%s</a>', $args['login_text'] );
	}

	return $link;
}

add_filter( 'comment_reply_link', 'boombox_edit_comment_reply_url', 10, 2 );

/**
 * Show post badge list
 *
 * @param array     $args                    {
 *                                           Optional. Override default arguments.
 *
 * @type int        $post_id                 Post ID
 * @type bool       $badges                  Show badges
 * @type int|string $badges_count            Badges count
 * @type string     $badges_before           HTML before badges
 * @type string     $badges_after            HTML after badges
 * @type bool       $post_type_badges        Show post type badges
 * @type int|string $post_type_badges_count  Post type badges count
 * @type string     $post_type_badges_before HTML before Post type badges
 * @type string     $post_type_badges_after  HTML after Post type badges
 * @type bool       $is_amp                  Is currently AMP version rendered
 * }
 * @return array|null
 */
function boombox_get_post_badge_list( $args = '' ) {

	$args = wp_parse_args( $args, array(
		'post_id'                 => 0,
		'badges'                  => true,
		'badges_count'            => 2,
		'badges_before'           => '<div class="bb-badge-list">',
		'badges_after'            => '</div>',
		'post_type_badges'        => true,
		'post_type_badges_count'  => 1,
		'post_type_badges_before' => '<div class="bb-post-format xs">',
		'post_type_badges_after'  => '</div>',
		'is_amp'                  => boombox_is_amp(),
	) );

	$return = array(
		'badges'           => '',
		'post_type_badges' => '',
	);

	// if all badges are disabled
	$hide_badges_option    = boombox_hide_badges_option();
	$show_post_type_badges = boombox_get_theme_option( 'extras_badges_post_type_badges' );
	if ( $hide_badges_option['hide_trending_badges'] && $hide_badges_option['hide_category_badges'] && $hide_badges_option['hide_reactions_badges'] && ! $show_post_type_badges ) {
		return $return;
	}

	if ( 0 == $args['post_id'] ) {
		global $post;
		if ( is_object( $post ) ) {
			$args['post_id'] = $post->ID;
		}
	}

	$post = get_post( $args['post_id'] );

	if ( 'post' !== get_post_type() || ! $post ) {
		return $return;
	}

	$args['post_id'] = $post->ID;
	$hash            = md5( json_encode( $args ) );

	/***** return data from cache if we already have it */
	if ( $cached = boombox_cache_get( $hash ) ) {
		return $cached;
	}

	$badges                   = array();
	$post_type_badges         = array();
	$badges_counter           = 0;
	$post_type_badges_counter = 0;

	// trending badge
	if ( ! $hide_badges_option['hide_trending_badges'] && $args['badges'] ) {
		$trending_types = array(
			'trending' => esc_html__( 'Trending', 'boombox' ),
			'hot'      => esc_html__( 'Hot', 'boombox' ),
			'popular'  => esc_html__( 'Popular', 'boombox' ),
		);

		foreach ( $trending_types as $trending_type => $trending_name ) {
			$show_badge  = (bool) boombox_get_theme_option( "extras_post_ranking_system_{$trending_type}_badge" );
			$is_trending = boombox_is_post_trending( $trending_type, $args['post_id'] );
			if ( $show_badge && $is_trending ) {
				$trending_page_id                      = boombox_get_trending_page_id( $trending_type );
				$trending_icon_name                    = boombox_get_trending_icon_name( 'icon', $trending_type );
				$badges[ $badges_counter ]['name']     = ucfirst( $trending_name );
				$badges[ $badges_counter ]['icon']     = ! empty( $trending_icon_name ) ? '<i class="bb-icon bb-icon-' . $trending_icon_name . '"></i>' : '';
				$badges[ $badges_counter ]['link']     = get_permalink( $trending_page_id );
				$badges[ $badges_counter ]['class']    = esc_attr( 'trending' );
				$badges[ $badges_counter ]['taxonomy'] = 'trending';
				$badges[ $badges_counter ]['term_id']  = '';
				if ( $args['is_amp'] ) {
					$badges[ $badges_counter ]['amp'] = array(
						'icon_type' => 'icon',
						'icon'      => ! empty( $trending_icon_name ) ? $trending_icon_name : '',
					);
				}
				++ $badges_counter;
				break;
			}
		}
	}

	$post_categories = $post_tags = array();
	if ( ( ! $hide_badges_option['hide_category_badges'] && $args['badges'] ) || ( $show_post_type_badges && $args['post_type_badges'] ) ) {
		$post_categories = get_the_category( $args['post_id'] );
		$post_tags       = wp_get_post_tags( $args['post_id'] );
	}

	if ( ! empty( $post_categories ) ) {

		$post_categories_slugs = wp_list_pluck( $post_categories, 'slug' );

		if ( ! $hide_badges_option['hide_category_badges'] && $args['badges'] ) {
			// categories badges
			$categories_with_badges = boombox_get_categories_with_badges( 'extras_badges_show_for_categories' );

			$loop_index                  = 0;
			$category_total_badges_count = apply_filters( 'boombox/post_badge_list_categories_count', 2, $args['post_id'] );
			foreach ( $categories_with_badges as $key => $categories_with_badge ) {
				if ( in_array( $key, $post_categories_slugs ) ) {
					$badges[ $badges_counter ] = $categories_with_badge;
					++ $badges_counter;
					if ( $category_total_badges_count == $loop_index + 1 ) {
						break;
					}
					$loop_index ++;
				}
			}

		}

		if ( $show_post_type_badges && $args['post_type_badges'] ) {

			// post type badges categories badges
			$post_type_badges_categories_with_badges = boombox_get_categories_with_badges( 'extras_badges_categories_for_post_type_badges' );

			foreach ( $post_type_badges_categories_with_badges as $key => $post_type_badges_categories_with_badge ) {
				if ( in_array( $key, $post_categories_slugs ) ) {
					$post_type_badges[ $post_type_badges_counter ] = $post_type_badges_categories_with_badge;
					++ $post_type_badges_counter;
					break;
				}
			}
		}
	}

	if ( ! empty( $post_tags ) ) {

		$post_tags_slugs = wp_list_pluck( $post_tags, 'slug' );

		if ( ! $hide_badges_option['hide_category_badges'] && $args['badges'] ) {
			// post tag badges
			$post_tags_with_badges = boombox_get_post_tags_with_badges( 'extras_badges_show_for_post_tags' );

			foreach ( $post_tags_with_badges as $key => $post_tags_with_badge ) {
				if ( in_array( $key, $post_tags_slugs ) ) {
					$badges[ $badges_counter ] = $post_tags_with_badge;
					++ $badges_counter;
					break;
				}
			}
		}

		if ( $show_post_type_badges && $args['post_type_badges'] ) {
			// post type badges post tag badges
			$post_type_badges_post_tags_with_badges = boombox_get_post_tags_with_badges( 'extras_badges_post_tags_for_post_type_badges' );

			foreach ( $post_type_badges_post_tags_with_badges as $key => $post_type_badges_post_tags_with_badge ) {
				if ( in_array( $key, $post_tags_slugs ) ) {
					$post_type_badges[ $post_type_badges_counter ] = $post_type_badges_post_tags_with_badge;
					++ $post_type_badges_counter;
					break;
				}
			}
		}

	}

	// reactions badges
	if ( ! $hide_badges_option['hide_reactions_badges'] && ! is_tax( 'reaction' ) && $args['badges'] ) {
		$reactions = boombox_get_post_reactions( $args['post_id'] );
		if ( is_array( $reactions ) && count( $reactions ) > 0 ) {

			$loop_index                   = 0;
			$reactions_total_badges_count = apply_filters( 'boombox/post_badge_list_reactions_count', 2, $args['post_id'] );
			foreach ( $reactions as $key => $reaction ) {
				$reaction_icon_url                     = boombox_get_reaction_icon_url( $reaction->term_id );
				$badges[ $badges_counter ]['name']     = $reaction->name;
				$badges[ $badges_counter ]['icon']     = ! empty( $reaction_icon_url ) ? ' <img src="' . esc_url( $reaction_icon_url ) . '" alt="' . $reaction->name . '">' : '';
				$badges[ $badges_counter ]['link']     = get_term_link( $reaction->term_id );
				$badges[ $badges_counter ]['class']    = $reaction->taxonomy;
				$badges[ $badges_counter ]['taxonomy'] = $reaction->taxonomy;
				$badges[ $badges_counter ]['term_id']  = $reaction->term_id;
				if ( $args['is_amp'] ) {
					$badges[ $badges_counter ]['amp'] = array(
						'icon_type' => 'image',
						'icon'      => ! empty( $reaction_icon_url ) ? esc_url( $reaction_icon_url ) : '',
					);
				}
				++ $badges_counter;
				if ( $reactions_total_badges_count == $loop_index + 1 ) {
					break;
				}
				$loop_index ++;
			}
		}
	}

	$badges_return           = '';
	$post_type_badges_return = '';

	if ( ! empty( $badges ) ) {
		// for "You may also like", "More From" and "Don't miss" sections on post single page
		$badges = array_slice( $badges, 0, $args['badges_count'] );

		if ( $args['is_amp'] ) {
			$badges_return = $badges;
		} else {
			$badges_return .= $args['badges_before'];
			foreach ( $badges as $badge_key => $badge ) {
				$badge_class = apply_filters( 'boombox_badge_wrapper_advanced_classes', esc_attr( $badge['class'] ), $badge['taxonomy'], $badge['term_id'] );
				$badge_url   = esc_url( $badge['link'] );
				$badge_title = esc_html( $badge['name'] );
				$badge_icon  = $badge['icon'];

				$badges_return .= sprintf( '<a class="bb-badge badge %1$s" href="%2$s" title="%3$s"><span class="circle">%4$s</span><span class="text">%3$s</span></a>', $badge_class, $badge_url, $badge_title, $badge_icon );
			}
			$badges_return .= $args['badges_after'];
		}
	}

	if ( ! empty( $post_type_badges ) ) {
		$post_type_badges = array_slice( $post_type_badges, 0, $args['post_type_badges_count'] );

		if ( $args['is_amp'] ) {
			$post_type_badges_return = $post_type_badges;
		} else {
			$post_type_badges_return .= $args['post_type_badges_before'];
			foreach ( $post_type_badges as $badge_key => $post_type_badge ) {
				$badge_class = sprintf( 'category format-%d', $post_type_badge['term_id'] );
				$badge_title = esc_html( $post_type_badge['name'] );
				$badge_icon  = $post_type_badge['icon'];

				$post_type_badges_return .= sprintf( '<span class="bb-badge badge %1$s" title="%2$s"><span class="circle">%3$s</span><span class="text">%2$s</span></span>',

					$badge_class, $badge_title, $badge_icon );
			}
			$post_type_badges_return .= $args['post_type_badges_after'];
		}
	}

	$return['badges']           = $badges_return;
	$return['post_type_badges'] = $post_type_badges_return;

	boombox_cache_set( $hash, $return );

	return $return;

}

/**
 * Hide badges option
 *
 * @return mixed
 */
function boombox_hide_badges_option() {
	$cache_key   = 'hide_badges';
	$hide_badges = boombox_cache_get( $cache_key );

	if ( ! $hide_badges ) {
		$set         = boombox_get_theme_options_set( array(
			'extras_badges_trending',
			'extras_badges_category',
			'extras_badges_reactions',
		) );
		$hide_badges = array(
			'hide_trending_badges'  => ! $set['extras_badges_trending'],
			'hide_category_badges'  => ! $set['extras_badges_category'],
			'hide_reactions_badges' => ! $set['extras_badges_reactions'],
		);

		boombox_cache_set( $cache_key, $hide_badges );
	}

	return apply_filters( 'boombox/hide-badges', $hide_badges );
}

/**
 * Get categories with badges
 *
 * @return array
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_get_categories_with_badges( $theme_option ) {

	$cache_key              = $theme_option . '_categories_with_badges';
	$categories_with_badges = boombox_cache_get( $cache_key );

	if ( ! $categories_with_badges ) {

		$categories_with_badges = array();
		$categories             = boombox_get_theme_option( $theme_option );

		if ( ! empty( $categories ) ) {
			$categories = array_filter( $categories );
			foreach ( $categories as $category ) {
				$category = get_term_by( 'slug', $category, 'category' );

				if ( $category ) {
					$term_icon = boombox_get_term_icon_html( $category->term_id, $category->name, $category->taxonomy );
					if ( $term_icon ) {
						$categories_with_badges[ $category->slug ] = array(
							'term_id'  => $category->term_id,
							'taxonomy' => 'category',
							'name'     => $category->name,
							'link'     => esc_url( get_term_link( $category->term_id ) ),
							'icon'     => $term_icon,
							'class'    => 'category',
						);
					}
				}
			}
		}

		boombox_cache_set( $cache_key, $categories_with_badges );

	}

	return $categories_with_badges;
}

/**
 * Get post_tags with badges
 *
 * @return array
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_get_post_tags_with_badges( $theme_option ) {

	$cache_key             = $theme_option . '_post_tags_with_badges';
	$post_tags_with_badges = boombox_cache_get( $cache_key );

	if ( ! $post_tags_with_badges ) {

		$post_tags_with_badges = array();
		$post_tags             = boombox_get_theme_option( $theme_option );
		if ( $post_tags ) {
			if ( ! is_array( $post_tags ) ) {
				$post_tags = array_filter( explode( ',', $post_tags ) );
			}

			foreach ( $post_tags as $post_tag ) {
				$post_tag = get_term_by( 'slug', $post_tag, 'post_tag' );

				if ( $post_tag ) {
					$term_icon = boombox_get_term_icon_html( $post_tag->term_id, $post_tag->name, $post_tag->taxonomy );
					if ( $term_icon ) {
						$post_tags_with_badges[ $post_tag->slug ] = array(
							'term_id'  => $post_tag->term_id,
							'taxonomy' => 'post_tag',
							'name'     => $post_tag->name,
							'link'     => esc_url( get_term_link( $post_tag->term_id ) ),
							'icon'     => $term_icon,
							'class'    => 'post_tag',
						);
					}
				}
			}
		}

		boombox_cache_set( $cache_key, $post_tags_with_badges );

	}

	return $post_tags_with_badges;
}

/**
 * @param $key 'trending' || 'hot' || 'popular'
 *
 * @return mixed|string
 */
function get_trending_icon_by_key( $key ) {
	switch ( $key ) {
		case 'trending':
			$icon = boombox_get_theme_option( 'extras_badges_trending_icon' );
			break;
		case 'hot':
			$icon = 'hot';
			break;
		case 'popular':
			$icon = 'popular';
			break;
		default:
			$icon = '';
	}

	return $icon;
}

/**
 * @param $get_by 'post_id' or 'icon'
 * @param $key    'trending_TYPE_post_id' or 'icon_key' ( 'trending', 'hot', 'popular' )
 *
 * @return mixed|string
 */
function boombox_get_trending_icon_name( $get_by, $key ) {
	$icon_key = '';

	if ( $get_by == 'post_id' ) {
		if ( boombox_is_trending_page( 'trending', $key ) ) {
			$icon_key = 'trending';
		} elseif ( boombox_is_trending_page( 'hot', $key ) ) {
			$icon_key = 'hot';
		} elseif ( boombox_is_trending_page( 'popular', $key ) ) {
			$icon_key = 'popular';
		}
	} else {
		if ( $get_by == 'icon' ) {
			$icon_key = $key;
		}
	}

	return $icon_key ? get_trending_icon_by_key( $icon_key ) : '';
}

/**
 * Get Term icon name
 *
 * @param $term_id
 *
 * @return string
 */
function boombox_get_term_icon_name( $term_id, $taxonomy ) {
	switch ( $taxonomy ) {
		case 'category':
			$meta_key = 'cat_icon_name';
			break;
		case 'post_tag':
			$meta_key = 'post_tag_icon_name';
			break;
		default:
			$meta_key = false;
	}
	if ( $meta_key ) {
		$cat_icon_name = sanitize_text_field( boombox_get_term_meta( $term_id, $meta_key ) );
		if ( $cat_icon_name ) {
			return $cat_icon_name;
		}
	}

	return '';
}

function boombox_get_term_custom_image_icon( $term_id ) {
	$term_image_icon_url = false;
	$term_image_icon_id  = boombox_get_term_meta( $term_id, 'term_image_icon_id' );

	if ( $term_image_icon_id ) {
		$term_image_icon_url = wp_get_attachment_thumb_url( $term_image_icon_id );
	}

	return $term_image_icon_url;
}

function boombox_get_term_icon_html( $id, $name, $tax ) {
	$html = false;

	if ( $term_image_icon_url = boombox_get_term_custom_image_icon( $id ) ) {
		$html = sprintf( '<img src="%1$s" alt="%2$s" />', $term_image_icon_url, $name );
	} elseif ( $icon_name = boombox_get_term_icon_name( $id, $tax ) ) {
		$html = sprintf( '<i class="bb-icon bb-icon-%1$s"></i>', $icon_name );
	}

	return $html;
}

/**
 * Get single post reactions
 *
 * @param int $post_id Post ID
 *
 * @return array|null
 */
function boombox_get_post_reactions( $post_id ) {
	$reactions        = null;
	$is_module_active = boombox_module_management_service()->is_module_active( 'prs' );

	if ( $is_module_active && function_exists( 'boombox_get_reaction_taxonomy_name' ) ) {
		$reactions     = array();
		$reactions_ids = Boombox_Reaction_Helper::get_post_reactions( $post_id );
		if ( ! empty( $reactions_ids ) ) {
			$taxonomy = boombox_get_reaction_taxonomy_name();
			foreach ( $reactions_ids as $reaction_id ) {
				$reaction = get_term_by( 'term_id', $reaction_id, $taxonomy );
				if ( $reaction ) {
					$reactions[] = $reaction;
				}
			}
		}
	}

	return $reactions;
}

/**
 * Get list type classes
 *
 * @param string $list_type          Current listing type
 * @param array  $add_grid_class     Additional classes for grid type layouts
 * @param array  $additional_classes Additional classes for all listings types
 *
 * @return array|string
 */
function boombox_get_list_type_classes( $list_type, $add_grid_class = array(), $additional_classes = array() ) {
	$classes = array();

	switch ( $list_type ) {
		case 'grid':
		case 'grid-2-1':
			$classes = array_merge( array( 'post-grid', 'bb-thumbnail-stretched' ), (array) $add_grid_class );
			break;

		case 'four-column':
			$classes = array(
				'post-grid',
				'col-3',
				'bb-thumbnail-stretched',
			);
			break;

		case 'list':
			$classes = array(
				'post-list',
				'list',
				'list',
				'big-item',
				'bb-thumbnail-stretched',
			);
			break;

		case 'list2':
			$classes = array( 'post-list', 'list', 'small-item' );
			break;

		case 'classic':
		case 'classic2':
		case 'stream':
			$classes = array(
				'post-list',
				'standard',
				'bb-thumbnail-stretched',
				'bb-media-playable',
			);
			break;

		case 'masonry-boxed':
		case 'masonry-stretched':
			$classes = array(
				'masonry-grid',
				'bb-thumbnail-stretched',
				'bb-media-playable',
			);
			break;

		case 'mixed':
			$classes = array(
				'mixed-list',
				'bb-media-playable',
			);
			break;
	}

	$classes = apply_filters( 'boombox/list-type-classes', array_merge( $classes, $additional_classes ), $list_type );

	if ( $classes ) {
		array_unshift( $classes, 'hfeed' );
	}

	return esc_attr( implode( ' ', $classes ) );
}

/**
 * Render listing container classes by listing type
 *
 * @param string $list_type           Listing type
 * @param string $additional_position Additional position
 *
 * @return string
 */
function boombox_get_container_classes_by_type( $list_type, $additional_position = '' ) {
	$classes = '';
	switch ( $list_type ) {
		case 'stream':
			$classes = 'bb-stream';
			break;
		case 'masonry-stretched':
			$classes = 'bb-stretched-full';
			break;
	}

	return $classes;
}

/**
 * Get Term Custom Thumbnail URL
 *
 * @return string|null
 */
function boombox_get_term_thumbnail_url() {
	$src            = null;
	$queried_object = get_queried_object();
	if ( $queried_object ) {
		switch ( $queried_object->taxonomy ) {
			case 'category':
				$meta_key = 'cat_thumbnail_id';
				break;
			case 'post_tag':
				$meta_key = 'post_tag_thumbnail_id';
				break;
			default:
				$meta_key = '';
		}
		$thumbnail_id = absint( boombox_get_term_meta( $queried_object->term_id, $meta_key ) );
		if ( $thumbnail_id ) {
			$image = wp_get_attachment_image_src( $thumbnail_id, 'image1600' );
			if ( $image ) {
				$src = $image[0];
			}
		}
	}

	return $src;
}

/**
 * Generate compose button html
 *
 * @param array  $classes    CSS classes
 * @param string $label      Button label
 * @param bool   $icon       Whether to use the icon
 * @param bool   $static_url Constant URL for the button
 *
 * @return string
 * @since   1.0.0
 * @version 2.0.4
 */
function boombox_get_create_post_button( $classes = array(), $label = '', $icon = false, $static_url = false ) {

	$button = '';

	if ( boombox_get_theme_option( 'header_layout_button' ) ) {

		if ( $static_url ) {
			$args = array(
				'url'     => apply_filters( 'boombox/create_post_button/static_url', $static_url ),
				'classes' => $classes,
			);
		} else {
			if ( is_user_logged_in() ) {
				$ssl = is_ssl() ? 'https' : 'http';
				$url = admin_url( 'post-new.php', $ssl );
			} else {
				$url       = esc_url( '#sign-in' );
				$classes[] = 'js-authentication';
			}

			$args = apply_filters( 'boombox/create-post-button-args', array(
				'url'     => $url,
				'classes' => $classes,
			) );

			$args['url']     = isset( $args['url'] ) ? $args['url'] : $url;
			$args['classes'] = isset( $args['classes'] ) ? (array) $args['classes'] : array();
		}

		if ( $icon ) {
			array_unshift( $args['classes'], 'h-icon' );
		}

		$button = sprintf( '<a class="%1$s" href="%2$s">%3$s <span>%4$s</span></a>', esc_attr( implode( ' ', $args['classes'] ) ), esc_url( $args['url'] ), $icon ? '<span class="bb-icon bb-ui-icon-plus"></span>' : '', $label ? $label : esc_html__( 'Create a post', 'boombox' ) );

	}

	return $button;
}

/**
 * Return Log Out Button
 *
 * @return string
 */
function boombox_get_logout_button() {
	if ( is_user_logged_in() ) {
		$url     = wp_logout_url( home_url() );
		$title   = esc_attr__( 'Log Out', 'boombox' );
		$classes = esc_attr( implode( ' ', array(
			'user',
			'icon-sign-out',
		) ) );

		return sprintf( '<a class="%1$s" href="%2$s" title="%3$s"></a>', $classes, $url, $title );
	}

	return '';
}

/**
 * Return point classes
 *
 * @param $post_id
 *
 * @return array
 */
function boombox_get_point_classes( $post_id ) {
	$classes = array(
		'up'   => '',
		'down' => '',
	);

	if ( boombox_module_management_service()->is_module_active( 'prs' ) ) {
		if ( Boombox_Point_Count_Helper::pointed_up( $post_id ) ) {
			$classes['up'] = ' active';
		} else {
			if ( Boombox_Point_Count_Helper::pointed_down( $post_id ) ) {
				$classes['down'] = ' active';
			}
		}
	}

	return $classes;
}

/**
 * Return post point count
 *
 * @param $post_id
 *
 * @return int
 */
function boombox_get_post_point_count( $post_id ) {
	$points_count = 0;
	if ( boombox_module_management_service()->is_module_active( 'prs' ) ) {
		$points_count = Boombox_Point_Count_Helper::get_post_points( $post_id );
	}

	return $points_count;
}

/**
 * Return views count
 *
 * @param $post_id
 *
 * @return int
 */
function boombox_get_views_count( $post_id ) {
	$views_count = 0;
	if ( boombox_module_management_service()->is_module_active( 'prs' ) ) {
		$views_count = Boombox_View_Count_Helper::get_post_views( $post_id );
	}

	return $views_count;
}

/**
 * Show advertisement
 *
 * @param string $location Advertisement location
 * @param array  $args     Advertisement arguments
 *
 * @since   1.0.0
 * @version 2.0.4
 */
function boombox_the_advertisement( $location, $args = array() ) {
	if ( ! boombox_plugin_management_service()->is_plugin_active( 'quick-adsense-reloaded/quick-adsense-reloaded.php' ) ) {
		return;
	}

	echo Boombox_WP_Quads::get_instance()->get_adv( $location, $args );
}

/**
 * Return advertisement settings
 *
 * @param string $listing_type Current listing type
 *
 * @return array
 */
function boombox_get_adv_settings( $listing_type ) {
	if ( in_array( $listing_type, array(
		'grid',
		'four-column',
	) ) ) {
		$location = 'boombox-listing-type-grid-instead-post';
		$class    = 'bb-instead-gr-lst-post';
	} else {
		$location = 'boombox-listing-type-non-grid-instead-post';
		$class    = 'large bb-instead-none-gr-lst-post';
	}

	return array(
		'location' => $location,
		'class'    => $class,
	);
}

/**
 * Check if advertisement inject is enabled
 *
 * @param string $status Current status
 *
 * @return bool
 */
function boombox_is_adv_enabled( $status ) {
	return ( boombox_plugin_management_service()->is_plugin_active( 'quick-adsense-reloaded/quick-adsense-reloaded.php' ) && 'inject_into_posts_list' == $status );
}

/**
 * Check if newsletter inject is enabled
 *
 * @param string $status Current status
 *
 * @return bool
 */
function boombox_is_newsletter_enabled( $status ) {
	return ( boombox_plugin_management_service()->is_plugin_active( 'mailchimp-for-wp/mailchimp-for-wp.php' ) && 'inject_into_posts_list' == $status );
}

/**
 * Check if product inject is enabled
 *
 * @param string $status Current status
 *
 * @return bool
 */
function boombox_is_product_enabled( $status ) {
	return ( boombox_plugin_management_service()->is_plugin_active( 'woocommerce/woocommerce.php' ) && 'inject_into_posts_list' == $status );
}

/**
 * Check whether post has thumbnail
 *
 * @param int|WP_Post $post Optional post
 *
 * @return bool
 * @since   1.0.0
 * @version 2.1.2
 */
function boombox_has_post_thumbnail( $post = null ) {
	return (bool) apply_filters( 'boombox/post/has-thumbnail', has_post_thumbnail( $post ), $post );
}

/**
 * Get next attachment post for attachment template
 *
 * @return array|bool|mixed|null|string|WP_Post
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_get_next_attachment_post() {
	global $wpdb;
	$post = get_post();

	$sql = $wpdb->prepare( 'SELECT
              p.`ID`
            FROM
              `' . $wpdb->posts . '` AS p
              LEFT JOIN `' . $wpdb->posts . '` AS `p1`
                ON `p1`.`ID` = p.`post_parent`
            WHERE p.`post_type` = %s
              AND p.post_date > %s
              AND ( p1.`post_status` IS NULL OR p1.`post_status` = %s', 'attachment', $post->post_date, 'publish' );

	if ( is_user_logged_in() ) {
		$user_id = get_current_user_id();

		$post_type_object = get_post_type_object( $post->post_type );
		if ( empty( $post_type_object ) ) {
			$post_type_cap    = $post->post_type;
			$read_private_cap = 'read_private_' . $post_type_cap . 's';
		} else {
			$read_private_cap = $post_type_object->cap->read_private_posts;
		}

		/*
		 * Results should include private posts belonging to the current user, or private posts where the
		 * current user has the 'read_private_posts' cap.
		 */
		$private_states = get_post_stati( array( 'private' => true ) );
		foreach ( (array) $private_states as $state ) {
			if ( current_user_can( $read_private_cap ) ) {
				$sql .= $wpdb->prepare( " OR p1.post_status = %s", $state );
			} else {
				$sql .= $wpdb->prepare( " OR (p.post_author = %d AND p1.post_status = %s)", $user_id, $state );
			}
		}
	}

	$sql .= ") ORDER BY p.post_date ASC LIMIT 1";

	$query_key = 'adjacent_post_' . md5( $sql );
	$result    = wp_cache_get( $query_key, 'counts' );
	if ( false !== $result ) {
		if ( $result ) {
			$result = get_post( $result );
		}

		return $result;
	}

	$result = $wpdb->get_var( $sql );
	if ( null === $result ) {
		$result = '';
	}

	wp_cache_set( $query_key, $result, 'counts' );

	if ( $result ) {
		$result = get_post( $result );

	}

	return $result;

}

/**
 * Get previous attachment post for attachment template
 *
 * @return array|bool|mixed|null|string|WP_Post
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_get_previous_attachment_post() {
	global $wpdb;
	$post = get_post();

	$sql = $wpdb->prepare( 'SELECT
              p.`ID`
            FROM
              `' . $wpdb->posts . '` AS p
              LEFT JOIN `' . $wpdb->posts . '` AS `p1`
                ON `p1`.`ID` = p.`post_parent`
            WHERE p.`post_type` = %s
              AND p.post_date < %s
              AND ( p1.`post_status` IS NULL OR p1.`post_status` = %s', 'attachment', $post->post_date, 'publish' );

	if ( is_user_logged_in() ) {
		$user_id = get_current_user_id();

		$post_type_object = get_post_type_object( $post->post_type );
		if ( empty( $post_type_object ) ) {
			$post_type_cap    = $post->post_type;
			$read_private_cap = 'read_private_' . $post_type_cap . 's';
		} else {
			$read_private_cap = $post_type_object->cap->read_private_posts;
		}

		/*
		 * Results should include private posts belonging to the current user, or private posts where the
		 * current user has the 'read_private_posts' cap.
		 */
		$private_states = get_post_stati( array( 'private' => true ) );
		foreach ( (array) $private_states as $state ) {
			if ( current_user_can( $read_private_cap ) ) {
				$sql .= $wpdb->prepare( " OR p1.post_status = %s", $state );
			} else {
				$sql .= $wpdb->prepare( " OR (p.post_author = %d AND p1.post_status = %s)", $user_id, $state );
			}
		}
	}

	$sql .= ") ORDER BY p.post_date DESC LIMIT 1";

	$query_key = 'adjacent_post_' . md5( $sql );
	$result    = wp_cache_get( $query_key, 'counts' );
	if ( false !== $result ) {
		if ( $result ) {
			$result = get_post( $result );
		}

		return $result;
	}

	$result = $wpdb->get_var( $sql );
	if ( null === $result ) {
		$result = '';
	}

	wp_cache_set( $query_key, $result, 'counts' );

	if ( $result ) {
		$result = get_post( $result );
	}

	return $result;
}

/**
 * Render post categories list
 *
 * @since   2.0.5
 * @version 2.0.5
 * @return string
 *
 * @param array $args Arguments {
 *
 * @type bool category Show categories
 * @type bool post_tag Show post tags
 * @type string before Content before list
 * @type string after Content after list
 * @type string class Css classes for wrapper
 * }
 */
function boombox_terms_list_html( $args = array() ) {

	$args = wp_parse_args( $args, array(
		'post_id'         => false,
		'category'        => false,
		'before_category' => '',
		'after_category'  => '',
		'post_tag'        => false,
		'before_post_tag' => false,
		'after_post_tag'  => false,
		'wrapper'         => 'div',
		'class'           => 'bb-cat-links',
		'before'          => '',
		'after'           => '',
		'microdata'       => false
	) );
	$r    = apply_filters( 'boombox/post/terms-list', $args );

	$list = '';
	if ( $r['category'] ) {
		$categories_list = get_the_category_list( '<span class="mf-hide">, </span>', '', $r['post_id'] );
		if ( $categories_list ) {
			$list .= $r['before_category'] . $categories_list . $r['after_category'];
		}
	}

	if ( $r['post_tag'] ) {
		$post_tags_list = get_the_tag_list( '', '<span class="mf-hide">, </span>', '', $r['post_id'] );
		if ( $post_tags_list ) {
			$list .= $r['before_post_tag'] . $post_tags_list . $r['after_post_tag'];
		}
	}

	/***** Do nothing with empty lists */
	if ( ! $list ) {
		return;
	}

	return sprintf(
		'%1$s<%2$s%3$s%4$s>%5$s</%2$s>%6$s',
		$r['before'],
		$r['wrapper'],
		( $r['class'] ? ' class="' . esc_attr( $r['class'] ) . '"' : '' ),
		( $r['microdata'] ? ' itemprop="keywords"' : '' ),
		$list,
		$r['after']
	);
}

/**
 * Get HTML subtitle for current post.
 *
 * @param array $args Configuration
 *
 * @return string
 * @since   2.0.0
 * @version 2.5.0
 */
function boombox_get_post_subtitle( $args = array() ) {
	$r = wp_parse_args( $args, array(
		'post'              => null,
		'before'            => '',
		'after'             => '',
		'classes'           => 'entry-summary entry-sub-title',
		'microdata'         => false,
		'wrapper'           => 'p',
		'subtitle'          => true,
		'reading_time'      => false,
		'reading_time_size' => ''
	) );

	$post = get_post( $r['post'] );

	$html = '';
	if ( 'post' == $post->post_type ) {

		$subtitle = '';
		if ( $r['subtitle'] ) {
			$excerpt_length = apply_filters( 'excerpt_length', 25 );
			$excerpt_more   = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );

			if ( is_single() ) {
				$subtitle = $post->post_excerpt;
			} else {
				$subtitle = wp_trim_words( mb_substr( get_the_excerpt( $post ), 0 ), $excerpt_length, $excerpt_more );
			}

			$subtitle = wp_kses_post( apply_filters( 'boombox/the_post_subtitle', $subtitle ) );
		}

		$reading_time = '';
		if ( $r['reading_time'] ) {
			$reading_time_classes = $r['reading_time_size'];
			if ( $subtitle ) {
				$reading_time_classes .= ' with-content';
			}
			$reading_time = boombox_get_post_reading_time( array(
				'post'  => $post,
				'class' => $reading_time_classes
			) );
		}

		if ( boombox_is_amp() ) {
			$html = $subtitle ? sprintf( '<h2 class="post-summary m-b-sm-1">%s</h2>', $subtitle ) : '';
		} elseif ( $subtitle || $reading_time ) {
			$microdata = '';
			if ( $subtitle && $r['microdata'] ) {
				$microdata = ' itemprop="description"';
			}

			$html = sprintf( '%1$s<%2$s class="%3$s"%4$s>%5$s</%2$s>%6$s', $r['before'], $r['wrapper'], esc_attr( trim( $r['classes'] ) ), $microdata, $subtitle . $reading_time, $r['after'] );
		}
	}

	return $html;
}

/**
 * Get single post date
 *
 * @param string      $type Type: published or modified
 * @param int|WP_Post $post WP_Post object or ID. Default is global `$post` object.
 *
 * @return string
 */
function booombox_get_single_post_date( $type, $post = false ) {
	if ( ! $post ) {
		$post = get_post();
	}

	$display_format = apply_filters( 'boombox/post_date_render_format', 'difference' );
	$date           = '';

	if ( 'difference' == $display_format ) {
		switch ( $type ) {
			case 'modified':
				$difference = human_time_diff( get_the_modified_time( 'U', $post ), current_time( 'timestamp' ) );
				break;
			case 'published':
				$difference = human_time_diff( get_the_time( 'U', $post ), current_time( 'timestamp' ) );
				break;
			default:
				$difference = false;
		}
		if ( $difference ) {
			$postfix = apply_filters( 'boombox/time_difference_postfix', ( " " . esc_html__( 'ago', 'boombox' ) ) );
			$date    = $difference . $postfix;
			$date = apply_filters( 'boombox_post_date', $date, $difference, $postfix, $type );
		}
	} else {
		$format = get_option( 'date_format' );
		switch ( $type ) {
			case 'modified':
				$date = date_i18n( $format, get_the_modified_time( 'U', $post ) );
				break;
			case 'published':
				$date = date_i18n( $format, get_the_time( 'U', $post ) );
				break;
		}
	}

	return $date;
}

/**
 * Get HTML with date information for current post.
 *
 * @param bool $show Whether to show or hide
 *
 * @return string
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_get_post_date( $args = array() ) {

	$args = wp_parse_args( $args, array(
		'display'   => true,
		'microdata' => false,
		'published' => array(
			'display' => true,
			'classes' => ''
		),
		'modified'  => array(
			'display' => true,
			'classes' => 'mf-hide'
		)
	) );
	$r    = apply_filters( 'boombox/post_date_args', $args );

	$date_html = '';
	if ( $r['published']['display'] ) {
		$classes = 'entry-date published ';
		if ( $r['published']['classes'] ) {
			$classes .= $r['published']['classes'];
		}
		$datetime  = esc_attr( get_the_date( 'c' ) );
		$microdata = $r['microdata'] ? ' itemprop="datePublished"' : '';
		$date      = booombox_get_single_post_date( 'published' );

		$date_html .= sprintf( '<time class="%s" datetime="%s"%s>%s</time>', $classes, $datetime, $microdata, $date );
	}


	if ( $r['modified']['display'] ) {
		$classes = 'entry-date updated ';
		if ( $r['modified']['classes'] ) {
			$classes .= $r['modified']['classes'];
		}
		$datetime  = esc_attr( get_the_modified_date( 'c' ) );
		$microdata = $r['microdata'] ? ' itemprop="dateModified"' : '';
		$date      = booombox_get_single_post_date( 'modified' );

		$date_html .= sprintf( '<time class="%s" datetime="%s"%s>%s</time>', rtrim( $classes ), $datetime, $microdata, $date );
	}

	$classes = 'auth-posted-on';
	if ( ! $r['display'] ) {
		$classes .= ' mf-hide';
	}

	return sprintf( '<span class="%s">%s</span>', esc_attr( $classes ), $date_html );

}

/**
 * Render HTML with date information for current post.
 *
 * @param bool $show Whether to show or hide
 *
 * @return string
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_render_post_date( $args = array() ) {
	echo boombox_get_post_date( $args );
}

/**
 * Generate user mini card
 *
 * @param array $args Card configuration
 *
 * @return string
 * @since   2.5.0
 * @version 2.5.0
 */
function boombox_generate_user_mini_card( $args = array() ) {

	$args = wp_parse_args( $args, array(
		'class'     => '',
		'author'    => false,
		'avatar'    => false,
		'date'      => false,
		'user_id'   => false,
		'microdata' => false,
		'before'    => '',
		'after'     => ''
	) );
	$r    = apply_filters( 'boombox/user_mini_card_options', $args );

	$user_id      = $r['user_id'] ? $r['user_id'] : get_the_author_meta( 'ID' );
	$display_name = wp_kses_post( get_the_author_meta( 'display_name', $user_id ) );
	$user_url     = esc_url( apply_filters( 'boombox/user_mini_card/author_url', get_author_posts_url( $user_id ) ) );

	// region Avatar
	$avatar = '';
	if ( $r['avatar'] || $r['microdata'] ) {
		$size   = apply_filters( 'boombox_author_avatar_size', 74 );
		$image  = get_avatar( $user_id, $size, '', $display_name, array(
			'extra_attr' => ( $r['microdata'] ? 'itemprop="image"' : '' ),
		) );
		$avatar = sprintf( '<div class="author-avatar%s"><a href="%s">%s</a></div>', ( ! $r['author'] && $r['microdata'] ? ' mf-hide' : '' ), $user_url, $image );
	}
	// endregion

	// region Author
	$author = '';
	if ( $r['author'] || $r['microdata'] ) {
		$author = sprintf( '
		<span class="auth-name%s">
			<span class="auth-byline">%s</span>
			<a class="auth-url" href="%s"%s>
				<span%s>%s</span>
			</a>
		</span>',
			( ! $r['author'] && $r['microdata'] ? ' mf-hide' : '' ),
			apply_filters( 'boombox/author/posted-by', esc_html__( 'by', 'boombox' ), 'author_meta' ),
			$user_url,
			$r['microdata'] ? ' itemprop="url"' : '',
			$r['microdata'] ? ' itemprop="name"' : '',
			$display_name
		);
	}
	// endregion

	// region Date
	$date = '';
	if ( $r['date'] ) {
		$date = boombox_get_post_date( array(
			'display' => $r['date'],
		) );
	}
	// endregion

	// region HTML
	$html = '';
	if ( $avatar || $author || $date ) {
		$class = 'bb-author-vcard-mini ';

		if ( $r['class'] ) {
			$class .= $r['class'];
		}

		$additional_title = true ? '' : '<span class="auth-title">Title for her</span>';
		$author_info_html = apply_filters( 'boombox/author_mini_card/author_content', ( $author . $date . $additional_title ) );
		$microdata        = $r['microdata'] ? ' itemprop="author" itemscope="" itemtype="http://schema.org/Person"' : '';

		$html = $r['before'] . '<div class="' . esc_attr( trim( $class ) ) . '"' . $microdata . '>' . $avatar . '<div class="author-info">' . $author_info_html . '</div></div>' . $r['after'];
	}

	// endregion

	return $html;

}

/**
 * Render author expanded information block
 *
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_get_post_author_card( $args = array() ) {

	if ( is_singular() || is_author() ) {

		$r = wp_parse_args( $args, array(
			'avatar_size'            => 186,
			'user_id'                => get_the_author_meta( 'ID' ),
			'class'                  => '',
			'extended_data_location' => 'bottom'
		) );
		$r = apply_filters( 'boombox/post_author_author_card_args', $r );

		$author_name   = wp_kses_post( get_the_author_meta( 'display_name', $r['user_id'] ) );
		$author_url    = esc_url( get_author_posts_url( $r['user_id'] ) );
		$author_avatar = get_avatar( $r['user_id'], $r['avatar_size'], '', $author_name, array(
			'extra_attr' => 'itemprop="image"',
			'type'       => 'full',
		) );
		$bio           = apply_filters( 'boombox/author_bio', wp_kses_post( get_the_author_meta( 'description' ) ), $r['user_id'] );

		$class = 'bb-author-vcard';
		if ( $r['class'] ) {
			$class .= ' ' . $r['class'];
		}
		if ( ! $bio ) {
			$class .= ' no-author-info';
		}

		$extended_data = apply_filters( 'author_extended_data', '', $r['user_id'] );
		$html          = sprintf(
			'<section class="%7$s">
				<div class="author" itemscope="" itemtype="http://schema.org/Person">
					<header>
						<div class="avatar auth-avatar circle-frame">
							<a href="%1$s">%2$s</a>
						</div>
						
						<div class="header-info">
							<h3 class="auth-name">
								<span class="auth-byline">%4$s</span>
								<a class="auth-url" itemprop="url" href="%1$s">
									<span itemprop="name">%3$s</span>
								</a>
								%8$s
							</h3>
							%9$s
						</div>
					</header>
			
					<div class="author-info">
						<div class="auth-desc" itemprop="description">%6$s</div>
						%5$s
					</div>
				</div>
			</section>',
			/* 1 */
			$author_url,
			/* 2 */
			$author_avatar,
			/* 3 */
			$author_name,
			/* 4 */
			apply_filters( 'boombox/author/posted-by', esc_html__( 'Posted by', 'boombox' ), 'expanded' ),
			/* 5 */
			( $r['extended_data_location'] == 'bottom' ) ? $extended_data : '',
			/* 6 */
			$bio,
			/* 7 */
			$class,
			/* 8 */
			apply_filters( 'boombox/author/name_row', '', $r['user_id'] ),
			/* 9 */
			( $r['extended_data_location'] == 'top' ) ? $extended_data : ''
		);

		return $html;
	}
}

/**
 * Check if thumbnail should be shown for multipaged post
 *
 * @return bool
 *
 * @since   2.5.0
 * @version 2.5.0
 */
function boombox_show_multipage_thumbnail() {
	global $page, $multipage;

	return $multipage ? ( $page == 1 ) : true;
}

if ( ! function_exists( 'boombox_single_post_link_pages' ) ) {

	/**
	 * Prints HTML for single post link pages navigation
	 *
	 * @since   1.0.0
	 * @version 2.0.0
	 */
	function boombox_single_post_link_pages( $args = array() ) {

		global $page, $numpages, $multipage, $more;

		$defaults        = apply_filters( 'boombox/single/link-pages-args', array(
			'before'             => '<p>' . __( 'Pages:', 'boombox' ),
			'after'              => '</p>',
			'link_before'        => '',
			'link_after'         => '',
			'link_class'         => '',
			'paging'             => '',
			'reverse'            => 0,
			'next'               => 1,
			'next_class'         => array( 'next-page' ),
			'prev'               => 1,
			'prev_class'         => array( 'prev-page' ),
			'in_same_term'       => false,
			'excluded_terms'     => '',
			'taxonomy'           => 'category',
			'url_on_end'         => false,
			'prev_text_on_end'   => '',
			'next_text_on_end'   => '',
			'previous_page_link' => __( 'Previous page', 'boombox' ),
			// paginated prev page
			'next_page_link'     => __( 'Next page', 'boombox' ),
			// paginated next page
			'previous_post_link' => __( 'Previous post', 'boombox' ),
			// prev page
			'next_post_link'     => __( 'Next post', 'boombox' ),
			// next page
			'go_to_prev_next'    => 1,
			'pagelink'           => '%',
			'link_wrap_before'   => '',
			'link_wrap_after'    => '',
			'echo'               => 1,
		) );
		$r               = wp_parse_args( $args, $defaults );
		$r['link_class'] = $r['link_class'] ? sprintf( 'class="%s"', ( is_array( $r['link_class'] ) ? implode( ' ', $r['link_class'] ) : $r['link_class'] ) ) : '';
		$r['next_class'] = is_array( $r['next_class'] ) ? $r['next_class'] : explode( ' ', preg_replace( '/\s\s+/', ' ', $r['next_class'] ) );
		$r['prev_class'] = is_array( $r['prev_class'] ) ? $r['prev_class'] : explode( ' ', preg_replace( '/\s\s+/', ' ', $r['prev_class'] ) );

		$has_page_pagination = false;
		$is_amp              = boombox_is_amp();
		$prev_output         = $next_output = '';
		$render_prev_output  = $render_next_output = true;

		$next_post_function     = 'get_next_post';
		$previous_post_function = 'get_previous_post';
		if ( is_attachment() ) {
			$next_post_function     = 'boombox_get_next_attachment_post';
			$previous_post_function = 'boombox_get_previous_attachment_post';
		}

		if ( $multipage && $more ) {

			// previous page
			if ( $r['prev'] ) {

				$prev       = $page - 1;
				$link_class = sprintf( $r['link_class'], 'prev' );

				// paginated single post
				if ( $prev > 0 ) {

					$link                = str_replace( 'href="', sprintf( '%s href="', $link_class ), _wp_link_page( $prev ) ) . $r['link_before'] . $r['previous_page_link'] . $r['link_after'] . '</a>';
					$prev_output         = apply_filters( 'wp_link_pages_link', $link, $prev );
					$has_page_pagination = true;

					// not paginated post ( go to next/prev post )
				} else {
					if ( $r['go_to_prev_next'] && $boombox_post = ( $r['reverse'] ? $next_post_function( $r['in_same_term'], $r['excluded_terms'], $r['taxonomy'] ) : $previous_post_function( $r['in_same_term'], $r['excluded_terms'], $r['taxonomy'] ) ) ) {

						$permalink = $is_amp ? amp_get_permalink( $boombox_post->ID ) : get_permalink( $boombox_post->ID );

						$prev_output = sprintf( '<a href="%1$s" %2$s rel="prev">', esc_url( $permalink ), $link_class ) . $r['link_before'] . $r['previous_post_link'] . $r['link_after'] . '</a>';

						// no single post pagination and no next/prev post
					} else {

						if ( $r['url_on_end'] ) {
							$text = $r['prev_text_on_end'] ? $r['prev_text_on_end'] : $r['previous_post_link'];
							$url  = $r['url_on_end'];
							$type = 'url_on_end';
						} else {
							$text              = $r['previous_post_link'];
							$url               = '#';
							$type              = 'empty';
							$r['prev_class'][] = $r['go_to_prev_next'] ? 'bb-disabled' : 'p-hidden';
							if ( $is_amp ) {
								$render_prev_output = false;
							}
						}

						$url         = apply_filters( 'boombox/prev_post_empty_url', $url, $type, $is_amp );
						$prev_output = sprintf( '<a href="%1$s" %2$s rel="prev">', esc_url( $url ), $link_class ) . $r['link_before'] . $text . $r['link_after'] . '</a>';

					}
				}

			}

			// next page
			if ( $r['next'] ) {

				$next       = $page + 1;
				$link_class = sprintf( $r['link_class'], 'next' );

				// paginated single post
				if ( $next <= $numpages ) {

					$link                = str_replace( 'href="', sprintf( '%s href="', $link_class ), _wp_link_page( $next ) ) . $r['link_before'] . $r['next_page_link'] . $r['link_after'] . '</a>';
					$next_output         = apply_filters( 'wp_link_pages_link', $link, $next );
					$has_page_pagination = true;

					// not paginated post ( go to next/prev post )
				} else {
					if ( $r['go_to_prev_next'] && $boombox_post = ( $r['reverse'] ? $previous_post_function( $r['in_same_term'], $r['excluded_terms'], $r['taxonomy'] ) : $next_post_function( $r['in_same_term'], $r['excluded_terms'], $r['taxonomy'] ) ) ) {

						$permalink = $is_amp ? amp_get_permalink( $boombox_post->ID ) : get_permalink( $boombox_post->ID );

						$next_output = sprintf( '<a href="%1$s" %2$s rel="next">', esc_url( $permalink ), $link_class ) . $r['link_before'] . $r['next_post_link'] . $r['link_after'] . '</a>';

						// no single post pagination and no next/prev post
					} else {
						if ( $r['url_on_end'] ) {
							$text = $r['next_text_on_end'] ? $r['next_text_on_end'] : $r['next_post_link'];
							$url  = $r['url_on_end'];
							$type = 'url_on_end';
						} else {
							$text              = $r['next_post_link'];
							$url               = '#';
							$type              = 'empty';
							$r['next_class'][] = $r['go_to_prev_next'] ? 'bb-disabled' : 'p-hidden';
							if ( $is_amp ) {
								$render_next_output = false;
							}
						}

						$url         = apply_filters( 'boombox/next_post_empty_url', $url, $type, $is_amp );
						$next_output = sprintf( '<a href="%1$s" %2$s rel="next">', esc_url( $url ), $link_class ) . $r['link_before'] . $text . $r['link_after'] . '</a>';

					}
				}
			}

		}

		if ( ! $prev_output && $r['prev'] ) {

			$link_class = sprintf( $r['link_class'], 'prev' );

			if ( $r['go_to_prev_next'] && $boombox_post = ( $r['reverse'] ? $next_post_function( $r['in_same_term'], $r['excluded_terms'], $r['taxonomy'] ) : $previous_post_function( $r['in_same_term'], $r['excluded_terms'], $r['taxonomy'] ) ) ) {
				$permalink   = $is_amp ? amp_get_permalink( $boombox_post->ID ) : get_permalink( $boombox_post->ID );
				$prev_output = sprintf( '<a href="%1$s" %2$s rel="prev">', esc_url( $permalink ), $link_class ) . $r['link_before'] . $r['previous_post_link'] . $r['link_after'] . '</a>';
			} else {

				if ( $r['url_on_end'] ) {
					$text = $r['prev_text_on_end'] ? $r['prev_text_on_end'] : $r['previous_post_link'];
					$url  = $r['url_on_end'];
					$type = 'url_on_end';
				} else {
					$text              = $r['previous_post_link'];
					$url               = '#';
					$type              = 'empty';
					$r['prev_class'][] = $r['go_to_prev_next'] ? 'bb-disabled' : 'p-hidden';
					if ( $is_amp ) {
						$render_prev_output = false;
					}
				}

				$url         = apply_filters( 'boombox/prev_post_empty_url', $url, $type, $is_amp );
				$prev_output = sprintf( '<a href="%1$s" %2$s rel="prev">', esc_url( $url ), $link_class ) . $r['link_before'] . $text . $r['link_after'] . '</a>';
			}
		}

		if ( ! $next_output && $r['next'] ) {

			$link_class = sprintf( $r['link_class'], 'next' );

			if ( $r['go_to_prev_next'] && $boombox_post = ( $r['reverse'] ? $previous_post_function( $r['in_same_term'], $r['excluded_terms'], $r['taxonomy'] ) : $next_post_function( $r['in_same_term'], $r['excluded_terms'], $r['taxonomy'] ) ) ) {
				$permalink   = $is_amp ? amp_get_permalink( $boombox_post->ID ) : get_permalink( $boombox_post->ID );
				$next_output = sprintf( '<a href="%1$s" %2$s rel="next">', esc_url( $permalink ), $link_class ) . $r['link_before'] . $r['next_post_link'] . $r['link_after'] . '</a>';
			} else {
				if ( $r['url_on_end'] ) {
					$text = $r['next_text_on_end'] ? $r['next_text_on_end'] : $r['next_post_link'];
					$url  = $r['url_on_end'];
					$type = 'url_on_end';
				} else {
					$text              = $r['next_post_link'];
					$url               = '#';
					$type              = 'empty';
					$r['next_class'][] = $r['go_to_prev_next'] ? 'bb-disabled' : 'p-hidden';
					if ( $is_amp ) {
						$render_next_output = false;
					}
				}

				$url         = apply_filters( 'boombox/next_post_empty_url', $url, $type, $is_amp );
				$next_output = sprintf( '<a href="%1$s" %2$s rel="next">', esc_url( $url ), $link_class ) . $r['link_before'] . $text . $r['link_after'] . '</a>';
			}
		}

		$r['paging'] = ( $numpages > 1 ) ? $r['paging'] : '';

		if ( $render_prev_output ) {
			$prev_output = sprintf( $r['link_wrap_before'], implode( ' ', $r['prev_class'] ) ) . $prev_output . $r['link_wrap_after'];
		} else {
			$prev_output = '';
		}
		if ( $render_next_output ) {
			$next_output = sprintf( $r['link_wrap_before'], implode( ' ', $r['next_class'] ) ) . $next_output . $r['link_wrap_after'];
		} else {
			$next_output = '';
		}

		if ( $has_page_pagination ) {
			$r['before'] = strtr( $r['before'], array( 'next-prev-pagination' => 'next-prev-pagination boombox-keep' ) );
		} else {
			if ( apply_filters( 'boombox/single/link_pages/hide_container', ! $r['go_to_prev_next'] ) ) {
				$r['before'] = strtr( $r['before'], array( 'next-prev-pagination' => 'next-prev-pagination hidden' ) );
			}
		}

		$output = '';
		if ( $render_prev_output || $render_next_output ) {
			$output = $r['before'] . $prev_output . $r['paging'] . $next_output . $r['after'];
		}

		if ( $r['echo'] ) {
			echo $output;
		} else {
			return $output;
		}

	}

}

/**
 *
 * Get share count HTML for post.
 *
 * @param array $args Configuration arguments
 *
 * @return string
 *
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_get_post_share_count( $args = array() ) {

	$r = wp_parse_args( $args, array(
		'post_id'  => get_the_ID(),
		'html'     => true,
		'location' => '',
		'before'   => '',
		'after'    => ''
	) );

	$count       = 0;
	$share_limit = 0;
	if ( function_exists( 'essb_core' ) ) {
		if ( 'share-box' !== $r['location'] ) {
			$count = boombox_post_shares_count( (int) boombox_get_post_meta( $r['post_id'], 'essb_c_total' ), $r['post_id'] );
		}
	} elseif ( boombox_plugin_management_service()->is_plugin_active( 'mashsharer/mashshare.php' ) && ( 'post' === get_post_type() ) ) {
		$count = roundshares( boombox_get_post_meta( $r['post_id'], 'mashsb_shares' ) + getFakecount() );
		global $mashsb_options;
		$share_limit = isset( $mashsb_options['hide_sharecount'] ) ? $mashsb_options['hide_sharecount'] : 0;
	}

	$count = apply_filters( 'boombox/post-share-count', $count );

	$return = '';
	if ( ! $count ) {
		return $return;
	}

	/***** Do nothing if shares count is tresholded */
	$share_limit = apply_filters( 'boombox/post-share-count-threshold', $share_limit );
	if ( $share_limit && ( $count < $share_limit ) ) {
		return $return;
	}

	$return = boombox_numerical_word( $count );
	if ( $r['html'] ) {
		$return = $r['before'] . '<span class="bb-icon bb-ui-icon-share"></span><span class="count">' . $count . '</span>' . $r['after'];
	}

	return $return;
}

/**
 * Get HTML with views and votes count for provided post
 *
 * @param array $args Configuration arguments
 *
 * @return string
 *
 * @since   2.5.0
 * @version 2.5.0
 */
function boombox_get_post_view_vote_count_html( $args = array() ) {

	$r = wp_parse_args( $args, array(
		'post_id' => get_the_ID(),
		'views'   => true,
		'votes'   => true,
		'before'  => '',
		'after'   => ''
	) );

	$html = '';
	if ( $r['views'] ) {
		$html .= boombox_get_post_view_count_html( $r['post_id'], array(
			'before' => '<span class="post-meta-item post-views">',
			'after'  => '</span>'
		) );
	}

	if ( $r['votes'] ) {
		$html .= boombox_get_post_vote_count_html( $r['post_id'], array(
			'before' => '<span class="post-meta-item post-votes">',
			'after'  => '</span>'
		) );
	}

	if ( $html ) {
		$html = $r['before'] . $html . $r['after'];
	}

	return $html;
}

/**
 * Get views count HTML for provided post
 *
 * @param int   $post_id Post ID
 * @param array $args    Additional arguments
 *
 * @return string
 *
 * @since   1.0.0
 * @version 2.5.0
 */
function boombox_get_post_view_count_html( $post_id, $args = array() ) {
	$view_count = boombox_get_views_count( $post_id );

	$html = '';
	if ( ! boombox_is_view_count_tresholded( $view_count ) ) {
		$r = wp_parse_args( $args, array(
			'before' => '',
			'after'  => ''
		) );

		if ( 'rounded' == boombox_get_theme_option( 'extras_post_ranking_system_views_count_style' ) ) {
			$view_count = boombox_numerical_word( $view_count );
		}
		$icon = 'bb-ui-icon-eye-1';
		if ( boombox_is_post_trending( 'trending', $post_id ) ) {
			$icon = 'bb-ui-icon-trending-eye';
		}
		$html = $r['before'] . '<span class="bb-icon ' . $icon . '"></span><span class="count">' . $view_count . '</span>' . $r['after'];
	}

	return $html;
}

/**
 * Get votes count HTML for provided post
 *
 * @param int   $post_id Post ID
 * @param array $args    Additional arguments
 *
 * @return string
 *
 * @since   1.0.0
 * @version 2.5.0
 */
function boombox_get_post_vote_count_html( $post_id, $args = array() ) {
	$r           = wp_parse_args( $args, array(
		'before' => '',
		'after'  => ''
	) );
	$votes_count = boombox_get_post_point_count( $post_id );

	return $r['before'] . '<span class="bb-icon bb-ui-icon-vote"></span><span class="count">' . $votes_count . '</span>' . $r['after'];
}

/**
 * Get post meta HTML
 *
 * @param array $args Arguments
 *
 * @return string
 */
function boombox_get_post_meta_html( $args = array() ) {
	/*Do nothing with Woocommerce products*/
	if ( get_post_type( get_the_ID() ) === 'product' ) {
		return '';
	}

	$r = wp_parse_args( $args, array(
		'post_id' => get_the_ID(),
		'order'   => array( 'views', 'votes', 'shares' ),

		'views'        => true,
		'views_before' => '<span class="post-meta-item post-views">',
		'views_after'  => '</span>',

		'votes'        => true,
		'votes_before' => '<span class="post-meta-item post-votes">',
		'votes_after'  => '</span>',

		'shares'        => true,
		'shares_before' => '<span class="post-meta-item post-shares">',
		'shares_after'  => '</span>',

		'before' => '',
		'after'  => ''
	) );

	$html = '';
	foreach ( (array) $r['order'] as $meta_item ) {
		if ( isset( $r[ $meta_item ] ) && $r[ $meta_item ] ) {
			switch ( $meta_item ) {
				case 'views':
					$html .= boombox_get_post_view_count_html( $r['post_id'], array(
						'before' => $r['views_before'],
						'after'  => $r['views_after']
					) );
					break;
				case 'votes':
					$html .= boombox_get_post_vote_count_html( $r['post_id'], array(
						'before' => $r['votes_before'],
						'after'  => $r['votes_after']
					) );
					break;
				case 'shares':
					$html .= boombox_get_post_share_count( array(
						'post_id' => $r['post_id'],
						'before'  => $r['shares_before'],
						'after'   => $r['shares_after']
					) );
					break;
			}
		}
	}

	return $html ? $r['before'] . $html . $r['after'] : '';

}

/**
 * Get share box heading
 *
 * @param array $args Configuration arguments
 *
 * @return string
 */
function boombox_get_share_box_heading( $args = array() ) {
	$r = wp_parse_args( $args, array(
		'before' => '<h2>',
		'after'  => '</h2>',
	) );

	$heading = apply_filters( 'boombox_share_box_text', esc_html__( 'Like it? Share with your friends!', 'boombox' ) );

	/***** Do nothing with empty heading */
	if ( ! $heading ) {
		return;
	}

	return $r['before'] . $heading . $r['after'];
}

/**
 * Render share buttons
 *
 * @see     Mashshare Plugin
 *
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_get_post_share_buttons_html() {

	$html = '';

	if ( function_exists( 'essb_core' ) ) {
		ob_start();
		Boombox_Essb::get_instance()->render();
		$html = ob_get_clean();

		if ( ! is_single() ) {
			$html = strtr( $html, array( 'href="#comments"' => sprintf( 'href="%s"', ( get_permalink() . '#comments' ) ) ) );
		}

	} elseif ( boombox_plugin_management_service()->is_plugin_active( 'mashsharer/mashshare.php' ) ) {
		$html = do_shortcode( '[mashshare shares="false"]' );
	}

	return $html;

}

/**
 * Get mobile share buttons HTML
 *
 * @param array $args Configuration arguments
 *
 * @since   2.5.0
 * @version 2.5.0
 * @return string
 */
function boombox_get_post_share_mobile_buttons_html( $args ) {

	$r = wp_parse_args( $args, array(
		'comments' => false,
		'shares'   => false,
		'points'   => false,
	) );

	$boombox_post_share_box_elements = array();
	if ( $r['comments'] ) {
		$comments_count = get_comments_number();
		if ( $comments_count ) {
			$boombox_post_share_box_elements[] = sprintf( '<span class="mobile-comments-count">%1$d</span> %2$s', $comments_count, _n( 'comment', 'comments', $comments_count, 'boombox' ) );
		}
	}
	if ( $r['shares'] ) {
		$share_count = boombox_get_post_share_count( array( 'html' => false ) );
		if ( $share_count ) {
			$boombox_post_share_box_elements[] = sprintf( '<span class="mobile-shares-count">%s</span> %2$s', $share_count, _n( 'share', 'shares', $share_count, 'boombox' ) );
		}
	}
	if ( $r['points'] ) {
		$points_count = boombox_get_post_point_count( get_the_ID() );
		if ( $points_count ) {
			$boombox_post_share_box_elements[] = sprintf( '<span class="mobile-votes-count">%1$d</span> %2$s', $points_count, _n( 'point', 'points', $points_count, 'boombox' ) );
		}
	}

	$html = '';
	if ( ! empty( $boombox_post_share_box_elements ) ) {
		$html = sprintf( '<div class="mobile-info">%s</div>', implode( ', ', $boombox_post_share_box_elements ) );
	}

	return $html;

}

/**
 * Get newsletter form HTML
 *
 * @param array $args Configuration arguments
 *
 * @return string
 *
 * @since   2.5.0
 * @version 2.5.0
 */
function boombox_get_mailchimp_form_html( $args = array() ) {
	if ( ! class_exists( 'Boombox_Mailchimp' ) ) {
		return '';
	}

	return Boombox_Mailchimp::get_instance()->get_form_html( $args );
}

/**
 * Render post points HTML
 *
 * @param $args array Configuration arguments
 *
 * @return string
 * @since   2.5.0
 * @version 2.5.0
 */
function boombox_get_post_points_html( $args = array() ) {

	$r = wp_parse_args( $args, array(
		'post_id' => get_the_ID(),
	) );

	$point_classes        = boombox_get_point_classes( $r['post_id'] );
	$points_login_require = boombox_get_theme_option( 'extras_post_ranking_system_points_login_require' );
	$container_class      = ' js-post-point';
	$authentication_url   = '';
	$tag                  = 'button';
	if ( $points_login_require && ! is_user_logged_in() ) {
		$point_classes['up']   .= ' js-authentication';
		$point_classes['down'] .= ' js-authentication';
		$authentication_url    = esc_url( '#sign-in' );
		$container_class       = '';
		$tag                   = 'a';
	}
	$authentication_href = ! empty( $authentication_url ) ? 'href="' . $authentication_url . '"' : '';
	$points_count        = boombox_get_post_point_count( $r['post_id'] );

	return sprintf( '<div class="bb-post-rating post-rating%1$s" data-post_id="%2$d">
					        <div class="inner">
					            <%3$s %4$s class="up point-btn%5$s" data-action="up">
					                <i class="bb-icon bb-ui-icon-arrow-up"></i>
					            </%3$s>
					
					            <%3$s %4$s class="down point-btn%6$s" data-action="down">
					                <i class="bb-icon bb-ui-icon-arrow-down"></i>
					            </%3$s>
					
					            <span class="count">
									<i class="bb-icon spinner-pulse"></i>
									<span class="text" label="%7$s">%8$s</span>
								</span>
					        </div>
					    </div>', esc_attr( $container_class ), $r['post_id'], $tag, $authentication_href, esc_attr( $point_classes['up'] ), esc_attr( $point_classes['down'] ), esc_html__( 'points', 'boombox' ), $points_count );
}

/**
 * Render post comments HTML
 *
 * @return string|null
 *
 * @since   2.5.0
 * @version 2.5.0
 */
function boombox_get_post_comments_count_html( $args = array() ) {

	/***** Do nothing with password protected post */
	if ( post_password_required() ) {
		return;
	}

	/***** Do nothing with posts with closed content */
	if ( ! comments_open() ) {
		return;
	}

	$r = wp_parse_args( $args, array(
		'class'   => '',
		'post_id' => get_the_ID(),
		'before'  => '',
		'after'   => ''
	) );

	$comments_count = absint( get_comments_number( absint( $r['post_id'] ) ) );
	$treshhold      = absint( apply_filters( 'boombox/comments_count_treshold', 1 ) );

	/***** Do nothing if post doesn't have enough comments */
	if ( $comments_count < $treshhold ) {
		return;
	}

	$html = sprintf(
		'<a href="%s" class="post-meta-item post-comments%s"><i class="bb-icon bb-ui-icon-comment"></i><span class="count">%s</span></a>',
		get_comments_link(),
		( $r['class'] ? ' ' . $r['class'] : '' ),
		$comments_count
	);

	return $r['before'] . $html . $r['after'];
}

/**
 * Render views HTML
 *
 * @param array $args Arguments
 *
 * @return string|null
 * @since   2.5.0
 * @version 2.5.0
 */
function boombox_get_post_views_count_html( $args = array() ) {

	$r           = wp_parse_args( $args, array(
		'class'   => '',
		'post_id' => get_the_ID(),
	) );
	$views_count = boombox_get_views_count( absint( $r['post_id'] ) );

	/***** Do nothing with threshold count */
	if ( boombox_is_view_count_tresholded( $views_count ) ) {
		return;
	}

	$view_count_style = boombox_get_theme_option( 'extras_post_ranking_system_views_count_style' );
	$views            = ( $view_count_style == 'rounded' ) ? boombox_numerical_word( $views_count ) : $views_count;
	$class            = 'post-meta-item post-views' . ( $r['class'] ? sprintf( ' %s', $r['class'] ) : '' );

	$icon = 'bb-ui-icon-eye';
	if ( boombox_is_post_trending( 'trending', $r['post_id'] ) ) {
		$icon = 'bb-ui-icon-trending-eye';
	}

	$icon = apply_filters( 'boombox/post_views_count_icon', $icon, $r['post_id'], $views_count );

	return sprintf( '<span class="%s"><span class="bb-icon ' . $icon . '"></span><span class="count">%s</span><span class="txt">%s</span></span>', $class, $views, _n( 'view', 'views', $views_count, 'boombox' ) );
}

if ( ! function_exists( 'boombox_remove_editor_article_classes' ) ) {

	/**
	 * Modify editor classes
	 *
	 * @param array $classes Current classes
	 * @param       $class
	 * @param int   $post_id Post ID
	 *
	 * @return array
	 *
	 * @since   1.0.0
	 * @version 2.0.0
	 */
	function boombox_remove_editor_article_classes( $classes, $class, $post_id ) {

		$index = array_search( 'hentry', $classes );
		if ( false !== $index ) {
			unset( $classes[ $index ] );
		}

		return $classes;
	}

}

if ( ! function_exists( 'boombox_render_affiliate_content' ) ) {

	/**
	 * Render affiliate post pricing content
	 *
	 * @param $args array Additional arguments
	 *
	 * @since   1.0.0
	 * @version 2.0.0
	 */
	function boombox_render_affiliate_content( $args = array() ) {

		$r = wp_parse_args( $args, array(
			'class'   => '',
			'post_id' => get_the_ID()
		) );

		$boombox_post_regular_price  = boombox_get_post_meta( $r['post_id'], 'boombox_post_regular_price' );
		$boombox_post_discount_price = boombox_get_post_meta( $r['post_id'], 'boombox_post_discount_price' );
		$boombox_post_affiliate_link = boombox_get_post_meta( $r['post_id'], 'boombox_post_affiliate_link' );

		$extra_additional_affiliate_button_text      = boombox_get_theme_option( 'extra_additional_affiliate_button_text' ); //get "Affiliate Button Text" from Customizer
		$boombox_affiliate_button_label              = $extra_additional_affiliate_button_text ? $extra_additional_affiliate_button_text : 'Check It Out';
		$boombox_post_affiliate_button_text_selector = boombox_get_post_meta( $r['post_id'], 'boombox_post_affiliate_button_text_selector' );

		if ( $boombox_post_affiliate_button_text_selector && 'custom' == $boombox_post_affiliate_button_text_selector ) {
			$boombox_affiliate_button_label = boombox_get_post_meta( $r['post_id'], 'boombox_post_affiliate_button_text' );
		}

		if ( $boombox_post_regular_price || $boombox_post_discount_price || $boombox_post_affiliate_link ) {

			$html          = '';
			$current_price = $old_price = false;
			if ( $boombox_post_regular_price && $boombox_post_discount_price ) {
				$current_price = $boombox_post_discount_price;
				$old_price     = $boombox_post_regular_price;
			} else {
				if ( $boombox_post_regular_price ) {
					$current_price = $boombox_post_regular_price;
				} else {
					if ( $boombox_post_discount_price ) {
						$current_price = $boombox_post_discount_price;
					}
				}
			}

			if ( boombox_is_amp() ) {

				if ( $old_price ) {
					$html .= sprintf( '<div class="old-price"><span class="txt old-price-txt">%s</span></div>', $old_price );
				}
				if ( $current_price ) {
					$html .= sprintf( '<div class="current-price"><span class="icon icon-tag m-r-sm"></span><span class="txt current-price-txt">%s</span></div>', $current_price );
				}
				if ( $html ) {
					$html = sprintf( '<div class="col-sec col-sec-1 vmiddle m-b-sm"><div class="bb-price-block">%s</div></div>', $html );
				}

				if ( $boombox_post_affiliate_link ) {
					$html .= sprintf( '<div class="col-sec col-sec-2 vmiddle m-b-sm"><a class="bb-btn btn-default btn-xs hvr-btm-shadow" rel="nofollow" href="%s">%s</a></div>', $boombox_post_affiliate_link, apply_filters( 'boombox_affiliate_button_label', esc_html__( $boombox_affiliate_button_label, 'boombox' ) ) );
				}

				if ( $html ) {
					printf( '<div class="bb-affiliate-content col-sec-wrapper m-b-sm">%s</div>', $html );
				}

			} else {
				if ( $old_price ) {
					$html .= sprintf( '<div class="old-price">%s</div>', $old_price );
				}
				if ( $current_price ) {
					$html .= sprintf( '<div class="current-price">%s</div>', $current_price );
				}
				if ( $html ) {
					$html = sprintf( '<div class="bb-price-block">%s</div>', $html );
				}

				if ( $boombox_post_affiliate_link ) {
					$html .= sprintf( '<a class="item-url" href="%s" target="_blank" rel="nofollow noopener">%s</a>', $boombox_post_affiliate_link, apply_filters( 'boombox_affiliate_button_label', esc_html__( $boombox_affiliate_button_label, 'boombox' ) ) );
				}

				if ( $html ) {
					$class = 'bb-affiliate-content';
					if ( $r['class'] ) {
						$class .= ' ' . $r['class'];
					}
					printf( '<div class="%s">%s</div>', $class, $html );
				}
			}
		}
	}

}
add_action( 'boombox_affiliate_content', 'boombox_render_affiliate_content' );
add_action( 'boombox_amp_affiliate_content', 'boombox_render_affiliate_content' );

/**
 * Check for HTMl containing gif or mp4 video
 *
 * @param string $html HTML to check
 *
 * @return bool
 *
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_html_contains_gif_image( $html ) {

	preg_match( '/src="(?P<image>.*\.(gif|mp4))"/i', $html, $has_gif );

	return (bool) $has_gif;

}

/**
 * Check for HTMl containing embed or mp4 video
 *
 * @param string $html HTML to check
 *
 * @return bool
 *
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_html_contains_embed_or_static_video( $html ) {
	preg_match( '/class=("|"([^"]*)\s)(boombox-featured-embed|boombox-featured-video)("|\s([^"]*)")/', $html, $has_video );

	return (bool) $has_video;
}

/**
 * Wrap embed within responsive container
 *
 * @param string $html    Embed HTMl
 * @param string $classes Additional CSS classes for wrapper
 *
 * @return string
 * @since   1.0.0
 * @verison 2.0.0
 */
function boombox_wrap_embed_within_responsive_container( $html, $classes = '' ) {
	return sprintf( '<div class="boombox-responsive-embed %1$s">%2$s</div>', $classes, $html );
}

/**
 * Get "NSFW" terms
 *
 * @return array
 * @since   2.1.3
 * @version 2.1.3
 */
function boombox_get_nsfw_terms() {

	$cache_key = 'nsfw_terms';
	$terms     = boombox_cache_get( $cache_key );
	if ( false === $terms ) {
		$terms = array();

		/***** Check by categories */
		$category_slugs = boombox_get_theme_option( 'extras_nsfw_categories' );
		foreach ( $category_slugs as $slug ) {
			$term = get_term_by( 'slug', $slug, 'category' );
			if ( $term ) {
				$terms[] = $term;
			}
		}

		boombox_cache_set( $cache_key, $terms );
	}

	return $terms;
}

/**
 * Wrapper method to get post thumbnail HTML
 *
 * @param int|WP_Post  $post Optional. Post ID or WP_Post object.  Default is global `$post`.
 * @param string|array $size Optional. Image size to use. Accepts any valid image size, or
 *                           an array of width and height values in pixels (in that order).
 *                           Default 'post-thumbnail'.
 * @param string|array $attr Optional. Query string or array of attributes. Default empty.
 *
 * @return string The post thumbnail image tag.
 *
 * @return string
 */
function boombox_get_post_thumbnail( $post = null, $size = 'post-thumbnail', $attr = array() ) {

	$attr         = wp_parse_args( $attr, array(
		'play'         => false,
		'template'     => '',
		'listing_type' => ''
	) );
	$width        = false;
	$height       = false;
	$post         = get_post( $post );
	$thumbnail_id = get_post_thumbnail_id( $post );
	if ( $attr['play'] && ( 'full' != $size ) ) {
		$url = wp_get_attachment_image_url( $thumbnail_id, 'full' );
		if ( $url ) {
			$url = parse_url( $url );
			if ( 'gif' == pathinfo( $url['path'], PATHINFO_EXTENSION ) ) {
				$size = 'full';
			}
		}
	}

	$before  = '';
	$after   = '';
	$content = get_the_post_thumbnail( $post, $size, $attr );

	if ( ! boombox_is_amp() ) {

		$media_type = Boombox_Template::get_clean( 'bb_post_media_type' );
		$width      = Boombox_Template::get_clean( 'bb_post_media_w', $width );
		$height     = Boombox_Template::get_clean( 'bb_post_media_h', $height );

		if ( $content && ( 'video' != $media_type ) ) {
			list( $before, $after ) = array_values( boombox_get_media_placeholder_atts( $thumbnail_id, $size, $width, $height ) );
			if ( boombox_is_nsfw_post( $post ) ) {
				if ( 'single' == $attr['template'] ) {
					if ( boombox_get_theme_option( 'extras_nsfw_require_auth' ) ) {
						$content = boombox_get_nsfw_message();
						$before  = $after = '';
					}
				} else {
					$content = boombox_get_nsfw_message();
				}
			}
		}

	}

	return $before . $content . $after;
}

/**
 * Get media placeholder attributes
 *
 * @param int          $media_id Media ID
 * @param string|array $size     Optional. Image size.
 * @param bool|int     $width    Optional. Width
 * @param bool|int     $height   Optional. Height
 *
 * @return array
 * @since   2.1.0
 * @version 2.1.0
 */
function boombox_get_media_placeholder_atts( $media_id, $size, $width = false, $height = false ) {
	if ( ! $width || ! $height ) {
		list( $url, $width, $height ) = wp_get_attachment_image_src( $media_id, $size );
	}

	$before = '';
	$after  = '';
	if ( $width && $height ) {
		$padding = round( ( $height / $width ) * 100, 3 );
		$before  = '<div class="bb-media-placeholder" style="padding-bottom:' . $padding . '%;">';
		$after   = '</div>';
	} else {
		$before = '<div class="bb-media-placeholder no-padding">';
		$after  = '</div>';
	}

	return compact( 'before', 'after' );

}

/**
 * Get NSFW message
 *
 * @return string
 *
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_get_nsfw_message() {

	$args = array(
		'icon'    => '<i class="bb-icon bb-ui-icon-skull"></i>',
		'title'   => sprintf( '<h3>%s</h3>', esc_html__( 'Not Safe For Work', 'boombox' ) ),
		'content' => sprintf( '<p>%s</p>', esc_html__( 'Click to view this post.', 'boombox' ) ),
	);

	$args = wp_parse_args( (array) apply_filters( 'boombox_nsfw_message_args', $args ), $args );

	$html = sprintf( '<div class="nsfw-post"><div class="nsfw-content">%s</div></div>', ( $args['icon'] . $args['title'] . $args['content'] ) );

	return $html;
}

/**
 * Get post thumbnail caption
 *
 * @return string
 *
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_get_post_thumbnail_caption( $echo = true ) {
	$html    = '';
	$caption = get_the_post_thumbnail_caption();
	if ( $caption ) {

		$args = array(
			'before'  => '',
			'after'   => '',
			'classes' => 'thumbnail-caption',
		);

		$args = wp_parse_args( apply_filters( 'boombox/post/thumbnail/caption', $args ), $args );

		$html = sprintf( '%1$s<div class="%2$s">%3$s</div>%4$s', $args['before'], $args['classes'], get_the_post_thumbnail_caption(), $args['after'] );
	}

	return $html;
}

/**
 * Get embed data from URL
 *
 * @param string $url URL to check
 *
 * @return array
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_get_embed_video_data_from_url( $url ) {

	$embed_data = array();

	while ( true ) {
		/***** "Youtube" Iframe */
		preg_match( boombox_get_regex( 'youtube' ), $url, $youtube_matches );
		if ( isset( $youtube_matches[1] ) && $youtube_matches[1] ) {
			$embed_data = array(
				'type'     => 'youtube',
				'video_id' => $youtube_matches[1],
			);
			break;
		}

		/***** "Vimeo" Iframe */
		preg_match( boombox_get_regex( 'vimeo' ), $url, $vimeo_matches );
		if ( isset( $vimeo_matches[5] ) && $vimeo_matches[5] ) {
			$embed_data = array(
				'type'     => 'vimeo',
				'video_id' => $vimeo_matches[5],
			);
			break;
		}

		/***** "Dailymotion" Iframe */
		preg_match( boombox_get_regex( 'dailymotion' ), $url, $dailymotion_matches );
		if ( isset( $dailymotion_matches[1] ) && $dailymotion_matches[1] ) {
			$embed_data = array(
				'type'     => 'dailymotion',
				'video_id' => $dailymotion_matches[1],
			);
			break;
		}

		/***** "Vine" Iframe */
		preg_match( boombox_get_regex( 'vine' ), $url, $vine_matches );
		if ( isset( $vine_matches[1] ) && $vine_matches[1] ) {
			$embed_data = array(
				'type'     => 'vine',
				'video_id' => $vine_matches[1],
			);
			break;
		}

		/***** "Ok" Iframe */
		preg_match( boombox_get_regex( 'ok' ), $url, $ok_matches );
		if ( isset( $ok_matches[2] ) && $ok_matches[2] ) {
			$embed_data = array(
				'type'     => 'ok',
				'video_id' => $ok_matches[2],
			);
			break;
		}

		/***** "Facebook" Iframe */
		preg_match( boombox_get_regex( 'facebook' ), $url, $fb_matches );
		if ( isset( $fb_matches[1] ) && $fb_matches[1] ) {
			$embed_data = array(
				'type'      => 'facebook',
				'video_url' => $fb_matches[1],
			);
			break;
		}

		/***** "Vidme" Iframe */
		preg_match( boombox_get_regex( 'vidme' ), $url, $vidme_matches );
		if ( isset( $vidme_matches[1] ) && $vidme_matches[1] ) {
			$embed_data = array(
				'type'     => 'vidme',
				'video_id' => $vidme_matches[1],
			);
			break;
		}

		/***** "VK" Iframe */
		preg_match( boombox_get_regex( 'vk' ), $url, $vk_matches );
		if ( isset( $vk_matches[2] ) && $vk_matches[2] ) {
			parse_str( $vk_matches[2], $vk_matches );
			if ( isset( $vk_matches['id'], $vk_matches['oid'], $vk_matches['hash'] ) ) {
				$embed_data = array(
					'type' => 'vk',
					'id'   => $vk_matches['id'],
					'oid'  => $vk_matches['oid'],
					'hash' => $vk_matches['hash'],
				);
			}
			break;
		}

		/***** "Twitch" Iframe */
		preg_match( boombox_get_regex( 'twitch' ), $url, $twitch_matches );
		if ( ! empty( $twitch_matches ) ) {
			if ( strpos( $url, '/videos/' ) !== false || strpos( $url, '/clip/' ) !== false ) {
				$stream_type = strpos( $url, '/videos/' ) !== false ? 'videos' : 'clips';
			} else {
				$stream_type = 'live';
			}

			$embed_data = array(
				'type'        => 'twitch',
				'stream_type' => $stream_type,
				'video_id'    => end( $twitch_matches ),
			);

			break;
		}

		/***** "Instagram" Iframe */
		preg_match( boombox_get_regex( 'instagram' ), $url, $instagram_matches );
		if ( isset( $instagram_matches[3] ) && $instagram_matches[3] ) {
			$embed_data = array(
				'type'     => 'instagram',
				'video_id' => $instagram_matches[3],
			);
			break;
		}

		/***** "Coub" Iframe */
		preg_match( boombox_get_regex( 'coub' ), $url, $coub_matches );
		if ( isset( $coub_matches[3] ) && $coub_matches[3] ) {
			$embed_data = array(
				'type'     => 'coub',
				'video_id' => $coub_matches[3],
			);
			break;
		}

		/***** "Twitter" Iframe */
		preg_match( boombox_get_regex( 'twitter' ), $url, $twitter_matches );
		if ( isset( $twitter_matches[1] ) && isset( $twitter_matches[2] ) && $twitter_matches[1] && $twitter_matches[2] ) {
			$embed_data = array(
				'type'      => 'twitter',
				'video_url' => $url,
			);
			break;
		}

		break;
	}

	return $embed_data;
}

/**
 * Get embed iframe HTML
 *
 * @param       $embed_type       string  Embed type youtube, vimeo, dailymotion
 * @param array $params           array   Embed additional attributes
 *
 * @return string
 *
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_get_embed_html( $embed_type, $params = array() ) {

	$html        = '';
	$embed_src   = false;
	$iframe_atts = array();

	switch ( $embed_type ) {

		case 'youtube':
			$embed_atts = apply_filters( 'boombox/embed/youtube/src_atts', array(), $params['video_id'] );
			$embed_src  = sprintf( 'https://www.youtube.com/embed/%1$s?%2$s', $params['video_id'], build_query( $embed_atts ) );

			$iframe_atts = apply_filters( 'boombox/embed/youtube/iframe_atts', array(
				'type'            => 'text/html',
				'width'           => '100%',
				'height'          => 376,
				'src'             => $embed_src,
				'frameborder'     => 0,
				'allowfullscreen' => true,
			), $params['video_id'] );

			break;
		case 'vimeo':

			$embed_atts = apply_filters( 'boombox/embed/vimeo/src_atts', array(
				'autopause' => 1,
				'badge'     => 0,
				'byline'    => 0,
				'loop'      => 0,
				'title'     => 0,
				'autoplay'  => 0,
			), $params['video_id'] );
			$embed_src  = sprintf( '//player.vimeo.com/video/%1$s?%2$s', $params['video_id'], build_query( $embed_atts ) );

			$iframe_atts = apply_filters( 'boombox/embed/vimeo/iframe_atts', array(
				'width'           => '100%',
				'height'          => 376,
				'src'             => $embed_src,
				'frameborder'     => 0,
				'allowfullscreen' => true,
			), $params['video_id'] );

			break;
		case 'dailymotion':

			$embed_atts = apply_filters( 'boombox/embed/dailymotion/src_atts', array( 'autoplay' => 0 ), $params['video_id'] );
			$embed_src  = sprintf( '//www.dailymotion.com/embed/video/%1$s?%2$s', $params['video_id'], build_query( $embed_atts ) );

			$iframe_atts = apply_filters( 'boombox/embed/dailymotion/iframe_atts', array(
				'type'            => 'text/html',
				'width'           => '100%',
				'height'          => 376,
				'src'             => $embed_src,
				'frameborder'     => 0,
				'allowfullscreen' => true,
			), $params['video_id'] );

			break;
		case 'vine':
			$embed_atts = apply_filters( 'boombox/embed/vine/src_atts', array( 'audio' => 1 ), $params['video_id'] );
			$embed_src  = sprintf( 'https://vine.co/v/%s/embed/simple?%s', $params['video_id'], build_query( $embed_atts ) );

			$iframe_atts = apply_filters( 'boombox/embed/vine/iframe_atts', array(
				'width'             => '100%',
				'height'            => 376,
				'src'               => $embed_src,
				'frameborder'       => 0,
				'allowfullscreen'   => true,
				'allowTransparency' => 'true',
			), $params['video_id'] );

			break;
		case 'ok':
			$embed_atts = apply_filters( 'boombox/embed/ok/src_atts', array( 'autoplay' => 0 ), $params['video_id'] );
			$embed_src  = sprintf( 'https://ok.ru/videoembed/%s?%s', $params['video_id'], build_query( $embed_atts ) );

			$iframe_atts = apply_filters( 'boombox/embed/ok/iframe_atts', array(
				'width'           => '100%',
				'height'          => 376,
				'src'             => $embed_src,
				'frameborder'     => 0,
				'allowfullscreen' => true,
			), $params['video_id'] );

			break;
		case 'vidme':
			$embed_atts = apply_filters( 'boombox/embed/vidme/src_atts', array( 'stats' => 1 ), $params['video_id'] );
			$embed_src  = sprintf( 'https://vid.me/e/%s?%s', $params['video_id'], build_query( $embed_atts ) );

			$iframe_atts = apply_filters( 'boombox/embed/vidme/iframe_atts', array(
				'width'           => '100%',
				'height'          => 376,
				'src'             => $embed_src,
				'frameborder'     => 0,
				'allowfullscreen' => true,
			), $params['video_id'] );

			break;
		case 'vk':
			$embed_atts = apply_filters( 'boombox/embed/vk/src_atts', array(
				'id'   => $params['id'],
				'oid'  => $params['oid'],
				'hash' => $params['hash'],
			), $params['id'] );
			$embed_src  = sprintf( 'https://vk.com/video_ext.php?%s', build_query( $embed_atts ) );

			$iframe_atts = apply_filters( 'boombox/embed/vidme/iframe_atts', array(
				'width'           => '100%',
				'height'          => 376,
				'src'             => $embed_src,
				'frameborder'     => 0,
				'allowfullscreen' => true,
			), $params['id'] );

			break;
		case 'facebook':
		case 'twitter':
			global $wp_embed;
			$html = $wp_embed->shortcode( array(), $params['video_url'] );

			break;
		case 'twitch':
			if ( $params['stream_type'] == 'videos' ) {
				$embed_src = 'https://player.twitch.tv/?video=v' . $params['video_id'];
			} elseif ( $params['stream_type'] == 'clips' ) {
				$embed_src = 'https://clips.twitch.tv/embed?clip=' . $params['video_id'];
			} else {
				//Case for Live Video
				$embed_src = 'https://player.twitch.tv/?channel=' . $params['video_id'];
			}

			//Requirements: Domains that use Twitch embeds must use SSL certificates.
			$domain = basename( get_site_url() );

			/**
			 * @attr stinrg autoplay e.g 'true' || 'false'. The exception is mobile devices, on which video cannot
			 * be played without user interaction. Default: true.
			 * @attr string|array parent e.g 'boombox.px-lab.com' || ['boombox.px-lab.com', 'zombify.px-lab.com']
			 */
			$embed_atts = array( 'autoplay' => 'false', 'parent' => $domain );
			$embed_atts = apply_filters( 'boombox/embed/twitch/src_atts', $embed_atts, $params['video_id'] );
			$embed_src  = $embed_src . '&' . build_query( $embed_atts );

			$iframe_atts = apply_filters( 'boombox/embed/twitch/iframe_atts', array(
				'width'           => '100%',
				'height'          => 376,
				'src'             => $embed_src,
				'frameborder'     => 0,
				'allowfullscreen' => true,
			), $params['video_id'] );

			break;
		case 'coub':
			$embed_atts = apply_filters( 'boombox/embed/coub/src_atts', array(
				'muted'        => 'false',
				'autostart'    => 'false',
				'originalSize' => 'false',
				'startWithHD'  => 'false',
			), $params['video_id'] );
			$embed_src  = sprintf( '//coub.com/embed/%s?%s', $params['video_id'], build_query( $embed_atts ) );

			$iframe_atts = apply_filters( 'boombox/embed/coub/iframe_atts', array(
				'width'           => '100%',
				'height'          => 376,
				'src'             => $embed_src,
				'frameborder'     => 0,
				'allowfullscreen' => true,
			), $params['video_id'] );

			break;
		case 'instagram':
			$embed_src = 'https://www.instagram.com/p/' . $params['video_id'] . '/embed/captioned';

			$iframe_atts = apply_filters( 'boombox/embed/instagram/iframe_atts', array(
				'height'            => 800,
				'src'               => $embed_src,
				'allowtransparency' => true,
				'allowfullscreen'   => true,
				'frameborder'       => 0,
				'scrolling'         => 'no',
				'style'             => 'background: white; max-width: 658px; width: calc(100% - 2px); border-radius: 3px; border: 1px solid rgb(219, 219, 219); box-shadow: none; display: block; margin: 0px 0px 12px; min-width: 326px; padding: 0px;',
			), $params['video_id'] );

			break;
	}

	if ( $embed_src ) {
		foreach ( $iframe_atts as $attr => $value ) {
			$iframe_atts[ $attr ] = is_bool( $value ) ? $attr : sprintf( '%s="%s"', $attr, $value );
		}

		$html = sprintf( '<iframe %s></iframe>', implode( ' ', $iframe_atts ) );
	}

	return $html;
}

/**
 * Get HTML for HTML video
 *
 * @param      $video_url        string          HTML video source
 * @param null $image_src        string|null     Image src to use if javascript is disabled or is old browser
 *
 * @return string
 *
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_get_html_video( $video_url, $image_src = null ) {

	$set = boombox_get_theme_options_set( array(
		'extras_video_control_enable_mp4_video_loop',
		'extras_video_control_mp4_video_sound',
		'extras_video_control_mp4_video_player_controls',
		'extras_video_control_mp4_video_autoplay'
	) );

	$image = '';
	if ( $image_src ) {
		$image = sprintf( '<img src="%s" />', esc_url( $image_src ) );
	}

	$video_atts = array();

	/***** Video loop */
	if ( $set['extras_video_control_enable_mp4_video_loop'] ) {
		$video_atts[] = 'loop';
	}

	/***** Video mute */
	if ( $set['extras_video_control_mp4_video_sound'] =='muted' || $set['extras_video_control_mp4_video_autoplay'] == 'scroll') {
		$video_atts[] = 'muted';
	}

	/***** Video Controls */
	$sound_icons = '';
	if ( 'mute' == $set['extras_video_control_mp4_video_player_controls'] ) {
		if ( 'scroll' == $set['extras_video_control_mp4_video_autoplay'] ) {
			$sound_icons = '<span class="bb-btn-circle btn-sm btn-volume">
                				<i class="volume-off bb-icon bb-ui-icon-volume-mute"></i>
                				<i class="volume-on bb-icon bb-ui-icon-volume-up hidden"></i>
            				</span>';
		} else {
			$sound_icons = sprintf( '
            <span class="bb-btn-circle btn-sm btn-volume">
                <i class="volume-off bb-icon bb-ui-icon-volume-mute %1$s"></i>
                <i class="volume-on bb-icon bb-ui-icon-volume-up %2$s"></i>
            </span>', $set['extras_video_control_mp4_video_sound'] == 'muted' ? '' : 'hidden', $set['extras_video_control_mp4_video_sound'] == 'muted' ? 'hidden' : '' );
		}

	} else {
		$video_atts[] = 'controls';
	}

	$html = sprintf( '<video width="100%%" height="auto" %1$s>
            <source src="%2$s" type="video/mp4">%3$s
		</video>
		<span class="bb-btn-circle btn-lg btn-play"><i class="bb-icon bb-ui-icon-video"></i></span>%4$s
		<span class="badge-duration hidden"></span>
        ', implode( ' ', $video_atts ), esc_url( $video_url ), $image, $sound_icons );

	return $html;
}

/**
 * Check if html mp4 video allowed to render within template
 *
 * @param $template string  Current template
 *
 * @return bool
 *
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_is_video_mp4_allowed( $template ) {
	return ( ( 'single' == $template ) || ( 'listing' == $template && boombox_get_theme_option( 'extras_video_control_enable_mp4_video_on_post_listings' ) ) );
}

/**
 * Check if html embed video allowed to render within template
 *
 * @param $template string  Current template
 *
 * @return bool
 *
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_is_video_embed_allowed( $template ) {
	return ( ( 'single' == $template ) || ( 'listing' == $template && boombox_get_theme_option( 'extras_video_control_enable_embed_video_on_post_listings' ) ) );
}

/**
 * Get post featured video HTML
 *
 * @param int $post_id              int     Post ID
 * @param     $featured_image_size  string  Image Size
 * @param     $params               array   Additional attributes
 *
 * @return string
 *
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_get_post_featured_video( $post_id = 0, $featured_image_size = 'full', $params = array() ) {

	$featured_video_html = '';
	if ( 0 === $post_id ) {
		global $post;
		if ( is_object( $post ) ) {
			$post_id = $post->ID;
		}
	}

	if ( ! $post_id ) {
		return $featured_video_html;
	}

	$featured_video_url = boombox_get_post_meta( $post_id, 'boombox_video_url' );

	if ( ! empty( $featured_video_url ) ) {

		$template             = ( isset( $params['template'] ) && $params['template'] ) ? $params['template'] : '';
		$featured_image       = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $featured_image_size );
		$featured_image_src   = isset( $featured_image[0] ) ? $featured_image[0] : '';
		$featured_image_style = $featured_image_src ? 'style="background-image:url(' . esc_url( $featured_image_src ) . ')"' : '';
		$featured_image_class = ! $featured_image_src ? esc_attr( 'no-thumbnail' ) : '';
		$featured_video       = array();

		while ( true ) {

			/***** Embed video */
			if ( boombox_is_video_embed_allowed( $template ) ) {
				$embed_data = boombox_get_embed_video_data_from_url( $featured_video_url );
				if ( ! empty( $embed_data ) ) {
					switch ( $embed_data['type'] ) {
						case 'facebook':
						case 'twitter':
							$featured_video = array(
								'before' => '<div class="clearfix boombox-featured-embed bb-embed-' . $embed_data['type'] . '">',
								'after'  => '</div>',
								'html'   => boombox_get_embed_html( $embed_data['type'], array( 'video_url' => $embed_data['video_url'] ) ),
							);
							break;
						case 'vk':
							$featured_video = array(
								'before' => '<div class="clearfix boombox-featured-embed bb-embed-' . $embed_data['type'] . '">',
								'after'  => '</div>',
								'html'   => boombox_get_embed_html( $embed_data['type'], array(
									'id'   => $embed_data['id'],
									'oid'  => $embed_data['oid'],
									'hash' => $embed_data['hash'],
								) ),
							);
							break;
						case 'twitch':
							$featured_video = array(
								'before' => '<div class="clearfix boombox-featured-embed bb-embed-' . $embed_data['type'] . '">',
								'after'  => '</div>',
								'html'   => boombox_get_embed_html( $embed_data['type'], array(
									'video_id'    => $embed_data['video_id'],
									'stream_type' => $embed_data['stream_type'],
								) ),
							);
							break;
						default:
							$featured_video = array(
								'before' => sprintf( '<div class="video-wrapper boombox-featured-embed %s %s">', 'bb-embed-' . $embed_data['type'], $featured_image_class ),
								'after'  => '</div>',
								'html'   => boombox_get_embed_html( $embed_data['type'], array( 'video_id' => $embed_data['video_id'] ) ),
							);
					}
					break;
				}
			}

			/***** HTML video */
			if ( boombox_is_video_mp4_allowed( $template ) && strrpos( $featured_video_url, '.mp4' ) !== false ) {
				$featured_video = array(
					'before' => sprintf( '<div class="video-wrapper boombox-featured-video %s" %s>', $featured_image_class, $featured_image_style ),
					'after'  => '</div>',
					'html'   => boombox_get_html_video( $featured_video_url, $featured_image_src ),
				);
				break;
			}

			break;
		}

		$featured_video_html = ! empty( $featured_video ) ? ( $featured_video['before'] . $featured_video['html'] . $featured_video['after'] ) : '';
		$featured_video_html .= boombox_is_nsfw_post( $post_id ) ? boombox_get_nsfw_message() : '';
	}

	return $featured_video_html;
}

/**
 * Get meta key for share calculation
 *
 * @return string
 *
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_get_shares_meta_key() {
	return apply_filters( 'boombox/shares_meta_key', 'mashsb_shares' );
}

/**
 * Get comment form options
 *
 * @return array
 *
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_get_comment_form_args() {

	$commenter = wp_get_current_commenter();
	$req       = get_option( 'require_name_email' );
	$aria_req  = ( $req ? " aria-required='true'" : '' );

	$args = array(
		'title_reply_before' => '<h2 id="reply-title" class="comment-reply-title">',
		'title_reply'        => '',
		'title_reply_to'     => '',
		'title_reply_after'  => '</h2>',
		'class_submit'       => 'submit-button',
		'label_submit'       => esc_html__( 'Post', 'boombox' ),
		'fields'             => array(
			'author' => '<div class="row"><div class="col-lg-6 col-md-6"><div class="input-field">' . '<input id="author" name="author" type="text" placeholder="' . esc_html__( 'Name *', 'boombox' ) . '" ' . $aria_req . ' ' . ' value="' . esc_attr( $commenter['comment_author'] ) . '">' . '</div></div>',
			'email'  => '<div class="col-lg-6 col-md-6"><div class="input-field">' . '<input id="email" name="email" type="text" placeholder="' . esc_html__( 'Email *', 'boombox' ) . '" ' . $aria_req . ' ' . 'value="' . esc_attr( $commenter['comment_author_email'] ) . '">' . '</div></div></div>',
		),
		'must_log_in'        => '<p class="must-log-in">' . sprintf( __( 'You must be <a href="%s" class="js-authentication">logged in</a> to post a comment.', 'boombox' ), '#sign-in' ) . '</p>',
		'comment_field'      => '<div class="comment-form-comment"><div class="input-field">' . '<textarea id="comment" name="comment" placeholder="' . esc_html__( 'Write a comment *', 'boombox' ) . '" aria-required="true"></textarea>' . '</div></div>',
	);

	return apply_filters( 'boombox/comment_form_args', $args );
}

/**
 * Get post thumbnail wrapper link
 *
 * @param string $url    URL for wrapper
 * @param string $target URL target attribute
 * @param string $rel    URL rel attribute
 * @param string $class  CSS classes for wrapper anchor
 *
 * @return array
 *
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_get_post_thumbnail_wrapper_link( $url, $target, $rel, $class = "" ) {
	$class = $class ? sprintf( 'class="%s"', $class ) : '';

	return array(
		'before' => sprintf( '<a href="%1$s" title="%2$s" %3$s %4$s>', $url, esc_attr( the_title_attribute( array( 'echo' => false ) ) ), $target, $class ),
		'after'  => '</a>',
	);
}

/**
 * Get post thumbnail wrapper before / after
 *
 * @param string $html    Current HTML to wrap
 * @param bool   $is_nsfw Is NSFW post
 * @param string $url     URL for wrapper
 * @param string $target  URL target attribute
 * @param string $rel     URL rel attribute
 * @param string $class   CSS classes for wrapper anchor
 *
 * @return array
 *
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_do_post_thumbnail_wrap( $html, $url, $target, $rel, $class = '' ) {

	$return = array(
		'before'      => '',
		'after'       => '',
		'is_playable' => false
	);

	while ( true ) {

		if ( boombox_html_contains_gif_image( $html ) ) {
			$return['is_playable'] = true;
			break;
		}

		if ( boombox_html_contains_embed_or_static_video( $html ) ) {
			$return['is_playable'] = true;
			break;
		}

		$return = array_merge( $return, boombox_get_post_thumbnail_wrapper_link( $url, $target, $rel, $class ) );
		break;

	}

	return $return;
}

/**
 * Get user meta description
 *
 * @param int $user_id User ID
 *
 * @return string
 */
function boombox_get_user_meta_description( $user_id ) {
	$desciption = wp_kses_post( get_the_author_meta( 'description', $user_id ) );
	$desciption = apply_filters( 'boombox/author_bio', $desciption, $user_id );

	return strip_tags( $desciption );
}

/**
 * Get view count threshold
 *
 * @return int
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_get_view_count_threshold() {
	return absint( apply_filters( 'boombox/post-views-count-threshold', 0 ) );
}

/**
 * Conditional check for view count treshold
 *
 * @param int $view_count View count
 *
 * @return bool
 *
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_is_view_count_tresholded( $view_count ) {
	return ( $view_count < boombox_get_view_count_threshold() );
}

/**
 * Check if is AMP version
 *
 * @return bool
 *
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_is_amp() {
	$is_amp_url = false;

	if ( function_exists( 'boombox_amp' ) ) {
		$is_amp_url = boombox_amp()->is_amp();
	}

	return $is_amp_url;
}

/**
 * Get default thumbnail URL for post
 *
 * @param string $size                 Optional. Image size to use.
 * @param        $post                 int|WP_Post  $post Optional. Post ID or WP_Post object.  Default is global
 *                                     `$post`.
 *
 * @return string
 *
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_get_post_dafault_thumbnail_url( $size = 'full', $post = null ) {
	$url = apply_filters( 'boombox/post-default-thumbnail', '', $size, get_post( $post ) );

	return esc_url( $url );
}

/**
 * Render hidden SEO title
 *
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_render_hidden_seo_title() {
	$title = wp_title( ' - ', false, 'right' );
	if ( ! $title ) {
		$title = wp_get_document_title();
	}

	if ( $title ) {
		printf( '<h1 class="mf-hide site-title">%s</h1>', $title );
	}
}

/**
 * Get single post sortable sections
 *
 * @return array
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_get_single_sortable_sections() {
	$sections = boombox_get_theme_option( 'single_post_general_sections' );

	return apply_filters( 'boombox/single/sortable_sections', $sections );
}

/**
 * Check against fragment cache enabled feature
 *
 * @return bool
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_is_fragment_cache_enabled() {
	return (bool) apply_filters( 'boombox/frgcache.enabled', false );
}

/**
 * Check against fragment cache enabled feature
 *
 * @return bool
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_is_page_cache_enabled() {
	return (bool) apply_filters( 'boombox/pgcache.enabled', false );
}

/**
 * Get body classes by sidebar type
 *
 * @param string $type        Chosen sidebar type
 * @param string $orientation Sidebar orientation
 * @param bool   $reverse     Sidebar reverse
 *
 * @return string
 */
function boombox_get_body_classes_by_sidebar_type( $type, $orientation, $reverse = false ) {
	$has_sidebar = true;

	switch ( $type ) {
		case '1-sidebar-1_3':
			$classes = 'one-sidebar sidebar-1_3';
			break;
		case '1-sidebar-1_4':
			$classes = 'one-sidebar sidebar-1_4';
			break;
		case '2-sidebars-1_4-1_4':
			$classes = 'two-sidebar sidebar-1_4';
			break;
		case '2-sidebars-small-big':
			$classes = 'two-sidebar sidebar-1_3-1_4';
			break;
		case 'no-sidebar':
			$classes     = 'no-sidebar';
			$has_sidebar = false;
			break;
		default:
			$classes     = 'no-sidebar';
			$has_sidebar = false;
	}

	if ( $has_sidebar ) {
		if ( $reverse ) {
			$orientation = ( $orientation == 'right' ) ? 'left' : 'right';
		}
		$classes .= sprintf( ' %s-sidebar', $orientation );
	}

	return $classes;

}

/**
 * Check whether it's a visual post
 *
 * @param int $post_id Post ID
 *
 * @return bool
 */
function boombox_is_visual_post( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	return (bool) boombox_get_post_meta( $post_id, 'boombox_visual_post' );
}

/**
 * Override video player dimensions
 *
 * @param $out
 * @param $pairs
 * @param $atts
 *
 * @return mixed
 */
function boombox_shortcode_atts_video( $out, $pairs, $atts ) {

	global $content_width;

	$out['height'] = round( ( $content_width / $out['width'] ) * $out['height'] );
	$out['width']  = $content_width;

	return $out;
}

add_filter( 'shortcode_atts_video', 'boombox_shortcode_atts_video', 10, 4 );

/**
 * Remove custom attributes from image tag
 *
 * @param array $attr Current attributes
 *
 * @return array
 */
function boombox_on_wp_get_attachment_image_attributes( $attr ) {
	unset( $attr['play'], $attr['template'], $attr['listing_type'] );

	return $attr;
}

add_filter( 'wp_get_attachment_image_attributes', 'boombox_on_wp_get_attachment_image_attributes', 10, 1 );

/**
 * Flush theme caches
 *
 * @since   2.5.0
 * @version 2.5.0
 */
function boombox_flush_caches() {
	delete_site_transient( 'boombox-icons' );
}

add_action( 'boombox/after_migration', 'boombox_flush_caches' );

/**
 * Get post brands
 *
 * @param int|WP_Post|null $post   Optional. Post ID or post object. Defaults to global $post.
 * @param   @param array        $args     {
 *                                 Optional. Term query parameters. See WP_Term_Query::__construct() for supported arguments.
 *
 * @type string            $fields Term fields to retrieve. Default 'all'.
 * }
 * @return bool|WP_Term
 * @since   2.5.0
 * @version 2.5.0
 */
function boombox_get_post_brand( $post = null ) {

	$post     = get_post( $post );
	$taxonomy = 'brand';
	$brand_id = false;

	while ( true ) {
		if ( $post ) {

			// try to get from post object by taxonomy
			if ( taxonomy_exists( $taxonomy ) ) {
				$brand_ids = wp_get_post_terms( $post->ID, $taxonomy, array( 'number' => 1, 'fields' => 'ids' ) );
				if ( ! is_wp_error( $brand_ids ) && ! empty( $brand_ids ) ) {
					$brand_id = $brand_ids[0];
					break;
				}
			}

			// try to get one from post author associations
			$user_brand_id = boombox_get_user_meta( $post->post_author, 'boombox_user_brand_id' );
			if ( $user_brand_id ) {
				$brand_id = $user_brand_id;
				break;
			}

			// try to get one from associated ones from post attached categories
			$category_ids = wp_get_post_categories( $post->ID, array(
				'fields'     => 'ids',
				'number'     => 1,
				'meta_query' => array(
					array(
						'key'     => 'boombox_category_brand_id',
						'value'   => '',
						'compare' => '!='
					)
				)
			) );
			if ( ! empty( $category_ids ) ) {
				$category_brand_id = boombox_get_term_meta( $category_ids[0], 'boombox_category_brand_id' );
				if ( $category_brand_id ) {
					$brand_id = $category_brand_id;
					break;
				}
			}

			// try to get one from associated ones from post attached post_tags
			$post_tag_ids = wp_get_post_tags( $post->ID, array(
				'fields'     => 'ids',
				'number'     => 1,
				'meta_query' => array(
					array(
						'key'     => 'boombox_post_tag_brand_id',
						'value'   => '',
						'compare' => '!='
					)
				)
			) );
			if ( ! empty( $post_tag_ids ) ) {
				$tag_brand_id = boombox_get_term_meta( $post_tag_ids[0], 'boombox_post_tag_brand_id' );
				if ( $tag_brand_id ) {
					$brand_id = $tag_brand_id;
					break;
				}
			}
		}

		break;
	}

	return $brand_id ? get_term_by( 'id', $brand_id, $taxonomy ) : false;
}

/**
 * Wrapper function for core template part function with possibility to setup data for included template
 *
 * @param string $slug The slug name for the generic template.
 * @param string $name The name of the specialised template.
 * @param array  $data Data to set for template
 */
function boombox_get_template_part( $slug, $name = null, $data = array() ) {

	/***** Allow to set custom data in template */
	$data = (array) apply_filters( 'boombox/template-part-data', $data, $slug, $name );

	if ( ! empty( $data ) ) {
		foreach ( $data as $k => $v ) {
			if ( ! $k ) {
				continue;
			}
			Boombox_Template::set( $k, $v );
		}
	}

	do_action( 'boombox/before_template_part', $slug, $name, $data );
	get_template_part( $slug, $name );
	do_action( 'boombox/after_template_part', $slug, $name, $data );
}

/**
 * Get brand logo
 *
 * @param WP_Term $brand Brand term
 * @param array   $args  Additional argument
 *
 * @return array
 * @since   2.5.0
 * @version 2.5.0
 */
function boombox_get_brand_logo( $brand, $args = array() ) {
	$logo_id      = boombox_get_term_meta( $brand->term_id, 'brand_logo_id' );
	$logo_hdpi_id = boombox_get_term_meta( $brand->term_id, 'brand_logo_hdpi_id' );

	$data = array();
	if ( $logo_id || $logo_hdpi_id ) {

		$logo_url      = $logo_id ? wp_get_attachment_url( $logo_id ) : '';
		$logo_hdpi_url = $logo_hdpi_id ? wp_get_attachment_url( $logo_hdpi_id ) : '';

		$data['src']    = '';
		$data['src_2x'] = array();
		$data['width']  = boombox_get_term_meta( $brand->term_id, 'brand_logo_width' );
		$data['height'] = boombox_get_term_meta( $brand->term_id, 'brand_logo_height' );

		if ( $logo_hdpi_url ) {
			$data['src_2x'][] = $logo_hdpi_url . ' 2x';
		}

		if ( $logo_url ) {
			$data['src']      = $logo_url;
			$data['src_2x'][] = $logo_url . ' 1x';
		} elseif ( $logo_hdpi_url ) {
			$data['src'] = $logo_hdpi_url;
		}

		$data['src_2x'] = implode( ',', $data['src_2x'] );
	}

	return $data;
}

add_action( 'boombox/after_migration', 'boombox_flush_caches' );

/**
 * Get current URL
 *
 * @param bool $include_query Weather to include query string
 *
 * @return string
 * @since   2.5.0
 * @version 2.5.0
 */
function boombox_get_current_url( $include_query = false ) {
	global $wp;

	$url = home_url( $wp->request );
	if ( $include_query ) {
		$url = add_query_arg( $_SERVER['QUERY_STRING'], '', $url );
	}

	return $url;
}

/**
 * Get post reading time
 *
 * @param null|int|WP_Post $post Post ID or post object
 *
 * @return float|int|string
 * @since   2.5.0
 * @version 2.5.0
 */
function boombox_get_post_reading_time( $args = array() ) {

	$r = wp_parse_args( $args, array(
		'post'   => null,
		'before' => '',
		'after'  => '',
		'class'  => ''
	) );

	$post             = get_post( $r['post'] );
	$post_content     = get_post_field( 'post_content', $post );
	$words_count      = str_word_count( strip_tags( strip_shortcodes( $post_content ) ) );
	$words_per_minute = absint( boombox_get_theme_option( 'extras_reading_time_words_per_minute' ) );

	if ( boombox_get_theme_option( 'extras_reading_time_include_images' ) ) {
		$images_count = substr_count( strtolower( $post_content ), '<img ' );
		if ( $images_count ) {
			$additional_time = 0;
			// For the first image add 12 seconds, second image add 11, ..., for image 10+ add 3 seconds
			for ( $i = 1; $i <= $images_count; $i ++ ) {
				if ( $i >= 10 ) {
					$additional_time += 3 * (int) $words_per_minute / 60;
				} else {
					$additional_time += ( 12 - ( $i - 1 ) ) * (int) $words_per_minute / 60;
				}
			}

			$words_count += $additional_time;
		}
	}

	$reading_time = $words_per_minute ? round( $words_count / $words_per_minute ) : 0;
	if ( $reading_time < 1 ) {
		$reading_time = apply_filters( 'boombox/reading-time/less-than-minute-label', '<span class="bb-arrow bb-icon bb-ui-icon-chevron-left"></span> 1' );
	}
	$time_label = apply_filters( 'boombox/reading-time/unit_label', __( 'min', 'boombox' ) );
	$output     = sprintf( '%s %s', $reading_time, $time_label );

	$class = 'bb-reading-time';
	if ( $r['class'] ) {
		$class .= ' ' . $r['class'];
	}
	$output = '<!--googleoff: index--><span class="' . esc_attr( $class ) . '"><span class="bb-clock bb-icon bb-ui-icon-clock"></span><span class="bb-text">' . $output . '</span></span><!--googleon: index-->';

	return apply_filters( 'boombox/reading-time/output', $output, $reading_time, $post );

}

/**
 * Get "GDPR" checkbox label
 *
 * @return string
 * @since   2.5.5
 * @version 2.5.5
 */
function boombox_get_gdpr_message() {
	$options_set = boombox_get_theme_options_set( array(
		'extra_authentication_terms_of_use_page',
		'extra_authentication_privacy_policy_page'
	) );

	$links               = false;
	$terms_of_use_link   = $options_set['extra_authentication_terms_of_use_page'] ? sprintf( ' %1$s <a href="%2$s" target="_blank" rel="noopener">%3$s</a> ', esc_html__( 'the', 'boombox' ), get_permalink( $options_set['extra_authentication_terms_of_use_page'] ), apply_filters( 'boombox/signup/terms_of_use_title', esc_html__( 'terms of use', 'boombox' ) ) ) : false;
	$privacy_policy_link = $options_set['extra_authentication_privacy_policy_page'] ? sprintf( ' %1$s <a href="%2$s" target="_blank" rel="noopener">%3$s</a> ', esc_html__( 'the', 'boombox' ), get_permalink( $options_set['extra_authentication_privacy_policy_page'] ), apply_filters( 'boombox/signup/privacy_policy_title', esc_html__( 'privacy policy', 'boombox' ) ) ) : false;
	if ( $terms_of_use_link && $privacy_policy_link ) {
		$links = $terms_of_use_link . esc_html__( 'and', 'boombox' ) . $privacy_policy_link;
	} elseif ( $terms_of_use_link ) {
		$links = $terms_of_use_link;
	} elseif ( $privacy_policy_link ) {
		$links = $privacy_policy_link;
	}

	$label = $links ? sprintf( esc_html__( 'I have read and accepted %s', 'boombox' ), $links ) : '';

	return apply_filters( 'boombox/gdpr/checkbox_label', $label, $terms_of_use_link, $privacy_policy_link );
}