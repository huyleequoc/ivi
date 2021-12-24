<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! defined( 'BOOMBOX_CHILD_THEME_PATH' ) ) {
	define( 'BOOMBOX_CHILD_THEME_PATH', trailingslashit( get_stylesheet_directory() ) );
}
if ( ! defined( 'BOOMBOX_CHILD_THEME_URL' ) ) {
	define( 'BOOMBOX_CHILD_THEME_URL', trailingslashit( get_stylesheet_directory_uri() ) );
}

if ( ! defined( 'BOOMBOX_CHILD_INCLUDES_PATH' ) ) {
	define( 'BOOMBOX_INCLUDES_PATH', trailingslashit( BOOMBOX_CHILD_THEME_PATH . 'includes' ) );
}
if ( ! defined( 'BOOMBOX_CHILD_INCLUDES_URL' ) ) {
	define( 'BOOMBOX_INCLUDES_URL', BOOMBOX_CHILD_THEME_URL . 'includes/' );
}

function boombox_rewrite_rule(){
	add_rewrite_rule('world/?', 'index.php?world=local' ,' top');
}
add_action('init', 'boombox_rewrite_rule', 10, 0);

function custom_query_vars($query_vars){
    $query_vars[] = 'world';
    return $query_vars;
}
add_filter('query_vars', 'custom_query_vars');

add_action( 'wp_enqueue_scripts', 'boombox_child_enqueue_styles', 100 );
function boombox_child_enqueue_styles() {
	wp_enqueue_style( 'boombox-child-style', get_stylesheet_uri(), array(), boombox_get_assets_version() );
	wp_enqueue_style( 'boombox-child-style', get_stylesheet_uri(), array(), boombox_get_assets_version() );
}

//save usermeta first_login on register
add_action( 'user_register', 'boombox_register', 10, 2 );

function boombox_register( $user_id, $userdata ) {
    update_user_meta( $user_id, 'boombox_first_login', true );
}

//save categories_favorite usermeta
add_shortcode( 'boombox-all-categories', 'boombox_all_categories' );
function boombox_all_categories() {
	//get all cateogries
	$categories = get_categories();
	if( empty( $categories ) ) {
		return 'We are updating data...';
	}
	$user_world = (array)json_decode( get_user_meta(  get_current_user_id(), 'boombox_user_world', true ) );

	if( empty( $user_world ) || empty( $user_world[ 'cat' ] ) ) {
		$user_world = [ 'cat' => [], 'tag' => [] ];
	}
	$tags = get_tags();

	echo '<form class="bb-all-categories" method="post"><div class="row">';
	echo '<h4>Choose your favorites category: </h4>';
	foreach ( $categories as $category ) {
		$checked = '';
		if( in_array( $category->term_id, $user_world[ 'cat' ] ) ) $checked = 'checked';
		echo '<div class="col-md-6">
			<div class="form-group row">
				<input class="form-check-input" type="checkbox" name="user-cat[]" ' . $checked . ' value="' . $category->term_id . '">
				<label class="form-check-label d-inline text-info" for="gridCheck1">' . $category->name . '</label>
			</div>
		</div>';
	}
	echo '<hr><h4>Choose your favorites tag: </h4>';
	foreach ( $tags as $tag ) {
		$checked = '';
		if( in_array( $tag->term_id, $user_world[ 'tag' ] ) ) $checked = 'checked';
		echo '<div class="col-md-6">
			<div class="form-group row">
				<input class="form-check-input" type="checkbox" name="user-tag[]" ' . $checked . ' value="' . $tag->term_id . '">
				<label class="form-check-label d-inline text-info" for="gridCheck1">' . $tag->name . '</label>
			</div>
		</div>';
	}
	echo '</div><div class="col-12"><div class="form-group row text-center"><button type="submit" class="btn btn-primary">Submit</button></div></div>';
	echo '</form>';

	//save selected categories
	if( isset( $_POST['user-cat'] ) && isset( $_POST['user-tag'] ) ) {
		$user_world = [
			'cat' => $_POST['user-cat'],
			'tag' => $_POST['user-tag'],
		];

		update_user_meta( get_current_user_id(), 'boombox_user_world', sanitize_text_field( json_encode( $user_world ) ) );

		// //redirect to homepage
		wp_redirect( home_url( '/' ) );
		exit();
	}
}

function boombox_world_page( $query ) {
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

	if ( get_query_var('world') && 'local' == get_query_var('world') ) {
		$user_world = json_decode( get_user_meta(  get_current_user_id(), 'boombox_user_world', true ) );
		$query->set( 'category__in', $user_world );
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

add_action( 'pre_get_posts', 'boombox_world_page', 1 );