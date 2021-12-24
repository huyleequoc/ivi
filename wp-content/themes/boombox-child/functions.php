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

add_action( 'wp_enqueue_scripts', 'boombox_child_enqueue_styles', 100 );
function boombox_child_enqueue_styles() {
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
	if ( empty( $categories ) ) {
		return 'We are updating data...';
	}
	$user_world = (array)json_decode( get_user_meta(  get_current_user_id(), 'boombox_user_world', true ) );

	if ( empty( $user_world ) || empty( $user_world[ 'cat' ] ) ) {
		$user_world = [ 'cat' => [], 'tag' => [] ];
	}
	$tags = get_tags();

	echo '<form class="bb-all-categories" method="post"><div class="row">';
	echo '<h4>Choose your favorites category: </h4>';
	foreach ( $categories as $category ) {
		$checked = '';
		if ( in_array( $category->term_id, $user_world[ 'cat' ] ) ) $checked = 'checked';
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
		if ( in_array( $tag->term_id, $user_world[ 'tag' ] ) ) $checked = 'checked';
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
	if ( isset( $_POST['user-cat'] ) && isset( $_POST['user-tag'] ) ) {
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

	$user_world = ( array )json_decode( get_user_meta(  get_current_user_id(), 'boombox_user_world', true ) );

	if ( ! empty( $user_world['tag'] ) ) {
		$query->set( 'tag__in', $user_world[ 'tag' ] );
	}
	if ( ! empty( $user_world['cat'] ) ) {
		$query->set( 'category__in', $user_world[ 'cat' ] );
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



function boombox_register_widget() {
	register_widget( 'boombox_widget' );
}
add_action( 'widgets_init', 'boombox_register_widget' );
class boombox_widget extends WP_Widget {
	function __construct() {
		parent::__construct(
		// widget ID
		'boombox_widget',
		// widget name
		__('Boombox World Widget', ' boombox_world_widget'),
		// widget description
		array( 'description' => __( 'Boombox World Widget', 'boombox_world_widget' ), )
		);
	}
	public function widget( $args, $instance ) {
		$categories = get_categories();
		if ( empty( $categories ) ) {
			$return = 'We are updating data...';
		}
		else {
			$user_world = (array)json_decode( get_user_meta(  get_current_user_id(), 'boombox_user_world', true ) );
			if ( empty( $user_world ) || empty( $user_world[ 'cat' ] ) ) {
				$user_world = [ 'cat' => [] ];
			}
			if ( empty( $user_world ) || empty( $user_world[ 'tag' ] ) ) {
				$user_world = ['tag' => [] ];
			}

			$return = 'Please update yout favorite...';
			if ( ! empty( $user_world['cat'] ) && ! empty( $user_world[ 'tag' ] ) ) {			
				$title = apply_filters( 'widget_title', $instance['title'] );
				$return = $args['before_widget'];
				if ( ! empty( $title ) ) {
					$return .= $args['before_title'] . $title . $args['after_title'];
				}
				$return .= __( 'User favorite categories', 'boombox_world_widget' );
				
				$return .= '<ul class="user-world">';
				foreach( $user_world[ 'cat' ] as $cat_id ) {
					$return .= '<li class="item category"><a href="' . get_category_link( $cat_id ) . '"><span class="icon love">' . $this->svg_love_icon() . '</span><span class="name">' . get_cat_name( $cat_id ) . '</span></a></li>';
				}
				$return .= '</ul>';
			}
			if ( ! empty( $categories ) ) {			
				$return .= __( 'Categories', 'boombox_world_widget' );

				$return .= '<ul class="user-world">';
				foreach( $categories as $cat ) {
					if ( in_array( $cat->term_id, $user_world[ 'cat' ]  ) ) continue;
					$return .= '<li class="item category"><a href="' . get_category_link( $cat->term_id ) . '"><span class="icon">' . $this->svg_love_icon() . '</span><span class="name">' . $cat->name . '</span></a></li>';
				}
				$return .= '</ul>';

			}
			$return .= $args['after_widget'];
		}
		echo $return;
	}
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'Default Title', 'boombox_world_widget' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
	}
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
	private function svg_love_icon() {
		return '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 width="20px" height="20px" viewBox="0 0 343.422 343.422" style="enable-background:new 0 0 343.422 343.422;"
	 xml:space="preserve">
	<g><g id="Artwork_15_"><g id="Layer_5_15_"><path d="M254.791,33.251c-46.555,0-76.089,51.899-83.079,51.899c-6.111,0-34.438-51.899-83.082-51.899
				c-47.314,0-85.947,39.021-88.476,86.27c-1.426,26.691,7.177,47.001,19.304,65.402c24.222,36.76,130.137,125.248,152.409,125.248
				c22.753,0,127.713-88.17,152.095-125.247c12.154-18.483,20.731-38.711,19.304-65.402
				C340.738,72.272,302.107,33.251,254.791,33.251"/></g></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
		</svg>';
	}
}