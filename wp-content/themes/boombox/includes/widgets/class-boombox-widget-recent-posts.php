<?php
/**
 * Boombox_Widget_Recent_Posts class
 *
 * @package BoomBox_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}


if ( ! class_exists( 'Boombox_Widget_Recent_Posts' ) ) {
	/**
	 * Core class used to implement a Recent Posts widget.
	 *
	 * @see WP_Widget
	 */
	class Boombox_Widget_Recent_Posts extends WP_Widget {

		/**
		 * Sets up a new Recent Posts widget instance.
		 *
		 * @since  2.8.0
		 * @access public
		 */
		public function __construct() {
			$widget_ops = array(
				'classname'                   => 'widget_recent_entries',
				'description'                 => esc_html__( 'Your site&#8217;s most recent Posts.', 'boombox' ),
				'customize_selective_refresh' => true,
			);
			parent::__construct( 'boombox-recent-posts', esc_html__( 'Boombox Recent Posts', 'boombox' ), $widget_ops );
			$this->alt_option_name = 'widget_recent_entries';
		}

		/**
		 * Outputs the content for the current Recent Posts widget instance.
		 *
		 * @param array $args     Display arguments including 'before_title', 'after_title',
		 *                        'before_widget', and 'after_widget'.
		 * @param array $instance Settings for the current Recent Posts widget instance.
		 */
		public function widget( $args, $instance ) {
			if ( ! isset( $args['widget_id'] ) ) {
				$args['widget_id'] = $this->id;
			}

			$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : '';

			/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
			$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

			$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
			if ( ! $number ) {
				$number = 5;
			}

			$show_date   = isset( $instance['show_date'] ) ? $instance['show_date'] : false;
			$show_author = isset( $instance['show_author'] ) ? $instance['show_author'] : false;

			/**
			 * Filter the arguments for the Recent Posts widget.
			 *
			 * @see WP_Query::get_posts()
			 *
			 * @param array $args An array of arguments used to retrieve the recent posts.
			 */
			$r = new WP_Query( apply_filters( 'widget_recent_posts_args', array(
				'posts_per_page'      => $number,
				'no_found_rows'       => true,
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true
			) ) );

			if ( $r->have_posts() ) :
				?>
				<?php echo $args['before_widget']; ?>
				<?php if ( $title ) {
				echo $args['before_title'] . $title . $args['after_title'];
			} ?>
				<ul>
					<?php while ( $r->have_posts() ) : $r->the_post();
						$has_post_thumbnail = boombox_has_post_thumbnail() ? true : false;
						$post_class         = $has_post_thumbnail ? '' : 'no-thumbnail'; ?>
						<li>
							<article class="post bb-post <?php echo esc_attr( $post_class ); ?>">
								<?php if ( $has_post_thumbnail ) : ?>
									<div class="post-thumbnail">
										<a href="<?php echo esc_url( get_permalink() ); ?>"
										   title="<?php echo esc_attr( the_title_attribute() ); ?>">
											<?php echo boombox_get_post_thumbnail( null, 'thumbnail' ); ?>
										</a>
									</div>
								<?php endif; ?>
								<div class="content">
									<div class="entry-header">
										<h3 class="entry-title">
											<a href="<?php echo esc_url( get_permalink() ); ?>"><?php get_the_title() ? the_title() : the_ID(); ?></a>
										</h3>
										<?php echo boombox_generate_user_mini_card( array(
											'author' => $show_author,
											'avatar' => $show_author,
											'date'   => $show_date,
											'class'  => 'post-author-meta'
										) ); ?>
									</div>
								</div>
							</article>
						</li>
					<?php endwhile; ?>
				</ul>
				<?php echo $args['after_widget'];

				// Reset the global $the_post as this query will have stomped on it
				wp_reset_postdata();

			endif;
		}

		/**
		 * Handles updating the settings for the current Recent Posts widget instance.
		 *
		 * @param array $new_instance New settings for this instance as input by the user via
		 *                            WP_Widget::form().
		 * @param array $old_instance Old settings for this instance.
		 *
		 * @return array Updated settings to save.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance                = $old_instance;
			$instance['title']       = sanitize_text_field( $new_instance['title'] );
			$instance['number']      = (int) $new_instance['number'];
			$instance['show_date']   = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
			$instance['show_author'] = isset( $new_instance['show_author'] ) ? (bool) $new_instance['show_author'] : false;

			return $instance;
		}

		/**
		 * Outputs the settings form for the Recent Posts widget.
		 *
		 * @param array $instance
		 *
		 * @return string|void
		 */
		public function form( $instance ) {
			$title       = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
			$number      = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
			$show_date   = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
			$show_author = isset( $instance['show_author'] ) ? (bool) $instance['show_author'] : false; ?>

			<p><label
						for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'boombox' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
					   name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
					   value="<?php echo $title; ?>" />
			</p>

			<p><label
						for="<?php echo $this->get_field_id( 'number' ); ?>"><?php esc_html_e( 'Number of posts to show:', 'boombox' ); ?></label>
				<input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>"
					   name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1"
					   value="<?php echo $number; ?>" size="3" />
			</p>

			<p><input class="checkbox" type="checkbox"<?php checked( $show_date ); ?>
					  id="<?php echo $this->get_field_id( 'show_date' ); ?>"
					  name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
				<label
						for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php esc_html_e( 'Display post date?', 'boombox' ); ?></label>
			</p>

			<p><input class="checkbox" type="checkbox"<?php checked( $show_author ); ?>
					  id="<?php echo $this->get_field_id( 'show_author' ); ?>"
					  name="<?php echo $this->get_field_name( 'show_author' ); ?>" />
				<label
						for="<?php echo $this->get_field_id( 'show_author' ); ?>"><?php esc_html_e( 'Display post author?', 'boombox' ); ?></label>
			</p>
			<?php
		}
	}
}

/**
 * Register Boombox Recent Posts Widget
 */
if ( ! function_exists( 'boombox_load_recent_posts_widget' ) ) {

	function boombox_load_recent_posts_widget() {
		register_widget( 'Boombox_Widget_Recent_Posts' );
	}

	add_action( 'widgets_init', 'boombox_load_recent_posts_widget' );

}