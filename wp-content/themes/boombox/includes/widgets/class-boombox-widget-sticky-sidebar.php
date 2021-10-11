<?php
/**
 * Boombox_Widget_Sticky_Sidebar class
 *
 * @package BoomBox_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}


if ( ! class_exists( 'Boombox_Widget_Sticky_Sidebar' ) ) {
	/**
	 * Core class used to set widgets as sticky.
	 *
	 * @see WP_Widget
	 */
	class Boombox_Widget_Sticky_Sidebar extends WP_Widget {

		/**
		 * Sets up a new Sticky Sidebar widget instance.
		 *
		 * @access public
		 */
		public function __construct() {
			$widget_ops = array(
				'classname'                   => 'widget_sticky_sidebar',
				'description'                 => esc_html__( 'WIth this widget you can define a place where sticky area starts', 'boombox' ),
				'customize_selective_refresh' => true,
			);
			parent::__construct( 'boombox-sticky-sidebar', esc_html__( 'Boombox Sticky Sidebar', 'boombox' ), $widget_ops );
			$this->alt_option_name = 'widget_sticky_sidebar';
		}

		/**
		 * Outputs the content for the current Sticky Sidebar widget instance.
		 *
		 * @param array $args Display arguments including 'before_title', 'after_title',
		 *                        'before_widget', and 'after_widget'.
		 * @param array $instance Settings for the current Sticky Sidebar widget instance.
		 */
		public function widget( $args, $instance ) {
			echo '<section  class="sticky-sidebar"></section>';
		}

		/**
		 * Outputs the settings form for the Sticky Sidebar widget.
		 *
		 * @param array $instance
		 *
		 * @return string|void
		 */
		public function form( $instance ) {
			?>
			<p><?php esc_html_e( 'All following widgets will be sticky.', 'boombox' ); ?></p>
			<?php
		}
	}
}

/**
 * Register Boombox Sticky Sidebar Widget
 */
if( ! function_exists( 'boombox_load_sticky_sidebar_widget' ) ) {
	
	function boombox_load_sticky_sidebar_widget() {
		register_widget( 'Boombox_Widget_Sticky_Sidebar' );
	}
	
	add_action( 'widgets_init', 'boombox_load_sticky_sidebar_widget' );
 
}