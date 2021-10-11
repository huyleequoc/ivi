<?php
/**
 * Boombox Listing Image size helper
 *
 * @package BoomBox_Theme
 * @since   2.8.5.1
 * @version 2.8.5.1
 */

if ( ! class_exists( 'Boombox_Listing_Image_Size_Helper' ) ) {

	/**
	 * Class Boombox_Listing_Image_Size_Helper
	 */
	final class Boombox_Listing_Image_Size_Helper {

		/**
		 * Holds class single instance
		 * @var null
		 * @since 2.5.8.1
		 * @version 2.5.8.1
		 */
		private static $_instance = NULL;

		/**
		 * Get instance
		 * @return Boombox_Listing_Image_Size_Helper|null
		 * @since 2.5.8.1
		 * @version 2.5.8.1
		 */
		public static function get_instance() {

			if ( NULL == static::$_instance ) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * Holds active loop listing type
		 * @var string
		 * @since 2.5.8.1
		 * @version 2.5.8.1
		 */
		private $active_listing_type = '';

		/**
		 * Boombox_Listing_Image_Size_Helper constructor.
		 * @since 2.5.8.1
		 * @version 2.5.8.1
		 */
		private function __construct() {
			add_action( 'boombox/loop-item/before-content', array( $this, 'before_loop_item_content' ), 10, 1 );
			add_action( 'boombox/loop-item/after-content', array( $this, 'after_loop_item_content' ), 10, 1 );
		}

		/**
		 * Callback hooked in "boombox/loop-item/before-content" action
		 * @param string $type Loop item type
		 * @since 2.5.8.1
		 * @version 2.5.8.1
		 */
		public function before_loop_item_content( $type ) {
			$this->active_listing_type = $type;
			add_filter( 'wp_calculate_image_sizes', array( $this, 'edit_thumbnail_sizes' ), 9999, 1 );
		}

		/**
		 * Callback hooked in "boombox/loop-item/after-content" action
		 * @since 2.5.8.1
		 * @version 2.5.8.1
		 */
		public function after_loop_item_content() {
			remove_filter( 'wp_calculate_image_sizes', array( $this, 'edit_thumbnail_sizes' ), 9999 );
		}

		/**
		 * Edit thumbnail image sizes based on listing type
		 * @param string array|$sizes Current sizes
		 *
		 * @return string
		 * @since 2.5.8.1
		 * @version 2.5.8.1
		 */
		public function edit_thumbnail_sizes( $sizes ) {
			switch ( $this->active_listing_type ) {
				case 'content-four-column':
					$sizes = '(max-width: 700px) 100vw, (max-width: 991px) 450px,(max-width: 1200px) 190px, 260px';
					break;
				case 'content-grid':
				case 'content-grid-2-1':
					$sizes = '(max-width: 700px) 100vw, (max-width: 900px) 400px, (max-width: 1200px) 300px, 360px';
					break;
				case 'content-list':
					$sizes = '(max-width: 700px) 100vw, (max-width: 900px) 400px,(max-width: 1200px) 300px, 360px';
					break;
				case 'content-list2':
					$sizes = '150px';
					break;
				case 'content-classic':
				case 'content-classic2':
					$sizes = '(max-width: 900px) 100vw, (max-width: 1200px) 620px,  760px';
					break;
				case 'content-stream':
					$sizes = '(max-width: 900px) 100vw, 545px';
					break;
				case 'content-mixed-classic':
					$sizes = '(max-width: 900px) 100vw, (max-width: 1200px) 620px,  760px';
					break;
				case 'content-mixed-list':
					$sizes = '(max-width: 600px) 220px, (max-width: 900px) 290px, (max-width: 1200px) 220px,  260px';
					break;
				case 'content-masonry-boxed':
					$sizes = '(max-width: 700px) 100vw, (max-width: 992px) 450px, (max-width: 1200px) 300px,  260px';
					break;
				case 'content-masonry-stretched':
					$sizes = '(max-width: 700px) 100vw, (max-width: 992px) 450px, (max-width: 1200px) 360px, (max-width: 1400px) 310px, 350px';
					break;
			}

			return $sizes;
		}

	}

	Boombox_Listing_Image_Size_Helper::get_instance();

}