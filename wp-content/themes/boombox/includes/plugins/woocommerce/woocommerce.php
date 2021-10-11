<?php
/**
 * Woocommerce plugin functions
 *
 * @package BoomBox_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! boombox_plugin_management_service()->is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	return;
}

require_once 'customizer/customizer.php';

if ( ! class_exists( 'Boombox_Woocommerce' ) ) {

	final class Boombox_Woocommerce {

		private $injected_products = NULL;

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = NULL;

		/**
		 * Get instance
		 * @return Boombox_Woocommerce|null
		 */
		public static function get_instance () {

			if ( NULL == static::$_instance ) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * Boombox_Woocommerce constructor.
		 */
		private function __construct () {
			$this->hooks();

			do_action( 'boombox/wc/wakeup', $this );
		}

		/**
		 * A dummy magic method to prevent Boombox_Woocommerce from being cloned.
		 *
		 */
		public function __clone () {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Setup Hooks
		 */
		public function hooks () {

			if ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) {
				return;
			}

			boombox_hook_management_service()->reassign_callback_action(
				'woocommerce_output_related_products',
				'woocommerce_after_single_product_summary',
				'woocommerce_after_single_product',
				20,
				1
			);

			add_filter( 'boombox/allow_archive_query_modification', array( $this, 'prevent_archive_query_modification' ), 10, 1 );
			add_action( 'after_setup_theme', array( $this, 'setup_theme' ), 12 );
			add_filter( 'woocommerce_enqueue_styles', array( $this, 'enqueue_styles' ), 10, 1 );
			add_filter( 'body_class', array( $this, 'edit_body_classes' ), 20, 1 );
			add_filter( 'boombox/sidebar_id', array( $this, 'edit_primary_sidebar_id' ), 10, 1 );
			add_filter( 'woocommerce_product_reviews_tab_title', array( $this, 'product_reviews_tab_title' ), 10, 1 );
			add_filter( 'boombox/color_scheme_styles', array( $this, 'color_scheme_styles' ), 10, 1 );
			add_filter( 'woocommerce_product_review_comment_form_args', array( $this, 'product_review_comment_form_args' ), 10, 1 );

			add_action( 'widgets_init', array( $this, 'override_woocommerce_widgets' ), 15 );

			add_filter( 'boombox/woocommerce/product_inject_choices', array( $this, 'product_inject_choices' ), 10, 1 );

			add_action( 'woocommerce_single_product_summary', array( $this, 'single_product_categories' ), 10 );
			add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'edit_header_add_to_cart_fragment' ), 10, 1 );
			add_filter( 'boombox/customizer/fields/archive_main_posts', array( $this, 'add_inject_settings_to_customizer' ), 10, 3 );
			add_filter( 'boombox/customizer/fields/home_main_posts', array( $this, 'add_inject_settings_to_customizer' ), 10, 3 );
			add_filter( 'boombox/customizer_default_values', array( $this, 'edit_customizer_default_values' ), 10, 1 );
			add_filter( 'boombox/admin/post/meta-boxes/structure', array( $this, 'edit_page_metaboxes_structure' ), 10, 4 );
			add_filter( 'boombox/admin/post/meta-boxes/structure', array( $this, 'edit_aiom_page_meta_boxes' ), 9999, 4 );

			/** inject */
			add_action( 'boombox/loop-helper/product', array( $this, 'loop_helper_product' ), 10, 5 );
			add_action( 'boombox/loop-item/before-content', array( $this, 'loop_item_before_content' ), 10, 1 );
			add_action( 'boombox/loop-item/after-content', array( $this, 'loop_item_after_content' ), 10, 1 );
			add_action( 'boombox/loop-item/content-start', array( $this, 'loop_item_content_start_open_wrapper' ), 10 );
			add_action( 'boombox/loop-item/content-start', array( $this, 'loop_item_content_start_before_title' ), 20 );

			add_action( 'boombox/loop-item/content-end', array( $this, 'loop_item_content_end_add_product_features' ), 20 );
			add_action( 'boombox/loop-item/content-end', array( $this, 'loop_item_content_end_close_wrapper' ), 20 );
			
			add_filter( 'boombox/template_helpers_map', array( $this, 'edit_template_helpers_map' ), 10, 1 );
			add_filter( 'boombox/header_composition_component_choices', array( $this, 'edit_header_composition_component_choices' ), 10, 1 );
			add_filter( 'boombox/mobile_header_composition_component_choices', array( $this, 'edit_header_composition_component_choices' ), 10, 1 );
			add_action( 'boombox/header/render_composition_item/woocommerce-cart', array( $this, 'render_header_composition_item' ) );
			add_action( 'boombox/mobile/header/render_composition_item/woocommerce-cart', array( $this, 'render_header_composition_item' ) );
			add_filter( 'boombox/sidebar_id', array( $this, 'get_single_product_primary_sidebar' ), 10, 1 );
			add_filter( 'boombox/secondary-sidebar-id', array( $this, 'get_single_product_secondary_sidebar' ), 10, 1 );

			$template_with_actions_overwriting = array( 'content-single-product', 'cart/cart', 'content-product' );
			foreach ( $template_with_actions_overwriting as $template ) {
				$this->change_template_actions_order( $template );
			}

		}

		/**
		 * Add theme support for woocommerce
		 */
		public function setup_theme() {
			add_theme_support( 'woocommerce' );

			add_theme_support( 'wc-product-gallery-zoom' );
			add_theme_support( 'wc-product-gallery-lightbox' );
			add_theme_support( 'wc-product-gallery-slider' );
		}

		/**
		 * Deny archive template query modification for woocommerce templates
		 * @param bool $allow Current status
		 *
		 * @return bool
		 */
		public function prevent_archive_query_modification( $allow ) {
			if( is_shop() || is_product_taxonomy() ) {
				$allow = false;
			}

			return $allow;
		}

		/**
		 * Add theme styles
		 *
		 * @param $styles
		 *
		 * @return mixed
		 */
		public function enqueue_styles( $styles ) {

			$styles[ 'boombox-woocommerce' ] = array(
				'src'     => str_replace( array( 'http:', 'https:' ), '', BOOMBOX_THEME_URL ) . 'woocommerce/css/woocommerce' . ( is_rtl() ? '-rtl' : '' ) . '.min.css',
				'deps'    => 'woocommerce-general',
				'version' => boombox_get_assets_version(),
				'media'   => 'all',
			);

			return $styles;
		}

		/**
		 * Add body classes for different templates
		 *
		 * @param $classes
		 *
		 * @return array
		 */
		public function edit_body_classes( $classes ) {

			if ( is_product() ) {

				$sidebar_type = $this->get_single_product_sidebar_type();
				$sidebar_orientation = $this->get_single_product_sidebar_orientation();
				$sidebar_reverse = false;
				$classes[ 'sidebar_position' ] = boombox_get_body_classes_by_sidebar_type( $sidebar_type,
					$sidebar_orientation, $sidebar_reverse );

			} else if ( is_shop() || is_product_taxonomy() ) {

				$shop_page_id = wc_get_page_id( 'shop' );

				$sidebar_type = boombox_get_post_meta( $shop_page_id, 'boombox_sidebar_type' );
				if( ! $sidebar_type ) {
					$sidebar_type = '1-sidebar-1_3';
				}
				$sidebar_orientation = boombox_get_post_meta( $shop_page_id, 'boombox_sidebar_orientation' );
				if( ! $sidebar_orientation ) {
					$sidebar_orientation = 'right';
				}
				$sidebar_reverse = false;

				$classes[ 'sidebar_position' ] = boombox_get_body_classes_by_sidebar_type( $sidebar_type,
					$sidebar_orientation, $sidebar_reverse );

			}

			return $classes;
		}

		/**
		 * Modify sidebar id
		 *
		 * @param $sidebar_id
		 *
		 * @return mixed
		 */
		public function edit_primary_sidebar_id( $sidebar_id ) {
			if ( is_woocommerce() ) {
				$shop_page_id = wc_get_page_id( 'shop' );

				$boombox_sidebar_id = boombox_get_post_meta( $shop_page_id, 'boombox_primary_sidebar' );
				$sidebar_id = empty( $boombox_sidebar_id ) ? $sidebar_id : $boombox_sidebar_id;
			}

			return $sidebar_id;
		}

		/**
		 * Parse review tab count html
		 *
		 * @param $title
		 *
		 * @return string
		 */
		public function product_reviews_tab_title( $title ) {
			preg_match_all( '/\((.*?)\)/', $title, $matches );
			$title = strtr( $title, array( $matches[ 0 ][ 0 ] => sprintf( '<span class="count"> %s</span>', $matches[ 1 ][ 0 ] ) ) );

			return $title;
		}

		/**
		 * Color scheme support
		 *
		 * @param $css
		 *
		 * @return string
		 * See boombox_global_style_css() for available colors
		 */
		public function color_scheme_styles( $css ) {
			$css .= '
                /* * Woocommerce specific styles * */

                /* -base text color */
                .woocommerce, .woocommerce .variations label, .woocommerce-checkout ul.payment_methods label,
                .woocommerce .widget .buttons a:nth-child(1), .woocommerce .widget .buttons a:nth-child(1):focus, .woocommerce .widget .buttons a:nth-child(1):hover,
                .woocommerce .widget .price_slider_amount .button, .woocommerce .widget .price_slider_amount .button:focus, .woocommerce .widget .price_slider_amount .button:hover,
                .woocommerce div.product .woocommerce-variation-price ins .amount, .woocommerce div.product .woocommerce-variation-price span.price>.amount,
                .woocommerce div.product p.price,
                .woocommerce-account .woocommerce-MyAccount-content .woocommerce-Address-title .edit, .woocommerce-account .woocommerce-MyAccount-content p a, .woocommerce-account .woocommerce-MyAccount-content table a, .woocommerce-account .woocommerce-MyAccount-navigation a {
                    color: %8$s;
                }

                /* -content bg color */
                @media screen and (max-width: 768px) {
                    .woocommerce table.shop_table_responsive.cart tbody tr.cart_item:nth-child(2n) td, .woocommerce-page table.shop_table_responsive.cart tbody tr.cart_item:nth-child(2n) td {
                        background-color: %5$s;
                    }
                }
                .woocommerce .cart-totals-col .cart_totals .shipping-calculator-form, .woocommerce-page .cart-totals-col .cart_totals .shipping-calculator-form,
                .woocommerce div.product div.images .woocommerce-product-gallery__trigger,
                .bb-cards-view.woocommerce.single-product div.product, .bb-cards-view.woocommerce .products .product {
                    background-color: %5$s;
                }

                /* -primary color and bg */
                .woocommerce a.button, .woocommerce a.button:hover, .woocommerce a.button:focus,
                .woocommerce input.button, .woocommerce input.button:hover, .woocommerce input.button:focus,
                .woocommerce button.button, .woocommerce button.button:hover, .woocommerce button.button:focus,
                .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt,
                .woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover,
                .woocommerce #respond input#submit.alt:focus, .woocommerce a.button.alt:focus, .woocommerce button.button.alt:focus, .woocommerce input.button.alt:focus,
                .woocommerce .button.alt.single_add_to_cart_button, .woocommerce .button.alt.single_add_to_cart_button:hover, .woocommerce .button.alt.single_add_to_cart_button:focus,
                .woocommerce .button.alt.single_add_to_cart_button.disabled, .woocommerce .button.alt.single_add_to_cart_button.disabled:hover, .woocommerce .button.alt.single_add_to_cart_button.disabled:focus,
                .woocommerce .widget_price_filter .ui-slider .ui-slider-range,
                 div.pp_woocommerce a.pp_contract, div.pp_woocommerce a.pp_expand, div.pp_woocommerce .pp_close, div.pp_woocommerce a.pp_contract:hover, div.pp_woocommerce a.pp_expand:hover, div.pp_woocommerce .pp_close:hover,
                .woocommerce nav.woocommerce-pagination ul li .next:hover, .woocommerce nav.woocommerce-pagination ul li .prev:hover {
                    background-color: %6$s;
                }
                .woocommerce div.product .woocommerce-product-rating .star-rating span {
                    color: %6$s;
                }
                div.pp_woocommerce .pp_next:before, div.pp_woocommerce .pp_previous:before, div.pp_woocommerce .pp_arrow_next:before, div.pp_woocommerce .pp_arrow_previous:before {
                     color: %6$s!important;
                }
                .woocommerce-account .woocommerce-MyAccount-navigation li.is-active {
                	border-color: %6$s;
                }

                /* --primary text color */
                .woocommerce a.button, .woocommerce a.button:hover, .woocommerce input.button,.woocommerce button.button, .woocommerce button.button:hover, .woocommerce input.button:hover,
                .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt,
                .woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover,
                .woocommerce #respond input#submit.alt:focus, .woocommerce a.button.alt:focus, .woocommerce button.button.alt:focus, .woocommerce input.button.alt:focus,
                .woocommerce .button.alt.single_add_to_cart_button, .woocommerce .button.alt.single_add_to_cart_button:hover,
                .woocommerce .button.alt.single_add_to_cart_button.disabled, .woocommerce .button.alt.single_add_to_cart_button.disabled:hover,
                 div.pp_woocommerce a.pp_contract, div.pp_woocommerce a.pp_expand, div.pp_woocommerce .pp_close, div.pp_woocommerce a.pp_contract:hover, div.pp_woocommerce a.pp_expand:hover, div.pp_woocommerce .pp_close:hover {
                    color: %7$s;
                }
                div.pp_woocommerce a.pp_contract, div.pp_woocommerce a.pp_expand, div.pp_woocommerce .pp_close, div.pp_woocommerce a.pp_contract:hover, div.pp_woocommerce a.pp_expand:hover, div.pp_woocommerce .pp_close:hover {
                    color: %7$s!important;
                }

                /* --heading text color */
                .woocommerce .product-name a,
                .woocommerce .shop_table .shipping-calculator-button, .woocommerce .shop_table .shipping-calculator-button,
                .woocommerce ul.cart_list li a, .woocommerce ul.product_list_widget li a,
                .woocommerce .star-rating span,
                .widget_layered_nav a, .widget_layered_nav a:focus, .widget_layered_nav a:hover,
                .widget_product_categories a, .widget_product_categories a:focus, .widget_product_categories a:hover,
                .woocommerce.widget ul li.chosen a, .woocommerce .widget ul li.chosen a,
                .woocommerce #reviews #comments ol.commentlist li .meta strong,
                .woocommerce div.product div.images .woocommerce-product-gallery__trigger {
                    color: %10$s;
                }

                /* --secondary text color */
                .woocommerce .woocommerce-result-count, .woocommerce a.added_to_cart, .woocommerce table.shop_table .product-name p, .woocommerce .coupon span, .woocommerce .checkout_coupon span,
                .woocommerce .cart_totals table.shop_table_responsive tbody tr.cart-subtotal td:before,
                .woocommerce .create-account p, .woocommerce .woocommerce form.checkout_coupon span, .woocommerce form.login p,
                .product_list_widget li .quantity .amount,
                .widget_shopping_cart .total .txt, .widget_rating_filter ul li a, .widget_layered_nav .count, .product_list_widget .reviewer,
                .woocommerce .widget ul li.chosen a:before, .woocommerce.widget ul li.chosen a:before,
                .woocommerce div.product .woocommerce-review-link, .woocommerce .woocommerce-tabs .star-rating span, .woocommerce .reset_variations, .woocommerce div.product .stock,
                .woocommerce #reviews #comments ol.commentlist li .meta time, .woocommerce div.product #tab-description p, .woocommerce div.product #reviews .woocommerce-noreviews,
                .woocommerce div.product p.stars a.active, .woocommerce div.product p.stars a:hover, .woocommerce div.product p.stars a:focus, .woocommerce p.stars.selected a:not(.active):before,
                .woocommerce p.stars.selected a.active:before, .woocommerce p.stars:hover a:before,
                #order_review #payment .payment_box,
                .widget_product_categories .post_count {
                    color: %9$s;
                }
                /* Remove button */
                .widget_shopping_cart .cart_list li a.remove, .woocommerce a.remove {
                    color: %9$s!important;
                }
                .woocommerce a.remove:hover, .widget_shopping_cart .cart_list li a.remove:hover {
                    background: %9$s;
                    color: %5$s!important;
                }


                /* -border-color */
                .woocommerce table.shop_table td, .woocommerce table.shop_table tbody th, .woocommerce-cart .cart-collaterals .cart_totals tr th,
                .woocommerce table.shop_table_responsive.cart tbody tr.cart_item, .woocommerce-page table.shop_table_responsive.cart tbody tr.cart_item,
                .woocommerce form .form-row.woocommerce-validated .select2-container,
                 #order_review .woocommerce-checkout-review-order-table, #order_review #payment ul.payment_methods, .woocommerce-page .shop_table.customer_details,
                .woocommerce div.product form.cart .variations-wrapper, .woocommerce div.product #comments .comment_container, .woocommerce table.shop_attributes td, .woocommerce table.shop_attributes th,
                .woocommerce div.product .woocommerce-tabs,
                .woocommerce ul.cart_list li dl, .woocommerce ul.product_list_widget li dl {
                    border-color: %13$s;
                }
                hr {
                    background-color: %13$s;
                }

                /* -loader styling */
                .woocommerce .blockUI.blockOverlay:before, .woocommerce .loader:before {
                    border-color: %13$s;
                }

                /* -secondary components bg color */
                .woocommerce .cart-totals-col .cart_totals, .woocommerce-page .cart-totals-col .cart_totals, #order_review .woocommerce-checkout-review-order-table tfoot,
                .woocommerce-page .shop_table.order_details tfoot,
                #order_review #payment .payment_box,
                .woocommerce .widget .buttons a:nth-child(1), .woocommerce .widget .buttons a:nth-child(1):focus, .woocommerce .widget .buttons a:nth-child(1):hover,
                .woocommerce .widget .price_slider_amount .button, .woocommerce .widget .price_slider_amount .button:focus, .woocommerce .widget .price_slider_amount .button:hover,
                .woocommerce .widget_price_filter .price_slider_wrapper .ui-widget-content, .woocommerce .widget_price_filter .ui-slider .ui-slider-handle,
                .woocommerce div.product table.shop_attributes {
                    background-color: %14$s;
                }
                #order_review #payment .payment_box:before {
                    border-color: %14$s;
                }

                /* -secondary components bg color for woocommerce */
                @media screen and (max-width: 768px){
                    .woocommerce table.shop_table_responsive.cart tbody tr.cart_item td.product-remove, .woocommerce-page table.shop_table_responsive.cart tbody tr.cart_item td.product-remove {
                        background-color: %14$s;
                    }
                }

                /* -border-radius */
                .woocommerce .cart-totals-col .cart_totals, .woocommerce-page .cart-totals-col .cart_totals,
                .woocommerce table.shop_table_responsive.cart tbody tr.cart_item, .woocommerce-page table.shop_table_responsive.cart tbody tr.cart_item,
                #order_review .woocommerce-checkout-review-order-table, #order_review #payment ul.payment_methods,
                .woocommerce .cart-totals-col .cart_totals .shipping-calculator-form, .woocommerce-page .cart-totals-col .cart_totals .shipping-calculator-form,
                .woocommerce div.product table.shop_attributes,
                .bb-cards-view.woocommerce.single-product div.product, .bb-cards-view.woocommerce .products .product {
                -webkit-border-radius: %11$s;
                -moz-border-radius: %11$s;
                    border-radius: %11$s;
                }

                /* --border-radius inputs, buttons */
                .woocommerce a.button, .woocommerce input.button, .woocommerce button.button  {
                    -webkit-border-radius: %12$s;
                    -moz-border-radius: %12$s;
                    border-radius: %12$s;
                }
            ';

			return $css;
		}

		/**
		 * Edit single product comment form args
		 *
		 * @param $args
		 *
		 * @return array
		 */
		public function product_review_comment_form_args( $args ) {

			$args = boombox_get_comment_form_args();

			if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' ) {
				$rating_html = '<div class="comment-form-rating"><div class="input-field"><label for="rating">' . __( 'Your Rating', 'woocommerce' ) . '</label><select name="rating" id="rating" aria-required="true" required>
						<option value="">' . __( 'Rate&hellip;', 'woocommerce' ) . '</option>
						<option value="5">' . __( 'Perfect', 'woocommerce' ) . '</option>
						<option value="4">' . __( 'Good', 'woocommerce' ) . '</option>
						<option value="3">' . __( 'Average', 'woocommerce' ) . '</option>
						<option value="2">' . __( 'Not that bad', 'woocommerce' ) . '</option>
						<option value="1">' . __( 'Very Poor', 'woocommerce' ) . '</option>
					</select></div></div>';
				
				if( is_user_logged_in() ) {
					$args[ 'comment_field' ] = $rating_html . $args[ 'comment_field' ];
				} else {
					$args[ 'fields' ][ 'author' ] = $rating_html . $args[ 'fields' ][ 'author' ];
				}
			}

			return $args;
		}

		/**
		 * Overwrite some widgets
		 */
		public function override_woocommerce_widgets() {
			if ( class_exists( 'WC_Widget_Recent_Reviews' ) ) {
				unregister_widget( 'WC_Widget_Recent_Reviews' );

				include_once( 'widgets/class-wc-widget-recent-reviews.php' );

				register_widget( 'Boombox_WC_Widget_Recent_Reviews' );
			}

			if ( class_exists( 'WC_Widget_Price_Filter' ) ) {
				unregister_widget( 'WC_Widget_Price_Filter' );

				include_once( 'widgets/class-wc-widget-price-filter.php' );

				register_widget( 'Boombox_WC_Widget_Price_Filter' );
			}
		}

		/**
		 * Setup choices for product injection
		 *
		 * @param $choices
		 *
		 * @return array
		 */
		public function product_inject_choices( $choices ) {
			return array_merge( (array)$choices, array(
				'inject_into_posts_list' => esc_html__( 'Inject Into Posts List', 'boombox' ),
				'none'                   => esc_html__( 'None', 'boombox' ),
			) );
		}

		/**
		 * Change register actions registration order
		 *
		 * @param array $actions_data
		 */
		private function change_registered_actions_order( $actions_data = array() ) {
			foreach ( $actions_data as $action_data ) {
				$action = ( isset( $action_data[ 'action' ] ) && $action_data[ 'action' ] ) ? $action_data[ 'action' ] : false;
				$function = ( isset( $action_data[ 'function' ] ) && $action_data[ 'function' ] ) ? $action_data[ 'function' ] : false;
				$old_priority = ( isset( $action_data[ 'old_priority' ] ) && $action_data[ 'old_priority' ] ) ? $action_data[ 'old_priority' ] : 10;
				$new_priority = ( isset( $action_data[ 'new_priority' ] ) && $action_data[ 'new_priority' ] ) ? $action_data[ 'new_priority' ] : 10;
				$accepted_args = ( isset( $action_data[ 'accepted_args' ] ) && $action_data[ 'accepted_args' ] ) ? $action_data[ 'accepted_args' ] : 1;

				if ( ! $action || ! $function ) {
					continue;
				}
				$this->change_registered_action_order( $action, $function, $old_priority, $new_priority, $accepted_args );
			}
		}

		/**
		 * Change single action registration order
		 *
		 * @param     $action
		 * @param     $function
		 * @param int $old_priority
		 * @param int $new_priority
		 * @param int $accepted_args
		 */
		private function change_registered_action_order( $action, $function, $old_priority = 10, $new_priority = 10, $accepted_args = 1 ) {

			if ( $old_priority != $new_priority ) {
				remove_action( $action, $function, $old_priority );
				add_action( $action, $function, $new_priority, $accepted_args );
			}

		}

		/**
		 * Add/edit/remove template actions
		 *
		 * @param $template
		 */
		private function change_template_actions_order( $template ) {

			/** Single product */
			if ( $template === 'content-single-product' ) {

				$this->change_registered_actions_order( array(
					array(
						'action'       => 'woocommerce_single_product_summary',
						'function'     => 'woocommerce_template_single_rating',
						'old_priority' => 10,
						'new_priority' => 20,
					),
					array(
						'action'       => 'woocommerce_single_product_summary',
						'function'     => 'woocommerce_template_single_title',
						'old_priority' => 5,
						'new_priority' => 30,
					),
					array(
						'action'       => 'woocommerce_single_product_summary',
						'function'     => 'woocommerce_template_single_excerpt',
						'old_priority' => 20,
						'new_priority' => 40,
					),
					array(
						'action'       => 'woocommerce_single_product_summary',
						'function'     => 'woocommerce_template_single_price',
						'old_priority' => 10,
						'new_priority' => 50,
					),
					array(
						'action'       => 'woocommerce_single_product_summary',
						'function'     => 'woocommerce_template_single_add_to_cart',
						'old_priority' => 30,
						'new_priority' => 60,
					),
					array(
						'action'       => 'woocommerce_single_product_summary',
						'function'     => 'woocommerce_template_single_meta',
						'old_priority' => 40,
						'new_priority' => 70,
					),
					array(
						'action'       => 'woocommerce_single_product_summary',
						'function'     => 'woocommerce_template_single_sharing',
						'old_priority' => 50,
						'new_priority' => 80,
					),
				) );
			} // cart
			else if ( $template === 'cart/cart' ) {

				remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10 );

				add_action( 'boombox_woocommerce_cart_totals', 'woocommerce_cart_totals', 10 );
			} // content-product
			else if ( $template === 'content-product' ) {

				remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );

				add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 20 );
				add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'bw_before_shop_loop_item_title_open_wrapper' ), 20 );
				add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'single_product_categories' ), 20 );
				add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_rating', 20 );
				add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'bw_before_shop_loop_item_title_close_wrapper' ), 20 );
				add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_open', 20 );

			}

			// add possibility for modification
			do_action( 'boombox/woocommerce/actions-order/' . $template );
		}

		/**
		 * Display single product categories
		 */
		public function single_product_categories() {
			global $product;

			printf( '<div class="bb-cat-links">%s</div>', wc_get_product_category_list( $product->get_id(), ' ' ) );
		}

		/**
		 * Render shopping cart icon as header composition item
		 */
		public function render_header_composition_item() {

			$count = WC()->cart->get_cart_contents_count();
			printf( '
                <div class="header-item user-cart bb-icn-count">
                    <a class="icn-link bb-header-icon %1$s" href="%2$s" title="%3$s"><i class="bb-icon bb-ui-icon-shopping-bag"></i>%4$s</a>
                </div>',
				$count ? 'has-count' : '',
				wc_get_cart_url(),
				__( 'View your shopping cart', 'boombox' ),
				$count ? sprintf( '<span class="count">%d</span>', $count ) : ''
			);

		}

		/**
		 * Partially refresh shopping cart to place actual count of products
		 *
		 * @param $fragments
		 *
		 * @return mixed
		 */
		public function edit_header_add_to_cart_fragment( $fragments ) {

			$count = WC()->cart->get_cart_contents_count();

			$fragments[ 'div.user-cart .icn-link' ] = sprintf(
				'<a class="icn-link bb-header-icon %1$s" href="%2$s" title="%3$s"><i class="bb-icon bb-ui-icon-shopping-bag"></i>%4$s</a>',
				$count ? 'has-count' : '',
				wc_get_cart_url(),
				__( 'View your shopping cart', 'boombox' ),
				$count ? sprintf( '<span class="count">%d</span>', $count ) : ''
			);

			return $fragments;
		}

		/**
		 * Set a wrapper before shop loop item
		 */
		public function bw_before_shop_loop_item_title_open_wrapper() {
			echo '<div class="cat-rat-row">';
		}

		/**
		 * Close wrapper after shop loop item
		 */
		public function bw_before_shop_loop_item_title_close_wrapper() {
			echo '</div>';
		}

		/**
		 * Add extra fields to theme customizer
		 *
		 * @param array  $fields   Current fields
		 * @param string $section  Section ID
		 * @param array  $defaults Default values
		 *
		 * @return mixed
		 */
		public function add_inject_settings_to_customizer( $fields, $section, $defaults ) {
			$priority = false;
			$section_prefix = '';
			if ( $section == boombox_customizer_get_archive_main_posts_section_id() ) {
				$priority = 112;
				$section_prefix = 'archive_main_posts_';
			} else if ( $section == boombox_customizer_get_home_main_posts_section_id() ) {
				$priority = 132;
				$section_prefix = 'home_main_posts_';
			}

			if ( $priority ) {
				$fields = array_merge( $fields, array(
					array(
						'settings' => $section_prefix . 'inject_products',
						'label'    => esc_html__( 'Products', 'boombox' ),
						'section'  => $section,
						'type'     => 'select',
						'priority' => $priority,
						'default'  => $defaults[ $section_prefix . 'inject_products' ],
						'multiple' => 1,
						'choices'  => apply_filters( 'boombox/woocommerce/product_inject_choices', array() ),
					),
					array(
						'settings' => $section_prefix . 'injected_products_count',
						'label'    => esc_html__( 'Inject # Product(s)', 'boombox' ),
						'section'  => $section,
						'type'     => 'number',
						'priority' => $priority,
						'default'  => $defaults[ $section_prefix . 'injected_products_count' ],
						'choices'  => array(
							'min'  => 1,
							'step' => 1,
						),
						'active_callback'    => array(
							array(
								'setting'  => $section_prefix . 'inject_products',
								'value'    => 'none',
								'operator' => '!=',
							)
						),
					),
					array(
						'settings' => $section_prefix . 'injected_products_position',
						'label'    => esc_html__( 'Inject Product(s) After Every # Post', 'boombox' ),
						'section'  => $section,
						'type'     => 'number',
						'priority' => $priority,
						'default'  => $defaults[ $section_prefix . 'injected_products_position' ],
						'choices'  => array(
							'min'  => 1,
							'step' => 1,
						),
						'active_callback'    => array(
							array(
								'setting'  => $section_prefix . 'inject_products',
								'value'    => 'none',
								'operator' => '!=',
							)
						),
					),
				) );
			}

			return $fields;
		}

		/**
		 * Setup default values for customizer extra fields
		 *
		 * @param $values
		 *
		 * @return mixed
		 */
		public function edit_customizer_default_values( $values ) {
			$section_prefixes = array( 'archive_main_posts_', 'home_main_posts_' );
			foreach ( $section_prefixes as $prefix ) {
				$values[ $prefix . 'inject_products' ] = 'none';
				$values[ $prefix . 'injected_products_count' ] = 1;
				$values[ $prefix . 'injected_products_position' ] = 1;
			}

			return $values;
		}

		/**
		 * Start boombox-loop-helper
		 *
		 * @param array $placement_data Placement data
		 * @param int $current_page Current page number
		 * @param int $loop_index Current query loop index
		 * @param int $replaced_count Total count of replaced item in current query
		 */
		public function loop_helper_product( $placement_data, $query, $current_page, $loop_index, $replaced_count ) {
			if ( is_null( $this->injected_products ) ) {

				$posts_per_page = $query->get( 'posts_per_page' );
				if ( $posts_per_page != -1 ) {
					$posts_per_page += $replaced_count;
					$periods_count = floor( $posts_per_page / $placement_data[ 'position' ] );
					$offset = ( $current_page - 1 ) * $placement_data[ 'count' ] * $periods_count;
				} else {
					$periods_count = floor( $query->found_posts / $placement_data[ 'position' ] );
					$offset = 0;
				}
				$args = array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => $placement_data[ 'count' ] * $periods_count,
					'offset'         => $offset,
				);
				$query = new WP_Query( $args );
				$this->injected_products = $query->get_posts();
			}
			if ( ! empty( $this->injected_products ) ) {

				if ( boombox_plugin_management_service()->is_plugin_active( 'quick-adsense-reloaded/quick-adsense-reloaded.php' ) ) {
					global $quads_options;
					$quads_options[ 'post_types' ] = array_key_exists( 'post_types', $quads_options ) ? (array)$quads_options[ 'post_types' ] : array();
					if ( ! in_array( 'product', (array)$quads_options[ 'post_types' ] ) ) {
						$quads_options[ 'post_types' ][] = 'product';
					}
				}

				global $post;
				$post = array_shift( $this->injected_products );
			} else {
				global $post;
				$post = NULL;
			}
		}

		/**
		 * Manipulate loop-item content for injected product
		 *
		 * @param $layout
		 */
		public function loop_item_before_content( $layout ) {
			if ( get_post_type() != 'product' ) {
				return;
			}

			global $product;
			$product = new WC_Product( get_the_ID() );

			add_filter( 'boombox/loop-item/show-badges', array( $this, 'injected_product_hide_block' ), 10, 1 );
			add_filter( 'boombox/loop-item/show-box-index', array( $this, 'injected_product_hide_block' ), 10, 1 );
			add_filter( 'boombox/loop-item/show-post-vote-count', array( $this, 'injected_product_hide_block' ), 10, 1 );
			add_filter( 'boombox/loop-item/show-share-count', array( $this, 'injected_product_hide_block' ), 10, 1 );
			add_filter( 'boombox/loop-item/show-post-type-badges', array( $this, 'injected_product_hide_block' ), 10, 1 );
			add_filter( 'boombox/loop-item/show-categories', array( $this, 'injected_product_hide_block' ), 10, 1 );
			add_filter( 'boombox/loop-item/show-comments-count', array( $this, 'injected_product_hide_block' ), 10, 1 );
			add_filter( 'boombox/loop-item/show-post-author-meta', array( $this, 'injected_product_hide_block' ), 10, 1 );
			add_filter( 'boombox/loop-item/show-post-excerpt', array( $this, 'injected_product_hide_block' ), 10, 1 );

			remove_action( 'boombox_affiliate_content', 'boombox_render_affiliate_content', 10 );

			if ( in_array( $layout, array( 'content-classic', 'content-classic2', 'content-stream', 'content-mixed-classic' ) ) ) {
				remove_action( 'boombox/loop-item/content-end', array( $this, 'loop_item_content_end_add_product_features' ), 20 );
				add_action( 'boombox_affiliate_content', array( $this, 'loop_item_content_start_open_wrapper' ), 10 );
				add_action( 'boombox_affiliate_content', array( $this, 'loop_item_content_end_add_product_features' ), 10 );
			}

			if ( $layout == 'content-numeric-list' ) {
				add_action( 'boombox/loop-item/show-box-index', array( $this, 'injected_product_hide_block' ), 10, 1 );
			}
		}

		/**
		 * Revert back all manipulation for injected products
		 *
		 * @param $layout
		 */
		public function loop_item_after_content( $layout ) {
			if ( get_post_type() != 'product' ) {
				return;
			}

			global $product;
			unset( $product );

			remove_filter( 'boombox/loop-item/show-badges', array( $this, 'injected_product_hide_block' ), 10 );
			remove_filter( 'boombox/loop-item/show-box-index', array( $this, 'injected_product_hide_block' ), 10 );
			remove_filter( 'boombox/loop-item/show-post-vote-count', array( $this, 'injected_product_hide_block' ), 10 );
			remove_filter( 'boombox/loop-item/show-share-count', array( $this, 'injected_product_hide_block' ), 10 );
			remove_filter( 'boombox/loop-item/show-post-type-badges', array( $this, 'injected_product_hide_block' ), 10 );
			remove_filter( 'boombox/loop-item/show-categories', array( $this, 'injected_product_hide_block' ), 10 );
			remove_filter( 'boombox/loop-item/show-comments-count', array( $this, 'injected_product_hide_block' ), 10 );
			remove_filter( 'boombox/loop-item/show-post-author-meta', array( $this, 'injected_product_hide_block' ), 10 );
			remove_filter( 'boombox/loop-item/show-post-excerpt', array( $this, 'injected_product_hide_block' ), 10, 1 );

			add_action( 'boombox_affiliate_content', 'boombox_render_affiliate_content', 10 );

			if ( in_array( $layout, array( 'content-classic', 'content-classic2', 'content-stream' ) ) ) {
				remove_action( 'boombox_affiliate_content', array( $this, 'loop_item_content_end_add_product_features' ), 10 );
				remove_action( 'boombox_affiliate_content', array( $this, 'loop_item_content_start_open_wrapper' ), 10 );
				add_action( 'boombox/loop-item/content-end', array( $this, 'loop_item_content_end_add_product_features' ), 20 );
			}

			if ( $layout == 'content-numeric-list' ) {
				remove_action( 'boombox/loop-item/show-box-index', array( $this, 'injected_product_hide_block' ), 10, 1 );
			}
		}

		/**
		 * Hide loop-item block
		 *
		 * @param $show
		 *
		 * @return bool
		 */
		public function injected_product_hide_block( $show ) {
			$show = false;

			return $show;
		}

		/**
		 * Open woocommerce wrapper for injected product features
		 */
		public function loop_item_content_start_open_wrapper() {
			if ( get_post_type() != 'product' ) {
				return;
			}

			echo sprintf( '<div class="woocommerce product-affiliate">' );
		}

		/**
		 * Add rating and product categories for injected product
		 */
		public function loop_item_content_start_before_title() {
			if ( get_post_type() != 'product' ) {
				return;
			}

			$this->bw_before_shop_loop_item_title_open_wrapper();
			$this->single_product_categories();
			woocommerce_template_loop_rating();
			$this->bw_before_shop_loop_item_title_close_wrapper();
		}

		/**
		 * Render injected product features
		 */
		public function loop_item_content_end_add_product_features() {
			if ( get_post_type() != 'product' ) {
				return;
			}

			global $product;
			woocommerce_template_loop_price();
			echo WC_Shortcodes::product_add_to_cart( array( 'id' => $product->get_id(), 'show_price' => 'false', 'style' => '' ) );
		}

		/**
		 * Close woocommerce wrapper for injected product features
		 */
		public function loop_item_content_end_close_wrapper() {
			if ( get_post_type() != 'product' ) {
				return;
			}
			echo '</div>';
		}
		
		/**
		 * Edit page metaboxes structure and add additional fields
		 *
		 * @param array  $structure $structure  Current structure
		 * @param string $id Current instance
		 * @param string $post_type Current post type
		 * @param string $context Meta box context
		 * @return array
		 * @since 2.5.0
		 * @version 2.5.0
		 */
		public function edit_page_metaboxes_structure( $structure, $id, $post_type, $context ) {
			
			if( 'page' == $post_type ) {
				
				$structure[ 'tab_listing' ][ 'fields' ] = array_merge( $structure[ 'tab_listing' ][ 'fields' ], array(
					// "Injects" heading
					'boombox_listing_injects_heading' => array(
						'type'            => 'custom',
						'html'            => sprintf( '<h2>%s</h2><hr/>', esc_html__( 'Injects', 'boombox' ) ),
						'order'           => 90,
						'active_callback' => array(
							array(
								'field_id' => 'boombox_listing_type',
								'value'    => 'none',
								'compare'  => '!=',
							),
						),
					),
					// Products
					'boombox_page_products_inject'  => array(
						'type'            => 'select',
						'label'           => esc_html__( 'Products', 'boombox' ),
						'order'           => 90,
						'sub_order'       => 60,
						'choices'         => apply_filters( 'boombox/woocommerce/product_inject_choices', array() ),
						'default'         => 'none',
						'active_callback' => array(
							array(
								'field_id' => 'boombox_listing_type',
								'value'    => 'none',
								'compare'  => '!=',
							),
						),
					),
					// Inject X product(s)
					'boombox_page_injected_products_count'    => array(
						'type'            => 'number',
						'label'           => esc_html__( 'Inject X product(s)', 'boombox' ),
						'order'           => 90,
						'sub_order'       => 70,
						'default'         => 1,
						'callback'        => array( $this, 'sanitize_page_metaboxes_products_count_value' ),
						'render_callback' => array( $this, 'render_page_metaboxes_products_count_value' ),
						'attributes'      => array(
							'min' => 1,
						),
						'active_callback' => array(
							array(
								'field_id' => 'boombox_listing_type',
								'value'    => 'none',
								'compare'  => '!=',
							),
							array(
								'field_id' => 'boombox_page_products_inject',
								'value'    => 'none',
								'compare'  => '!=',
							),
						),
					),
					// After every X post(s)
					'boombox_page_injected_products_position' => array(
						'type'            => 'number',
						'label'           => esc_html__( 'After every X post(s)', 'boombox' ),
						'order'           => 90,
						'sub_order'       => 80,
						'default'         => 1,
						'callback'        => array( $this, 'sanitize_page_metaboxes_products_position_value' ),
						'render_callback' => array( $this, 'render_page_metaboxes_products_position_value' ),
						'attributes'      => array(
							'min' => 1,
						),
						'active_callback' => array(
							array(
								'field_id' => 'boombox_listing_type',
								'value'    => 'none',
								'compare'  => '!=',
							),
							array(
								'field_id' => 'boombox_page_products_inject',
								'value'    => 'none',
								'compare'  => '!=',
							),
						),
					),
				) );
				
			}

			return $structure;
		}

		/**
		 * Sanitize page metaboxes products count value on rendering
		 *
		 * @param int $value Current value
		 *
		 * @return int
		 */
		public function render_page_metaboxes_products_count_value( $value ) {
			return max( 1, absint( $value ) );

		}

		/**
		 * Sanitize page metaboxes products count value on saving
		 *
		 * @param mixed $value Current value
		 *
		 * @return int
		 */
		public function sanitize_page_metaboxes_products_count_value( $value ) {
			$post = get_post();
			return max( 1, min( $value, boombox_get_post_meta( $post->ID, 'boombox_posts_per_page' ) ) );
		}

		/**
		 * Sanitize page metaboxes products position value on rendering
		 *
		 * @param int $value Current value
		 *
		 * @return int
		 */
		public function render_page_metaboxes_products_position_value( $value ) {
			$post = get_post();
			return max( 1, min( $value, boombox_get_post_meta( $post->ID, 'boombox_posts_per_page' ) ) );

		}

		/**
		 * Sanitize page metaboxes products position value on saving
		 *
		 * @param mixed $value Current value
		 *
		 * @return int
		 */
		public function sanitize_page_metaboxes_products_position_value( $value ) {
			return min( max( 1, absint( $value ) ), absint( $_POST[ AIOM_Config::get_post_meta_key() ][ 'boombox_posts_per_page' ] ) );
		}

		/**
		 * Edit header composition components choices
		 *
		 * @param array $choices Current choices
		 *
		 * @return array
		 * @since   2.0.0
		 * @version 2.0.0
		 */
		public function edit_header_composition_component_choices( $choices ) {
			$choices[ 'woocommerce-cart' ] = __( 'WooCommerce Cart Icon', 'boombox' );

			return $choices;
		}

		/**
		 * Edit template helper map
		 * @param array $map Current map
		 *
		 * @return array
		 */
		public function edit_template_helpers_map( $map ) {
			$map[ 'woocommerce' ] = array(
				'class' => 'Boombox_Woocommerce_Template_Helper',
				'path'  => __DIR__ . DIRECTORY_SEPARATOR . 'class-woocommerce-template-helper.php'
			);

			return $map;
		}

		/**
		 * Get single product sidebar type
		 * @return string
		 */
		public function get_single_product_sidebar_type() {
			return apply_filters( 'boombox_woocommerce_sidebar_type', get_option( 'boombox_woocommerce_sidebar_type', '1-sidebar-1_3' ) );
		}

		/**
		 * Get single product sidebar type
		 * @return string
		 */
		public function get_single_product_sidebar_orientation() {
			return apply_filters( 'boombox_woocommerce_sidebar_orientation', get_option( 'boombox_woocommerce_sidebar_orientation', 'right' ) );
		}

		/**
		 * Setup single product primary sidebar
		 * @param string $sidebar_id Current sidebar
		 * @hooked in "boombox/sidebar_id" filter
		 *
		 * @return string
		 */
		public function get_single_product_primary_sidebar( $sidebar_id ) {
			$condition = true;
			if( 'boombox/sidebar_id' == current_filter() ) {
				$condition = is_product();
			}
			if( $condition ) {
				$selected_sidebar = get_option( 'boombox_woocommerce_primary_sidebar', '' );
				if( ! $selected_sidebar ) {
					$shop_page_id = wc_get_page_id( 'shop' );
					$selected_sidebar = boombox_get_post_meta( $shop_page_id, 'boombox_primary_sidebar' );
					if( ! $selected_sidebar ) {
						$selected_sidebar = 'default-sidebar';
					}
				}
				$sidebar_id = apply_filters( 'boombox_woocommerce_primary_sidebar', $selected_sidebar );
			}

			return $sidebar_id;
		}

		/**
		 * Setup single product primary sidebar
		 * @param string $sidebar_id Current sidebar
		 * @hooked in "boombox/secondary-sidebar-id" filter
		 *
		 * @return string
		 */
		public function get_single_product_secondary_sidebar( $sidebar_id ) {
			$condition = true;
			if( 'boombox/secondary-sidebar-id' == current_filter() ) {
				$condition = is_product();
			}

			if( $condition ) {
				$selected_sidebar = get_option( 'boombox_woocommerce_secondary_sidebar', '' );
				if( ! $selected_sidebar ) {
					$shop_page_id = wc_get_page_id( 'shop' );
					$selected_sidebar = boombox_get_post_meta( $shop_page_id, 'boombox_secondary_sidebar' );
					if( ! $selected_sidebar ) {
						$selected_sidebar = 'page-secondary';
					}
				}
				$sidebar_id = apply_filters( 'boombox_woocommerce_secondary_sidebar', $selected_sidebar );
			}

			return $sidebar_id;
		}

		/**
		 * Edit page meta boxes structure
		 * @param $structure  array   Current structure
		 * @param $id         string  Meta box ID
		 * @param $post_type  string  Current post type
		 * @param $context    string  Meta box render context
		 *
		 * @return array
		 */
		public function edit_aiom_page_meta_boxes( $structure, $id, $post_type, $context ) {
			if( ( 'page' == $post_type ) && ( wc_get_page_id( 'shop' ) === get_the_ID() ) ) {
				unset( $structure[ 'tab_listing' ], $structure[ 'tab_featured_area' ] );
			}

			return $structure;
		}

	}

	Boombox_Woocommerce::get_instance();

}