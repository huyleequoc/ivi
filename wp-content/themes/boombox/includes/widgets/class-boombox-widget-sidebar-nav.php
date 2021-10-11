<?php
/**
 * Boombox_Widget_Sidebar_Navigation class
 *
 * @package BoomBox_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}


if ( ! class_exists( 'Boombox_Widget_Sidebar_Navigation' ) ) {

	/**
	 * Core class used to create sidebar navigation widget
	 *
	 * @see WP_Widget
	 */
	class Boombox_Widget_Sidebar_Navigation extends WP_Widget {

		/**
		 * Sets up a new Sidebar Navigation widget instance.
		 *
		 * @access public
		 */
		public function __construct() {
			$widget_ops = array(
				'classname'                   => 'widget_bb-side-navigation',
				'description'                 => esc_html__( 'With this widget you can create a custom navigation for your sidebars', 'boombox' ),
				'customize_selective_refresh' => true,
			);
			parent::__construct( 'boombox-side-navigation', esc_html__( 'Boombox Sidebar Navigation', 'boombox' ),
				$widget_ops );
			$this->alt_option_name = 'widget_bb-side-navigation';
		}

		/**
		 * Outputs the content for the current Sidebar Navigation widget instance.
		 *
		 * @param array $args     Display arguments including 'before_title', 'after_title',
		 *                        'before_widget', and 'after_widget'.
		 * @param array $instance Settings for the current Sidebar Navigation widget instance.
		 */
		public function widget( $args, $instance ) {
			if ( ! isset( $args['widget_id'] ) ) {
				$args['widget_id'] = $this->id;
			}
			$title = isset( $instance['title'] ) && ! empty( $instance['title'] ) ? $instance['title'] : '';
			$nav_title = isset( $instance['nav_title'] ) && ! empty( $instance['nav_title'] ) ? $instance['nav_title'] : '';
			$nav_menu = isset( $instance['nav_menu'] ) && ! empty( $instance['nav_menu'] ) ? $instance['nav_menu'] : '';

			echo $args['before_widget'];

			/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
			$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
			if ( $title ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}

			/***** Render Navigation Menu if any */
			if ( $nav_menu ) { ?>
				<nav>

					<?php if( $nav_title ) { ?>
					<h2 class="bb-widget-title"><?php echo esc_html( $nav_title ); ?></h2>
					<?php } ?>

					<ul>
						<?php

						$menu_items = wp_get_nav_menu_items( $nav_menu, array( 'update_post_term_cache' => false ) );
						_wp_menu_item_classes_by_context( $menu_items );

						$sorted_menu_items = $menu_items_with_children = array();
						foreach ( (array) $menu_items as $menu_item ) {
							$sorted_menu_items[ $menu_item->menu_order ] = $menu_item;
							if ( $menu_item->menu_item_parent )
								$menu_items_with_children[ $menu_item->menu_item_parent ] = true;
						}

						// Add the menu-item-has-children class where applicable
						if ( $menu_items_with_children ) {
							foreach ( $sorted_menu_items as &$menu_item ) {
								if ( isset( $menu_items_with_children[ $menu_item->ID ] ) )
									$menu_item->classes[] = 'menu-item-has-children';
							}
						}

						$walker_args = array(
							'walker' => new Boombox_Walker_Side_Nav_Menu(),
							'menu' => '',
							'container' => 'div',
							'container_class' => '',
							'container_id' => '',
							'menu_class' => 'menu',
							'menu_id' => '',
							'echo' => true,
							'fallback_cb' => 'wp_page_menu',
							'before' => '',
							'after' => '',
							'link_before' => '',
							'link_after' => '',
							'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
							'item_spacing' => 'preserve',
							'depth' => 0,
							'theme_location' => '',
						);

						$walker_args = (object) $walker_args;

						echo walk_nav_menu_tree( $sorted_menu_items, $walker_args->depth, $walker_args ); ?>
					</ul>
				</nav>
				<?php
			}

			echo $args['after_widget'];
		}

		/**
		 * Handles updating the settings for the current Boombox Sidebar Navigation instance.
		 *
		 * @param array $new_instance New settings for this instance as input by the user via
		 *                            WP_Widget::form().
		 * @param array $old_instance Old settings for this instance.
		 *
		 * @return array Updated settings to save.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance               = $old_instance;
			$instance['title']      = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
			$instance['nav_title']  = isset( $new_instance['nav_title'] ) ? sanitize_text_field( $new_instance['nav_title'] ) : '';
			$instance['nav_menu'] = isset( $new_instance['nav_menu'] ) ? sanitize_text_field( $new_instance['nav_menu'] ) : '';

			return $instance;
		}

		/**
		 * Outputs the settings form for the Sidebar Navigation widget.
		 *
		 * @param array $instance
		 *
		 * @return string|void
		 */
		public function form( $instance ) {

			$title = isset( $instance[ 'title' ] ) ? esc_attr( $instance[ 'title' ] ) : esc_html__( 'Sidebar Navigation', 'boombox' );
			$nav_title = isset( $instance[ 'nav_title' ] ) ? esc_attr( $instance[ 'nav_title' ] ) : '';
			$nav_menu  = isset( $instance['nav_menu'] ) ? $instance['nav_menu'] : '';
			$nav_menu_choices = wp_list_pluck( wp_get_nav_menus(), 'name', 'term_id' ); ?>

			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>">
					<?php esc_html_e( 'Title:', 'boombox' ); ?>
				</label>
				<input class="widefat"
				       id="<?php echo $this->get_field_id( 'title' ); ?>"
				       name="<?php echo $this->get_field_name( 'title' ); ?>"
				       type="text"
				       value="<?php echo $title; ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'nav_title' ); ?>">
					<?php esc_html_e( 'Navigation Title:', 'boombox' ); ?>
				</label>
				<input class="widefat"
				       id="<?php echo $this->get_field_id( 'nav_title' ); ?>"
				       name="<?php echo $this->get_field_name( 'nav_title' ); ?>"
				       type="text"
				       value="<?php echo $nav_title; ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'nav_menu' ); ?>">
					<?php esc_html_e( 'Menu:', 'boombox' ); ?>
				</label>

				<?php if ( ! empty( $nav_menu_choices ) ) { ?>
					<select id="<?php echo $this->get_field_id( 'nav_menu' ); ?>"
					        name="<?php echo $this->get_field_name( 'nav_menu' ); ?>"
					        class="widefat">
						<?php foreach ( $nav_menu_choices as $menu_id => $name ) { ?>
							<option value="<?php echo esc_attr( $menu_id ); ?>"
								<?php selected( $menu_id, $nav_menu ); ?>>
								<?php echo esc_html( $name ); ?>
							</option>
						<?php } ?>
					</select>
				<?php } else { ?>
					<br/>
				<?php
					_e( 'There are no available menus', 'boombox' );
				} ?>
			</p>
			<?php
		}
	}
}

/**
 * Register Boombox Sidebar Navigation Widget
 */
if ( ! function_exists( 'boombox_load_sidebar_nav_widget' ) ) {

	function boombox_load_sidebar_nav_widget() {
		register_widget( 'Boombox_Widget_Sidebar_Navigation' );
	}

	add_action( 'widgets_init', 'boombox_load_sidebar_nav_widget' );

}