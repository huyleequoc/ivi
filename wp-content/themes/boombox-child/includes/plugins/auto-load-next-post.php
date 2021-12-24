<?php
/**
 * Auto Load Nex Post plugin functions
 *
 * @package BoomBox_Theme
 * @version 1.0.0
 * @since   1.9.5.0
 */

// Prevent direct script access.
if( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if( ! boombox_plugin_management_service()->is_plugin_active( 'auto-load-next-post/auto-load-next-post.php' ) ) {
	return;
}

if( ! class_exists( 'Boombox_Auto_Load_Next_Post' ) ) {
	
	final class Boombox_Auto_Load_Next_Post {
		
		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = null;
		
		/**
		 * Get instance
		 * @return Boombox_Auto_Load_Next_Post|null
		 */
		public static function get_instance() {
			
			if( null == static::$_instance ) {
				static::$_instance = new self();
			}
			
			return static::$_instance;
			
		}
		
		/**
		 * Boombox_Auto_Load_Next_Post constructor.
		 */
		private function __construct() {
			$this->hooks();

			do_action( 'boombox/alnp/wakeup', $this );
		}
		
		/**
		 * A dummy magic method to prevent Boombox_Auto_Load_Next_Post from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}
		
		/**
		 * Setup Hooks
		 */
		private function hooks() {
			add_action( 'after_setup_theme', array( $this, 'add_theme_support' ), 11 );
			
			add_filter( 'auto_load_next_post_theme_selectors_settings', array( $this, 'edit_general_settings' ), 10, 1 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'boombox/single/after_main_content', array( $this, 'render_next_prev_urls' ) );
			
			add_action( 'boombox/single/after_sortables', array( $this, 'render_container' ) );
			add_action( 'balnp_load_before_content', array( $this, 'before_load_content' ) );
			add_action( 'balnp_load_after_content', array( $this, 'after_load_content' ) );
			
			
			add_action( 'alnp_load_before_loop', array( $this, 'boombox_almp_before_loop' ), 10, 1 );
			add_action( 'alnp_load_after_loop', array( $this, 'boombox_almp_after_loop' ), 10, 1 );
			add_filter( 'boombox/prev_post_empty_url', array( $this, 'on_empty_prev_next_url' ), 10, 3 );
			add_filter( 'boombox/next_post_empty_url', array( $this, 'on_empty_prev_next_url' ), 10, 3 );
			add_filter( 'boombox/single/link_pages/hide_container', array( $this, 'single_link_pages_hide_container' ), 10, 1 );
		}
		
		/**
		 * Add theme support
		 */
		public function add_theme_support() {
			add_theme_support( 'auto-load-next-post' );
		}
		
		/**
		 * Edit general settings
		 *
		 * @param array <string,mixed> $fields Current fields setup
		 *
		 * @return array
		 */
		public function edit_general_settings( $fields ) {
			
			$denied_fields = array(
				'auto_load_next_post_content_container'    => 'div#bb-alnp-content-container',
				'auto_load_next_post_title_selector'       => 'h1.entry-title',
				'auto_load_next_post_navigation_container' => 'div.bb-alnp-urls:last',
				'auto_load_next_post_comments_container'   => 'section#boombox_comments',
			);
			
			foreach ( $fields as $index => $field ) {
				
				if( array_key_exists( $field[ 'id' ], $denied_fields ) ) {
					$fields[ $index ][ 'default' ] = $denied_fields[ $field[ 'id' ] ];
					$fields[ $index ][ 'desc' ] = sprintf( __( 'Boombox: <code>%s</code>', 'auto-load-next-post' ), $denied_fields[ $field[ 'id' ] ] );
					$fields[ $index ][ 'custom_attributes' ] = array(
						'readonly' => 'readonly',
					);
				}
				
			}
			
			return $fields;
		}
		
		/**
		 * Enqueue scripts
		 */
		public function enqueue_scripts() {
			wp_add_inline_script( 'auto-load-next-post-script', '
                jQuery( document ).on( "ready", function() {
                    var boombox_alnp_container = nav_container,
                        boombox_keep_class = "boombox-keep";

                    jQuery( "body" ).on( "alnp-post-url", function(){
                        nav_container += ":not( ." + boombox_keep_class + " )";
                    } );
                    jQuery( "body" ).on( "alnp-post-data", function(){
                        nav_container = boombox_alnp_container;
                    } );
                } );
            ', 'after' );
		}
		
		/**
		 * Render next / prev posts URLs
		 * @since 2.5.0
		 * @version 2.5.0
		 */
		public function render_next_prev_urls() {
			/**
			 * @var $helper Boombox_Single_Post_Template_Helper Template Helper
			 */
			$helper = Boombox_Template::init( 'post' );
			$nav_posts = $helper->get_post_next_prev_posts( 'navigation' );
			
			$reverse = ( boombox_get_theme_option( 'single_post_general_navigation_direction' ) == 'to-oldest' );
			$post = $reverse ? $nav_posts[ 'next' ] : $nav_posts[ 'prev' ];
			if( ! $post ) {
				return;
			} ?>
			<div class="hidden bb-alnp-urls">
				<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" rel="prev"><?php echo esc_html( $post->post_title ); ?></a>
			</div>
			<?php
		}
		
		/**
		 * Render container where the new loaded posts will be appended
		 */
		public function render_container() {
			echo '<div id="bb-alnp-content-container"></div>';
		}
		
		/**
		 * Stuff to do before content render
		 */
		public function before_load_content() {
			add_filter( 'quads_render_ad', array( $this, 'render_empty' ), 100, 1 );
			add_filter( 'boombox/single_post/hide_ads', array( $this, 'hide_ads' ), 10, 2 );
			add_filter( 'boombox/single_post/template', array( $this, 'force_single_post_template' ), 10, 1 );
			remove_action( 'boombox/single/after_sortables', array( $this, 'render_container' ) );
		}
		
		/**
		 * Stuff to do after content render
		 */
		public function after_load_content() {
			remove_filter( 'quads_render_ad', array( $this, 'render_empty' ), 100 );
			remove_filter( 'boombox/single_post/hide_ads', array( $this, 'hide_ads' ), 10 );
			remove_filter( 'boombox/single_post/template', array( $this, 'force_single_post_template' ), 10, 1 );
			add_action( 'boombox/single/after_sortables', array( $this, 'render_container' ) );
		}

		/**
		 * Edit single post template
		 * @param string $template Current template
		 *
		 * @return string
		 */
		public function force_single_post_template( $template ) {
			$template = 'style1';

			return $template;
		}

		/**
		 * Edit single template settings
		 * @param array $settings Current settings
		 *
		 * @return array
		 */
		public function edit_single_template_settings( $settings ) {
			$settings[ 'classes' ] .= ' bb-alnp-item item-added';
			return $settings;
		}
		
		/**
		 * Hook into before load
		 */
		public function boombox_almp_before_loop() {
			add_filter( 'boombox/single_template_settings', array( $this, 'edit_single_template_settings' ), 10, 1 );
		}
		
		/**
		 * Hook into after load
		 */
		public function boombox_almp_after_loop() {
			remove_filter( 'boombox/single_template_settings', array( $this, 'edit_single_template_settings' ), 10 );
		}
		
		/**
		 * Set empty URL for non AMP versions if it should be
		 *
		 * @param string $url    Current URL
		 * @param string $type   Case identifier
		 * @param bool   $is_amp If current state an AMP version
		 *
		 * @return string
		 */
		public function on_empty_prev_next_url( $url, $type, $is_amp ) {
			if( ! $is_amp ) {
				$url = '';
			}
			
			return $url;
		}
		
		/**
		 * Hide container
		 *
		 * @param bool $hide Current state
		 *
		 * @return bool
		 */
		public function single_link_pages_hide_container( $hide ) {
			$hide = true;
			
			return $hide;
		}
		
		/**
		 * Callback to hide ads
		 *
		 * @param bool   $hide     Current state
		 * @param string $location Ad location
		 *
		 * @return bool
		 */
		public function hide_ads( $hide, $location ) {
			$hide = true;
			
			return $hide;
		}
		
		/**
		 * Edit ad html
		 *
		 * @param string $html Current HTML
		 *
		 * @return string
		 */
		public function render_empty( $html ) {
			$html = '';
			
			return $html;
		}
		
	}
	
	Boombox_Auto_Load_Next_Post::get_instance();
	
}