<?php
/**
 * Boombox color scheme
 *
 * @package BoomBox_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

final class Boombox_Design_Scheme {

	/**
	 * Holds class single instance
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * Get instance
	 * @return Boombox_Design_Scheme|null
	 */
	public static function get_instance() {

		if (null == static::$_instance) {
			static::$_instance = new self();
		}

		return static::$_instance;

	}

	/**
	 * Boombox_Design_Scheme constructor.
	 */
	private function __construct() {
		$this->hooks();
	}

	/**
	 * A dummy magic method to prevent Boombox_Design_Scheme from being cloned.
	 *
	 */
	public function __clone() {
		throw new Exception('Cloning ' . __CLASS__ . ' is forbidden');
	}

	/**
	 * Setup Hooks
	 */
	public function hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'header_style_css' ), 11 );
		add_action( 'wp_enqueue_scripts', array( $this, 'footer_style_css' ), 11 );
		add_action( 'wp_enqueue_scripts', array( $this, 'global_style_css' ), 11 );
		add_action( 'wp_enqueue_scripts', array( $this, 'badges_style_css' ), 11 );
		add_action( 'wp_head', array( $this, 'set_mobile_address_bar_color' ) );
	}

	/**
	 * Enqueue front-end CSS for header styles
	 *
	 * @see wp_add_inline_style()
	 */
	public function header_style_css() {

		$set = boombox_get_theme_options_set( array(
			'header_design_site_title_color',
			'design_content_background_color',
			'header_design_top_background_color',
			'header_design_top_gradient_color',
			'header_design_top_text_color',
			'header_design_top_text_hover_color',
			'header_typography_top_menu_configuration',
			'header_typography_top_menu',
			'header_design_bottom_background_color',
			'header_design_bottom_gradient_color',
			'header_design_bottom_text_color',
			'header_design_bottom_text_hover_color',
			'header_typography_bottom_menu_configuration',
			'header_typography_bottom_menu',
			'header_typography_sub_menu_configuration',
			'header_typography_sub_menu',
			'header_design_button_background_color',
			'header_design_button_text_color',
			'header_layout_logo_position',
			'mobile_header_bg_color',
			'mobile_header_text_color',
			'mobile_header_gradient_color',
			'header_layout_logo_margin_top',
			'header_layout_logo_margin_bottom',
			'mobile_header_logo_margin_top',
			'mobile_header_logo_margin_bottom'
		) );

		/***** top menu inheritance */
		if( 'inherit' == $set['header_typography_top_menu_configuration'] ) {
			$set[ 'header_typography_top_menu' ] = array(
				'font-family'    => 'inherit',
				'font-size'      => 'inherit',
				'font-style'     => 'inherit',
				'font-weight'    => 'inherit',
				'letter-spacing' => 'inherit',
				'text-transform' => 'inherit',
			);
		} else {
			$set[ 'header_typography_top_menu' ][ 'font-family' ] = htmlspecialchars_decode( $set[ 'header_typography_top_menu' ][ 'font-family' ] );
			$set[ 'header_typography_top_menu' ][ 'font-size' ] = str_replace( 'pxpx', 'px', ( $set[ 'header_typography_top_menu' ][ 'font-size' ] . 'px' ) );
			$set[ 'header_typography_top_menu' ][ 'letter-spacing' ] = str_replace( 'pxpx', 'px', ( $set[ 'header_typography_top_menu' ][ 'letter-spacing' ] . 'px' ) );
			if( 'inherit' == $set[ 'header_typography_top_menu' ][ 'font-family' ] ) {
				$set['header_typography_top_menu']['font-style']  = 'inherit';
				$set['header_typography_top_menu']['font-weight'] = 'inherit';
			}
		}

		/***** bottom menu inheritance */
		if( 'inherit' == $set['header_typography_bottom_menu_configuration'] ) {
			$set[ 'header_typography_bottom_menu' ] = array(
				'font-family'    => 'inherit',
				'font-size'      => 'inherit',
				'font-style'     => 'inherit',
				'font-weight'    => 'inherit',
				'letter-spacing' => 'inherit',
				'text-transform' => 'inherit',
			);
		} else {
			$set[ 'header_typography_bottom_menu' ][ 'font-family' ] = htmlspecialchars_decode( $set[ 'header_typography_bottom_menu' ][ 'font-family' ] );
			$set[ 'header_typography_bottom_menu' ][ 'font-size' ] = str_replace( 'pxpx', 'px', ( $set[ 'header_typography_bottom_menu' ][ 'font-size' ] . 'px' ) );
			$set[ 'header_typography_bottom_menu' ][ 'letter-spacing' ] = str_replace( 'pxpx', 'px', ( $set[ 'header_typography_bottom_menu' ][ 'letter-spacing' ] . 'px' ) );
			if( 'inherit' == $set[ 'header_typography_bottom_menu' ][ 'font-family' ] ) {
				$set['header_typography_bottom_menu']['font-style']  = 'inherit';
				$set['header_typography_bottom_menu']['font-weight'] = 'inherit';
			}
		}

		/***** sub menu inheritance */
		if( 'inherit' == $set['header_typography_sub_menu_configuration'] ) {
			$set[ 'header_typography_sub_menu' ] = array(
				'font-family'    => 'inherit',
				'font-size'      => '14px',
				'font-style'     => 'inherit',
				'font-weight'    => 500,
				'letter-spacing' => 'inherit',
				'text-transform' => 'inherit',
			);
		} else {
			$set[ 'header_typography_sub_menu' ][ 'font-family' ] = htmlspecialchars_decode( $set[ 'header_typography_sub_menu' ][ 'font-family' ] );
			$set[ 'header_typography_sub_menu' ][ 'font-size' ] = str_replace( 'pxpx', 'px', ( $set[ 'header_typography_sub_menu' ][ 'font-size' ] . 'px' ) );
			$set[ 'header_typography_sub_menu' ][ 'letter-spacing' ] = str_replace( 'pxpx', 'px', ( $set[ 'header_typography_sub_menu' ][ 'letter-spacing' ] . 'px' ) );
		}

		$options = array(
			'site_title_clr' => $set['header_design_site_title_color'],
			'content_bg_clr' => $set['design_content_background_color'],
			'logo'           => array(
				'margin_top'    => $set[ 'header_layout_logo_margin_top' ],
				'margin_bottom' => $set[ 'header_layout_logo_margin_bottom' ]
			),
			'top' => array(
				'bg_clr' => $set['header_design_top_background_color'],
				'gr_clr' => $set['header_design_top_gradient_color'],
				'text_clr'  => $set['header_design_top_text_color'],
				'text_hover_clr' => $set['header_design_top_text_hover_color'],
				'typography' => array(
					'main_nav' => $set['header_typography_top_menu'],
					'sub_nav'  => $set['header_typography_sub_menu'],
				)
			),
			'bottom' => array(
				'bg_clr' => $set['header_design_bottom_background_color'],
				'gr_clr' => $set['header_design_bottom_gradient_color'],
				'text_clr' => $set['header_design_bottom_text_color'],
				'text_hover_clr' => $set['header_design_bottom_text_hover_color'],
				'typography' => array(
					'main_nav' => $set['header_typography_bottom_menu'],
					'sub_nav'  => $set['header_typography_sub_menu'],
				)
			),
			'button' => array(
				'bg_clr' => $set['header_design_button_background_color'],
				'text_clr' => $set['header_design_button_text_color'],
			),
			'mobile' => array(
				'header_logo_width' => '200px', //todo 2.0.1: Set this value from customizer
				'header_breakpoint' => '992px', //todo 2.0.1: Set this value from customizer
				'header_bg_clr'     => $set['mobile_header_bg_color'],
				'header_gr_clr'     => $set['mobile_header_gradient_color'],
				'header_text_clr'   => $set['mobile_header_text_color'],
				'logo'           => array(
					'margin_top'    => $set[ 'mobile_header_logo_margin_top' ],
					'margin_bottom' => $set[ 'mobile_header_logo_margin_bottom' ]
				)
			)
		);

		$fonts_helper = Boombox_Fonts_Helper::get_instance();
		$css = '';

		/***** Header top layer gradient */
		if( $options['top']['gr_clr'] ) {
			$css .= '/* -- Header Gradient For Top Layer -- */
		.bb-header.header-desktop .top-header {
			background: %2$s;
			background: -webkit-linear-gradient(20deg, %2$s, %38$s);
			background: -o-linear-gradient(20deg, %2$s, %38$s);
			background: -moz-linear-gradient(20deg, %2$s, %38$s);
			background: linear-gradient(20deg, %2$s, %38$s);
		}';
		}

		/***** Header bottom layer gradient */
		if( $options['bottom']['gr_clr'] ) {
			$css .= '/* -- Header Gradient For Bottom Layer -- */
		.bb-header.header-desktop .bottom-header {
			background: %3$s;
			background: -webkit-linear-gradient(20deg, %3$s, %39$s);
			background: -o-linear-gradient(20deg, %3$s, %39$s);
			background: -moz-linear-gradient(20deg, %3$s, %39$s);
			background: linear-gradient(20deg, %3$s, %39$s);
		}';
		}

		/***** Mobile header gradient */
		if( $options['mobile']['header_gr_clr'] ) {
			$css .= '/* -- Header Gradient for Mobile -- */
		.bb-header.header-mobile .header-row {
			background: %12$s;
			background: -webkit-linear-gradient(20deg, %12$s, %40$s);
			background: -o-linear-gradient(20deg, %12$s, %40$s);
			background: -moz-linear-gradient(20deg, %12$s, %40$s);
			background: linear-gradient(20deg, %12$s, %40$s);
		}';
		}

		$css .= '
		/* -- Mobile Header -- */
		.bb-header.header-mobile .header-row {
			background-color: %12$s;
			color: %13$s;
		}
		/* If you need to specify different color for bottom/top header, use this code */
		.bb-header.header-mobile.g-style .header-c {
			color: %13$s;
		}
		/* Mobile header breakpoint */
		@media (min-width: %11$s) {
			.bb-show-desktop-header {
			  display: block;
			}
			.bb-show-mobile-header {
			  display: none;
			}
		}
	
		/* --site title color */
		.branding h1 {
		  color: %1$s;
		}
	
		/* -top */
		.bb-header.header-desktop .top-header {
		  background-color: %2$s;
		}
	
		.bb-header.header-desktop .top-header .bb-header-icon:hover,
		.bb-header.header-desktop .top-header  .bb-header-icon.active,
		.bb-header.header-desktop .top-header .main-navigation > ul > li:hover,
		.bb-header.header-desktop .top-header .main-navigation > ul > li.current-menu-item > a {
		  color: %5$s;
		}
	
		/* --top pattern */
		.bb-header.header-desktop .top-header svg {
		  fill: %2$s;
		}
	
		/* --top text color */
		.bb-header.header-desktop .top-header {
		  color: %4$s;
		}
	
		.bb-header.header-desktop .top-header .create-post {
		  background-color: %8$s;
		}
	
		/* --top button color */
		.bb-header.header-desktop .top-header .create-post {
		  color: %9$s;
		}
		
		/* --top Typography */
		.bb-header.header-desktop .top-header .main-navigation {
			%14$s
			font-size: %15$s;
			font-style : %16$s;
			font-weight: %17$s;
			letter-spacing: %18$s;
			text-transform: %19$s;
		}
		
		.bb-header.header-desktop .top-header .main-navigation .sub-menu {
			%20$s
			font-size: %21$s;
			font-style : %22$s;
			font-weight: %23$s;
			letter-spacing: %24$s;
			text-transform: %25$s;
		}
	
		.bb-header.header-desktop .bottom-header .bb-header-icon:hover,
		.bb-header.header-desktop .bottom-header .bb-header-icon.active,
		.bb-header.header-desktop .bottom-header .main-navigation > ul > li:hover,
		.bb-header.header-desktop .bottom-header .main-navigation > ul > li.current-menu-item > a,
		.bb-header.header-desktop .bottom-header .main-navigation > ul > li.current-menu-item > .dropdown-toggle {
		  color: %7$s;
		}
	
		/* -bottom */
		.bb-header.header-desktop .bottom-header {
		  background-color: %3$s;
		}
	
		.bb-header.header-desktop .bottom-header svg {
		  fill: %3$s;
		}
	
		/* --bottom text color */
		.bb-header.header-desktop .bottom-header {
		  color: %6$s;
		}
	
		.bb-header.header-desktop .bottom-header .main-navigation ul li:before,
		.bb-header.header-desktop .bottom-header .account-box .user:after,
		.bb-header.header-desktop .bottom-header .create-post:before,
		.bb-header.header-desktop .bottom-header .menu-button:after {
		  border-color: %6$s;
		}
	
		.bb-header.header-desktop .bottom-header .create-post {
		  background-color: %8$s;
		}
	
		/* --bottom button color */
		.bb-header.header-desktop .create-post {
		  color: %9$s;
		}
		
		/* --bottom Typography */
		.bb-header.header-desktop .bottom-header .main-navigation {
			%26$s
			font-size: %27$s;
			font-style : %28$s;
			font-weight: %29$s;
			letter-spacing: %30$s;
			text-transform: %31$s;
		}
		
		.bb-header.header-desktop .bottom-header .main-navigation .sub-menu {
			%32$s
			font-size: %33$s;
			font-style : %34$s;
			font-weight: %35$s;
			letter-spacing: %36$s;
			text-transform: %37$s;
		}

		/* -- Logo Margin for Desktop */
		.bb-header.header-desktop .branding {
			margin-top: %41$dpx;
			margin-bottom: %42$dpx;
		}
		/* -- Logo Margin for Mobile */
		.bb-header.header-mobile .branding {
			margin-top: %43$dpx;
			margin-bottom: %44$dpx;
		}
		';

		wp_add_inline_style( 'boombox-primary-style',
			sprintf(
				$css,
				/* -1- */
				$options['site_title_clr'],
				/* -2- */
				$options['top']['bg_clr'],
				/* -3- */
				$options['bottom']['bg_clr'],
				/* -4- */
				$options['top']['text_clr'],
				/* -5- */
				$options['top']['text_hover_clr'],
				/* -6- */
				$options['bottom']['text_clr'],
				/* -7- */
				$options['bottom']['text_hover_clr'],
				/* -8- */
				$options['button']['bg_clr'],
				/* -9- */
				$options['button']['text_clr'],
				/* -10- */
				$options['content_bg_clr'],
				/* -11- */
				$options['mobile']['header_breakpoint'],
				/* -12- */
				$options['mobile']['header_bg_clr'],
				/* -13- */
				$options['mobile']['header_text_clr'],
				/* -14- */
				$fonts_helper->generate_inline_css_font_family( $options['top']['typography']['main_nav']['font-family'], 'sans-serif' ),
				/* -15- */
				$options['top']['typography']['main_nav']['font-size'],
				/* -16- */
				$options['top']['typography']['main_nav']['font-style'],
				/* -17- */
				$options['top']['typography']['main_nav']['font-weight'],
				/* -18- */
				$options['top']['typography']['main_nav']['letter-spacing'],
				/* -19- */
				$options['top']['typography']['main_nav']['text-transform'],
				/* -20- */
				$fonts_helper->generate_inline_css_font_family( $options['top']['typography']['sub_nav']['font-family'], 'sans-serif' ),
				/* -21- */
				$options['top']['typography']['sub_nav']['font-size'],
				/* -22- */
				$options['top']['typography']['sub_nav']['font-style'],
				/* -23- */
				$options['top']['typography']['sub_nav']['font-weight'],
				/* -24- */
				$options['top']['typography']['sub_nav']['letter-spacing'],
				/* -25- */
				$options['top']['typography']['sub_nav']['text-transform'],
				/* -26- */
				$fonts_helper->generate_inline_css_font_family( $options['bottom']['typography']['main_nav']['font-family'], 'sans-serif' ),
				/* -27- */
				$options['bottom']['typography']['main_nav']['font-size'],
				/* -28- */
				$options['bottom']['typography']['main_nav']['font-style'],
				/* -29- */
				$options['bottom']['typography']['main_nav']['font-weight'],
				/* -30- */
				$options['bottom']['typography']['main_nav']['letter-spacing'],
				/* -31- */
				$options['bottom']['typography']['main_nav']['text-transform'],
				/* -32- */
				$fonts_helper->generate_inline_css_font_family( $options['bottom']['typography']['sub_nav']['font-family'], 'sans-serif' ),
				/* -33- */
				$options['bottom']['typography']['sub_nav']['font-size'],
				/* -34- */
				$options['bottom']['typography']['sub_nav']['font-style'],
				/* -35- */
				$options['bottom']['typography']['sub_nav']['font-weight'],
				/* -36- */
				$options['bottom']['typography']['sub_nav']['letter-spacing'],
				/* -37- */
				$options['bottom']['typography']['sub_nav']['text-transform'],
				/* -38- */
				$options['top']['gr_clr'],
				/* -39- */
				$options['bottom']['gr_clr'],
				/* -40- */
				$options['mobile']['header_gr_clr'],
				/* -41- */
				$options['logo']['margin_top'],
				/* -42- */
				$options['logo']['margin_bottom'],
				/* -43- */
				$options['mobile']['logo']['margin_top'],
				/* -44- */
				$options['mobile']['logo']['margin_bottom']
			)
		);
	}


	/**
	 * Enqueue front-end CSS for footer styles
	 *
	 * @see wp_add_inline_style()
	 */
	public function footer_style_css() {

		$css = '
		/* Custom Footer Styles */
	
		/* -top */
		.footer {
		  background-color: %1$s;
		}
	
		.footer .footer-top svg {
		  fill: %1$s;
		}
	
		.footer .footer-bottom svg {
		  fill: %5$s;
		}
	
		/* -primary color */
		/* --primary bg */
		#footer .cat-item.current-cat a,
		#footer .widget_mc4wp_form_widget:before,#footer .widget_create_post:before,
		#footer .cat-item a:hover,
		#footer button[type="submit"],
		#footer input[type="submit"],
		#footer .bb-btn, #footer .bnt.primary {
		  background-color: %8$s;
		}
	
		/* --primary text */
		#footer .widget_mc4wp_form_widget:before,#footer .widget_create_post:before,
		#footer button[type="submit"],
		#footer input[type="submit"],
		#footer .bb-btn, #footer .bb-bnt-primary {
		  color: %9$s;
		}
	
		/* --primary hover */
		#footer a:hover {
		  color: %8$s;
		}
	
		#footer .widget_categories ul li a:hover,
		#footer .widget_archive ul li a:hover,
		#footer .widget_pages ul li a:hover,
		#footer .widget_meta ul li a:hover,
		#footer .widget_nav_menu ul li a:hover {
		  background-color: %8$s;
		  color: %9$s;
		}
	
		#footer .slick-dots li.slick-active button:before,
		#footer .widget_tag_cloud a:hover {
		  border-color:%8$s;
		}
	
		/* -heading color */
		#footer .bb-featured-strip .item .title,
		#footer .slick-dots li button:before,
		#footer h1,#footer h2,#footer h3,#footer h4, #footer h5,#footer h6,
		#footer .widget-title {
		  color: %2$s;
		}
	
		/* -text color */
		#footer,
		#footer .widget_recent_comments .recentcomments .comment-author-link,
		#footer .widget_recent_comments .recentcomments a,
		#footer .byline, #footer .posted-on,
		#footer .widget_nav_menu ul li,
		#footer .widget_categories ul li,
		#footer .widget_archive ul li,
		#footer .widget_pages ul li,
		#footer .widget_meta ul li {
		  color: %3$s;
		}
		#footer .widget_tag_cloud  a, #footer select, #footer textarea, #footer input[type="tel"], #footer input[type="text"], #footer input[type="number"], #footer input[type="date"], #footer input[type="time"], #footer input[type="url"], #footer input[type="email"], #footer input[type="search"],#footer input[type="password"],
		#footer .widget_mc4wp_form_widget:after, #footer .widget_create_post:after {
			border-color: %3$s;
		}
	
		#footer .widget_categories ul li a,
		#footer .widget_archive ul li a,
		#footer .widget_pages ul li a,
		#footer .widget_meta ul li a,
		#footer .widget_nav_menu ul li a,
		#footer .widget_tag_cloud a {
		  color: %4$s;
		}
	
		/* -bottom */
		/* --text  color */
		#footer .footer-bottom {
		  background-color: %5$s;
		  color: %6$s;
		}
	
		/* --text  hover */
		#footer .footer-bottom a:hover {
		  color: %7$s;
		}';

		$set = boombox_get_theme_options_set( array(
			'footer_design_top_background_color',
			'footer_design_top_heading_color',
			'footer_design_top_text_color',
			'footer_design_top_link_color',
			'footer_design_bottom_background_color',
			'footer_design_bottom_text_color',
			'footer_design_bottom_text_hover_color',
			'footer_design_top_primary_color',
			'footer_design_top_primary_text_color'
		) );

		wp_add_inline_style( 'boombox-primary-style',
			sprintf(
				$css,
				$set['footer_design_top_background_color'],         /* -1- */
				$set['footer_design_top_heading_color'],            /* -2- */
				$set['footer_design_top_text_color'],               /* -3- */
				$set['footer_design_top_link_color'],               /* -4- */
				$set['footer_design_bottom_background_color'],      /* -5- */
				$set['footer_design_bottom_text_color'],            /* -6- */
				$set['footer_design_bottom_text_hover_color'],      /* -7- */
				$set['footer_design_top_primary_color'],            /* -8- */
				$set['footer_design_top_primary_text_color']        /* -9- */
			)
		);
	}



	/**
	 * Enqueue front-end CSS for global styles
	 *
	 * @see wp_add_inline_style()
	 */
	function global_style_css() {

		/**
		 * @var $title_template_helper Boombox_Title_Template_Helper Title template helper
		 */
		$title_template_helper = Boombox_Template::init( 'title' );
		$title_template_options = $title_template_helper->get_options();
		$set = boombox_get_theme_options_set( array(
			'design_primary_font_family',
			'design_secondary_font_family',
			'design_background_style',
			'design_body_background_color',
			'design_content_background_color',
			'design_primary_color',
			'design_primary_text_color',
			'design_base_text_color',
			'design_secondary_text_color',
			'design_heading_text_color',
			'design_border_radius',
			'design_inputs_buttons_border_radius',
			'design_border_color',
			'design_secondary_components_background_color',
			'design_social_icons_border_radius',
			'design_post_titles_font_family',
			'design_logo_font_family',
			'design_general_text_font_size',
			'design_single_post_heading_font_size',
			'design_widget_heading_font_size',
			'design_link_text_color',
			'design_global_custom_css',
			'design_secondary_components_text_color',
			'design_body_background_image_type',
			'extra_additional_enable_back_to_top_btn'
		) );

		switch( $set['design_body_background_image_type'] ) {
			case 'cover':
				$set['design_body_background_image_css'] = 'background-size:cover;';
				break;
			case 'repeat':
				$set['design_body_background_image_css'] = 'background-repeat:repeat;';
				break;
			default:
				$set['design_body_background_image_css'] = '';
		}

		switch( $set['design_background_style'] ) {
			case 'boxed':
				$set['design_background_style'] = '1200px';
				break;
			case 'full_width':
				$set['design_background_style'] = '100%';
				break;
			default:
				$set['design_background_style'] = '100%';
		}

		$set['design_border_radius'] = absint( $set['design_border_radius'] ) . 'px';
		$set['design_inputs_buttons_border_radius'] = absint( $set['design_inputs_buttons_border_radius'] ) . 'px';
		$set['design_social_icons_border_radius'] = absint( $set['design_social_icons_border_radius'] ) . 'px';
		$screen_mb_boxed = '701px';

		$css = '/* - Page header - */';

		/***** Title Area Text Color ******/
		if ( $title_template_options['text_color'] ) {
			$css .= "
			   .bb-page-header .page-title, .bb-page-header .col .page-subtitle,.bb-page-header .cat-dropdown .dropdown-toggle {
			      color: {$title_template_options['text_color']};
			   }";
		}

		/***** Back To Top Button ******/
		if( ! $set['extra_additional_enable_back_to_top_btn'] ) {
			$css .= "
				/* Back To Top Button */			
				#go-top{
					display: none !important;
				}";
		}

		if( ! empty( $title_template_options['background'] ) ) {

			/***** Background Color */
			if ( in_array( 'color', $title_template_options[ 'background' ][ 'features' ] ) ) {
				$css .= "
					/* Bg color */
				   .bb-page-header.has-bg-clr .container-bg {
				      background-color: {$title_template_options['background']['color']};
				   }";
			}

			/***** Background Image */
			if ( in_array( 'image', $title_template_options[ 'background' ][ 'features' ] ) ) {
				$css .= "
					/* Bg image */
				   .bb-page-header.has-bg-img .container-bg {
				      background-image: url({$title_template_options['background']['url']});
				   }";
			}
			/***** Background Gradient */
			else if ( in_array( 'gradient', $title_template_options[ 'background' ][ 'features' ] ) ) {
				$css .= sprintf( '
					/* Bg gradient */
					.bb-page-header.bg-gradient-bottom .container-bg {
						background: -webkit-gradient(linear, left top, left bottom, color-stop(0%%,%1$s), color-stop(100%%,%2$s));
						background: -webkit-linear-gradient(to bottom,%1$s,%2$s);
						background: -ms-linear-gradient(to bottom,%1$s,%2$s);
						background: linear-gradient(to bottom,%1$s,%2$s);
					}
					.bb-page-header.bg-gradient-top .container-bg {
						background: -webkit-gradient(linear, left top, left bottom, color-stop(0%%,%1$s), color-stop(100%%,%2$s));
						background: -webkit-linear-gradient(to top,%1$s,%2$s);
						background: -ms-linear-gradient(to top,%1$s,%2$s);
						background: linear-gradient(to top,%1$s,%2$s);
					}
					.bb-page-header.bg-gradient-right .container-bg {
						background: -webkit-gradient(linear, left top, right top, color-stop(0%%,%1$s), color-stop(100%%,%2$s));
						background: -webkit-linear-gradient(to right,%1$s,%2$s);
						background: -ms-linear-gradient(to right,%1$s,%2$s);
						background: linear-gradient(to right,%1$s,%2$s);
					}
					.bb-page-header.bg-gradient-left .container-bg {
						background: -webkit-gradient(linear, left top, right top, color-stop(0%%,%1$s), color-stop(100%%,%2$s));
						background: -webkit-linear-gradient(to left,%1$s,%2$s);
						background: -ms-linear-gradient(to left,%1$s,%2$s);
						background: linear-gradient(to left,%1$s,%2$s);
					}', $title_template_options[ 'background' ][ 'start' ], $title_template_options[ 'background' ][ 'end' ] );
			}
		}

		$css .= '

	/* -body bg color */
	body,.bb-post-gallery-content .bb-gl-header {
	    background-color: %4$s;
	}
	.bb-cards-view .bb-post-single.style5 .site-main, .bb-cards-view .bb-post-single.style6 .container-inner {
		background-color: %4$s;
	}
	
	.branding .site-title {
		%17$s
		font-weight:%24$s;
		font-style:%25$s;
	}

	#background-image {
		%23$s
	}

	/* -Font sizes */
	.widget-title {
		font-size: %20$dpx;
	}
	body {
	    font-size: %18$dpx;
	}
	@media screen and (min-width: 992px) {
		html {
			font-size: %18$dpx;
		}
		.bb-post-single .s-post-title {
	    	font-size: %19$dpx;
		}
	}

	/* -content bg color */
	.page-wrapper,
	#main,
	.bb-cards-view .bb-card-item,
	.bb-cards-view .widget_mc4wp_form_widget:after,
	.bb-cards-view .widget_create_post:after,
	.light-modal .modal-body,.light-modal,
	.bb-toggle .bb-header-dropdown.toggle-content,
	.bb-header.header-desktop .main-navigation .sub-menu,
	.bb-post-share-box .post-share-count,
	.bb-post-rating a,.comment-respond input[type=text], .comment-respond textarea, .comment-respond #commentform textarea#comment,
	.bb-fixed-pagination .page,.bb-fixed-pagination .pg-content,
	.bb-floating-navbar .floating-navbar-inner,
	.bb-featured-strip .bb-arrow-next, .bb-featured-strip .bb-arrow-prev,
	.bb-mobile-navigation,
	.mejs-container,.bb-post-gallery-content,
	.bb-dropdown .dropdown-toggle, .bb-dropdown .dropdown-content,
	.bb-stretched-view .bb-post-single.style5 .site-main, .bb-stretched-view .bb-post-single.style6 .container-inner,
	.bb-boxed-view .bb-post-single.style5 .site-main, .bb-boxed-view .bb-post-single.style6 .container-inner,
	.bb-advertisement.bb-sticky-bottom-area .bb-sticky-btm-el {
	  background-color: %5$s;
	  border-color: %5$s;
	}
	/* Temp Color: will be option in future */
	.bb-header.header-mobile.g-style .header-c {
		background: %5$s;
	}
	.bb-header-navigation .main-navigation .sub-menu:before,
	.bb-toggle .toggle-content.bb-header-dropdown:before {
	    border-color: transparent transparent %5$s;
	}
	select, .bb-form-block input, .bb-form-block select, .bb-form-block textarea,
	.bb-author-vcard .author {
		background-color: %5$s;
	}
	.bb-tabs .tabs-menu .count {
	  color: %5$s;
	}

	/* -page width */
	.page-wrapper {
	  width: %3$s;
	}

	/* -primary color */
	/* --primary color for bg */
	.mark, mark,.box_list,
	.bb-tooltip:before,
	.bb-text-highlight.primary-color,
	#comments .nav-links a,
	.light-modal .modal-close,
	.quiz_row:hover,
	.progress-bar-success,
	.onoffswitch,.onoffswitch2,
	.widget_nav_menu ul li a:hover,
	.widget_categories ul li a:hover,
	.widget_archive ul li a:hover,
	.widget_pages ul li a:hover,
	.widget_meta ul li a:hover,
	.widget_mc4wp_form_widget:before,.widget_create_post:before,
	.widget_calendar table th a,
	.widget_calendar table td a,
	.go-top, .bb-affiliate-content .item-url,
	.bb-mobile-navigation .close,
	.bb-wp-pagination .page-numbers.next, .bb-wp-pagination .page-numbers.prev,
	.navigation.pagination .page-numbers.next, .navigation.pagination .page-numbers.prev,
	.bb-next-prev-pagination .page-link,
	.bb-next-pagination .page-link,
	.bb-post-share-box .post-share-count,
	.cat-item.current-cat a,
	.cat-item a:hover,
	.bb-fixed-pagination .page:hover .pg-arrow,
	button[type="submit"],
	input[type="submit"],
	.bb-btn.bb-btn-primary,.bb-btn.bb-btn-primary:hover,
	blockquote:before,.bb-btn.bb-btn-primary-outline:hover,.bb-post-gallery-content .bb-gl-meta .bb-gl-arrow,
	hr.primary-color,
	.bb-bg-primary, .bb-bg-primary.bb-btn,
	.bb-sticky-btm .btn-close {
	  background-color: %6$s;
	}
	.bb-tooltip:after,
	hr.bb-line-dashed.primary-color, hr.bb-line-dotted.primary-color {
		border-top-color:%6$s;
	}

	/* --primary text */
	.mark, mark,
	.bb-tooltip:before,
	.bb-wp-pagination .page-numbers.next, .bb-wp-pagination .page-numbers.prev,
	.navigation.pagination .page-numbers.next, .navigation.pagination .page-numbers.prev,
	.bb-text-highlight.primary-color,
	#comments .nav-links a,
	.light-modal .modal-close,
	.sr-only,.box_list,
	.quiz_row:hover, .bb-affiliate-content .item-url,
	.onoffswitch,.onoffswitch2,
	.bb-next-prev-pagination .page-link,
	.bb-next-pagination .page-link,
	.widget_nav_menu ul li a:hover,
	.widget_categories ul li a:hover,
	.widget_archive ul li a:hover,
	.widget_pages ul li a:hover,
	.widget_meta ul li a:hover,
	.cat-item.current-cat a,
	.widget_mc4wp_form_widget:before,.widget_create_post:before,
	.go-top,
	.widget_calendar table th a,
	.widget_calendar table td a,
	.bb-mobile-navigation .close,
	.bb-post-share-box .post-share-count,
	.bb-fixed-pagination .page:hover .pg-arrow,
	button[type="submit"],
	input[type="submit"],
	.bb-btn.bb-btn-primary,.bb-btn.bb-btn-primary:hover,.bb-btn.bb-btn-primary-outline:hover,
	blockquote:before,.bb-post-gallery-content .bb-gl-meta .bb-gl-arrow,
	.bb-bg-primary,
	.bb-sticky-btm .btn-close {
	  color: %7$s;
	}

	/* -primary color */
	/* --primary color for text */
	#cancel-comment-reply-link,
	.bb-affiliate-content .price:before,
	.bb-header-navigation .main-navigation > ul .sub-menu li:hover > a,
	.bb-header-navigation .main-navigation > ul .sub-menu li.current-menu-item a,
	.bb-header-navigation .more-navigation .section-navigation ul li:hover a,
	.bb-mobile-navigation .main-navigation li a:hover,.bb-mobile-navigation .main-navigation>ul>li .sub-menu li a:hover,
	.bb-mobile-navigation .main-navigation li.current-menu-item > a, .bb-mobile-navigation .main-navigation .sub-menu li.current-menu-item > a,
	.bb-mobile-navigation .main-navigation li.current-menu-item > .dropdown-toggle, .bb-mobile-navigation .main-navigation .sub-menu li.current-menu-item > .dropdown-toggle,
	.single.nsfw-post .bb-post-single .nsfw-post h3,
	.sticky .post-thumbnail:after,
	.entry-no-lg,
	.entry-title:hover a,
	.post-types .item:hover .bb-icon,
	.bb-text-dropcap.primary-color,
	.bb-btn-primary-outline,
	.bb-btn-link:hover,
	.bb-btn-link,#comments .bypostauthor > .comment-body .vcard .fn,
	.more-link:hover,
	.widget_bb-side-navigation .menu-item.menu-item-icon .bb-icon,
	.bb-post-nav .nsfw-post h3,
	.post-thumbnail .nsfw-post h3,
	.bb-price-block .current-price:before, .bb-price-block ins:before, .bb-price-block .amount:before, .product_list_widget ins .amount:before {
	  color: %6$s;
	}

	.post-types .item:hover,
	.more-load-button button:hover,
	.bb-btn-primary-outline,.bb-btn-primary:hover,
	.widget_tag_cloud .tagcloud a:hover {
	  border-color: %6$s;
	}

	.bb-tabs .tabs-menu li.active:before  {
		background-color: %6$s;
	}

	/* -link color */
	a,.bb-timing-block .timing-seconds {
	  color:%21$s
	}

	/* - base text color */
	body, html,
	.widget_recent_comments .recentcomments .comment-author-link,.widget_recent_comments .recentcomments a,
	.bb-header.header-desktop .main-navigation .sub-menu,
	.bb-header-dropdown.toggle-content,.comment-respond input[type=text], .comment-respond textarea,
	.featured-strip .slick-dots li button:before,
	.more-load-button button,.comment-vote .count,
	.bb-mobile-navigation .bb-header-search .search-submit,
	#comments .comment .comment-body .comment-content small .dropdown-toggle,
	.byline a,.byline .author-name,
	.bb-featured-strip .bb-arrow-next, .bb-featured-strip .bb-arrow-prev,
	.bb-price-block, .bb-price-block > .amount, .bb-price-block ins .amount,
	.bb-dropdown .dropdown-content a,
	.bb-author-vcard .auth-references a,
	.light-modal,
    .bb-author-vcard-mini .auth-url,
	.bb-post-gallery-content .bb-gl-meta .bb-gl-pagination b,
	 .bb-post-gallery-content.bb-mode-slide .bb-mode-switcher[data-mode=slide],
	 .bb-post-gallery-content.bb-mode-grid .bb-mode-switcher[data-mode=grid]{
	  color: %8$s;
	}

	/* --heading text color */
	#comments .vcard .fn,
	.bb-fixed-pagination .page .pg-title,
	.more_items_x legend, .more_items legend, .more_items_glow,
	h1, h2, h3, h4, h5, h6 {
	  color: %10$s;
	}
	.bb-tabs .tabs-menu li.active, .bb-tabs .tabs-menu li.active {
	  border-color: %10$s;
	}
	.bb-tabs .tabs-menu .count {
	  background-color: %10$s;
	}

	/* --secondary text color */
	s, strike, del,label,#comments .pingback .comment-body .comment-content, #comments .comment .comment-body .comment-content,
	#TB_ajaxWindowTitle,
	.bb-affiliate-content .price .old-price,
	.bb-header-navigation .more-navigation .sections-header,
	.bb-mobile-navigation .more-menu .more-menu-body .sections-header,
	.bb-post-share-box .bb-post-rating .count .text:after,
	.inline-popup .intro,.comment-vote a .bb-icon,
	.authentication .intro,.widget_recent_comments .recentcomments,
	.post-types .item .bb-icon,
	.bb-post-rating a,.post-thumbnail .thumbnail-caption,
	table thead th, table tfoot th, .bb-post-share-box .mobile-info,
	.widget_create_post .text,
	.widget_footer .text,
	.bb-author-vcard .author-info,.bb-author-vcard .auth-byline,
	.wp-caption .wp-caption-text, .wp-caption-dd,
	#comments .comments-title span,
	#comments .comment-notes,
	#comments .comment-metadata,
	.short-info .create-post .text,
	.bb-cat-links,
	.widget_bb-side-navigation .menu-item.menu-item-has-children .dropdown-toggle,
	.bb-post-meta .post-comments,.entry-sub-title,
	.bb-page-header .page-subtitle,
	.widget_bb-side-navigation .bb-widget-title,
	.bb-price-block .old-price,.bb-price-block del .amount,
	.widget_recent_comments .recentcomments,
	.bb-post-gallery-content .bb-gl-mode-switcher,
    .bb-author-vcard-mini .auth-byline, .bb-author-vcard-mini .auth-posted-on, .bb-author-vcard-mini .auth-title,
	.s-post-meta .post-comments,
	.bb-sec-label,
	.bb-breadcrumb.clr-style1, .bb-breadcrumb.clr-style1 a {
	  color: %9$s;
	}

	::-webkit-input-placeholder {
	  color: %9$s;
	}

	:-moz-placeholder {
	  color: %9$s;
	}

	:-ms-input-placeholder {
	  color: %9$s;
	}

	/* -font family */
	/* --base font family */
	body, html,
	#cancel-comment-reply-link,
	#comments .comments-title span {
	  %1$s
	}

	/* --Post heading font family */
	.entry-title {
	 %16$s
	}

	/* --secondary font family */
	.bb-wp-pagination,.navigation.pagination,
	.comments-area h3,[class*=" mashicon-"] .text, [class^=mashicon-] .text,
	.entry-no-lg,
	.bb-reaction-box .title, .bb-reaction-box .reaction-item .reaction-vote-btn,
	#comments .comments-title, #comments .comment-reply-title,
	.bb-page-header .bb-trending-navigation ul li a,
	.widget-title,
	.bb-badge .text,.post-number,
	.more_items_x legend, .more_items legend, .more_items_glow,
	section.error-404 .text,
	.inline-popup .title,
	.authentication .title,
	.bb-other-posts .title,
	.bb-post-share-box h2,
	.bb-page-header h1 {
	  %2$s
	}

	/* -border-color */
	.bb-page-header .container-bg, .bb-page-header.boxed.has-bg .container-bg,
	.bb-header-navigation .main-navigation .sub-menu,
	.bb-header-navigation .more-navigation .more-menu-header,
	.bb-header-navigation .more-navigation .more-menu-footer,
	.bb-mobile-navigation .more-menu .bb-badge-list,
	.bb-mobile-navigation .main-navigation,
	.bb-mobile-navigation .more-menu-body,
	.spinner-pulse,
	.bb-border-thumb,#comments .pingback, #comments .comment,
	.more-load-button button,
	.bb-post-rating .count .bb-icon,
	.quiz_row,.bb-post-collection .post-items .post-item .post-author-meta, .post-grid .page .post-author-meta, .post-list .post .post-author-meta, .post-list .page .post-author-meta,.post-list.standard .post footer,
	.post-list.standard .entry-sub-title,
	.more-load-button:before,
	.bb-mobile-navigation .bb-header-search form,
	#TB_window .shares,
	.wp-playlist,.boombox-comments .tabs-content,
	.post-types .item,
	.bb-page-header .bb-trending-navigation,
	.widget_mc4wp_form_widget:after,.widget_create_post:after,
	.bb-post-rating .inner,
	.bb-post-rating .point-btn,
	.widget_bb-side-navigation .menu-item.menu-item-has-children>a,
	.bb-author-vcard .author, #comments .comment-list, #comments .pingback .children .comment, #comments .comment .children .comment,
	.widget_social,
	.widget_subscribe,.bb-post-nav .pg-item,
	.bb-post-nav .page,.bb-tags a,.tagcloud a,
	.bb-next-prev-pagination,
	.widget_tag_cloud .tagcloud a,
	select, textarea, input[type="tel"], input[type="text"], input[type="number"], input[type="date"], input[type="time"], input[type="url"], input[type="email"], input[type="search"], input[type="password"],
	.bb-featured-menu:before,
	.select2-container--default .select2-selection--single, .select2-container--default .select2-search--dropdown .select2-search__field, .select2-dropdown,
	.bb-bordered-block:after,
	.bb-dropdown .dropdown-toggle, .bb-dropdown .dropdown-content, .bb-dropdown .dropdown-content li,.bb-post-gallery-content .bb-gl-mode-switcher,.bb-post-gallery-content .bb-gl-mode-switcher .bb-mode-switcher:first-child,
	.bb-tabs.tabs-horizontal .tabs-menu,.mixed-list .post-item-classic footer {
	  border-color: %13$s;
	}
	hr, .bb-brand-block .brand-content:before {
	  background-color: %13$s;
	}

	/* -secondary components bg color */
	.bb-fixed-pagination .page .pg-arrow,
	.captcha-container,.comment-respond form,
	.bb-post-share-box .post-meta,
	table tbody tr:nth-child(2n+1) th,
	table tbody tr:nth-child(2n+1) td,
	.bb-reaction-box .reaction-item .reaction-bar,
	.bb-reaction-box .reaction-item .reaction-vote-btn,
	.widget_bb-side-navigation .sub-menu .menu-item.menu-item-icon .bb-icon,
	#comments .pingback .comment-body .comment-reply-link, #comments .comment .comment-body .comment-reply-link,.bb-btn, button,
	.widget_sidebar_footer,
	.bb-form-block,
	.bb-author-vcard header,.bb-post-gallery-content .bb-gl-image-text,
	.bb-wp-pagination span.current, .bb-wp-pagination a.page-numbers:not(.next):not(.prev):hover,
	.navigation.pagination span.current, .navigation.pagination a.page-numbers:not(.next):not(.prev):hover,
	.bb-dropdown .dropdown-content li.active,
	.bb-post-gallery-content .bb-gl-image-text,
	.bb-media-placeholder:before,
	.bb-source-via .s-v-itm,
	.bb-tabs .tabs-content,.bb-reading-time {
		background-color: %14$s;
	}

	/* -secondary components text color */
	.bb-fixed-pagination .page .pg-arrow,.bb-post-share-box .post-meta,.captcha-container input,.form-captcha .refresh-captcha,#comments .pingback .comment-body .comment-reply-link, #comments .comment .comment-body .comment-reply-link,.bb-reaction-box .reaction-item .reaction-vote-btn,.bb-reaction-box .reaction-item .reaction-bar,.bb-btn,.comment-respond form,
	.bb-wp-pagination span.current, .bb-wp-pagination a.page-numbers:not(.next):not(.prev):hover,
	.navigation.pagination span.current, .navigation.pagination a.page-numbers:not(.next):not(.prev):hover,
	.widget_bb-side-navigation .sub-menu .menu-item.menu-item-icon .bb-icon,
	.widget_sidebar_footer,
	.bb-author-vcard .header-info a,.bb-author-vcard .auth-name,
	.bb-dropdown .dropdown-content li.active,
	.bb-source-via .s-v-link,.bb-reading-time {
		color:%22$s;
	}
	.captcha-container input {border-color:%22$s}

	/* -border-radius */
	img,video,.comment-respond form,
	.captcha-container,
	.bb-media-placeholder,
	.bb-cards-view .bb-card-item,
	.post-thumbnail .video-wrapper,
	.post-thumbnail .view-full-post,
	.bb-post-share-box .post-meta,
	.hy_plyr canvas,.bb-featured-strip .item .media,
	.quiz_row,.box_list,
	.bb-border-thumb,
	.advertisement .massage,
	[class^="mashicon-"],
	#TB_window,
	#score_modal .shares a div, #TB_window .shares a div,
	.bb-mobile-navigation .close,
	.onoffswitch-label,
	.light-modal .modal-close,
	.onoffswitch2-label,
	.post-types .item,
	.onoffswitch,.onoffswitch2,
	.bb-page-header .bb-trending-navigation ul li.active a,
	.widget_mc4wp_form_widget:after,.widget_create_post:after,
	.bb-author-vcard .author,
	.widget_sidebar_footer,
	.short-info,
	.inline-popup,
	.bb-reaction-box .reaction-item .reaction-bar,
	.bb-reaction-box .reaction-item .reaction-vote-btn,
	.bb-post-share-box .post-share-count,
	.post-thumbnail,
	.share-button,
	.bb-post-rating .inner,
	.bb-page-header.boxed.has-bg .container-bg,
	.widget_subscribe,
	.widget_social,
	.sub-menu,
	.fancybox-skin,
	.widget_tag_cloud .tagcloud a,
	.bb-tags a,.tagcloud a, .bb-header-dropdown.toggle-content,
	.authentication .button, #respond .button, .wp-social-login-provider-list .button,
	.bb-bordered-block:after,
	.wpml-ls-legacy-dropdown, .wpml-ls-legacy-dropdown a.wpml-ls-item-toggle, .wpml-ls-legacy-dropdown-click, .wpml-ls-legacy-dropdown-click a.wpml-ls-item-toggle,
	.wpml-ls-legacy-dropdown .wpml-ls-sub-menu, .wpml-ls-legacy-dropdown-click .wpml-ls-sub-menu,
	.nsfw-post,
	.light-modal .modal-body,
	.bb-featured-area .featured-media, .bb-featured-area .featured-item,
	.s-post-featured-media.boxed .featured-media-el,
	.bb-source-via .s-v-itm,
	.bb-tabs .tabs-content,
	.bb-sticky-btm .btn-close {
	  -webkit-border-radius: %11$s;
	     -moz-border-radius: %11$s;
	          border-radius: %11$s;
	}
	.bb-featured-area .featured-header {
      border-bottom-left-radius: %11$s;
      border-bottom-right-radius: %11$s;
    }

	/* --border-radius for inputs, buttons */
	.form-captcha img,.go-top,
	.bb-next-prev-pagination .page-link,
	.bb-next-pagination .page-link,
	.bb-wp-pagination a,.bb-wp-pagination span,
	.navigation.pagination a,.navigation.pagination span,
	.bb-affiliate-content .affiliate-link,
	.bb-btn, input, select, .select2-container--default .select2-selection--single, textarea, button, .bb-btn, #comments  li .comment-body .comment-reply-link, .bb-header.header-desktop  .create-post,
	.bb-affiliate-content .item-url,
	.bb-btn, input, select, textarea, button, .bb-btn, #comments  li .comment-body .comment-reply-link {
	  -webkit-border-radius: %12$s;
	  -moz-border-radius: %12$s;
	  border-radius: %12$s;
	}

	/* --border-radius social icons */
	.social.circle ul li a {
	    -webkit-border-radius: %15$s;
	    -moz-border-radius: %15$s;
	    border-radius: %15$s;
	}
	
	/* --Featured Menu options */
	.bb-featured-menu a {
	    background-color: %26$s;
	    color: %27$s;
	    
	    -webkit-border-radius: %28$dpx;
	  -moz-border-radius: %28$dpx;
	  border-radius: %28$dpx;
	}
';

		$css = apply_filters( 'boombox/color_scheme_styles', $css );
		$fonts_helper = Boombox_Fonts_Helper::get_instance();
		$featured_labels_design = Boombox_Template::init( 'featured-labels' )->get_designs_options();

		wp_add_inline_style( 'boombox-primary-style',
			sprintf(
				$css,
				/* -1- */
				$fonts_helper->generate_inline_css_font_family( $set['design_primary_font_family']['font-family'], 'sans-serif' ),
				/* -2- */
				$fonts_helper->generate_inline_css_font_family( $set['design_secondary_font_family']['font-family'], 'sans-serif' ),
				/* -3- */
				$set['design_background_style'],
				/* -4- */
				$set['design_body_background_color'],
				/* -5- */
				$set['design_content_background_color'],
				/* -6- */
				$set['design_primary_color'],
				/* -7- */
				$set['design_primary_text_color'],
				/* -8- */
				$set['design_base_text_color'],
				/* -9- */
				$set['design_secondary_text_color'],
				/* -10- */
				$set['design_heading_text_color'],
				/* -11- */
				$set['design_border_radius'],
				/* -12- */
				$set['design_inputs_buttons_border_radius'],
				/* -13- */
				$set['design_border_color'],
				/* -14- */
				$set['design_secondary_components_background_color'],
				/* -15- */
				$set['design_social_icons_border_radius'],
				/* -16- */
				$fonts_helper->generate_inline_css_font_family( $set['design_post_titles_font_family']['font-family'], 'sans-serif' ),
				/* -17- */
				$fonts_helper->generate_inline_css_font_family( $set['design_logo_font_family']['font-family'], 'sans-serif' ),
				/* -18- */
				$set['design_general_text_font_size'],
				/* -19- */
				$set['design_single_post_heading_font_size'],
				/* -20- */
				$set['design_widget_heading_font_size'],
				/* -21- */
				$set['design_link_text_color'],
				/* -22- */
				$set['design_secondary_components_text_color'],
				/* -23- */
				$set['design_body_background_image_css'],
				/* -24- */
				$set['design_logo_font_family']['font-weight'],
				/* -25- */
				$set['design_logo_font_family']['font-style'],
				/* -26- */
				$featured_labels_design['bg_clr'],
				/* -27- */
				$featured_labels_design['text_clr'],
				/* -28- */
				$featured_labels_design['border_radius']
			)
		);
	}


	/**
	 * Enqueue front-end CSS for badges styles
	 *
	 * @see wp_add_inline_style()
	 */
	public function badges_style_css() {
		$css = '
		/* Custom Header Styles */
	
		/* -badge bg color */
		.reaction-item .reaction-bar .reaction-stat,
		.bb-badge .circle {
		  background-color: %1$s;
		}
	
		.reaction-item .reaction-vote-btn:not(.disabled):hover,
		.reaction-item.voted .reaction-vote-btn {
			background-color: %1$s !important;
		}
	
		/* -badge text color */
		.reaction-item .reaction-vote-btn:not(.disabled):hover,
		.reaction-item.voted .reaction-vote-btn,
		.bb-badge .text {
		  color: %2$s;
		}
	
		/* -category/tag bg color */
		.bb-badge.category .circle,
		.bb-badge.post_tag .circle {
		  background-color:  %6$s;
		}
	
		/* -category/tag text color */
		.bb-badge.category .text,
		.bb-badge.post_tag .text {
		  color:  %8$s;
		}
	
		/* -category/tag icon color */
		.bb-badge.category .circle i,
		.bb-badge.post_tag .circle i {
		  color:  %7$s;
		}
	
		/* --Trending */
		.bb-badge.trending .circle,
		.bb-page-header .bb-trending-navigation ul li.active a,
		.post-number {
		  background-color: %3$s;
		}
	
		.widget-title .bb-icon,
		.bb-trending-navigation ul li a .bb-icon, .trending-post .bb-post-single .s-post-views {
		  color: %3$s;
		}
	
		.bb-badge.trending .circle i,
		.bb-page-header .bb-trending-navigation ul li.active a,
		.bb-page-header .bb-trending-navigation ul li.active a .bb-icon,
		.post-number {
		  color: %4$s;
		}
	
		.bb-badge.trending .text {
			color: %5$s;
		}
	
		%9$s
	';

		$set = boombox_get_theme_options_set( array(
			'extras_badges_reactions_background_color',
			'extras_badges_reactions_text_color',
			'extras_badges_trending_background_color',
			'extras_badges_trending_icon_color',
			'extras_badges_trending_text_color',
			'extras_badges_category_background_color',
			'extras_badges_category_icon_color',
			'extras_badges_category_text_color'
		) );

		wp_add_inline_style( 'boombox-primary-style',
			sprintf(
				$css,
				$set['extras_badges_reactions_background_color'],       /* -1- */
				$set['extras_badges_reactions_text_color'],             /* -2- */
				$set['extras_badges_trending_background_color'],        /* -3- */
				$set['extras_badges_trending_icon_color'],              /* -4- */
				$set['extras_badges_trending_text_color'],              /* -5- */
				$set['extras_badges_category_background_color'],        /* -6- */
				$set['extras_badges_category_icon_color'],              /* -7- */
				$set['extras_badges_category_text_color'],              /* -8- */
				$this->get_terms_personal_styles()                      /* -9- */
			)
		);
	}

	/**
	 * Get terms personal styles
	 * @return string
	 */
	public function get_terms_personal_styles() {
		global $wpdb;

		$query = "SELECT `t`.`term_id`,`t`.`name`,`t`.`slug`,`tt`.`taxonomy`,`tm`.`meta_value` AS `color`
		FROM `" . $wpdb->terms . "` AS `t`
		LEFT JOIN `" . $wpdb->term_taxonomy . "` AS `tt` ON `tt`.`term_id` = `t`.`term_id`
		INNER JOIN `" . $wpdb->termmeta . "` AS `tm` ON `tm`.`term_id` = `t`.`term_id` AND `tm`.`meta_key` = %s";

		$query = $wpdb->prepare( apply_filters( 'boombox/term_personal_styles_query', $query ),'term_icon_background_color' );

		$terms_color_data = $wpdb->get_results( $query );
		$css = '';

		$format = boombox_is_amp() ? '.bb-badge-list .bb-badge.%1$s-%2$d { background-color: %3$s; }' : '.bb-badge.%1$s-%2$d .circle { background-color: %3$s; }';
		foreach( $terms_color_data as $term_color_data ) {
			$css .= sprintf(
				$format,
				$term_color_data->taxonomy,
				$term_color_data->term_id,
				$term_color_data->color
			);
		}
		return $css;
	}

	/**
	 * Setup mobile bar color
	 * @since 2.5.5
	 * @version 2.5.5
	 */
	public function set_mobile_address_bar_color() {
		if( ! wp_is_mobile() ) {
			return;
		}

		$address_bar__color = apply_filters( 'boombox/mobile/address_bar_color', boombox_get_theme_option( 'mobile_header_address_bar_color' ) );
		if( ! $address_bar__color ) {
			return;
		}

		printf( '<meta name="theme-color" content="%s" />', $address_bar__color );
	}

}

Boombox_Design_Scheme::get_instance();