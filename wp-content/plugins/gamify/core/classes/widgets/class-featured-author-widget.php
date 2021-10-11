<?php

/**
 * Widget: Featured Author
 * @since   1.1.3
 * @version 1.3.1
 */
if ( ! class_exists( 'GFY_Widget_Featured_Author' ) ) {

	class GFY_Widget_Featured_Author extends WP_Widget {

		/**
		 * Construct
		 */
		public function __construct() {
			$options = array(
				'classname'   => 'widget_gfy-featured-author',
				'description' => __( 'Displays chosen user as a featured | Gamify', 'gamify' ),
				'customize_selective_refresh' => true,
			);
			parent::__construct( 'widget_gfy-featured-author', __( 'Featured author | Gamify', 'gamify' ), $options );
			$this->alt_option_name = 'widget_gfy-featured-author';
		}

		/**
		 * Widget Output
		 * @param array $args Widget arguments
		 * @param array $instance Widget instance
		 */
		public function widget( $args, $instance ) {
			if ( ! isset( $args['widget_id'] ) ) {
				$args['widget_id'] = $this->id;
			}

			$user_id = isset( $instance[ 'user_id' ] ) ? $instance[ 'user_id' ] : 0;
			if( ! $user_id ) {
				return;
			}

			$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : __( 'Featured Author', 'gamify' );
			$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
			$user_url = esc_url( get_author_posts_url( $user_id ) );
			$type = ( isset( $instance[ 'type' ] ) && $instance[ 'type' ] ) ? $instance[ 'type' ] : MYCRED_DEFAULT_TYPE_KEY;

			/***** User Cover */
			$disable_cover = isset( $instance[ 'disable_cover' ] ) ? !!$instance[ 'disable_cover' ] : false;
			$user_cover_url = '';
			if( ! $disable_cover ) {
				$user_cover_url = apply_filters( 'gfy/widget/featured_author/cover_image_url', '', $user_id );
			}

			/***** Display Name */
			$disable_display_name = isset( $instance[ 'disable_display_name' ] ) ? !!$instance[ 'disable_display_name' ] : false;
			$display_name = '';
			if( ! $disable_display_name ) {
				$display_name = wp_kses_post( get_the_author_meta( 'display_name', $user_id ) );
			}

			/***** User Avatar */
			$disable_user_avatar = isset( $instance[ 'disable_user_avatar' ] ) ? !!$instance[ 'disable_user_avatar' ] : false;
			$user_avatar = '';
			if( ! $disable_user_avatar ) {
				$avatar_size = apply_filters( 'gfy/widget/featured_author/avatar_size', 96, $user_id );
				$display_name = $display_name ? $display_name : wp_kses_post( get_the_author_meta( 'display_name', $user_id ) );
				$user_avatar = get_avatar( $user_id, $avatar_size, '', $display_name );
			}

			/***** User Rank */
			if ( function_exists( 'mycred_get_users_rank' ) ) {
				$disable_user_rank = isset( $instance['disable_user_rank'] ) ? ! ! $instance['disable_user_rank'] : false;
				$rank              = false;
				if ( ! $disable_user_rank ) {
					$rank = mycred_get_users_rank( $user_id, $type );
				}
			}

			/***** User Bio */
			$disable_bio = isset( $instance[ 'disable_bio' ] ) ? !!$instance[ 'disable_bio' ] : false;
			$user_bio = '';
			if( ! $disable_bio ) {
				$user_bio = wp_kses_post( get_the_author_meta( 'description', $user_id ) );
				$user_bio = apply_filters( 'gfy/author_bio', $user_bio, $user_id );
			}

			/***** Badges max count */
			$badges_count = isset( $instance[ 'user_badges_max_count' ] ) ? absint( $instance[ 'user_badges_max_count' ] ) : 5;

			/***** User Badges */
			if ( function_exists( 'mycred_get_users_badges' ) ) {
				$disable_user_badges = isset( $instance['disable_user_badges'] ) ? ! ! $instance['disable_user_badges'] : false;
				$badges              = array();
				if ( ! $disable_user_badges ) {
					$badges = mycred_get_users_badges( $user_id );
					if ( $badges_count ) {
						$badges = array_slice( $badges, 0, $badges_count, true );
					}
				}
			}

			$total_data = array();

			/***** Total Points */
			$disable_total_points = isset( $instance[ 'disable_total_points' ] ) ? !!$instance[ 'disable_total_points' ] : false;
			if( ! $disable_total_points ) {
				$total_data[ 'total_points' ] = array(
					'label' => sprintf( __( 'Total %s', 'gamify' ), mycred_get_point_type_name( $type, false ) ),
					'value' => mycred_get_users_balance( $user_id, $type ),
					'priority' => 20
				);
			}

			/***** Total Posts */
			$disable_total_posts = isset( $instance[ 'disable_total_posts' ] ) ? !!$instance[ 'disable_total_posts' ] : false;
			if( ! $disable_total_posts ) {
				$total_data[ 'total_posts' ] = array(
					'label'    => __( 'Total Posts', 'gamify' ),
					'value'    => count_user_posts( $user_id, 'post', true ),
					'priority' => 30
				);
			}

			$total_data = apply_filters( 'gfy/widget/featured_author/total_data', $total_data, $user_id, $instance );

			/***** Sort by `priority` field */
			if( ! empty( $total_data ) ) {
				uasort( $total_data, function ( $a, $b ) {
					if ( $a[ 'priority' ] == $b[ 'priority' ] ) {
						return 0;
					}

					return ( $a[ 'priority' ] < $b[ 'priority' ] ) ? -1 : 1;
				} );
			}

			/***** User Socials */
			$disable_socials = isset( $instance[ 'disable_socials' ] ) ? !!$instance[ 'disable_socials' ] : false;
			$user_socials = array();
			if( ! $disable_socials ) {
				$user_socials = apply_filters( 'gfy/widget/featured_author/social_links', array(), $user_id );
			}

			echo $args['before_widget'];
			if ( $title ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}

			// region Widget content
			?>
			<div class="gfy-featured-author-content">
				<?php $style = $user_cover_url ? 'style="background-image: url(\'' . $user_cover_url . '\')"' : ''; ?>
				<a href="<?php echo $user_url; ?>">
					<div class="gfy-cover" <?php echo $style; ?>></div>
				</a>

				<?php if( $user_avatar || ( $rank && $rank->has_logo ) ) { ?>
				<div class="gfy-avatar">
					<?php if( $user_avatar ) { ?>
					<a href="<?php echo $user_url; ?>"><?php echo $user_avatar; ?></a>
					<?php } ?>

					<?php if( $rank && $rank->has_logo ) { ?>
					<div class="gfy-badge"><?php echo $rank->get_image(); ?></div>
					<?php } ?>
				</div>
				<?php } ?>

				<?php if( $display_name || $user_bio ) { ?>
				<div class="gfy-author-info">
					<?php if( $display_name ) { ?>
					<a href="<?php echo $user_url; ?>" class="gfy-name"><?php echo $display_name; ?></a>
					<?php } ?>

					<?php if( $user_bio ) { ?>
					<div class="gfy-description"><?php echo $user_bio; ?></div>
					<?php } ?>
				</div>
				<?php } ?>

				<?php if( ! empty( $user_socials ) ) { ?>
				<ul class="gfy-social">
					<?php foreach( $user_socials as $name => $social ) {
						printf( '<li><a href="%1$s" title="%3$s" target="_blank" rel="nofollow noopener"><span class="bb-icon bb-ui-icon-%2$s"></span></a></li>', $social[ 'field_data' ], $social[ 'icon' ], $name );
					} ?>
				</ul>
				<?php } ?>

				<?php if ( ! empty( $badges ) ) { ?>
				<ul class="gfy-badge-list">
					<?php
					foreach ( $badges as $badge_id => $level ) {
						$badge = mycred_get_badge( $badge_id, $level );

						if ( $badge && $badge->level_image && ( $image = $badge->get_image( $level ) ) ) {
							printf( '<li>%s</li>', $image );
						}
					} ?>
				</ul>
				<?php } ?>

				<?php if( ! empty( $total_data ) ) { ?>
				<div class="gfy-count-list">
					<?php foreach( $total_data as $data ) { ?>
					<div class="gfy-item">
						<div class="gfy-count"><?php echo number_format( floatval( $data[ 'value' ] ) ); ?></div>
						<div class="gfy-count-name"><?php echo esc_html( $data[ 'label' ] ); ?></div>
					</div>
					<?php } ?>
				</div>
				<?php } ?>

				<?php do_action( 'gfy/widget/featured_author/after_totals', $user_id ); ?>

			</div>
			<?php
			// endregion

			echo $args['after_widget'];

		}

		/**
		 * Outputs the options form on admin
		 * @param array $instance
		 *
		 * @return string
		 */
		public function form( $instance ) {

			// Defaults
			$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : __( 'Featured Author' );
			$user_id = isset( $instance[ 'user_id' ] ) ? $instance[ 'user_id' ] : 0;
			$type = isset( $instance[ 'type' ] ) ? $instance[ 'type' ] : MYCRED_DEFAULT_TYPE_KEY;
			$disable_cover = isset( $instance[ 'disable_cover' ] ) ? $instance[ 'disable_cover' ] : false;
			$disable_display_name = isset( $instance[ 'disable_display_name' ] ) ? $instance[ 'disable_display_name' ] : false;
			$disable_user_avatar = isset( $instance[ 'disable_user_avatar' ] ) ? $instance[ 'disable_user_avatar' ] : false;
			$disable_user_rank = isset( $instance[ 'disable_user_rank' ] ) ? $instance[ 'disable_user_rank' ] : false;
			$disable_bio = isset( $instance[ 'disable_bio' ] ) ? $instance[ 'disable_bio' ] : false;
			$disable_user_badges = isset( $instance[ 'disable_user_badges' ] ) ? $instance[ 'disable_user_badges' ] : false;
			$user_badges_max_count = isset( $instance[ 'user_badges_max_count' ] ) ? $instance[ 'user_badges_max_count' ] : 5;
			$disable_total_points = isset( $instance[ 'disable_total_points' ] ) ? $instance[ 'disable_total_points' ] : false;
			$disable_total_posts = isset( $instance[ 'disable_total_posts' ] ) ? $instance[ 'disable_total_posts' ] : false;
			$disable_total_reads = isset( $instance[ 'disable_total_reads' ] ) ? $instance[ 'disable_total_reads' ] : false;
			$disable_socials = isset( $instance[ 'disable_socials' ] ) ? $instance[ 'disable_socials' ] : false; ?>

			<p class="gfy-widget-field">
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
					<?php _e( 'Title', 'gamify' ); ?>:
				</label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				       name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				       type="text"
				       value="<?php echo esc_attr( $title ); ?>" class="widefat"/>
			</p>

			<p class="gfy-widget-field">
				<label for="<?php echo esc_attr( $this->get_field_id( 'user_id' ) ); ?>"><?php _e( 'User', 'gamify' ); ?>:</label>
				<?php wp_dropdown_users( array(
					'id' => esc_attr( $this->get_field_id( 'user_id' ) ),
					'class' => 'widefat',
					'name' => esc_attr( $this->get_field_name( 'user_id' ) ),
					'role__not_in' => 'subscriber',
					'selected' => $user_id
				) ); ?>
			</p>

			<?php if ( count( mycred_get_types() ) > 1 ) { ?>
				<p class="gfy-widget-field">
					<label for="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>"><?php _e( 'Point Type', 'gamify' ); ?>:</label>
					<?php mycred_types_select_from_dropdown( $this->get_field_name( 'type' ), $this->get_field_id( 'type' ), $type, false, ' class="widefat"' ); ?>
				</p>
			<?php } ?>

			<p class="gfy-widget-field">
				<input type="hidden" name="<?php echo $this->get_field_name( 'disable_cover' ) ?>" value="0" />
				<label for="<?php echo esc_attr( $this->get_field_id( 'disable_cover' ) ); ?>">
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'disable_cover' ) ); ?>" <?php checked( $disable_cover, 1 ); ?> name="<?php echo $this->get_field_name('disable_cover' ) ?>"
					       value="1" />
					<?php _e( 'Disable Cover Image', 'gamify' ); ?>
				</label>
			</p>

			<p class="gfy-widget-field">
				<input type="hidden" name="<?php echo $this->get_field_name( 'disable_display_name' ) ?>" value="0" />
				<label for="<?php echo esc_attr( $this->get_field_id( 'disable_display_name' ) ); ?>">
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'disable_display_name' ) ); ?>" <?php checked( $disable_display_name, 1 ); ?> name="<?php echo $this->get_field_name( 'disable_display_name' ) ?>" value="1" />
					<?php _e( 'Disable Name', 'gamify' ); ?>
				</label>
			</p>

			<p class="gfy-widget-field">
				<input type="hidden" name="<?php echo $this->get_field_name( 'disable_user_avatar' ) ?>" value="0" />
				<label for="<?php echo esc_attr( $this->get_field_id( 'disable_user_avatar' ) ); ?>">
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'disable_user_avatar' ) ); ?>" <?php checked( $disable_user_avatar, 1 ); ?> name="<?php echo $this->get_field_name( 'disable_user_avatar' ) ?>" value="1" />
					<?php _e( 'Disable User Avatar', 'gamify' ); ?>
				</label>
			</p>

			<p class="gfy-widget-field">
				<input type="hidden" name="<?php echo $this->get_field_name( 'disable_user_rank' ) ?>" value="0" />
				<label for="<?php echo esc_attr( $this->get_field_id( 'disable_user_rank' ) ); ?>">
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'disable_user_rank' ) ); ?>" <?php checked( $disable_user_rank, 1 ); ?> name="<?php echo $this->get_field_name( 'disable_user_rank' ) ?>" value="1" />
					<?php _e( 'Disable Rank Logo', 'gamify' ); ?>
				</label>
			</p>

			<p class="gfy-widget-field">
				<input type="hidden" name="<?php echo $this->get_field_name( 'disable_bio' ) ?>" value="0" />
				<label for="<?php echo esc_attr( $this->get_field_id( 'disable_bio' ) ); ?>">
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'disable_bio' ) ); ?>" <?php checked( $disable_bio, 1 ); ?> name="<?php echo $this->get_field_name( 'disable_bio' ) ?>" value="1" />
					<?php _e( 'Disable Biography', 'gamify' ); ?>
				</label>
			</p>

			<p class="gfy-widget-field">
				<input type="hidden" name="<?php echo $this->get_field_name( 'disable_user_badges' ) ?>" value="0" />
				<label for="<?php echo esc_attr( $this->get_field_id( 'disable_user_badges' ) ); ?>">
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'disable_user_badges' ) ); ?>" <?php checked( $disable_user_badges, 1 ); ?> name="<?php echo $this->get_field_name( 'disable_user_badges' ) ?>" value="1" />
					<?php _e( 'Disable Earned Badges', 'gamify' ); ?>
				</label>
			</p>

			<p class="gfy-widget-field">
				<label for="<?php echo esc_attr( $this->get_field_id( 'user_badges_max_count' ) ); ?>"><?php _e( 'Max Badges Count', 'gamify' ); ?>:</label>
				<input type="number" id="<?php echo esc_attr( $this->get_field_id( 'user_badges_max_count' ) ); ?>" class="widefat" name="<?php echo $this->get_field_name( 'user_badges_max_count' ) ?>" min="1" step="1" value="<?php echo absint( $user_badges_max_count ); ?>" />
			</p>

			<p class="gfy-widget-field">
				<input type="hidden" name="<?php echo $this->get_field_name( 'disable_total_points' ) ?>" value="0" />
				<label for="<?php echo esc_attr( $this->get_field_id( 'disable_total_points' ) ); ?>">
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'disable_total_points' ) ); ?>" <?php checked( $disable_total_points, 1 ); ?> name="<?php echo $this->get_field_name( 'disable_total_points' ) ?>" value="1" />
					<?php _e( 'Disable Total Points', 'gamify' ); ?>
				</label>
			</p>

			<p class="gfy-widget-field">
				<input type="hidden" name="<?php echo $this->get_field_name( 'disable_total_posts' ) ?>" value="0" />
				<label for="<?php echo esc_attr( $this->get_field_id( 'disable_total_posts' ) ); ?>">
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'disable_total_posts' ) ); ?>" <?php checked( $disable_total_posts, 1 ); ?> name="<?php echo $this->get_field_name( 'disable_total_posts' ) ?>" value="1" />
					<?php _e( 'Disable Total Posts', 'gamify' ); ?>
				</label>
			</p>

			<p class="gfy-widget-field">
				<input type="hidden" name="<?php echo $this->get_field_name( 'disable_total_reads' ) ?>" value="0" />
				<label for="<?php echo esc_attr( $this->get_field_id( 'disable_total_reads' ) ); ?>">
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'disable_total_reads' ) ); ?>" <?php checked( $disable_total_reads, 1 ); ?> name="<?php echo $this->get_field_name( 'disable_total_reads' ) ?>" value="1" />
					<?php _e( 'Disable Total Reads', 'gamify' ); ?>
				</label>
			</p>

			<p class="gfy-widget-field">
				<input type="hidden" name="<?php echo $this->get_field_name( 'disable_socials' ) ?>" value="0" />
				<label for="<?php echo esc_attr( $this->get_field_id( 'disable_socials' ) ); ?>">
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'disable_socials' ) ); ?>" <?php checked( $disable_socials, 1 ); ?> name="<?php echo $this->get_field_name( 'disable_socials' ) ?>" value="1" />
					<?php _e( 'Disable Social Icons', 'gamify' ); ?>
				</label>
			</p>

			<?php
			return '';
		}

		/**
		 * Processes widget options to be saved
		 * @param array $new_instance The new options set
		 * @param array $old_instance The old options set
		 *
		 * @return array
		 */
		public function update( $new_instance, $old_instance ) {

			$instance = $old_instance;

			$instance[ 'title' ] = isset( $new_instance[ 'title' ] ) ? wp_kses_post( $new_instance[ 'title' ] ) : '';
			$instance[ 'user_id' ] = isset( $new_instance[ 'user_id' ] ) ? absint( $new_instance[ 'user_id' ] ) : '';
			$instance[ 'type' ] = isset( $new_instance[ 'type' ] ) ? MYCRED_DEFAULT_TYPE_KEY : '';
			$instance[ 'disable_cover' ] = isset( $new_instance[ 'disable_cover' ] ) ? !!$new_instance[ 'disable_cover' ] : false;
			$instance[ 'disable_display_name' ] = isset( $new_instance[ 'disable_display_name' ] ) ? !!$new_instance[ 'disable_display_name' ] : false;
			$instance[ 'disable_user_avatar' ] = isset( $new_instance[ 'disable_user_avatar' ] ) ? !!$new_instance[ 'disable_user_avatar' ] : false;
			$instance[ 'disable_user_rank' ] = isset( $new_instance[ 'disable_user_rank' ] ) ? !!$new_instance[ 'disable_user_rank' ] : false;
			$instance[ 'disable_bio' ] = isset( $new_instance[ 'disable_bio' ] ) ? !!$new_instance[ 'disable_bio' ] : false;
			$instance[ 'disable_user_badges' ] = isset( $new_instance[ 'disable_user_badges' ] ) ? !!$new_instance[ 'disable_user_badges' ] : false;
			$instance[ 'user_badges_max_count' ] = isset( $new_instance[ 'user_badges_max_count' ] ) ? absint( $new_instance[ 'user_badges_max_count' ] ) : 5;
			$instance[ 'disable_total_points' ] = isset( $new_instance[ 'disable_total_points' ] ) ? !!$new_instance[ 'disable_total_points' ] : false;
			$instance[ 'disable_total_posts' ] = isset( $new_instance[ 'disable_total_posts' ] ) ? !!$new_instance[ 'disable_total_posts' ] : false;
			$instance[ 'disable_total_reads' ] = isset( $new_instance[ 'disable_total_reads' ] ) ? !!$new_instance[ 'disable_total_reads' ] : false;
			$instance[ 'disable_socials' ] = isset( $new_instance[ 'disable_socials' ] ) ? !!$new_instance[ 'disable_socials' ] : false;

			mycred_flush_widget_cache( 'widget_gfy-featured-author' );

			return $instance;

		}

	}

	function gfy_register_featured_author_widget() {
		register_widget( 'GFY_Widget_Featured_Author' );
	}

	add_action( 'mycred_widgets_init', 'gfy_register_featured_author_widget' );

}