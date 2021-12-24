<?php
/**
 * Mashshare plugin functions
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 1.0.0
 */

// Prevent direct script access.
if ( !defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( !boombox_plugin_management_service()->is_plugin_active( 'mashsharer/mashshare.php' ) ) {
	return;
}

if ( !class_exists( 'Boombox_Mashshare' ) ) {

	class Boombox_Mashshare {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = NULL;

		/**
		 * Get instance
		 * @return Boombox_Mashshare|null
		 */
		public static function get_instance () {

			if ( NULL == static::$_instance ) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * Boombox_Mashshare constructor.
		 */
		private function __construct () {
			$this->hooks();

			do_action( 'boombox/mashshare/wakeup', $this );
		}

		/**
		 * A dummy magic method to prevent Boombox_Mashshare from being cloned.
		 */
		public function __clone () {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Setup Hooks
		 */
		private function hooks () {

			add_filter( 'mashsb_opengraph_meta', array( $this, 'opengraph_meta' ), 10, 1 );
			add_filter( 'mashsb_twitter_title', array( $this, 'bb_listings_page_mashsb_twitter_title' ), 10, 1 );
			add_action( 'pre_amp_render_post', array( $this, 'amp_pre_render_post' ), 10, 1 );
			add_filter( 'boombox_conditions_choices', array( $this, 'add_most_shared_to_conditions' ), 10, 1 );
			add_filter( 'boombox_trending_conditions_choices', array( $this, 'add_most_shared_to_conditions' ), 10, 1 );

		}

		/**
		 * Set right title for listing posts for twitter share
		 *
		 * @param string $title Current title
		 *
		 * @return string
		 */
		public function bb_listings_page_mashsb_twitter_title ( $title ) {
			if ( is_page() ) {
				$listing_type = boombox_get_post_meta( get_the_ID(), 'boombox_listing_type' );
				if ( $listing_type != 'none' ) {
					global $post;
					$title = strtr( htmlspecialchars_decode( $post->post_title ), array(
						'"'       => '\'',
						'&#8216;' => '\'',
						'&#8217;' => '\'',
						'&#8220;' => '\'',
						'&#8221;' => '\'',
						'�'       => '&quot;',
						'�'       => '&quot;',
					) );
				}
			}

			return $title;
		}

		/**
		 * Modify opengraph meta tags
		 *
		 * @param string $opengraph_meta Current OG metadata
		 *
		 * @return string
		 */
		public function opengraph_meta ( $opengraph_meta ) {

			preg_match( '/https?:\/\/[^ ]+?(?:\.gif)/', $opengraph_meta, $matches );

			if ( empty( $matches ) && ( strpos( $opengraph_meta, 'og:description' ) == false ) ) {
				$opengraph_meta .= PHP_EOL . '<meta property="og:description" content="&nbsp;" />';
			}

			return $opengraph_meta;

		}

		/**
		 * Setup template hooks for AMP template
		 */
		public function amp_pre_render_post () {
			add_filter( 'the_content', array( $this, 'add_to_manual_position' ), 10, 1 );
		}

		/**
		 * Render in manual position
		 *
		 * @param string $content Current content
		 *
		 * @return string
		 */
		public function add_to_manual_position ( $content ) {

			global $mashsb_options;
			// Default position
			$position = !empty( $mashsb_options[ 'mashsharer_position' ] ) ? $mashsb_options[ 'mashsharer_position' ] : '';
			// Check if we have a post meta setting which overrides the global position than we use that one instead
			if ( true == ( $position_meta = mashsb_get_post_meta_position() ) ) {
				$position = $position_meta;
			}

			/**
			 * Do nothing if not set to manual position
			 */
			if ( 'manual' != $position ) {
				return $content;
			}

			/**
			 * Do nothing if disabled post type
			 */
			$enabled_post_types = isset( $mashsb_options[ 'post_types' ] ) ? $mashsb_options[ 'post_types' ] : NULL;
			if ( $enabled_post_types == NULL or !in_array( get_post_type(), $enabled_post_types ) ) {
				return $content;
			}

			/**
			 * Do nothing if excluded post
			 */
			$excluded = ( isset( $mashsb_options[ 'excluded_from' ] ) && $mashsb_options[ 'excluded_from' ] ) ? explode( ',', preg_replace( '/\s+/', '', $mashsb_options[ 'excluded_from' ] ) ) : array();
			if ( in_array( get_the_ID(), $excluded ) ) {
				return $content;
			}

            if( boombox_get_theme_option( 'single_post_general_top_sharebar' ) ) {
	            $content = do_shortcode( '[mashshare]' ) . $content;
            }

            if( ! boombox_get_theme_option( 'single_post_general_bottom_sharebar' ) ) {
	            $content = $content . do_shortcode( '[mashshare]' );
            }

			return $content;
		}

		/**
		 * Add most shared to conditions
		 *
		 * @param array $choices Current choices
		 *
		 * @return array
		 */
		public function add_most_shared_to_conditions ( $choices ) {
			$choices[ 'most_shared' ] = esc_html__( 'Most Shared', 'boombox' );

			return $choices;

		}

	}

	Boombox_Mashshare::get_instance();

}