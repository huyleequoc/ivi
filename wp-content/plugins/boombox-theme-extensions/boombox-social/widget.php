<?php
/**
 * Adds Boombox_Social_Widget widget.
 *
 * @package BoomBox_Theme_Extensions
 */

// Prevent direct script access
if ( ! defined( 'ABSPATH' ) ) {
	die ( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Social_Widget' )){

	class Boombox_Social_Widget extends WP_Widget {
		/**
		 * Register widget with WordPress.
		 */
		function __construct() {
			parent::__construct(
				'boombox_social', // Base ID
				__( 'Boombox Social Widget', 'boombox-theme-extensions' ), // Name
				array( 'description' => __( 'A Social Widget', 'boombox-theme-extensions' ), ) // Args
			);
		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {
			$title   = isset ( $instance['title'] ) && ! empty( $instance['title'] ) ? $instance['title'] : '';
			$exclude = isset ( $instance['exclude'] ) ? $instance['exclude'] : '';

			echo $args['before_widget'];

			$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

			if ( $title ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}
			echo '<div class="social circle">';
			echo boombox_get_social_links( array( 'exclude' => array_filter( explode( ',', str_replace(' ', '', $exclude ) ) ) ) );
			echo '</div>';

			echo $args['after_widget'];
		}

		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 *
		 * @param array $instance
		 *
		 * @return string|void
		 */
		public function form( $instance ) {
			$title   = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : __( 'Connect With Us', 'boombox-theme-extensions' );
			$exclude = isset( $instance['exclude'] ) ? esc_html( $instance['exclude'] ) : ''; ?>
			<p>
				<label
					for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'boombox-theme-extensions' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
				       name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
				       value="<?php echo esc_attr( $title ); ?>">
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'exclude' ); ?>">
					<?php esc_html_e( 'Exclude', 'boombox-theme-extensions' ); ?>
					<small><?php esc_html_e( '(eg. tumblr, twitter, facebook) :', 'boombox-theme-extensions' ); ?></small>
				</label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'exclude' ); ?>"
				       name="<?php echo esc_attr( $this->get_field_name( 'exclude' ) ); ?>" type="text"
				       value="<?php echo esc_html( $exclude ); ?>">
			</p>
		<?php
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance            = array();
			$instance['title']   = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
			$instance['exclude'] = isset( $new_instance['exclude'] ) ? sanitize_text_field( $new_instance['exclude'] ) : '';

			return $instance;
		}

	}
}