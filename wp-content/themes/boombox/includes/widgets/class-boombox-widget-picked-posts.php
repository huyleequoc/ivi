<?php
/**
 * Boombox_Widget_Picked_Posts class
 *
 * @package BoomBox_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}


if ( ! class_exists( 'Boombox_Widget_Picked_Posts' ) ) {
	/**
	 * Core class used to implement a Picked Posts widget.
	 *
	 * @see WP_Widget
	 */
	class Boombox_Widget_Picked_Posts extends WP_Widget {

		/**
		 * Sets up a new Picked Posts widget instance.
		 *
		 * @access public
		 */
		public function __construct() {
			$widget_ops = array(
				'classname'                   => 'widget_picked_entries',
				'description'                 => esc_html__( 'Your Site&#8217;s Picked Posts.', 'boombox' ),
				'customize_selective_refresh' => true,
			);
			parent::__construct( 'boombox-picked-posts', esc_html__( 'Boombox Picked Posts', 'boombox' ), $widget_ops );
			$this->alt_option_name = 'widget_picked_entries';
		}

		/**
		 * Outputs the content for the current Picked Posts widget instance.
		 *
		 * @param array $args     Display arguments including 'before_title', 'after_title',
		 *                        'before_widget', and 'after_widget'.
		 * @param array $instance Settings for the current Picked Posts widget instance.
		 */
		public function widget( $args, $instance ) {
			if ( ! isset( $args['widget_id'] ) ) {
				$args['widget_id'] = $this->id;
			}

			$title       = isset( $instance['title'] ) && ! empty( $instance['title'] ) ? $instance['title'] : '';
			$condition   = isset( $instance['condition'] ) ? $instance['condition'] : 'recent';
			$time_range  = isset( $instance['time_range'] ) ? $instance['time_range'] : 'all';
			$categories  = isset( $instance['categories'] ) ? $instance['categories'] : '';
			$tags        = isset( $instance['tags'] ) ? $instance['tags'] : '';
			$number      = isset( $instance['number'] ) && ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 5;
			$numbered    = isset( $instance['numbered'] ) ? ( bool ) $instance['numbered'] : false;
			$show_date   = isset( $instance['show_date'] ) ? ( bool ) $instance['show_date'] : false;
			$show_author = isset( $instance['show_author'] ) ? ( bool ) $instance['show_author'] : false;

			/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
			$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

			/**
			 * Get query for the Picked Posts widget.
			 */
			$query = boombox_get_posts_query( $condition, $time_range, array(
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
		 * Handles updating the settings for the current Picked Posts widget instance.
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
			$instance['condition']  = isset( $new_instance['condition'] ) ? sanitize_text_field( $new_instance['condition'] ) : 'recent';
			$instance['time_range'] = isset( $new_instance['time_range'] ) ? sanitize_text_field( $new_instance['time_range'] ) : 'all';

			$instance['categories'] = '';
			if ( isset( $new_instance['categories'] ) && is_array( $new_instance['categories'] ) ) {
				$categories = array_filter( $new_instance['categories'], 'sanitize_text_field' );
				if ( ! empty( $categories ) ) {
					$instance['categories'] = $categories;
				}
			}

			$instance['tags'] = array();
			if ( isset( $new_instance['tags'] ) && $new_instance['tags'] ) {
				$tags = explode( ',', preg_replace( '/\s+/', '', sanitize_text_field( $new_instance['tags'] ) ) );

				if ( ! empty( $tags ) ) {
					$instance['tags'] = $tags;
				}
			}

			$instance['numbered']    = isset( $new_instance['numbered'] ) ? ( bool ) $new_instance['numbered'] : false;
			$instance['number']      = isset( $new_instance['number'] ) ? (int) $new_instance['number'] : 5;
			$instance['show_date']   = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
			$instance['show_author'] = isset( $new_instance['show_author'] ) ? (bool) $new_instance['show_author'] : false;

			return $instance;
		}

		/**
		 * Outputs the settings form for the Picked Posts widget.
		 *
		 * @param array $instance
		 *
		 * @return string|void
		 */
		public function form( $instance ) {
			$title       = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : esc_html__( 'Recent Posts', 'boombox' );
			$condition   = isset( $instance['condition'] ) ? sanitize_text_field( $instance['condition'] ) : 'recent';
			$time_range  = isset( $instance['time_range'] ) ? sanitize_text_field( $instance['time_range'] ) : 'all';
			$categories  = isset( $instance['categories'] ) ? (array) $instance['categories'] : '';
			$tags        = isset( $instance['tags'] ) ? ( is_array( $instance['tags'] ) ? implode( ',', $instance['tags'] ) : $instance['tags'] ) : '';
			$number      = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
			$numbered    = isset( $instance['numbered'] ) ? ( bool ) $instance['numbered'] : false;
			$show_date   = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
			$show_author = isset( $instance['show_author'] ) ? (bool) $instance['show_author'] : false;

			$conditions_choices = Boombox_Choices_Helper::get_instance()->get_conditions();
			$time_range_choices = Boombox_Choices_Helper::get_instance()->get_time_ranges();
			$category_choices   = Boombox_Choices_Helper::get_instance()->get_categories(); ?>
			<p>
				<label
						for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'boombox' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
					   name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
					   value="<?php echo $title; ?>" />
			</p>

			<p>
				<label
						for="<?php echo $this->get_field_id( 'condition' ); ?>"><?php esc_html_e( 'Condition:', 'boombox' ); ?></label>
				<?php if ( is_array( $conditions_choices ) ): ?>
					<select id="<?php echo $this->get_field_id( 'condition' ); ?>"
							name="<?php echo $this->get_field_name( 'condition' ); ?>" class="widefat">
						<?php foreach ( $conditions_choices as $condition_slug => $condition_name ) : ?>
							<option
									value="<?php echo esc_attr( $condition_slug ); ?>" <?php echo selected( $condition_slug, $condition, false ); ?>><?php echo esc_html( $condition_name ); ?></option>
						<?php endforeach; ?>
					</select>
				<?php endif; ?>
			</p>

			<p>
				<label
						for="<?php echo $this->get_field_id( 'time_range' ); ?>"><?php esc_html_e( 'Time Range:', 'boombox' ); ?></label>
				<?php if ( is_array( $time_range_choices ) ): ?>
					<select id="<?php echo $this->get_field_id( 'time_range' ); ?>"
							name="<?php echo $this->get_field_name( 'time_range' ); ?>" class="widefat">
						<?php foreach ( $time_range_choices as $time_range_slug => $time_range_name ) : ?>
							<option
									value="<?php echo esc_attr( $time_range_slug ); ?>" <?php echo selected( $time_range_slug, $time_range, false ); ?>><?php echo esc_html( $time_range_name ); ?></option>
						<?php endforeach; ?>
					</select>
				<?php endif; ?>
			</p>

			<p>
				<label
						for="<?php echo $this->get_field_id( 'categories' ); ?>"><?php esc_html_e( 'Categories:', 'boombox' ); ?></label>
				<?php if ( is_array( $category_choices ) ): ?>
					<select id="<?php echo $this->get_field_id( 'categories' ); ?>"
							name="<?php echo $this->get_field_name( 'categories' ); ?>[]" class="widefat"
							multiple="multiple">
						<?php foreach ( $category_choices as $category_slug => $category_name ) :
							if ( is_array( $categories ) ) {
								$selected = selected( in_array( $category_slug, $categories, true ), true, false );
							} else {
								$selected = selected( $category_slug, $categories, false );
							} ?>
							<option
									value="<?php echo esc_attr( $category_slug ); ?>" <?php echo $selected; ?>><?php echo esc_html( $category_name ); ?></option>
						<?php endforeach; ?>
					</select>
				<?php endif; ?>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'tags' ); ?>"><?php esc_html_e( 'Tags:', 'boombox' ); ?></label>
				<textarea id="<?php echo $this->get_field_id( 'tags' ); ?>" name="<?php echo $this->get_field_name( 'tags' ); ?>" class="widefat"><?php echo $tags; ?></textarea>
				<small><?php _e( 'Comma separated list of tags slugs', 'boombox' ); ?></small>
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
 * Register Boombox Picked Posts Widget
 */
if ( ! function_exists( 'boombox_load_picked_posts_widget' ) ) {

	function boombox_load_picked_posts_widget() {
		register_widget( 'Boombox_Widget_Picked_Posts' );
	}

	add_action( 'widgets_init', 'boombox_load_picked_posts_widget' );

}