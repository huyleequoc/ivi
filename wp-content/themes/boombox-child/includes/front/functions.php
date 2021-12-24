<?php
/**
 * Boombox global functions
 *
 * @package BoomBox_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Edit archive template settings
 *
 * @param $settings
 *
 * @return mixed
 */
function boombox_edit_archive_template_settings( $settings ) {
	if ( is_category() && $settings['featured_area'] ) {
		$settings['featured_area'] = ! boombox_get_term_meta( get_queried_object_id(), 'hide_featured_area' );
	}

	return $settings;
}

add_filter( 'boombox/archive_template_settings', 'boombox_edit_archive_template_settings', 10, 1 );

/**
 * Wrap embeds in wrapper
 *
 * @param $html
 * @param $url
 * @param $attr
 *
 * @return string
 */
function boombox_wrapper_embed_oembed_html( $html, $url, $attr ) {
	$is_video      = false;
	$is_vine_video = false;

	$domains = array(
		'youtube.com',
		'youtu.be',
		'vimeo.com',
		'dailymotion.com',
		'vine.co'
	);
	foreach ( $domains as $domain ) {
		if ( strpos( $url, $domain ) !== false ) {
			if ( 'vine.co' == $domain ) {
				$is_vine_video = true;
			}
			$is_video = true;
			break;
		}
	}

	if ( $is_video ) {
		$classes = $is_vine_video ? esc_attr( 'vine-embed' ) : '';

		return boombox_wrap_embed_within_responsive_container( $html, $classes );
	}

	return $html;
}

add_filter( 'embed_oembed_html', 'boombox_wrapper_embed_oembed_html', 10, 999 );

/**
 * Set 'posts_per_page' params to archive
 *
 * @param WP_Query $query
 *
 * @since  1.0.0
 * @verion 2.0.0
 */
function boombox_edit_archive_template_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( ! is_archive() || is_author() ) {
		return;
	}

	if ( ! apply_filters( 'boombox/allow_archive_query_modification', true ) ) {
		return;
	}

	$queried_object = get_queried_object();
	if ( ! $queried_object ) {
		return;
	}

	$options_set = boombox_get_theme_options_set( array(
		'archive_main_posts_posts_per_page',
		'archive_main_posts_inject_ad',
		'archive_main_posts_injected_ad_position',
		'archive_main_posts_inject_newsletter',
		'archive_main_posts_injected_newsletter_position',
		'archive_main_posts_inject_products',
		'archive_main_posts_injected_products_count',
		'archive_main_posts_injected_products_position',
		'archive_featured_area_exclude_from_main_loop',
		'mobile_global_enable_featured_area',
		'archive_featured_area_type',
		'archive_main_posts_default_order',
		'archive_main_posts_listing_type'
	) );

	$paged  = boombox_get_paged();
	$offset = $query->get( 'offset' );
	$query->set( 'posts_per_page', $options_set['archive_main_posts_posts_per_page'] );

	if ( $options_set['archive_featured_area_exclude_from_main_loop'] ) {
		$hide_featured_area = is_author() ? false : boombox_get_term_meta( $queried_object->term_id, 'hide_featured_area' );
		if ( boombox_is_fragment_cache_enabled() ) {
			?>
			<!-- mfunc <?php echo W3TC_DYNAMIC_SECURITY; ?>
				$queried_object = get_queried_object();
				$hide_featured_area = is_author() ? false : boombox_get_term_meta( $queried_object->term_id, 'hide_featured_area' );
                $featured_area = wp_is_mobile() ?
                    (
                        boombox_get_theme_option( 'mobile_global_enable_featured_area' )
                        && ( boombox_get_theme_option( 'archive_featured_area_type' ) != 'disable' )
                        && ! $hide_featured_area
                    )
                    :
                    (
                        ( boombox_get_theme_option( 'archive_featured_area_type' ) != 'disable' )
                        && ! $hide_featured_area
                    );
		    -->
			<?php
			$featured_area = wp_is_mobile() ?
				(
					$options_set['mobile_global_enable_featured_area']
					&& ( $options_set['archive_featured_area_type'] != 'disable' )
					&& ! $hide_featured_area
				)
				:
				(
					( $options_set['archive_featured_area_type'] != 'disable' )
					&& ! $hide_featured_area
				);
			?>
			<!-- /mfunc <?php echo W3TC_DYNAMIC_SECURITY; ?> -->
			<?php
		} elseif ( boombox_is_page_cache_enabled() ) {
			$featured_area = (
				( $options_set['archive_featured_area_type'] != 'disable' )
				&& ! $hide_featured_area
			);
		} else {
			$featured_area = wp_is_mobile() ?
				(
					$options_set['mobile_global_enable_featured_area']
					&& ( $options_set['archive_featured_area_type'] != 'disable' )
					&& ! $hide_featured_area
				)
				:
				(
					( $options_set['archive_featured_area_type'] != 'disable' )
					&& ! $hide_featured_area
				);
		}

		/**
		 * Exclude featured area posts
		 */
		if ( $featured_area ) {
			$excluded_posts         = array();
			$boombox_featured_query = Boombox_Template::init( 'featured-area' )->get_query();
			if ( null != $boombox_featured_query && $boombox_featured_query->found_posts ) {
				$excluded_posts = array_merge( $excluded_posts, wp_list_pluck( $boombox_featured_query->posts, 'ID' ) );
			}

			if ( ! empty( $excluded_posts ) ) {
				$query->set( 'post__not_in', $excluded_posts );
			}
		}
	}

	if ( is_category() || is_tag() || is_tax( 'reaction' ) ) {
		$condition = $options_set['archive_main_posts_default_order'];
		if ( isset( $_GET['order'] )
		     && ! boombox_get_theme_option( 'archive_header_disable' )
		     && array_key_exists( $_GET['order'], Boombox_Choices_Helper::get_instance()->get_conditions() ) ) {
			$condition = $_GET['order'];
		}

		if ( $condition != 'recent' ) {

			$categories = array();
			$tags       = array();
			$reactions  = array();
			if ( is_category() ) {
				$categories[] = $queried_object->slug;
			} elseif ( is_tag() ) {
				$tags[] = $queried_object->slug;
			} elseif ( is_tax( 'reaction' ) ) {
				$reactions[] = $queried_object->slug;
			}

			$posts_query = boombox_get_posts_query(
				$condition,
				'all',
				array(
					'category' => $categories,
					'tag'      => $tags,
					'reaction' => $reactions,
				),
				array(
					'posts_per_page' => $query->get( 'posts_per_page' ),
					'paged'          => $paged,
					'excluded_posts' => $query->get( 'post__not_in' ),
					'is_page_query'  => false,
				) );

			$orderby = $posts_query->get( 'orderby' );
			$order   = $posts_query->get( 'order' );
			if ( ! is_array( $orderby ) ) {
				$orderby = array( $orderby => $order );
			}
			$query->set( 'orderby', $orderby );

			if ( $meta_key = $posts_query->get( 'meta_key' ) ) {
				$query->set( 'meta_query', array(
					'relation' => 'OR',
					array(
						'key'     => $meta_key,
						'compare' => 'NOT EXISTS',
						'value'   => 0
					),
					array(
						'key'     => $meta_key,
						'compare' => 'EXISTS'
					),
				) );
				$orderby = array_merge( array( 'meta_value_num' => 'DESC' ), $orderby );

				$query->set( 'orderby', $orderby );
			}
		}
	}

	do_action_ref_array( 'boombox/archive_template_query', array( &$query ) );

	$is_adv_enabled        = boombox_is_adv_enabled( $options_set['archive_main_posts_inject_ad'] );
	$is_newsletter_enabled = boombox_is_newsletter_enabled( $options_set['archive_main_posts_inject_newsletter'] );
	$is_product_enabled    = boombox_is_product_enabled( $options_set['archive_main_posts_inject_products'] );

	if ( $is_adv_enabled || $is_newsletter_enabled || $is_product_enabled ) {
		Boombox_Loop_Helper::init( array(
			'is_adv_enabled'        => $is_adv_enabled,
			'instead_adv'           => $options_set['archive_main_posts_injected_ad_position'],
			'is_newsletter_enabled' => $is_newsletter_enabled,
			'instead_newsletter'    => $options_set['archive_main_posts_injected_newsletter_position'],
			'is_product_enabled'    => $is_product_enabled,
			'page_product_position' => $options_set['archive_main_posts_injected_products_position'],
			'page_product_count'    => $options_set['archive_main_posts_injected_products_count'],
			'skip'                  => ( 'grid' == $options_set['archive_main_posts_listing_type'] ),
			'posts_per_page'        => $options_set['archive_main_posts_posts_per_page'],
			'paged'                 => $paged,
			'offset'                => $offset
		) );
	}
}

add_action( 'pre_get_posts', 'boombox_edit_archive_template_query', 1 );

/**
 * Edit index template query
 *
 * @param WP_Query $query Current query
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_edit_index_template_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( ! is_home() ) {
		return;
	}

	$options_set = boombox_get_theme_options_set( array(
		'home_main_posts_condition',
		'home_main_posts_category',
		'home_main_posts_tags',
		'home_main_posts_posts_per_page',
		'home_main_posts_inject_ad',
		'home_main_posts_inject_newsletter',
		'home_main_posts_inject_products',
		'home_main_posts_injected_products_count',
		'home_main_posts_injected_products_position',
		'home_featured_area_exclude_from_main_loop',
		'mobile_global_enable_featured_area',
		'home_featured_area_type',
		'home_main_posts_listing_type',
		'home_main_posts_injected_ad_position',
		'home_main_posts_injected_newsletter_position'
	) );

	$paged  = boombox_get_paged();
	$offset = $query->get( 'offset' );
	$query->set( 'posts_per_page', $options_set['home_main_posts_posts_per_page'] );

	$tax_query       = array();
	$categories_args = boombox_categories_args( $options_set['home_main_posts_category'] );
	if ( $categories_args ) {
		$tax_query[] = $categories_args;
	}
	$tags_args = boombox_tags_args( $options_set['home_main_posts_tags'] );
	if ( $tags_args ) {
		$tax_query[] = $tags_args;
	}
	if ( $tax_query ) {
		$query->set( 'tax_query', $tax_query );
	}

	if ( $options_set['home_featured_area_exclude_from_main_loop'] ) {
		if ( boombox_is_fragment_cache_enabled() ) {
			?>
			<!-- mfunc <?php echo W3TC_DYNAMIC_SECURITY; ?>
                $featured_area = wp_is_mobile() ? ( boombox_get_theme_option(
                'mobile_global_enable_featured_area' ) && ( boombox_get_theme_option( 'home_featured_area_type' ) !=
                'disable' ) ) :
                 ( boombox_get_theme_option( 'home_featured_area_type' ) != 'disable' );
		    -->
			<?php
			$featured_area = wp_is_mobile() ? ( $options_set['mobile_global_enable_featured_area'] &&
			                                    ( $options_set['home_featured_area_type'] != 'disable' ) ) : ( $options_set['home_featured_area_type'] != 'disable' );
			?>
			<!-- /mfunc <?php echo W3TC_DYNAMIC_SECURITY; ?> -->
			<?php
		} elseif ( boombox_is_page_cache_enabled() ) {
			$featured_area = ( $options_set['home_featured_area_type'] != 'disable' );
		} else {
			$featured_area = wp_is_mobile() ? ( $options_set['mobile_global_enable_featured_area'] &&
			                                    ( $options_set['home_featured_area_type'] != 'disable' ) ) : ( $options_set['home_featured_area_type'] != 'disable' );
		}

		/**
		 * Exclude featured area posts
		 */
		if ( $featured_area ) {
			$excluded_posts         = array();
			$boombox_featured_query = Boombox_Template::init( 'featured-area' )->get_query();
			if ( null != $boombox_featured_query && $boombox_featured_query->found_posts ) {
				$excluded_posts = array_merge( $excluded_posts, wp_list_pluck( $boombox_featured_query->posts, 'ID' ) );
			}

			if ( ! empty( $excluded_posts ) ) {
				$query->set( 'post__not_in', $excluded_posts );
			}
		}
	}

	$condition          = boombox_get_theme_option( 'home_main_posts_condition' );
	$listing_conditions = Boombox_Choices_Helper::get_instance()->get_conditions();
	if (
		isset( $_GET['order'] )
		&& ! boombox_get_theme_option( 'archive_header_disable' )
		&& array_key_exists( $_GET['order'], $listing_conditions ) ) {
		$condition = $_GET['order'];
	}

	if ( $condition != 'recent' ) {

		$time_range  = boombox_get_theme_option( 'home_main_posts_time_range' );
		$posts_query = boombox_get_posts_query(
			$condition,
			$time_range,
			array(
				'category' => $options_set['home_main_posts_category'],
				'tag'      => $options_set['home_main_posts_tags'],
				'reaction' => array(),
			),
			array(
				'posts_per_page' => $query->get( 'posts_per_page' ),
				'paged'          => $paged,
				'excluded_posts' => $query->get( 'post__not_in' ),
				'is_page_query'  => false,
			) );

		$orderby = $posts_query->get( 'orderby' );
		$order   = $posts_query->get( 'order' );
		if ( ! is_array( $orderby ) ) {
			$orderby = array( $orderby => $order );
		}
		$query->set( 'orderby', $orderby );

		$meta_key = $posts_query->get( 'meta_key' );
		if ( $meta_key ) {
			$query->set( 'meta_query', array(
				'relation' => 'OR',
				array(
					'key'     => $meta_key,
					'compare' => 'NOT EXISTS',
					'value'   => 0
				),
				array(
					'key'     => $meta_key,
					'compare' => 'EXISTS'
				),
			) );
			$orderby = array_merge( array( 'meta_value_num' => 'DESC' ), $orderby );

			$query->set( 'orderby', $orderby );
		}
	}

	do_action( 'boombox/index_template_query', array( &$query ) );

	$is_adv_enabled        = boombox_is_adv_enabled( $options_set['home_main_posts_inject_ad'] );
	$is_newsletter_enabled = boombox_is_newsletter_enabled( $options_set['home_main_posts_inject_newsletter'] );
	$is_product_enabled    = boombox_is_product_enabled( $options_set['home_main_posts_inject_products'] );

	if ( $is_adv_enabled || $is_newsletter_enabled || $is_product_enabled ) {
		$archive_listing_type = $options_set['home_main_posts_listing_type'];
		$instead_ad           = $options_set['home_main_posts_injected_ad_position'];
		$instead_newsletter   = $options_set['home_main_posts_injected_newsletter_position'];

		Boombox_Loop_Helper::init( array(
			'is_adv_enabled'        => $is_adv_enabled,
			'instead_adv'           => $instead_ad,
			'is_newsletter_enabled' => $is_newsletter_enabled,
			'instead_newsletter'    => $instead_newsletter,
			'is_product_enabled'    => $is_product_enabled,
			'page_product_position' => $options_set['home_main_posts_injected_products_position'],
			'page_product_count'    => $options_set['home_main_posts_injected_products_count'],
			'skip'                  => ( 'grid' == $archive_listing_type ),
			'posts_per_page'        => $options_set['home_main_posts_posts_per_page'],
			'paged'                 => $paged,
			'offset'                => $offset
		) );
	}
}

add_action( 'pre_get_posts', 'boombox_edit_index_template_query', 1 );

/**
 * Remove Admin Bar
 */
function boombox_remove_admin_bar() {
	$condition                   = ! is_super_admin() && ! current_user_can( 'administrator' );
	$admin_bar_removal_condition = apply_filters( 'boombox/admin_bar_removal_condition', $condition );
	if ( $admin_bar_removal_condition ) {
		add_filter( 'show_admin_bar', '__return_false' );
	}
}

add_action( 'wp', 'boombox_remove_admin_bar' );

/**
 * Hide featured media on specific posts that are assigned to categories or tags which prevent media showing
 */
function boombox_single_post_show_media_taxonomy_restriction( $show ) {

	if ( $show ) {
		$categories = wp_get_post_categories( get_the_ID(), array(
			'fields'     => 'ids',
			'meta_query' => array(
				array(
					'key'     => 'hide_attached_posts_featured_media',
					'value'   => 1,
					'compare' => '='
				)
			),
		) );

		$tags = wp_get_post_tags( get_the_ID(), array(
			'fields'     => 'ids',
			'meta_query' => array(
				array(
					'key'     => 'hide_attached_posts_featured_media',
					'value'   => 1,
					'compare' => '='
				)
			),
		) );

		$show = ( empty( $categories ) && empty( $tags ) );
	}

	return $show;
}

add_filter( 'boombox/single/show_media', 'boombox_single_post_show_media_taxonomy_restriction', 10, 1 );


/**
 * Get pagination item URL
 *
 * @param int $i Page
 *
 * @return string
 */
function _boombox_link_page( $i ) {
	global $wp_rewrite;
	$post       = get_post();
	$query_args = array();

	if ( 1 == $i ) {
		$url = get_permalink();
	} else {
		if ( '' == get_option( 'permalink_structure' ) || in_array( $post->post_status, array(
				'draft',
				'pending'
			) ) ) {
			$url = add_query_arg( 'page', $i, get_permalink() );
		} elseif ( 'page' == get_option( 'show_on_front' ) && get_option( 'page_on_front' ) == $post->ID ) {
			$url = trailingslashit( get_permalink() ) . user_trailingslashit( "$wp_rewrite->pagination_base/" . $i, 'single_paged' );
		} else {
			$url = trailingslashit( get_permalink() ) . user_trailingslashit( $i, 'single_paged' );
		}
	}

	if ( is_preview() ) {
		if ( ( 'draft' !== $post->post_status ) && isset( $_GET['preview_id'], $_GET['preview_nonce'] ) ) {
			$query_args['preview_id']    = wp_unslash( $_GET['preview_id'] );
			$query_args['preview_nonce'] = wp_unslash( $_GET['preview_nonce'] );
		}
		$url = get_preview_post_link( $post, $query_args, $url );
	}

	return esc_url( $url );
}

/**
 * Callback to edit singular post link pages args
 *
 * @param string $output Current content
 * @param array  $args   Pagination arguments
 *
 * @return string
 * @since   2.5.0
 * @version 2.5.0
 */
function boombox_on_hook_wp_link_pages( $output, $args ) {

	if ( is_singular( array( 'post', 'page' ) ) ) {

		global $page, $numpages, $multipage, $more;

		$defaults = array(
			'before'                          => '<p>' . esc_html__( 'Pages:', 'boombox' ),
			'after'                           => '</p>',
			'link_before'                     => '',
			'link_after'                      => '',
			'layout'                          => 'page_xy', // numeric, page_xy, next_xy
			'nextpagelink'                    => esc_html__( 'Next Page', 'boombox' ),
			'previouspagelink'                => esc_html__( 'Previous Page', 'boombox' ),
			'nextpostlink'                    => esc_html__( 'Next Post', 'boombox' ),
			'previouspostlink'                => esc_html__( 'Previous Post', 'boombox' ),
			'class'                           => '',
			'next'                            => true,
			'prev'                            => true,
			'in_same_term'                    => false,
			'excluded_terms'                  => '',
			'taxonomy'                        => 'category',
			'pagelink'                        => '%',
			'next_prev_posts'                 => false,
			'hide_disable_inactive_next_prev' => 'hide', // disable, hide
			'next_prev_direction'             => 'ASC', //ASC, DESC,
			'paging'                          => true,
			'prev_text_on_end'                => '',
			'next_text_on_end'                => '',
			'url_on_end'                      => '#',
			'end_size'                        => 1,
			'mid_size'                        => 2,
		);
		$params   = wp_parse_args( $args, $defaults );

		/**
		 * Filters the arguments used in retrieving page links for paginated posts.
		 *
		 * @param array $params An array of arguments for page links for paginated posts.
		 */
		$r = apply_filters( 'wp_link_pages_args', $params );

		$output        = '';
		$prev_post_obj = '';
		$next_post_obj = '';

		$prev = $page - 1;
		$next = $page + 1;

		$has_pagination = ( $multipage || $r['next_prev_posts'] );
		if ( ! $has_pagination ) {
			return $output;
		}

		$output .= $r['before'];

		if ( ( $prev <= 0 || $next > $numpages ) && $r['next_prev_posts'] && ! is_singular( 'page' ) ) {
			$prev_post_obj = get_adjacent_post( $r['in_same_term'], $r['excluded_terms'], true, $r['taxonomy'] );
			$next_post_obj = get_adjacent_post( $r['in_same_term'], $r['excluded_terms'], false, $r['taxonomy'] );
		}

		// region Prev Post Link
		if ( $prev <= 0 && $r['prev'] ) {
			$list_class = 'pg-item';

			if ( is_singular( 'page' ) ) {
				if ( $multipage && ( 'page_xy' == $r['layout'] ) ) {
					$output .= sprintf( '<li class="%s page-nav prev-page"></li>', esc_attr( $list_class ) );
				}
			} else {
				$prev_post = $prev_post_obj;
				if ( 'ASC' == $r['next_prev_direction'] ) {
					$prev_post = $next_post_obj;
				}

				$renderable = true;
				if ( $prev_post ) {
					$url = get_permalink( $prev_post->ID );
				} else {
					$url        = '#';
					$list_class .= ' bb-disabled';
					$renderable = ( $r['hide_disable_inactive_next_prev'] != 'hide' );

					if ( $r['prev_text_on_end'] ) {
						$r['previouspostlink'] = $r['prev_text_on_end'];
					}
				}

				switch ( $r['layout'] ) {
					case 'page_xy':
						$link_html = $renderable ? '<a class="prev-page-link page-link" href="%s" rel="prev">%s</a>' : '';
						$template  = '<li class="%s page-nav prev-page">' . $link_html . '</li>';
						break;
					case 'numeric':
						$template = $renderable ? '<li class="%s"><a href="%s" class="prev page-numbers" rel="prev">%s</a></li>' : '';
						break;
					default:
						$template = '';
				}

				if ( $template ) {
					$link   = $r['link_before'] . $r['previouspostlink'] . $r['link_after'];
					$link   = sprintf( $template, esc_attr( $list_class ), esc_url( $url ), $link );
					$output .= apply_filters( 'wp_link_pages_link', $link, $prev );
				}
			}
		}
		// endregion

		// region Multipage
		if ( $multipage ) {

			// region Multipage - Prev Page Link
			if ( ( $prev > 0 ) && $r['prev'] ) {

				switch ( $r['layout'] ) {
					case 'page_xy':
						$template = '<li class="pg-item page-nav prev-page"><a class="prev-page-link page-link" href="%s" rel="prev">%s</a></li>';
						break;
					case 'numeric':
						$template = '<li class="pg-item"><a href="%s" class="prev page-numbers" rel="prev">%s</a></li>';
						break;
					default:
						$template = '';
				}

				if ( $template ) {
					$link   = $r['link_before'] . $r['previouspagelink'] . $r['link_after'];
					$link   = sprintf( $template, _boombox_link_page( $prev ), $link );
					$output .= apply_filters( 'wp_link_pages_link', $link, $prev );
				}
			}
			// endregion

			// page_xy
			if ( ( 'page_xy' == $r['layout'] ) && $r['paging'] ) {
				$current = $r['link_before'] . $page . $r['link_after'];
				$total   = $r['link_before'] . $numpages . $r['link_after'];
				$output  .= sprintf( '<li class="pg-item pages"><span class="cur-page">%d</span><span class="all-page"> / %d</span></li>', $current, $total );
			} elseif ( 'next_xy' == $r['layout'] ) {

				$replace = '';
				if ( $r['paging'] ) {
					$replace = sprintf( ' %d / %d', $page, $numpages );
				}
				$r['nextpagelink'] = str_replace( '{{xy}}', $replace, $r['nextpagelink'] );
			} // numeric
			elseif ( 'numeric' == $r['layout'] ) {

				$end_size = (int) $r['end_size']; // Out of bounds?  Make it the default.
				if ( $end_size < 1 ) {
					$end_size = 1;
				}
				$mid_size = (int) $r['mid_size'];
				if ( $mid_size < 0 ) {
					$mid_size = 2;
				}
				$dots = false;


				for ( $i = 1; $i <= $numpages; $i ++ ) {

					$link = $r['link_before'] . str_replace( '%', $i, $r['pagelink'] ) . $r['link_after'];
					if ( $i == $page ) {
						$link = '<li class="pg-item"><span class="page-numbers current">' . $link . '</span></li>';
					} else {
						if ( ( $i <= $end_size || ( $page && $i >= $page - $mid_size && $i <= $page + $mid_size ) || $i > $numpages - $end_size ) ) {
							$link = sprintf( '<li class="pg-item"><a href="%1$s" class="page-numbers">%2$s</a></li>', _boombox_link_page( $i ), $link );
							$dots = true;
						} elseif ( $dots ) {
							$link = '<li class="pg-item"><span class="page-numbers dots">' . __( '&hellip;' ) . '</span></li>';
							$dots = false;
						} else {
							$link = '';
						}
					}
					/**
					 * Filters the HTML output of individual page number links.
					 *
					 * @param string $link The page number HTML output.
					 * @param int    $i    Page number for paginated posts' page links.
					 */
					$link   = apply_filters( 'wp_link_pages_link', $link, $i );
					$output .= $link;
				}

			}

			// region Multipage - Next Page Link
			if ( ( $next <= $numpages ) && $r['next'] ) {

				switch ( $r['layout'] ) {
					case 'page_xy':
						$template = '<li class="pg-item page-nav next-page"><a class="next-page-link page-link" href="%s" rel="next">%s</a></li>';
						break;
					case 'next_xy':
						$template = '<li class="pg-item page-nav next-page"><a class="next-page-link page-link" href="%s" rel="next">%s</a></li>';
						break;
					case 'numeric':
						$template = '<li class="pg-item"><a href="%s" class="next page-numbers" rel="next">%s</a></li>';
						break;
					default:
						$template = '';
				}

				if ( $template ) {
					$link   = $r['link_before'] . $r['nextpagelink'] . $r['link_after'];
					$link   = sprintf( $template, _boombox_link_page( $next ), $link );
					$output .= apply_filters( 'wp_link_pages_link', $link, $next );
				}

			}
			// endregion

		}
		// endregion

		// region Next Post Link
		if ( $next > $numpages && $r['next'] ) {

			$list_class = 'pg-item';

			if ( is_singular( 'page' ) ) {
				if ( $multipage && ( 'page_xy' == $r['layout'] ) ) {
					$output .= sprintf( '<li class="%s page-nav next-page"></li>', esc_attr( $list_class ) );
				}
			} else {
				$next_post = $next_post_obj;
				if ( 'ASC' == $r['next_prev_direction'] ) {
					$next_post = $prev_post_obj;
				}

				$renderable = true;
				if ( $next_post ) {
					$url = get_permalink( $next_post->ID );
				} else {
					$url        = '#';
					$list_class .= ' bb-disabled';
					$renderable = ( $r['hide_disable_inactive_next_prev'] != 'hide' );

					if ( $r['next_text_on_end'] ) {
						$r['nextpostlink'] = $r['next_text_on_end'];
					}
				}

				switch ( $r['layout'] ) {
					case 'page_xy':
						$link_html = $renderable ? '<a class="next-page-link page-link" href="%s" rel="next">%s</a>' : '';
						$template  = '<li class="%s page-nav next-page">' . $link_html . '</li>';
						break;
					case 'next_xy':
						$template = $renderable ? '<li class="%s page-nav next-page"><a class="next-page-link page-link" href="%s" rel="next">%s</a></li>' : '';
						break;
					case 'numeric':
						$template = $renderable ? '<li class="%s"><a href="%s" class="next page-numbers" rel="next">%s</a></li>' : '';
						break;
					default:
						$template = '';
				}

				if ( $template ) {
					$link   = $r['link_before'] . $r['nextpostlink'] . $r['link_after'];
					$link   = sprintf( $template, esc_attr( $list_class ), esc_url( $url ), $link );
					$output .= apply_filters( 'wp_link_pages_link', $link, $prev );
				}
			}
		}
		// endregion

		$output .= $r['after'];

	}

	return $output;
}

add_filter( 'wp_link_pages', 'boombox_on_hook_wp_link_pages', 10, 2 );

/**
 * Change theme pagination html
 *
 * @param array $params Current args
 *
 * @return array
 * @since   2.5.0
 * @version 2.5.0
 */
function boombox_on_hook_wp_link_pages_args( $params ) {

	if ( is_singular( array( 'post', 'page' ) ) ) {

		switch ( $params['layout'] ) {
			case 'page_xy':
				$class     = 'bb-next-prev-pagination ';
				$structure = '<i class="bb-icon bb-ui-icon-chevron-%s"></i><span class="text big-text">%s</span><span class="text small-text">%s</span>';

				$params['previouspagelink'] = sprintf( $structure, 'left', esc_html__( 'Previous Page', 'boombox' ), esc_html__( 'Previous', 'boombox' ) );
				$params['nextpagelink']     = sprintf( $structure, 'right', esc_html__( 'Next Page', 'boombox' ), esc_html__( 'Next', 'boombox' ) );

				$params['previouspostlink']    = sprintf( $structure, 'left', esc_html__( 'Previous Post', 'boombox' ), esc_html__( 'Previous', 'boombox' ) );
				$params['nextpostlink']        = sprintf( $structure, 'right', esc_html__( 'Next Post', 'boombox' ), esc_html__( 'Next', 'boombox' ) );
				$params['next_prev_direction'] = ( boombox_get_theme_option( 'single_post_general_navigation_direction' ) == 'to-newest' ) ? 'DESC' : 'ASC';

				break;
			case 'next_xy':
				$class     = 'bb-next-pagination ';
				$structure = '<i class="bb-icon bb-ui-icon-chevron-%s"></i><span class="text big-text">%s</span>';

				$params['nextpagelink'] = sprintf( $structure, 'right', esc_html( 'Page', 'boombox' ) . '{{xy}}' );
				$params['nextpostlink'] = sprintf( $structure, 'right', esc_html__( 'Next Post', 'boombox' ) );

				$params['prev']                = false;
				$params['next_prev_direction'] = ( boombox_get_theme_option( 'single_post_general_navigation_direction' ) == 'to-newest' ) ? 'DESC' : 'ASC';

				break;
			case 'numeric':
				$class = 'bb-wp-pagination ';

				$params['previouspagelink'] = esc_html__( 'Previous', 'boombox' );
				$params['nextpagelink']     = esc_html__( 'Next', 'boombox' );

				break;
			default:
				$class = '';
		}

		$class         .= $params['class'];
		$screen_reader = '<h2 class="screen-reader-text">' . esc_html__( 'Post Pagination', 'boombox' ) . '</h2>';

		$params['before'] = '<nav class="' . esc_attr( rtrim( $class ) ) . '">' . $screen_reader . '<ul class="pg-list">';
		$params['after']  = '</ul></nav>';

		$params = apply_filters( 'boombox/singular/wp_link_pages_args', $params );
	}

	return $params;
}

add_filter( 'wp_link_pages_args', 'boombox_on_hook_wp_link_pages_args', 10, 1 );

/**
 * Generate gallery HTML based on image IDs. Access private - should not be called outside
 *
 * @param array  $image_ids          Attachments IDs
 * @param string $preview_image_size Main image preview size
 *
 * @return string
 * @since   2.5.0
 * @version 2.5.0
 */
function _boombox_generate_gallery_html( $image_ids, $preview_image_size ) {

	static $galleries;
	if ( ! $galleries ) {
		$galleries = array();
	}

	$html = '';
	if ( ! empty( $image_ids ) ) {

		$attachments = array();
		foreach ( $image_ids as $image_id ) {
			$size_boombox_image1600 = wp_get_attachment_image( $image_id, 'boombox_image1600' );
			$boombox_image360x270   = wp_get_attachment_image( $image_id, 'boombox_image360x270' );
			if ( $size_boombox_image1600 && $boombox_image360x270 ) {

				$attachments[] = array(
					'id'                   => $image_id,
					'boombox_image1600'    => $size_boombox_image1600,
					'boombox_image360x270' => $boombox_image360x270,
				);
			}
		}

		if ( ! empty( $attachments ) ) {

			$gallery_id = 'post-gallery-' . substr( md5( json_encode( $attachments ) . $preview_image_size ), 0, 6 );
			$total      = count( $attachments );
			$title      = apply_filters( 'boombox/single_post/gallery/title', __( 'Gallery', 'boombox' ) );

			ob_start(); ?>
			<figure class="bb-post-gallery">
				<?php echo wp_get_attachment_image( $attachments[0]['id'], $preview_image_size ); ?>
				<span href="#<?php echo esc_attr( $gallery_id ); ?>_0" data-id="#<?php echo esc_attr( $gallery_id ); ?>" class="bb-gallery-link bb-js-gallery-link" data-class="post-gallery-lightbox">
					<i class="bb-icon bb-ui-icon-camera"></i>
					<div class="bb-gallery-text">
						<?php if ( $title ) { ?>
							<b><?php echo esc_html( $title ); ?></b><br>
						<?php } ?>
						<em><?php printf( _n( '%s image', '%s images', $total, 'boombox' ), $total ); ?></em>
					</div>
				</span>
			</figure>
			<?php
			$html = ob_get_clean();

			if ( ! in_array( $gallery_id, $galleries ) ) {
				$galleries[] = $gallery_id;
				add_action( 'wp_footer', function () use ( $gallery_id, $attachments, $total ) { ?>
					<div id="<?php echo esc_attr( $gallery_id ); ?>" class="bb-post-gallery-content">
						<div class="bb-gl-header">

							<?php if ( apply_filters( 'boombox/single_post/gallery_show_logo', true ) ) {
								$logo_url = boombox_get_theme_option( 'branding_logo_small' );
								if ( $logo_url ) { ?>
									<div class="bb-gl-logo">
										<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php bloginfo( 'name' ); ?>" />
									</div>
								<?php }
							}

							/***** Advertisement */
							boombox_the_advertisement( 'boombox-gallery-popup-header' ); ?>

							<a href="#" class="bb-gl-close bb-js-gl-close" rel="nofollow"><i class="bb-icon bb-ui-icon-close"></i></a>
						</div>
						<div class="bb-gl-body">
							<ul class="bb-gl-slide">
								<?php
								$i = 0;
								foreach ( $attachments as $attachment ) { ?>
									<li>
										<figure class="bb-gl-image">
											<div class="bb-gl-mode-switcher">
												<a href="#" class="bb-mode-switcher bb-js-mode-switcher" data-mode="grid" rel="nofollow"><i class="bb-icon bb-ui-icon-grid"></i></a>
												<a href="#" class="bb-mode-switcher bb-js-mode-switcher" data-mode="slide" rel="nofollow"><i class="bb-icon bb-ui-icon-square"></i></a>
											</div>
											<?php echo $attachment['boombox_image1600']; ?>
										</figure>
										<div class="bb-gl-image-text">
											<aside class="bb-gl-meta">
												<?php
												$prev_index = ( ( $i - 1 ) < 0 ) ? ( $total - 1 ) : ( $i - 1 );
												$next_index = ( ( $i + 1 ) >= $total ) ? 0 : ( $i + 1 );
												?>
												<a href="#<?php printf( '%s_%d', esc_attr( $gallery_id ), $prev_index ); ?>" class="bb-gl-arrow bb-gl-prev bb-js-slide" rel="nofollow"><i class="bb-icon bb-ui-icon-chevron-left"></i></a>
												<span class="bb-gl-pagination"><?php printf( '<b>%d</b>/%d', ( $i + 1 ), $total ); ?></span>
												<a href="#<?php printf( '%s_%d', esc_attr( $gallery_id ), $next_index ); ?>" class="bb-gl-arrow bb-gl-next bb-js-slide" rel="nofollow"><i class="bb-icon bb-ui-icon-chevron-right"></i></a>
											</aside>

											<?php
											$att_post         = get_post( $attachment['id'] );
											$show_image_title = apply_filters( 'boombox/single_post/gallery/show_image_title', true );
											if ( $show_image_title && $att_post->post_title ) { ?>
												<h5><?php echo $att_post->post_title; ?></h5>
											<?php }

											do_action( 'boombox_affiliate_content', array( 'post_id' => $att_post->ID ) );

											if ( $att_post->post_content ) {
												echo sprintf( '<p>%s</p>', $att_post->post_content );
											} ?>

										</div>
									</li>
									<?php ++ $i;
								} ?>
							</ul>
							<div class="bb-gl-grid">
								<div class="bb-gl-mode-switcher">
									<a href="#" class="bb-mode-switcher bb-js-mode-switcher" data-mode="grid" rel="nofollow"><i class="bb-icon bb-ui-icon-grid"></i></a>
									<a href="#" class="bb-mode-switcher bb-js-mode-switcher" data-mode="slide" rel="nofollow"><i class="bb-icon bb-ui-icon-square"></i></a>
								</div>
								<ul>
									<?php
									$i = 0;
									foreach ( $attachments as $attachment_id => $attachment ) { ?>
										<li>
											<a href="#<?php printf( '%s_%d', esc_attr( $gallery_id ), $i ); ?>" class="bb-gl-item bb-js-gl-item" rel="nofollow">
												<?php echo $attachment['boombox_image360x270']; ?>
											</a>
										</li>
										<?php ++ $i;
									} ?>
								</ul>
							</div>
						</div>
					</div>
					<?php
				} );
			}
		}
	}

	return $html;
}

/**
 * Get post gallery
 *
 * @param int|WP_Post|null $post Post to get gallery from
 *
 * @return string
 * @since   2.5.0
 * @version 2.5.0
 */
function boombox_get_post_gallery_html( $preview_image_size, $post = null ) {
	$post = get_post( $post );

	$ids = boombox_get_post_meta( $post->ID, 'boombox_post_gallery' );

	return _boombox_generate_gallery_html( (array) $ids, $preview_image_size );
}

/**
 * Callback to edit post gallery shortcode html
 *
 * @param string $html Current HTML
 * @param array  $attr Shortcode attributes
 *
 * @return string
 * @since   2.5.0
 * @version 2.5.0
 */
function boombox__on_hook__post_gallery( $html, $attr, $instance ) {

	$post = get_post();
	if ( ! isset( $attr['bb_is_widget'] ) && ( 'gallery' === get_post_format( $post ) ) && apply_filters( 'boombox/allow_gallery_html_modification', true ) ) {

		$atts = shortcode_atts( array(
			'order'   => 'ASC',
			'orderby' => 'menu_order ID',
			'id'      => $post ? $post->ID : 0,
			'columns' => 3,
			'size'    => 'boombox_image768',
			'include' => '',
			'exclude' => '',
		), $attr, 'gallery' );

		$cache_key = md5( json_encode( $atts ) );
		$ids       = boombox_cache_get( $cache_key );

		if ( false === $ids ) {
			$parent_id = intval( $atts['id'] );

			if ( ! empty( $atts['include'] ) ) {
				$attachments = get_posts( array(
					'include'        => $atts['include'],
					'post_status'    => 'inherit',
					'post_type'      => 'attachment',
					'post_mime_type' => 'image',
					'order'          => $atts['order'],
					'orderby'        => $atts['orderby']
				) );
			} elseif ( ! empty( $atts['exclude'] ) ) {
				$attachments = get_children( array(
					'post_parent'    => $parent_id,
					'exclude'        => $atts['exclude'],
					'post_status'    => 'inherit',
					'post_type'      => 'attachment',
					'post_mime_type' => 'image',
					'order'          => $atts['order'],
					'orderby'        => $atts['orderby']
				) );
			} else {
				$attachments = get_children( array(
					'post_parent'    => $parent_id,
					'post_status'    => 'inherit',
					'post_type'      => 'attachment',
					'post_mime_type' => 'image',
					'order'          => $atts['order'],
					'orderby'        => $atts['orderby']
				) );
			}

			$ids = array();
			if ( ! empty( $attachments ) ) {
				$ids = wp_list_pluck( $attachments, 'ID' );
			}
			boombox_cache_set( $cache_key, $ids );
		}

		$html .= _boombox_generate_gallery_html( $ids, $atts['size'] );

	}

	return $html;
}

add_filter( 'post_gallery', 'boombox__on_hook__post_gallery', 10, 3 );

/**
 * Setup theme identifier to be able to face gallery called from media gallery widget
 *
 * @param array $instance Widget arguments instance
 *
 * @return array
 * @since   2.5.8.2
 * @version 2.5.8.2
 */
function boombox_setup_media_gallery_theme_identifier( $instance ) {
	return array_merge( $instance, array( 'bb_is_widget' => true ) );
}

add_filter( 'widget_media_gallery_instance', 'boombox_setup_media_gallery_theme_identifier', 10, 1 );

/**
 * Setup filter to remove hentry class
 */
function boombox_setup_hentry_removal_filter() {
	add_filter( 'post_class', 'boombox_remove_editor_article_classes', 999, 3 );
}

add_action( 'boombox/loop-item/before-content', 'boombox_setup_hentry_removal_filter' );
add_action( 'boombox/before_page_content', 'boombox_setup_hentry_removal_filter' );

/**
 * Remove back remove hentry class removal filter
 */
function boombox_remove_hentry_removal_filter() {
	remove_filter( 'post_class', 'boombox_remove_editor_article_classes', 999 );
}

add_action( 'boombox/loop-item/after-content', 'boombox_remove_hentry_removal_filter' );
add_action( 'boombox/after_page_content', 'boombox_remove_hentry_removal_filter' );

/**
 * Attach filter that removes "hentry" from templates
 *
 * @param string $template Current template
 *
 * @since   2.5.5
 * @version 2.5.5
 */
function boombox_attach_hentry_removal_filter_for_templates( $template ) {
	if ( 'single' == $template ) {
		boombox_setup_hentry_removal_filter();
	}
}

add_action( 'boombox/before_template_content', 'boombox_attach_hentry_removal_filter_for_templates', 10 );

/**
 * Remove filter that removes "hentry" from templates
 *
 * @param string $template Current template
 *
 * @since   2.5.5
 * @version 2.5.5
 */
function boombox_remove_hentry_removal_filter_from_templates( $template ) {
	if ( 'single' == $template ) {
		boombox_remove_hentry_removal_filter();
	}
}

add_action( 'boombox/after_template_content', 'boombox_remove_hentry_removal_filter_from_templates', 10 );

/***
 * Theme mode css url, which is dependent to value of "Design Color Mode"
 *
 * @return string
 */
function get_theme_mode_css_url() {
	$coloring_mode = boombox_get_theme_option( 'design_color_mode' );

	if ( 'light' == $coloring_mode ) {
		$url = get_template_directory_uri() . '/css/night-mode.min.css';
	} else {
		$url = get_template_directory_uri() . '/css/day-mode.min.css';
	}

	return $url;
}

/***
 * Render script for "Design Color Mode"
 */
function render_script() {
	$url = get_theme_mode_css_url();
	?>
	<script type="text/javascript">
		(function( $ ) {

			var themeModeStylesheetURL = '<?php echo $url; ?>';
			var getCookie = function( cname ) {
				var name = cname + "=";
				var decodedCookie = decodeURIComponent( document.cookie );
				var ca = decodedCookie.split( ';' );
				for ( var i = 0; i < ca.length; i++ ) {
					var c = ca[i];
					while ( c.charAt( 0 ) == ' ' ) {
						c = c.substring( 1 );
					}
					if ( c.indexOf( name ) == 0 ) {
						return c.substring( name.length, c.length );
					}
				}
				return "";
			};
			var setCookie = function( cname, cvalue, exdays ) {
				var d = new Date();
				d.setTime( d.getTime() + (exdays * 24 * 60 * 60 * 1000) );
				var expires = "expires=" + d.toUTCString();
				document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
			};
			var delete_cookie = function( cname ) {
				setCookie( cname, null, 0 );
			};
			var appendThemeModeLink = function() {
				var link = document.createElement( 'link' );
				var targetNode = document.getElementById( 'boombox-primary-style-inline-css' );
				link.rel = 'stylesheet';
				link.id = 'boombox-theme-mode-css';
				link.href = themeModeStylesheetURL;
				link.media = 'all';

				targetNode.parentNode.insertBefore( link, targetNode.nextSibling );
			};

			document.addEventListener( 'DOMContentLoaded', function() {
				var switchers = document.querySelectorAll( '.bb-mode-switcher .bb-mode-toggle' );

				for ( var i = 0; i < switchers.length; i++ ) {
					switchers[i].addEventListener( 'click', function(e) {
					    e.preventDefault();
						var stylesheet = document.getElementById( 'boombox-theme-mode-css' );
						if ( !!getCookie( 'boombox-theme-mode' ) ) {
							if ( stylesheet ) {
								stylesheet.setAttribute( 'disabled', 'disabled' );
							}
                            document.body.classList.remove("boombox-mode-on");
							delete_cookie( 'boombox-theme-mode' );
						} else {
							if ( stylesheet ) {
								stylesheet.removeAttribute( 'disabled' );
							} else {
								appendThemeModeLink();
							}
                            document.body.classList.add("boombox-mode-on");
							setCookie( 'boombox-theme-mode', 1, 365 );
						}
					} );
				}

                if ( !!getCookie( 'boombox-theme-mode' ) ) {
                    document.body.classList.add("boombox-mode-on");
                }
			} );

			if ( !!getCookie( 'boombox-theme-mode' ) ) {

				var appendInterval = setInterval( function() {
					if ( document.getElementById( 'boombox-primary-style-inline-css' ) ) {
						clearInterval( appendInterval );
						appendThemeModeLink();
					}
				}, 25 );
			}

		})();
	</script><?php

}

add_action( 'wp_head', 'render_script', 1 );