<?php
/**
 * Boombox Authentication Template Helper
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Authentication_Template_Helper' ) ) {

	final class Boombox_Authentication_Template_Helper {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = NULL;

		/**
		 * Get instance
		 * @return Boombox_Authentication_Template_Helper|null
		 */
		public static function get_instance() {

			if ( NULL == static::$_instance ) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * Holds additional data
		 * @var array
		 */
		private $data = array();

		/**
		 * Setter
		 *
		 * @param string $name  Variable key
		 * @param mixed  $value Variable value
		 */
		public function __set( $name, $value ) {
			$this->data[ $name ] = $value;
		}

		/**
		 * Getter
		 *
		 * @param string $name Variable key
		 *
		 * @return mixed Variable value if it exists or null otherwise
		 */
		public function __get( $name ) {
			if ( array_key_exists( $name, $this->data ) ) {
				return $this->data[ $name ];
			}

			return null;
		}

		/**
		 * Boombox_Authentication_Template_Helper constructor.
		 */
		private function __construct() {
		}

		/**
		 * A dummy magic method to prevent Boombox_Authentication_Template_Helper from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Get elements
		 * @return array
		 */
		public function get_options() {

			$class  = 'js-authentication';
			$url = esc_url( '#sign-in' );
            $image = '<i class="bb-icon bb-ui-icon-user"></i>';
			$navigation = '';


			if ( is_user_logged_in() ) {

				$class = 'logged-in-user';
				$profile_menu_location = 'profile_nav';
				$current_user_id = get_current_user_id();

				$url = esc_url( get_author_posts_url( $current_user_id ) );
				$avatar = get_avatar( $current_user_id, 150 );
				if ( $avatar ) {
					$image = $avatar;
				}

				if ( has_nav_menu( $profile_menu_location ) ) {
				    $class .= ' element-toggle only-mobile';

					$navigation = wp_nav_menu( array(
						'theme_location' 	=> $profile_menu_location,
						'container'	     	=> 'div',
						'container_class' 	=> 'menu bb-header-dropdown toggle-content',
						'menu_class'     	=> '',
						'echo'			 	=> false,
						'depth'			 	=> 1,
						'walker'         	=> new Boombox_Walker_Nav_Menu_Custom_Fields()
					) );
				}
			}

			$template_settings = array(
				'class'      => $class,
				'url'        => $url,
				'image'      => $image,
				'navigation' => $navigation
			);

			return apply_filters( 'boombox/authentication_template_settings', $template_settings );
		}

	}

}