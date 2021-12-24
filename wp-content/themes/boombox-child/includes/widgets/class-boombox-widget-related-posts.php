<?php
/**
 * Boombox_Widget_Related_Posts class
 *
 * @package BoomBox_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Widget_Related_Posts' ) ) {

	/**
	 * Core class used to implement a Picked Posts widget.
	 *
	 * @see WP_Widget
	 */
	class Boombox_Widget_Related_Posts extends WP_Widget {

		/**
		 * Sets up a new Picked Posts widget instance.
		 *
		 * @access public
		 */
		public function __construct() {
			$widget_ops = array(
				'classname'                   => 'widget_related_entries',
				'description'                 => esc_html__( 'Single Post&#8217;s Related Posts.', 'boombox' ),
				'customize_selective_refresh' => true,
			);
			parent::__construct( 'boombox-related-posts', esc_html__( 'Boombox Related Posts', 'boombox' ), $widget_ops );
			$this->alt_option_name = 'widget_related_entries';
		}

		/**
		 * Outputs the content for the current Picked Posts widget instance.
		 *
		 * @param array $args     Display arguments including 'before_title', 'after_title',
		 *                        'before_widget', and 'after_widget'.
		 * @param array $instance Settings for the current Picked Posts widget instance.
		 */
		public function widget( $args, $instance ) {

			if ( ! is_single() ) {
				return;
			}

			if ( ! isset( $args['widget_id'] ) ) {
				$args['widget_id'] = $this->id;
			}

			$title       = isset( $instance['title'] ) && ! empty( $instance['title'] ) ? $instance['title'] : '';
			$restriction = isset( $instance['restriction'] ) && ! empty( $instance['restriction'] ) ? $instance['restriction'] : '';
			$number      = isset( $instance['number'] ) && ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 5;
			$numbered    = isset( $instance['numbered'] ) ? ( bool ) $instance['numbered'] : false;
			$show_date   = isset( $instance['show_date'] ) ? ( bool ) $instance['show_date'] : false;
			$show_author = isset( $instance['show_author'] ) ? ( bool ) $instance['show_author'] : false;

			/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
			$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

			$queried_object = get_queried_object();

			$categories = array();
			if ( in_array( $restriction, array( 'category', 'both' ) ) ) {
				$post_categories = wp_get_post_categories( $queried_object->ID, array( 'fields' => 'all' ) );
				if ( ! empty( $post_categories ) ) {
					$categories = wp_list_pluck( $post_categories, 'slug' );
				}
			}

			$tags = array();
			if ( in_array( $restriction, array( 'tag', 'both' ) ) ) {
				$post_tags = wp_get_post_tags( $queried_object->ID, array( 'fields' => 'all' ) );
				if ( ! empty( $post_tags ) ) {
					$tags = wp_list_pluck( $post_tags, 'slug' );
				}
			}

			/**
			 * Get query for the Related Posts widget.
			 */
			$query = boombox_get_posts_query( 'related', 'all', array(
				'category' => $categories,
				'tag'      => $tags
			), array(
				'posts_per_page' => $number
			) );

			if ( null != $query && $query->have_posts() ) {
				echo $args['before_widget'];
				if ( $title ) {
					echo $args['before_title'] . $title . $args['after_title'];
				} ?>
				<ul>
					<?php while ( $query->have_posts() ) : $query->the_post();
						$has_post_thumbnail = boombox_has_post_thumbnail() ? true : false;
						$post_class         = $has_post_thumbnail ? '' : 'no-thumbnail'; ?>
						<li>
							<article class="post bb-post <?php echo esc_attr( $post_class ); ?>">
								<div class="post-thumbnail">
									<?php if ( $numbered ):
										$index = $query->current_post + 1; ?>
										<div class="post-number"><?php echo $index; ?></div>
									<?php endif;
									if ( $has_post_thumbnail ): ?>
										<a href="<?php echo esc_url( get_permalink() ); ?>"
										   title="<?php echo esc_attr( the_title_attribute() ); ?>">
											<?php echo boombox_get_post_thumbnail( null, 'boombox_image360x180' ); ?>
										</a>
									<?php endif; ?>
								</div>
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

			}
		}

		/**
		 * Handles updating the settings for the current Related Posts widget instance.
		 *
		 * @param array $new_instance New settings for this instance as input by the user via
		 *                            WP_Widget::form().
		 * @param array $old_instance Old settings for this instance.
		 *
		 * @return array Updated settings to save.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance                = $old_instance;
			$instance['title']       = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
			$instance['restriction'] = isset( $new_instance['restriction'] ) ? sanitize_text_field( $new_instance['restriction'] ) : 'both';
			$instance['numbered']    = isset( $new_instance['numbered'] ) ? ( bool ) $new_instance['numbered'] : false;
			$instance['number']      = isset( $new_instance['number'] ) ? (int) $new_instance['number'] : 5;
			$instance['show_date']   = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
			$instance['show_author'] = isset( $new_instance['show_author'] ) ? (bool) $new_instance['show_author'] : false;

			return $instance;
		}

		/**
		 * Outputs the settings form for the Related Posts widget.
		 *
		 * @param array $instance
		 *
		 * @return string|void
		 */
		public function form( $instance ) {
			$title       = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : esc_html__( 'Related Posts', 'boombox' );
			$restriction = isset( $instance['restriction'] ) ? sanitize_text_field( $instance['restriction'] ) : 'both';
			$number      = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
			$numbered    = isset( $instance['numbered'] ) ? ( bool ) $instance['numbered'] : false;
			$show_date   = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
			$show_author = isset( $instance['show_author'] ) ? (bool) $instance['show_author'] : false; ?>

			<p>
				<label
						for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'boombox' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
					   name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
					   value="<?php echo $title; ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'restriction' ); ?>"><?php esc_html_e( 'Restrict By:', 'boombox' ); ?></label>
				<select id="<?php echo $this->get_field_id( 'restriction' ); ?>" name="<?php echo $this->get_field_name( 'restriction' ); ?>" class="widefat">
					<option value="category" <?php echo selected( $restriction, 'category' ); ?>><?php echo __( 'Category', 'boombox' ); ?></option>
					<option value="tag" <?php echo selected( $restriction, 'tag' ); ?>><?php echo __( 'Tag', 'boombox' ); ?></option>
					<option value="both" <?php echo selected( $restriction, 'both' ); ?>><?php echo __( 'Both', 'boombox' ); ?></option>
				</select>
			</p>

			<p>
				<input class="checkbox" type="checkbox"<?php checked( $numbered ); ?>
					   id="<?php echo $this->get_field_id( 'numbered' ); ?>"
					   name="<?php echo $this->get_field_name( 'numbered' ); ?>" />
				<label
						for="<?php echo $this->get_field_id( 'numbered' ); ?>"><?php esc_html_e( 'Numbered', 'boombox' ); ?></label>
			</p>

			<p>
				<label
						for="<?php echo $this->get_field_id( 'number' ); ?>"><?php esc_html_e( 'Number of posts to show:', 'boombox' ); ?></label>
				<input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>"
					   name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1"
					   value="<?php echo $number; ?>" size="3" />
			</p>

			<p>
				<input class="checkbox" type="checkbox"<?php checked( $show_date ); ?>
					   id="<?php echo $this->get_field_id( 'show_date' ); ?>"
					   name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
				<label
						for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php esc_html_e( 'Display post date?', 'boombox' ); ?></label>
			</p>

			<p>
				<input class="checkbox" type="checkbox"<?php checked( $show_author ); ?>
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
 * Register Boombox Related Posts Widget
 */
if ( ! function_exists( 'boombox_load_related_posts_widget' ) ) {

	function boombox_load_related_posts_widget() {
		register_widget( 'Boombox_Widget_Related_Posts' );
	}

	add_action( 'widgets_init', 'boombox_load_related_posts_widget' );

}