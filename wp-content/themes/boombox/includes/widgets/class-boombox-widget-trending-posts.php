<?php
/**
 * Boombox_Widget_Trending_Posts class
 *
 * @package BoomBox_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}


if ( ! class_exists( 'Boombox_Widget_Trending_Posts' ) ) {
	/**
	 * Core class used to implement a Trending Posts widget.
	 *
	 * @see WP_Widget
	 */
	class Boombox_Widget_Trending_Posts extends WP_Widget {

		/**
		 * Sets up a new Trending Posts widget instance.
		 *
		 * @access public
		 */
		public function __construct() {
			$widget_ops = array(
				'classname'                   => 'widget_trending_entries',
				'description'                 => esc_html__( 'Your Site&#8217;s Trending Posts.', 'boombox' ),
				'customize_selective_refresh' => true,
			);
			parent::__construct( 'boombox-trending-posts', esc_html__( 'Boombox Trending Posts', 'boombox' ), $widget_ops );
			$this->alt_option_name = 'widget_trending_entries';
		}

		/**
		 * Outputs the content for the current Trending Posts widget instance.
		 *
		 * @param array $args     Display arguments including 'before_title', 'after_title',
		 *                        'before_widget', and 'after_widget'.
		 * @param array $instance Settings for the current Trending Posts widget instance.
		 */
		public function widget( $args, $instance ) {
			if ( ! isset( $args['widget_id'] ) ) {
				$args['widget_id'] = $this->id;
			}

			$title                     = isset( $instance['title'] ) && ! empty( $instance['title'] ) ? $instance['title'] : '';
			$type                      = isset( $instance['type'] ) ? $instance['type'] : 'trending';
			$icon                      = ( $type == 'trending' ) ? boombox_get_theme_option( 'extras_badges_trending_icon' ) : $type;
			$number                    = isset( $instance['number'] ) && ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 5;
			$show_date                 = isset( $instance['show_date'] ) ? ( bool ) $instance['show_date'] : false;
			$show_author               = isset( $instance['show_author'] ) ? ( bool ) $instance['show_author'] : false;
			$disable_numeration_badges = isset( $instance['disable_numeration_badges'] ) ? ( bool ) $instance['disable_numeration_badges'] : false;

			/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
			$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

			/**
			 * Get query for the Trending Posts widget.
			 */
			$query = boombox_get_trending_posts( $type, $number, array( 'is_widget' => true ) );

			if ( null != $query && $query->have_posts() ) :
				?>
				<?php echo $args['before_widget']; ?>
				<?php if ( $title ) {
					$type_icon = $type ? '<i class="bb-icon bb-ui-icon-' . $icon . '"></i>' : '';
					$title 	   = '<span>' . $type_icon . $title . '</span>' ;
					echo $args['before_title'] . $title . $args['after_title'];
				} ?>
				<ul>
					<?php while ( $query->have_posts() ) : $query->the_post();
						$has_post_thumbnail = boombox_has_post_thumbnail() ? true : false;
						$post_class         = $has_post_thumbnail ? '' : 'no-thumbnail'; ?>
						<li>
							<article class="post bb-post <?php echo esc_attr( $post_class ); ?>">
								<div class="post-thumbnail">
									<?php if ( ! $disable_numeration_badges ) { ?>
										<div class="post-number"><?php echo( $query->current_post + 1 ); ?></div>
									<?php } ?>
									<?php if ( $has_post_thumbnail ) { ?>
										<a href="<?php echo esc_url( get_permalink() ); ?>"
										   title="<?php echo esc_attr( the_title_attribute() ); ?>">
											<?php echo boombox_get_post_thumbnail( null, 'boombox_image360x180' ); ?>
										</a>
									<?php } ?>
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

			endif;
		}

		/**
		 * Handles updating the settings for the current Trending Posts widget instance.
		 *
		 * @param array $new_instance New settings for this instance as input by the user via
		 *                            WP_Widget::form().
		 * @param array $old_instance Old settings for this instance.
		 *
		 * @return array Updated settings to save.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance                              = $old_instance;
			$instance['title']                     = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
			$instance['number']                    = isset( $new_instance['number'] ) ? (int) $new_instance['number'] : 5;
			$instance['type']                      = isset( $new_instance['type'] ) ? sanitize_text_field( $new_instance['type'] ) : false;
			$instance['show_date']                 = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
			$instance['show_author']               = isset( $new_instance['show_author'] ) ? (bool) $new_instance['show_author'] : false;
			$instance['disable_numeration_badges'] = isset( $new_instance['disable_numeration_badges'] ) ? (bool) $new_instance['disable_numeration_badges'] : false;

			return $instance;
		}

		/**
		 * Outputs the settings form for the Trending Posts widget.
		 *
		 * @param array $instance
		 *
		 * @return string|void
		 */
		public function form( $instance ) {
			$title                     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : esc_html__( 'Trending', 'boombox' );
			$number                    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
			$type                      = isset( $instance['type'] ) ? sanitize_text_field( $instance['type'] ) : false;
			$show_date                 = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
			$show_author               = isset( $instance['show_author'] ) ? (bool) $instance['show_author'] : false;
			$disable_numeration_badges = isset( $instance['disable_numeration_badges'] ) ? (bool) $instance['disable_numeration_badges'] : false; ?>

			<p>
				<label
						for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'boombox' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
					   name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
					   value="<?php echo $title; ?>" />
			</p>

			<p>
				<label
						for="<?php echo $this->get_field_id( 'number' ); ?>"><?php esc_html_e( 'Number of posts to show:', 'boombox' ); ?></label>
				<input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>"
					   name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1"
					   value="<?php echo $number; ?>" size="3" />
			</p>

			<p>
				<label
						for="<?php echo $this->get_field_id( 'type' ); ?>"><?php esc_html_e( 'Trending Type:', 'boombox' ); ?></label>
				<select id="<?php echo $this->get_field_id( 'type' ); ?>"
						name="<?php echo $this->get_field_name( 'type' ); ?>">
					<option
							value="trending" <?php echo selected( 'trending', $type, false ); ?>><?php esc_html_e( 'Trending', 'boombox' ); ?></option>
					<option
							value="hot" <?php echo selected( 'hot', $type, false ); ?>><?php esc_html_e( 'Hot', 'boombox' ); ?></option>
					<option
							value="popular" <?php echo selected( 'popular', $type, false ); ?>><?php esc_html_e( 'Popular', 'boombox' ); ?></option>
				</select>
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

			<p>
				<input class="checkbox" type="checkbox"<?php checked( $disable_numeration_badges ); ?>
					   id="<?php echo $this->get_field_id( 'disable_numeration_badges' ); ?>"
					   name="<?php echo $this->get_field_name( 'disable_numeration_badges' ); ?>" />
				<label
						for="<?php echo $this->get_field_id( 'disable_numeration_badges' ); ?>">
					<?php esc_html_e( 'Disable numeration badges', 'boombox' ); ?>
				</label>
			</p>
			<?php
		}
	}
}

/**
 * Register Boombox Trending Posts Widget
 */
if ( ! function_exists( 'boombox_load_trending_posts_widget' ) ) {

	function boombox_load_trending_posts_widget() {
		register_widget( 'Boombox_Widget_Trending_Posts' );
	}

	add_action( 'widgets_init', 'boombox_load_trending_posts_widget' );

}