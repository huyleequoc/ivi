<?php
/**
 * Boombox_Widget_Create_Post class
 *
 * @package BoomBox_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}


if ( ! class_exists( 'Boombox_Widget_Create_Post' ) ) {
	/**
	 * Core class used to implement a Create Post widget.
	 *
	 * @see WP_Widget
	 */
	class Boombox_Widget_Create_Post extends WP_Widget {

		/**
		 * Sets up a new Create Post widget instance.
		 *
		 * @since 2.8.0
		 * @access public
		 */
		public function __construct() {
			$widget_ops = array(
				'classname'                   => 'widget_create_post',
				'description'                 => esc_html__( 'Adds the "Create Post" button.', 'boombox' ),
				'customize_selective_refresh' => true,
			);
			parent::__construct( 'boombox-create-post', esc_html__( 'Boombox Create Post', 'boombox' ), $widget_ops );
			$this->alt_option_name = 'widget_create_post';
		}

		/**
		 * Outputs the content for the current Create Post widget instance.
		 *
		 * @param array $args Display arguments including 'before_title', 'after_title',
		 *                        'before_widget', and 'after_widget'.
		 * @param array $instance Settings for the current Create Post widget instance.
		 */
		public function widget( $args, $instance ) {
			if ( ! isset( $args['widget_id'] ) ) {
				$args['widget_id'] = $this->id;
			}

			$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : '';

			/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
			$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

			$subtitle                = ! empty( $instance['subtitle'] ) ? $instance['subtitle'] : '';
			$description             = ! empty( $instance['description'] ) ? $instance['description'] : '';
			$button_text             = ! empty( $instance['button_text'] ) ? $instance['button_text'] : '';
			$button_link			 = ! empty( $instance['button_link'] ) ? $instance['button_link'] : '';
			$button_enable_plus_icon = ! empty( $instance['button_enable_plus_icon'] ) ? $instance['button_enable_plus_icon'] : false;

			if ( boombox_get_theme_option( 'header_layout_button' ) && boombox_is_auth_allowed() ) :

				echo $args['before_widget'];
				if ( $title ) {
					echo $args['before_title'] . $title . $args['after_title'];
				}

				if( $subtitle ){
					printf( '<h3 class="sub-title">%s</h3>', $subtitle );
				}

				if( $description ){
					printf( '<div class="text">%s</div>', $description );
				}

				echo boombox_get_create_post_button(
				        array( 'bb-btn', 'btn-create', 'bb-btn-primary' ),
                        $button_text,
                        $button_enable_plus_icon,
                        $button_link
                );

			endif;

			echo $args['after_widget'];

		}

		/**
		 * Handles updating the settings for the current Create Post widget instance.
		 *
		 * @param array $new_instance New settings for this instance as input by the user via
		 *                            WP_Widget::form().
		 * @param array $old_instance Old settings for this instance.
		 *
		 * @return array Updated settings to save.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance                            = $old_instance;
			$instance['title']                   = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
			$instance['subtitle']                = isset( $new_instance['subtitle'] ) ? sanitize_text_field( $new_instance['subtitle'] ) : '';
			$instance['description']             = isset( $new_instance['description'] ) ? wp_kses_post( $new_instance['description'] ) : '';
			$instance['button_text']             = isset( $new_instance['button_text'] ) ? sanitize_text_field( $new_instance['button_text'] ) : '';
			$instance['button_link']             = isset( $new_instance['button_link'] ) ? sanitize_text_field( $new_instance['button_link'] ) : '';
			$instance['button_enable_plus_icon'] = isset( $new_instance['button_enable_plus_icon'] ) ? (bool) $new_instance['button_enable_plus_icon'] : false;

			return $instance;
		}

		/**
		 * Outputs the settings form for the Create Post widget.
		 *
		 * @param array $instance
		 *
		 * @return string|void
		 */
		public function form( $instance ) {
			$title                   = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
			$subtitle                = isset( $instance['subtitle'] ) ? esc_html( $instance['subtitle'] ) : esc_html__( 'Unleash Your Creativity', 'boombox' );
			$description             = isset( $instance['description'] ) ? esc_textarea( $instance['description'] ) : esc_html__( 'and share your story with us!', 'boombox' );
			$button_text             = isset( $instance['button_text'] ) ? esc_html( $instance['button_text'] ) : esc_html__( 'Create a post', 'boombox' );
			$button_link             = isset( $instance['button_link'] ) ? esc_html( $instance['button_link'] ) : '';
			$button_enable_plus_icon = isset( $instance['button_enable_plus_icon'] ) ? (bool) $instance['button_enable_plus_icon'] : false; ?>

			<p>
				<label
					for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'boombox' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
				       name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
				       value="<?php echo $title; ?>"/>
			</p>
			<p>
				<label
					for="<?php echo $this->get_field_id( 'subtitle' ); ?>"><?php esc_html_e( 'Subtitle:', 'boombox' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'subtitle' ); ?>"
				       name="<?php echo $this->get_field_name( 'subtitle' ); ?>" type="text"
				       value="<?php echo $subtitle; ?>"/>
			</p>
			<p>
				<label
					for="<?php echo $this->get_field_id( 'description' ); ?>"><?php esc_html_e( 'Description:', 'boombox' ); ?></label>
				<textarea class="widefat" rows="10" cols="20" id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>"><?php echo $description; ?></textarea>
			</p>
			<p>
				<label
					for="<?php echo $this->get_field_id( 'button_text' ); ?>"><?php esc_html_e( 'Button Text:', 'boombox' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'button_text' ); ?>"
				       name="<?php echo $this->get_field_name( 'button_text' ); ?>" type="text"
				       value="<?php echo $button_text; ?>"/>
			</p>
			<p>
				<label
					for="<?php echo $this->get_field_id( 'button_link' ); ?>"><?php esc_html_e( 'Button Link:', 'boombox' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'button_link' ); ?>"
					   name="<?php echo $this->get_field_name( 'button_link' ); ?>" type="text"
					   value="<?php echo $button_link; ?>"/>
			</p>

			<p>
				<input class="checkbox" type="checkbox"<?php checked( $button_enable_plus_icon ); ?>
				       id="<?php echo $this->get_field_id( 'button_enable_plus_icon' ); ?>"
				       name="<?php echo $this->get_field_name( 'button_enable_plus_icon' ); ?>"/>
				<label
					for="<?php echo $this->get_field_id( 'button_enable_plus_icon' ); ?>"><?php esc_html_e( 'Enable Plus Icon On Button', 'boombox' ); ?></label>
			</p>

		<?php
		}
	}
}

/**
 * Register Boombox Create Post Widget
 */
if( ! function_exists( 'boombox_load_create_post_widget' ) ) {
	
    function boombox_load_create_post_widget() {
		register_widget( 'Boombox_Widget_Create_Post' );
	}
	
	add_action( 'widgets_init', 'boombox_load_create_post_widget' );
 
}