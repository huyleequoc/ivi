<?php
/**
 * One Click Demo Import plugin functions
 *
 * @package BoomBox_Theme
 * @since 1.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! boombox_plugin_management_service()->is_plugin_active( 'one-click-demo-import/one-click-demo-import.php' ) ) {
	return;
}

if ( ! class_exists( 'Boombox_One_Click_Demo_Import' ) ) {

	final class Boombox_One_Click_Demo_Import {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = NULL;

		/**
		 * Get instance
		 * @return Boombox_One_Click_Demo_Import|null
		 */
		public static function get_instance() {

			if ( NULL == static::$_instance ) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * Boombox_One_Click_Demo_Import constructor.
		 */
		private function __construct() {
			$this->hooks();

			do_action( 'boombox/ocdi/wakeup', $this );
		}

		/**
		 * A dummy magic method to prevent Boombox_One_Click_Demo_Import from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Setup Hooks
		 */
		private function hooks() {
			/***** We will provide functionality for demo files importing only to registered copy of theme */
			if( ! boombox_is_registered() ) {
				return;
			}

			add_action( 'pt-ocdi/before_content_import', array( $this, 'before_demo_import' ) );
			add_filter( 'pt-ocdi/import_files', array( $this, 'demo_data' ), 10 );
			add_filter( 'pt-ocdi/before_widgets_import', array( $this, 'before_widgets_import' ), 10, 1 );
			add_action( 'pt-ocdi/after_import', array( $this, 'after_import_setup' ), 10, 1 );
			add_filter( 'pt-ocdi/plugin_page_setup', array( $this, 'page_setup' ), 10, 1 );
			add_filter( 'pt-ocdi/timeout_for_downloading_import_file', array( $this, 'edit_timeout_for_downloading_import_file' ), 10, 1 );
			add_filter( 'pt-ocdi/confirmation_dialog_options', array( $this, 'confirmation_dialog_options' ), 10, 1 );
		}

		/**
		 * Callback to execute before demo data import
		 */
		public function before_demo_import() {
			add_filter( 'aiom_allow_save', '__return_false' );
		}

		/**
		 * Configure Demo Content data
		 */
		public function demo_data() {

			$boombox_demo_url = 'https://boombox.px-lab.com/demos/v2';
			$warning_html = '';

			if ( get_option( 'boombox_has_demo_data', false ) ) {
				$wp_reset_plugin = sprintf( '<a href="%1$s" target="_blank" rel="noopener">%2$s</a>', 'https://srd.wordpress.org/plugins/wp-reset/', esc_html__( 'WP Reset', 'boombox' ) );
				$wordpress_reset_plugin = sprintf( '<a href="%1$s" target="_blank" rel="noopener">%2$s</a>', 'https://srd.wordpress.org/plugins/wordpress-reset/', esc_html__( 'WordPress Reset', 'boombox' ) );
				$wordpress_database_reset_plugin = sprintf( '<a href="%1$s" target="_blank" rel="noopener">%2$s</a>', 'https://hy.wordpress.org/plugins/wordpress-database-reset/', esc_html__( 'WordPress Database Reset', 'boombox' ) );

				$warning_text = sprintf(
					'<p>%1$s</p><p>%2$s</p><p>%3$s</p>',
					esc_html__( 'If you want your demo to looked exactly like selected demo and to prevent conflicts with current content, we highly recommend importing demo data on a clean installation.', 'boombox' ),
					esc_html__( 'We highly recommend to create backup of your site before database reset if you are working on your database. Please note that database reset means cleaning all content that you have in your WordPress and restore to WordPress defaults.', 'boombox' ),
					sprintf( esc_html__( 'Please feel free to use %1$s, %2$s or %3$s plugins to reset your WordPress site.', 'boombox' ), $wp_reset_plugin, $wordpress_reset_plugin, $wordpress_database_reset_plugin )
				);

				$warning_html = sprintf( '<p>%s</p>', $warning_text );
			}

			return array(
				/***** Original */
				array(
					'id'                         => 'original',
					'import_file_name'           => esc_html__( 'Boombox Original', 'boombox' ),
					'import_file_url'            => sprintf( '%1$s/original/demo-content.xml', $boombox_demo_url ),
					'import_widget_file_url'     => sprintf( '%1$s/original/widgets.wie', $boombox_demo_url ),
					'import_customizer_file_url' => sprintf( '%1$s/original/customizer.dat', $boombox_demo_url ),
					'import_preview_image_url'   => sprintf( '%1$s/original/screenshot.jpg', $boombox_demo_url ),
					'import_notice'              => sprintf( '<strong>%1$s</strong>%2$s', esc_html__( 'Original', 'boombox' ), $warning_html ),
				),
				/***** Animatrix */
				array(
					'id'                         => 'animatrix',
					'import_file_name'           => esc_html__( 'Animatrix', 'boombox' ),
					'import_file_url'            => sprintf( '%1$s/animatrix/demo-content.xml', $boombox_demo_url ),
					'import_widget_file_url'     => sprintf( '%1$s/animatrix/widgets.wie', $boombox_demo_url ),
					'import_customizer_file_url' => sprintf( '%1$s/animatrix/customizer.dat', $boombox_demo_url ),
					'import_preview_image_url'   => sprintf( '%1$s/animatrix/screenshot.jpg', $boombox_demo_url ),
					'import_notice'              => sprintf( '<strong>%1$s</strong>%2$s', esc_html__( 'Animatrix', 'boombox' ), $warning_html ),
				),
				/***** Another Gag */
				array(
					'id'                         => 'another_gag',
					'import_file_name'           => esc_html__( 'Another GAG', 'boombox' ),
					'import_file_url'            => sprintf( '%1$s/another_gag/demo-content.xml', $boombox_demo_url ),
					'import_widget_file_url'     => sprintf( '%1$s/another_gag/widgets.wie', $boombox_demo_url ),
					'import_customizer_file_url' => sprintf( '%1$s/another_gag/customizer.dat', $boombox_demo_url ),
					'import_preview_image_url'   => sprintf( '%1$s/another_gag/screenshot.jpg', $boombox_demo_url ),
					'import_notice'              => sprintf( '<strong>%1$s</strong>%2$s', esc_html__( 'Another GAG', 'boombox' ), $warning_html ),
				),
				/***** Alternative */
				array(
					'id'                         => 'alternative',
					'import_file_name'           => esc_html__( 'Boombox Alternative', 'boombox' ),
					'import_file_url'            => sprintf( '%1$s/alternative/demo-content.xml', $boombox_demo_url ),
					'import_widget_file_url'     => sprintf( '%1$s/alternative/widgets.wie', $boombox_demo_url ),
					'import_customizer_file_url' => sprintf( '%1$s/alternative/customizer.dat', $boombox_demo_url ),
					'import_preview_image_url'   => sprintf( '%1$s/alternative/screenshot.jpg', $boombox_demo_url ),
					'import_notice'              => sprintf( '<strong>%1$s</strong>%2$s', esc_html__( 'Alternative', 'boombox' ), $warning_html ),
				),
				/***** Buzzy */
				array(
					'id'                         => 'buzzy',
					'import_file_name'           => esc_html__( 'Buzzy', 'boombox' ),
					'import_file_url'            => sprintf( '%1$s/buzzy/demo-content.xml', $boombox_demo_url ),
					'import_widget_file_url'     => sprintf( '%1$s/buzzy/widgets.wie', $boombox_demo_url ),
					'import_customizer_file_url' => sprintf( '%1$s/buzzy/customizer.dat', $boombox_demo_url ),
					'import_preview_image_url'   => sprintf( '%1$s/buzzy/screenshot.jpg', $boombox_demo_url ),
					'import_notice'              => sprintf( '<strong>%1$s</strong>%2$s', esc_html__( 'Buzzy', 'boombox' ), $warning_html ),
				),
				/***** Pandaland */
				array(
					'id'                         => 'lonely_panda',
					'import_file_name'           => esc_html__( 'Pandaland', 'boombox' ),
					'import_file_url'            => sprintf( '%1$s/lonely_panda/demo-content.xml', $boombox_demo_url ),
					'import_widget_file_url'     => sprintf( '%1$s/lonely_panda/widgets.wie', $boombox_demo_url ),
					'import_customizer_file_url' => sprintf( '%1$s/lonely_panda/customizer.dat', $boombox_demo_url ),
					'import_preview_image_url'   => sprintf( '%1$s/lonely_panda/screenshot.jpg', $boombox_demo_url ),
					'import_notice'              => sprintf( '<strong>%1$s</strong>%2$s', esc_html__( 'Pandaland', 'boombox' ), $warning_html ),
				),
				/***** Boommag */
				array(
					'id'                         => 'boommag',
					'import_file_name'           => esc_html__( 'Boommag', 'boombox' ),
					'import_file_url'            => sprintf( '%1$s/boommag/demo-content.xml', $boombox_demo_url ),
					'import_widget_file_url'     => sprintf( '%1$s/boommag/widgets.wie', $boombox_demo_url ),
					'import_customizer_file_url' => sprintf( '%1$s/boommag/customizer.dat', $boombox_demo_url ),
					'import_preview_image_url'   => sprintf( '%1$s/boommag/screenshot.jpg', $boombox_demo_url ),
					'import_notice'              => sprintf( '<strong>%1$s</strong>%2$s', esc_html__( 'Boommag', 'boombox' ), $warning_html ),
				),
				/***** Minimal */
				array(
					'id'                         => 'minimal',
					'import_file_name'           => esc_html__( 'Boombox Minimal', 'boombox' ),
					'import_file_url'            => sprintf( '%1$s/minimal/demo-content.xml', $boombox_demo_url ),
					'import_widget_file_url'     => sprintf( '%1$s/minimal/widgets.wie', $boombox_demo_url ),
					'import_customizer_file_url' => sprintf( '%1$s/minimal/customizer.dat', $boombox_demo_url ),
					'import_preview_image_url'   => sprintf( '%1$s/minimal/screenshot.jpg', $boombox_demo_url ),
					'import_notice'              => sprintf( '<strong>%1$s</strong>%2$s', esc_html__( 'Minimal', 'boombox' ), $warning_html ),
				),
				/***** Zombify */
				array(
					'id'                         => 'zombify',
					'import_file_name'           => esc_html__( 'Zombify', 'boombox' ),
					'import_file_url'            => sprintf( '%1$s/zombify/demo-content.xml', $boombox_demo_url ),
					'import_widget_file_url'     => sprintf( '%1$s/zombify/widgets.wie', $boombox_demo_url ),
					'import_customizer_file_url' => sprintf( '%1$s/zombify/customizer.dat', $boombox_demo_url ),
					'import_preview_image_url'   => sprintf( '%1$s/zombify/screenshot.jpg', $boombox_demo_url ),
					'import_notice'              => sprintf( '<strong>%1$s</strong>%2$s', esc_html__( 'Zombify', 'boombox' ), $warning_html ),
				),
				/***** Affiliate */
				array(
					'id'                         => 'affiliate',
					'import_file_name'           => esc_html__( 'Boombox Affiliate', 'boombox' ),
					'import_file_url'            => sprintf( '%1$s/affiliate/demo-content.xml', $boombox_demo_url ),
					'import_widget_file_url'     => sprintf( '%1$s/affiliate/widgets.wie', $boombox_demo_url ),
					'import_customizer_file_url' => sprintf( '%1$s/affiliate/customizer.dat', $boombox_demo_url ),
					'import_preview_image_url'   => sprintf( '%1$s/affiliate/screenshot.jpg', $boombox_demo_url ),
					'import_notice'              => sprintf( '<strong>%1$s</strong>%2$s', esc_html__( 'Affiliate', 'boombox' ), $warning_html ),
				),
				/***** Catdog */
				array(
					'id'                         => 'catdog',
					'import_file_name'           => esc_html__( 'CatDog', 'boombox' ),
					'import_file_url'            => sprintf( '%1$s/catdog/demo-content.xml', $boombox_demo_url ),
					'import_widget_file_url'     => sprintf( '%1$s/catdog/widgets.wie', $boombox_demo_url ),
					'import_customizer_file_url' => sprintf( '%1$s/catdog/customizer.dat', $boombox_demo_url ),
					'import_preview_image_url'   => sprintf( '%1$s/catdog/screenshot.jpg', $boombox_demo_url ),
					'import_notice'              => sprintf( '<strong>%1$s</strong>%2$s', esc_html__( 'CatDog', 'boombox' ), $warning_html ),
				),
				/***** Sunshine */
				array(
					'id'                         => 'sunshine',
					'import_file_name'           => esc_html__( 'Sunshine', 'boombox' ),
					'import_file_url'            => sprintf( '%1$s/sunshine/demo-content.xml', $boombox_demo_url ),
					'import_widget_file_url'     => sprintf( '%1$s/sunshine/widgets.wie', $boombox_demo_url ),
					'import_customizer_file_url' => sprintf( '%1$s/sunshine/customizer.dat', $boombox_demo_url ),
					'import_preview_image_url'   => sprintf( '%1$s/sunshine/screenshot.jpg', $boombox_demo_url ),
					'import_notice'              => sprintf( '<strong>%1$s</strong>%2$s', esc_html__( 'Sunshine', 'boombox' ), $warning_html ),
				),
				/***** Readly */
				array(
					'id'                         => 'readly',
					'import_file_name'           => esc_html__( 'Readly', 'boombox' ),
					'import_file_url'            => sprintf( '%1$s/readly/demo-content.xml', $boombox_demo_url ),
					'import_widget_file_url'     => sprintf( '%1$s/readly/widgets.wie', $boombox_demo_url ),
					'import_customizer_file_url' => sprintf( '%1$s/readly/customizer.dat', $boombox_demo_url ),
					'import_preview_image_url'   => sprintf( '%1$s/readly/screenshot.jpg', $boombox_demo_url ),
					'import_notice'              => sprintf( '<strong>%1$s</strong>%2$s', esc_html__( 'Readly', 'boombox' ), $warning_html ),
				),
				/***** Dash */
				array(
					'id'                         => 'dash',
					'import_file_name'           => esc_html__( 'Dash', 'boombox' ),
					'import_file_url'            => sprintf( '%1$s/dash/demo-content.xml', $boombox_demo_url ),
					'import_widget_file_url'     => sprintf( '%1$s/dash/widgets.wie', $boombox_demo_url ),
					'import_customizer_file_url' => sprintf( '%1$s/dash/customizer.dat', $boombox_demo_url ),
					'import_preview_image_url'   => sprintf( '%1$s/dash/screenshot.jpg', $boombox_demo_url ),
					'import_notice'              => sprintf( '<strong>%1$s</strong>%2$s', esc_html__( 'Dash', 'boombox' ), $warning_html ),
				),
				/***** Gifdom */
				array(
					'id'                         => 'gifdom',
					'import_file_name'           => esc_html__( 'GifDom', 'boombox' ),
					'import_file_url'            => sprintf( '%1$s/gifdom/demo-content.xml', $boombox_demo_url ),
					'import_widget_file_url'     => sprintf( '%1$s/gifdom/widgets.wie', $boombox_demo_url ),
					'import_customizer_file_url' => sprintf( '%1$s/gifdom/customizer.dat', $boombox_demo_url ),
					'import_preview_image_url'   => sprintf( '%1$s/gifdom/screenshot.jpg', $boombox_demo_url ),
					'import_notice'              => sprintf( '<strong>%1$s</strong>%2$s', esc_html__( 'GifDom', 'boombox' ), $warning_html ),
				),
				/***** Advertimer */
				array(
					'id'                         => 'advertimer',
					'import_file_name'           => esc_html__( 'Advertimer', 'boombox' ),
					'import_file_url'            => sprintf( '%1$s/advertimer/demo-content.xml', $boombox_demo_url ),
					'import_widget_file_url'     => sprintf( '%1$s/advertimer/widgets.wie', $boombox_demo_url ),
					'import_customizer_file_url' => sprintf( '%1$s/advertimer/customizer.dat', $boombox_demo_url ),
					'import_preview_image_url'   => sprintf( '%1$s/advertimer/screenshot.jpg', $boombox_demo_url ),
					'import_notice'              => sprintf( '<strong>%1$s</strong>%2$s', esc_html__( 'Advertimer', 'boombox' ), $warning_html ),
				),
			);

		}

		/**
		 * Remove old sidebars before import
		 *
		 * @param string $selected_import Import ID
		 */
		public function before_widgets_import( $selected_import ) {
			$sidebars_widgets = get_option( 'sidebars_widgets' );
			$boombox_sidebars = boombox_get_sidebars();
			$boombox_sidebars_ids = wp_list_pluck( $boombox_sidebars, 'id' );

			foreach ( $boombox_sidebars_ids as $sidebars_id ) {
				$sidebars_widgets[ $sidebars_id ] = array();
			}

			update_option( 'sidebars_widgets', $sidebars_widgets );
		}

		/**
		 * Callback to set menu locations some required data after import
		 *
		 * @param string $selected_import Import ID
		 */
		public function after_import_setup( $selected_import ) {

			// update menus locations
			$menu_locations = array(
				'top_header_nav'       => 0,
				'bottom_header_nav'    => 0,
				'profile_nav'          => 0,
				'burger_mobile_menu_1' => 0,
				'burger_mobile_menu_2' => 0,
				'burger_mobile_menu_3' => 0,
				'burger_top_nav'       => 0,
				'burger_badges_nav'    => 0,
				'burger_bottom_nav'    => 0,
				'badges_nav'           => 0,
				'featured_labels'      => 0,
				'footer_nav'           => 0,
			);

			// front & blog page options
			$show_on_front = 'posts';
			$front_page_id = 0;
			$blog_page_id = 0;

			switch ( $selected_import[ 'id' ] ) {

				case 'original':

					/**
					 * Assign Menu Locations
					 */
					if( $top_header_nav = get_term_by( 'name', 'Main Menu', 'nav_menu' ) ) {
						$menu_locations[ 'top_header_nav' ] = $top_header_nav->term_id;
					}

					if( $bottom_header_nav = get_term_by( 'name', 'Ranking Menu', 'nav_menu' ) ) {
						$menu_locations[ 'bottom_header_nav' ] = $bottom_header_nav->term_id;
					}

					if( $profile_nav = get_term_by( 'name', 'Profile Menu', 'nav_menu' ) ) {
						$menu_locations[ 'profile_nav' ] = $profile_nav->term_id;
					}

					if( $burger_top_nav = get_term_by( 'name', 'More Button Top Menu', 'nav_menu' ) ) {
						$menu_locations[ 'burger_top_nav' ] = $burger_top_nav->term_id;
					}

					if( $burger_badges_nav = get_term_by( 'name', 'More Button Badges Menu', 'nav_menu' ) ){
						$menu_locations[ 'burger_badges_nav' ] = $burger_badges_nav->term_id;
					}

					if( $burger_bottom_nav = get_term_by( 'name', 'More Button Bottom Menu', 'nav_menu' ) ){
						$menu_locations[ 'burger_bottom_nav' ] = $burger_bottom_nav->term_id;
					}

					if( $badges_nav = get_term_by( 'name', 'Badges Menu', 'nav_menu' ) ) {
						$menu_locations[ 'badges_nav' ] = $badges_nav->term_id;
					}

					if( $featured_labels_menu = get_term_by( 'name', 'Featured Labels Menu', 'nav_menu' ) ) {
						$menu_locations[ 'featured_labels' ] = $featured_labels_menu->term_id;
					}

					if( $footer_nav = get_term_by( 'name', 'Footer Menu', 'nav_menu' ) ) {
						$menu_locations[ 'footer_nav' ] = $footer_nav->term_id;
					}

					break;

				case 'animatrix':

					/**
					 * Assign Menu Locations
					 */
					if( $top_header_nav = get_term_by( 'name', 'Ranking Menu', 'nav_menu' ) ) {
						$menu_locations[ 'top_header_nav' ] = $top_header_nav->term_id;
					}

					if( $profile_nav = get_term_by( 'name', 'Profile Menu', 'nav_menu' ) ) {
						$menu_locations[ 'profile_nav' ] = $profile_nav->term_id;
					}

					if( $burger_top_nav = get_term_by( 'name', 'More Button Top Menu', 'nav_menu' ) ) {
						$menu_locations[ 'burger_top_nav' ] = $burger_top_nav->term_id;
					}

					if( $burger_badges_nav = get_term_by( 'name', 'More Button Badges Menu', 'nav_menu' ) ){
						$menu_locations[ 'burger_badges_nav' ] = $burger_badges_nav->term_id;
					}

					if( $burger_bottom_nav = get_term_by( 'name', 'More Button Bottom Menu', 'nav_menu' ) ){
						$menu_locations[ 'burger_bottom_nav' ] = $burger_bottom_nav->term_id;
					}

					if( $footer_nav = get_term_by( 'name', 'Footer Menu', 'nav_menu' ) ) {
						$menu_locations[ 'footer_nav' ] = $footer_nav->term_id;
					}

					break;

				case 'another_gag':

					/**
					 * Assign Menu Locations
					 */
					if( $top_header_nav = get_term_by( 'name', 'Main Menu', 'nav_menu' ) ) {
						$menu_locations[ 'top_header_nav' ] = $top_header_nav->term_id;
					}

					if( $profile_nav = get_term_by( 'name', 'Profile Menu', 'nav_menu' ) ) {
						$menu_locations[ 'profile_nav' ] = $profile_nav->term_id;
					}

					if( $burger_top_nav = get_term_by( 'name', 'More Button Top Menu', 'nav_menu' ) ) {
						$menu_locations[ 'burger_top_nav' ] = $burger_top_nav->term_id;
					}

					if( $burger_badges_nav = get_term_by( 'name', 'More Button Badges Menu', 'nav_menu' ) ){
						$menu_locations[ 'burger_badges_nav' ] = $burger_badges_nav->term_id;
					}

					if( $burger_bottom_nav = get_term_by( 'name', 'More Button Bottom Menu', 'nav_menu' ) ){
						$menu_locations[ 'burger_bottom_nav' ] = $burger_bottom_nav->term_id;
					}

					if( $featured_labels_menu = get_term_by( 'name', 'Featured Labels Menu', 'nav_menu' ) ) {
						$menu_locations[ 'featured_labels' ] = $featured_labels_menu->term_id;
					}

					if( $footer_nav = get_term_by( 'name', 'Footer Menu', 'nav_menu' ) ) {
						$menu_locations[ 'footer_nav' ] = $footer_nav->term_id;
					}

					break;

				case 'alternative':

					// assign menu locations
					if( $bottom_header_nav = get_term_by( 'name', 'Bottom Header Menu - Alternative', 'nav_menu' ) ) {
						$menu_locations[ 'bottom_header_nav' ] = $bottom_header_nav->term_id;
					}

					if( $badges_nav = get_term_by( 'name', 'Badges Menu - Alternative', 'nav_menu' ) ) {
						$menu_locations[ 'badges_nav' ] = $badges_nav->term_id;
					}

					if( $burger_bottom_nav = get_term_by( 'name', 'Burger Bottom Menu - Alternative', 'nav_menu' ) ) {
						$menu_locations[ 'burger_bottom_nav' ] = $burger_bottom_nav->term_id;
					}

					// assign front and blog page options
					$show_on_front = 'page';

					$front_page = get_page_by_title( 'Home Page' );
					$front_page_id = $front_page->ID;

					break;

				case 'buzzy':

					/**
					 * Assign Menu Locations
					 */
					if( $top_header_nav = get_term_by( 'name', 'Main Menu', 'nav_menu' ) ) {
						$menu_locations[ 'top_header_nav' ] = $top_header_nav->term_id;
					}

					if( $profile_nav = get_term_by( 'name', 'Profile Menu', 'nav_menu' ) ) {
						$menu_locations[ 'profile_nav' ] = $profile_nav->term_id;
					}

					if( $burger_top_nav = get_term_by( 'name', 'More Button Top Menu', 'nav_menu' ) ) {
						$menu_locations[ 'burger_top_nav' ] = $burger_top_nav->term_id;
					}

					if( $burger_badges_nav = get_term_by( 'name', 'More Button Badges Menu', 'nav_menu' ) ){
						$menu_locations[ 'burger_badges_nav' ] = $burger_badges_nav->term_id;
					}

					if( $burger_bottom_nav = get_term_by( 'name', 'More Button Bottom Menu', 'nav_menu' ) ){
						$menu_locations[ 'burger_bottom_nav' ] = $burger_bottom_nav->term_id;
					}

					if( $badges_nav = get_term_by( 'name', 'Badges Menu', 'nav_menu' ) ) {
						$menu_locations[ 'badges_nav' ] = $badges_nav->term_id;
					}

					if( $footer_nav = get_term_by( 'name', 'Footer Menu', 'nav_menu' ) ) {
						$menu_locations[ 'footer_nav' ] = $footer_nav->term_id;
					}

					break;

				case 'lonely_panda':

					/**
					 * Assign Menu Locations
					 */
					if( $top_header_nav = get_term_by( 'name', 'Main Menu', 'nav_menu' ) ) {
						$menu_locations[ 'top_header_nav' ] = $top_header_nav->term_id;
					}

					if( $bottom_header_nav = get_term_by( 'name', 'Ranking Menu', 'nav_menu' ) ) {
						$menu_locations[ 'bottom_header_nav' ] = $bottom_header_nav->term_id;
					}

					if( $profile_nav = get_term_by( 'name', 'Profile Menu', 'nav_menu' ) ) {
						$menu_locations[ 'profile_nav' ] = $profile_nav->term_id;
					}

					if( $burger_top_nav = get_term_by( 'name', 'More Button Top Menu', 'nav_menu' ) ) {
						$menu_locations[ 'burger_top_nav' ] = $burger_top_nav->term_id;
					}

					if( $burger_badges_nav = get_term_by( 'name', 'More Button Badges Menu', 'nav_menu' ) ){
						$menu_locations[ 'burger_badges_nav' ] = $burger_badges_nav->term_id;
					}

					if( $burger_bottom_nav = get_term_by( 'name', 'More Button Bottom Menu', 'nav_menu' ) ){
						$menu_locations[ 'burger_bottom_nav' ] = $burger_bottom_nav->term_id;
					}

					if( $footer_nav = get_term_by( 'name', 'Footer Menu', 'nav_menu' ) ) {
						$menu_locations[ 'footer_nav' ] = $footer_nav->term_id;
					}

					break;

				case 'boommag':

					// assign menu locations
					if( $top_header_nav = get_term_by( 'name', 'Top Header Menu - Boommag', 'nav_menu' ) ) {
						$menu_locations['top_header_nav'] = $top_header_nav->term_id;
					}

					if( $bottom_header_nav = get_term_by( 'name', 'Bottom Header Menu - Boommag', 'nav_menu' ) ) {
						$menu_locations[ 'bottom_header_nav' ] = $bottom_header_nav->term_id;
					}

					if( $badges_nav = get_term_by( 'name', 'Badges Menu - Boommag', 'nav_menu' ) ) {
						$menu_locations[ 'badges_nav' ] = $badges_nav->term_id;
					}

					if( $burger_top_nav = get_term_by( 'name', 'Burger Top Menu - Boommag', 'nav_menu' ) ) {
						$menu_locations[ 'burger_top_nav' ] = $burger_top_nav->term_id;
					}

					if( $burger_bottom_nav = get_term_by( 'name', 'Burger Bottom Menu - Boommag', 'nav_menu' ) ) {
						$menu_locations[ 'burger_bottom_nav' ] = $burger_bottom_nav->term_id;
					}

					if( $burger_badges_nav = get_term_by( 'name', 'Burger Badges Menu - Boommag', 'nav_menu' ) ) {
						$menu_locations[ 'burger_badges_nav' ] = $burger_badges_nav->term_id;
					}

					if( $footer_nav = get_term_by( 'name', 'Footer Menu - Boommag', 'nav_menu' ) ) {
						$menu_locations[ 'footer_nav' ] = $footer_nav->term_id;
					}

					// assign front and blog page options
					$show_on_front = 'page';

					$front_page = get_page_by_title( 'Home Page' );
					$front_page_id = $front_page->ID;

					break;

				case 'minimal':

					// assign menu locations
					if( $bottom_header_nav = get_term_by( 'name', 'Bottom Header Menu - Minimal', 'nav_menu' ) ) {
						$menu_locations[ 'bottom_header_nav' ] = $bottom_header_nav->term_id;
					}

					// assign front and blog page options
					$show_on_front = 'page';

					$front_page = get_page_by_title( 'Home Page' );
					$front_page_id = $front_page->ID;

					break;

				case 'zombify':

					// assign menu locations
					if( $badges_nav = get_term_by( 'name', 'Badges Menu - Zombify', 'nav_menu' ) ) {
						$menu_locations[ 'badges_nav' ] = $badges_nav->term_id;
					}

					if( $footer_nav = get_term_by( 'name', 'Footer Menu - Zombify', 'nav_menu' ) ) {
						$menu_locations[ 'footer_nav' ] = $footer_nav->term_id;
					}

					// assign front and blog page options
					$show_on_front = 'page';

					$front_page = get_page_by_title( 'Home Page' );
					$front_page_id = $front_page->ID;

					break;

				case 'affiliate':

					/**
					 * Assign Menu Locations
					 */
					if( $bottom_header_nav = get_term_by( 'name', 'Ranking Menu', 'nav_menu' ) ) {
						$menu_locations[ 'bottom_header_nav' ] = $bottom_header_nav->term_id;
					}

					if( $profile_nav = get_term_by( 'name', 'Profile Menu', 'nav_menu' ) ) {
						$menu_locations[ 'profile_nav' ] = $profile_nav->term_id;
					}

					if( $burger_top_nav = get_term_by( 'name', 'More Button Top Menu', 'nav_menu' ) ) {
						$menu_locations[ 'burger_top_nav' ] = $burger_top_nav->term_id;
					}

					if( $burger_badges_nav = get_term_by( 'name', 'More Button Badges Menu', 'nav_menu' ) ){
						$menu_locations[ 'burger_badges_nav' ] = $burger_badges_nav->term_id;
					}

					if( $burger_bottom_nav = get_term_by( 'name', 'More Button Bottom Menu', 'nav_menu' ) ){
						$menu_locations[ 'burger_bottom_nav' ] = $burger_bottom_nav->term_id;
					}

					if( $featured_labels_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' ) ) {
						$menu_locations[ 'featured_labels' ] = $featured_labels_menu->term_id;
					}

					if( $footer_nav = get_term_by( 'name', 'Footer Menu', 'nav_menu' ) ) {
						$menu_locations[ 'footer_nav' ] = $footer_nav->term_id;
					}

					break;

				case 'catdog':

					// assign menu locations
					if( $bottom_header_nav = get_term_by( 'name', 'Bottom Header Menu - Catdog', 'nav_menu' ) ) {
						$menu_locations[ 'bottom_header_nav' ] = $bottom_header_nav->term_id;
					}

					if( $badges_nav = get_term_by( 'name', 'Badges Menu - Catdog', 'nav_menu' ) ) {
						$menu_locations[ 'badges_nav' ] = $badges_nav->term_id;
					}

					if( $footer_nav = get_term_by( 'name', 'Footer Menu - Catdog', 'nav_menu' ) ) {
						$menu_locations[ 'footer_nav' ] = $footer_nav->term_id;
					}

					// assign front and blog page options
					$show_on_front = 'page';

					$front_page = get_page_by_title( 'Home Page' );
					$front_page_id = $front_page->ID;

					break;

				case 'sunshine':

					// assign menu locations
					if( $top_header_nav = get_term_by( 'name', 'Burger Top Menu - Sunshine', 'nav_menu' ) ) {
						$menu_locations[ 'top_header_nav' ] = $top_header_nav->term_id;
					}

					if( $footer_nav = get_term_by( 'name', 'Footer Menu - Sunshine', 'nav_menu' ) ) {
						$menu_locations[ 'footer_nav' ] = $footer_nav->term_id;
					}

					// assign front and blog page options
					$show_on_front = 'page';

					$front_page = get_page_by_title( 'Home Page' );
					$front_page_id = $front_page->ID;

					break;

				case 'readly':

					/**
					 * Assign Menu Locations
					 */
					if( $top_header_nav = get_term_by( 'name', 'Ranking Menu', 'nav_menu' ) ) {
						$menu_locations[ 'top_header_nav' ] = $top_header_nav->term_id;
					}

					if( $profile_nav = get_term_by( 'name', 'Profile Menu', 'nav_menu' ) ) {
						$menu_locations[ 'profile_nav' ] = $profile_nav->term_id;
					}

					if( $burger_top_nav = get_term_by( 'name', 'More Button Top Menu', 'nav_menu' ) ) {
						$menu_locations[ 'burger_top_nav' ] = $burger_top_nav->term_id;
					}

					if( $burger_badges_nav = get_term_by( 'name', 'More Button Badges Menu', 'nav_menu' ) ){
						$menu_locations[ 'burger_badges_nav' ] = $burger_badges_nav->term_id;
					}

					if( $burger_bottom_nav = get_term_by( 'name', 'More Button Bottom Menu', 'nav_menu' ) ){
						$menu_locations[ 'burger_bottom_nav' ] = $burger_bottom_nav->term_id;
					}

					if( $footer_nav = get_term_by( 'name', 'Footer Menu', 'nav_menu' ) ) {
						$menu_locations[ 'footer_nav' ] = $footer_nav->term_id;
					}

					break;

				case 'dash':

					/**
					 * Assign Menu Locations
					 */
					if( $top_header_nav = get_term_by( 'name', 'Main Menu', 'nav_menu' ) ) {
						$menu_locations[ 'top_header_nav' ] = $top_header_nav->term_id;
					}

					if( $profile_nav = get_term_by( 'name', 'Profile Menu', 'nav_menu' ) ) {
						$menu_locations[ 'profile_nav' ] = $profile_nav->term_id;
					}

					if( $burger_top_nav = get_term_by( 'name', 'More Button Top Menu', 'nav_menu' ) ) {
						$menu_locations[ 'burger_top_nav' ] = $burger_top_nav->term_id;
					}

					if( $burger_badges_nav = get_term_by( 'name', 'More Button Badges Menu', 'nav_menu' ) ){
						$menu_locations[ 'burger_badges_nav' ] = $burger_badges_nav->term_id;
					}

					if( $burger_bottom_nav = get_term_by( 'name', 'More Button Bottom Menu', 'nav_menu' ) ){
						$menu_locations[ 'burger_bottom_nav' ] = $burger_bottom_nav->term_id;
					}

					if( $footer_nav = get_term_by( 'name', 'Footer Menu', 'nav_menu' ) ) {
						$menu_locations[ 'footer_nav' ] = $footer_nav->term_id;
					}

					break;

				case 'gifdom':

					/**
					 * Assign Menu Locations
					 */
					if( $top_header_nav = get_term_by( 'name', 'Main Menu', 'nav_menu' ) ) {
						$menu_locations[ 'top_header_nav' ] = $top_header_nav->term_id;
					}

					if( $profile_nav = get_term_by( 'name', 'Profile Menu', 'nav_menu' ) ) {
						$menu_locations[ 'profile_nav' ] = $profile_nav->term_id;
					}

					if( $burger_top_nav = get_term_by( 'name', 'More Button Top Menu', 'nav_menu' ) ) {
						$menu_locations[ 'burger_top_nav' ] = $burger_top_nav->term_id;
					}

					if( $burger_badges_nav = get_term_by( 'name', 'More Button Badges Menu', 'nav_menu' ) ){
						$menu_locations[ 'burger_badges_nav' ] = $burger_badges_nav->term_id;
					}

					if( $burger_bottom_nav = get_term_by( 'name', 'More Button Bottom Menu', 'nav_menu' ) ){
						$menu_locations[ 'burger_bottom_nav' ] = $burger_bottom_nav->term_id;
					}

					if( $featured_labels_menu = get_term_by( 'name', 'Featured Labels Menu', 'nav_menu' ) ) {
						$menu_locations[ 'featured_labels' ] = $featured_labels_menu->term_id;
					}

					if( $footer_nav = get_term_by( 'name', 'Footer Menu', 'nav_menu' ) ) {
						$menu_locations[ 'footer_nav' ] = $footer_nav->term_id;
					}

					break;

				case 'advertimer':

					/**
					 * Assign Menu Locations
					 */
					if( $top_header_nav = get_term_by( 'name', 'Top Header Menu', 'nav_menu' ) ) {
						$menu_locations[ 'top_header_nav' ] = $top_header_nav->term_id;
					}

					if( $bottom_header_nav = get_term_by( 'name', 'Featured Labels Menu', 'nav_menu' ) ) {
						$menu_locations[ 'bottom_header_nav' ] = $bottom_header_nav->term_id;
					}

					if( $burger_mobile_menu_1 = get_term_by( 'name', 'Top Header Menu', 'nav_menu' ) ) {
						$menu_locations[ 'burger_mobile_menu_1' ] = $burger_mobile_menu_1->term_id;
					}

					if( $burger_mobile_menu_2 = get_term_by( 'name', 'Featured Labels Menu', 'nav_menu' ) ) {
						$menu_locations[ 'burger_mobile_menu_2' ] = $burger_mobile_menu_2->term_id;
					}

					if( $featured_labels_menu = get_term_by( 'name', 'Featured Labels Menu', 'nav_menu' ) ) {
						$menu_locations[ 'featured_labels' ] = $featured_labels_menu->term_id;
					}

					if( $footer_nav = get_term_by( 'name', 'Footer Menu', 'nav_menu' ) ) {
						$menu_locations[ 'footer_nav' ] = $footer_nav->term_id;
					}

					break;
			}

			// set menu locations
			set_theme_mod( 'nav_menu_locations', $menu_locations );

			// set front & blog page options
			update_option( 'show_on_front', $show_on_front );
			update_option( 'page_on_front', $front_page_id );
			update_option( 'page_for_posts', $blog_page_id );

			update_option( 'boombox_has_demo_data', 1 );

		}

		/**
		 * Update titles for demo import page
		 *
		 * @param array $plugin_page_setup Current page data
		 *
		 * @return array
		 */
		public function page_setup( $plugin_page_setup ) {
			$plugin_page_setup[ 'page_title' ] = esc_html__( 'Boombox Demo Import', 'pt-ocdi' );
			$plugin_page_setup[ 'menu_title' ] = esc_html__( 'Boombox Demo Import', 'pt-ocdi' );

			return $plugin_page_setup;
		}

		/**
		 * Time in seconds, before the connection is dropped and an error is returned
		 *
		 * @param int $timeout Time in seconds
		 *
		 * @return int
		 */
		public function edit_timeout_for_downloading_import_file( $timeout ) {
			$timeout = 180;

			return $timeout;
		}

		/**
		 * Edit configuration dialog options
		 *
		 * @param array $options Current options
		 *
		 * @return array
		 */
		public function confirmation_dialog_options( $options ) {
			return array_merge( $options, array(
				'width'       => 500,
				'dialogClass' => 'wp-dialog',
				'resizable'   => false,
				'height'      => 'auto',
				'modal'       => true,
			) );
		}

	}

	Boombox_One_Click_Demo_Import::get_instance();

}