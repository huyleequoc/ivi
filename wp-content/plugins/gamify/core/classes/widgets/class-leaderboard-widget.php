<?php

/**
 * Widget: Leaderboard
 * @since   0.1
 * @version 1.3.1
 */
if ( ! class_exists( 'GFY_Widget_Leaderboard' ) ) {

	class GFY_Widget_Leaderboard extends WP_Widget {

		/**
		 * Construct
		 */
		public function __construct() {
			$widget_ops = array(
				'classname'   => 'widget_gfy_leaderboard',
				'description' => __( 'Leaderboard based on instances or balances. | Gamify', 'gamify' ),
				'customize_selective_refresh' => true,
			);
			parent::__construct( 'widget_gfy_leaderboard', __( 'Leaderboard | Gamify', 'gamify' ), $widget_ops );
			$this->alt_option_name = 'widget_gfy_leaderboard';
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

			$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : __( 'Leaderboard', 'gamify' );
			$type = isset( $instance[ 'type' ] ) ? $instance[ 'type' ] : MYCRED_DEFAULT_TYPE_KEY;
			$based_on = isset( $instance[ 'based_on' ] ) ? $instance[ 'based_on' ] : 'balance';
			$show_visitors = isset( $instance[ 'show_visitors' ] ) ? $instance[ 'show_visitors' ] : 0;
			$number = isset( $instance[ 'number' ] ) ? $instance[ 'number' ] : 5;
			$row_components = isset( $instance['row_components'] ) ?
				$instance['row_components'] : array(
					'position',
					'avatar',
					'name',
					'cred_f',
					'rank'
				);
			$offset = isset( $instance[ 'offset' ] ) ? $instance[ 'offset' ] : 0;
			$order = isset( $instance[ 'order' ] ) ? $instance[ 'order' ] : 'DESC';
			$current = isset( $instance[ 'current' ] ) ? $instance[ 'current' ] : 0;
			$timeframe = isset( $instance[ 'timeframe' ] ) ? $instance[ 'timeframe' ] : '';
			$start_date = isset( $instance[ 'start_date' ] ) ? $instance[ 'start_date' ] : '';
			$timeframe_tabs = isset( $instance[ 'timeframe_tabs' ] ) ? $instance[ 'timeframe_tabs' ] : array();
			if( $timeframe && $timeframe != 'custom' ) {
				$timeframe_tabs[] = $timeframe;
			}
			$timeframe_tabs = array_unique( $timeframe_tabs );

			/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
			$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

			// Check if we want to show this to visitors
			if ( ! $show_visitors && ! is_user_logged_in() ) {
				return;
			}

			$template = '';
			if( ! empty( $row_components ) ) {
				$html_syntax = array(
					'left'  => array(
						'position' => '',
						'avatar'   => array(
							'image' => '',
							'rank'  => '',
						),
					),
					'right' => array(
						'name'   => '',
						'cred_f' => ''
					)
				);
				/***** position */
				if ( in_array( 'position', $row_components ) ) {
					$html_syntax[ 'left' ][ 'position' ] = '<span class="item-number">%position%</span>';
				}

				/***** image */
				if ( in_array( 'avatar', $row_components ) ) {
					$html_syntax[ 'left' ][ 'avatar' ][ 'image' ] = '<div class="item-avatar gfy-avatar circle-frame"><a href="%user_profile_url%">%gfy_widget_user_avatar%</a></div>';
				}

				/***** rank */
				if ( in_array( 'avatar', $row_components )
					&& mycred_get_module( 'ranks' )
					&& in_array( 'rank', $row_components )
				) {
					$html_syntax[ 'left' ][ 'avatar' ][ 'rank' ] = '<span class="item-badge gfy-badge badge-xs">%gfy_widget_rank_image%</span>';
				}

				/***** avatar row */
				if ( $html_syntax[ 'left' ][ 'avatar' ][ 'image' ] || $html_syntax[ 'left' ][ 'avatar' ][ 'rank' ] ) {
					$html_syntax[ 'left' ][ 'avatar' ] = '<div class="item-avatar-block">' . join( '', $html_syntax[ 'left' ][ 'avatar' ] ) . '</div>';
				} else {
					$html_syntax[ 'left' ][ 'avatar' ] = '';
				}

				/***** left row */
				if ( $html_syntax[ 'left' ][ 'position' ] || $html_syntax[ 'left' ][ 'avatar' ] ) {
					$html_syntax[ 'left' ] = '<div class="item-left">' . join( '', $html_syntax[ 'left' ] ) . '</div>';
				} else {
					$html_syntax[ 'left' ] = '';
				}

				/***** user name */
				if ( in_array( 'name', $row_components ) ) {
					$html_syntax[ 'right' ][ 'name' ] = '<h3 class="item-title"><a href="%user_profile_url%">%display_name%</a></h3>';
				}
				/***** points count */
				if ( in_array( 'cred_f', $row_components ) ) {
					$creds_placeholder = '%cred_f%';
					if( apply_filters( 'gfy/render_current_creds', true, 'widget' ) ) {
						$creds_placeholder .= ' / %user_current_balance%';
					}
					$html_syntax[ 'right' ][ 'cred_f' ] = '<span class="item-points">' . $creds_placeholder . '</span>';
				}

				/***** right row */
				if ( $html_syntax[ 'right' ][ 'name' ] || $html_syntax[ 'right' ][ 'cred_f' ] ) {
					$html_syntax[ 'right' ] = '<div class="item-right">' . join( '', $html_syntax[ 'right' ] ) . '</div>';
				} else {
					$html_syntax[ 'right' ] = '';
				}

				if ( $html_syntax[ 'left' ] || $html_syntax[ 'right' ] ) {
					$template = '<div class="item-content">' . join( '', $html_syntax ) . '</div>';
				}
			}



			// Get Rankings
			$shortcode_args = array(
				'template'  => $template,
				'number'    => $number,
				'based_on'  => $based_on,
				'type'      => $type,
				'order'     => $order,
				'offset'    => $offset,
				'current'   => $current,
				'total'     => 1,
				'timeframe' => ( $timeframe == 'custom' ) ? $start_date : $timeframe
			);

			echo $args['before_widget'];
			if ( $title ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}

			$tabs_labels = array(
				'all-time'   => __( 'All Time', 'gamify' ),
				'today'      => __( 'Today', 'gamify' ),
				'this-week'  => __( 'Week', 'gamify' ),
				'this-month' => __( 'Month', 'gamify' ),
			);
			if( $template ) {
				add_filter( 'mycred_ranking_classes', array( $this, 'edit_ranking_row_classes' ), 10, 1 );
				add_filter( 'mycred_ranking_row', array( $this, 'edit_ranking_row_layout' ), 10, 5 ); ?>

				<div <?php if( count( $timeframe_tabs ) > 1 ) { ?>class="gfy-tabs"<?php } ?>>

					<?php if( count( $timeframe_tabs ) > 1 ) { ?>
					<ul class="tabs-menu">
						<?php
						$default_selected = $timeframe ? $timeframe : $timeframe_tabs[0];
						foreach( $timeframe_tabs as $timeframe_tab ) { ?>
						<li class="tab-menu-item<?php echo ( $timeframe_tab == $default_selected ) ? ' active' : ''; ?>"><a href="#widget_gfy_leaderboard_<?php echo esc_attr( $timeframe_tab ); ?>"><?php echo esc_html( $tabs_labels[ $timeframe_tab ] ); ?></a></li>
						<?php } ?>
					</ul>
					<?php } ?>

					<?php if( count( $timeframe_tabs ) > 1 ) { ?>
					<div class="tabs-content">
						<?php if( 'custom' == $timeframe ) { ?>
						<div id="widget_gfy_leaderboard_<?php echo esc_attr( $timeframe ); ?>" class="tab-content-item active">
							<?php
							$shortcode_args[ 'timeframe' ] = $start_date;
							echo mycred_render_shortcode_leaderboard( $shortcode_args ); ?>
						</div>
						<?php } ?>
						<?php foreach( $timeframe_tabs as $timeframe_tab ) { ?>
						<div id="widget_gfy_leaderboard_<?php echo esc_attr( $timeframe_tab ); ?>" class="tab-content-item<?php echo ( $timeframe_tab == $default_selected ) ? ' active' : ''; ?>">
							<?php
							$shortcode_args[ 'timeframe' ] = ( $timeframe_tab == 'all-time' ) ? '' : $timeframe_tab;
							echo mycred_render_shortcode_leaderboard( $shortcode_args );
							?>
						</div>
						<?php } ?>
					</div>
					<?php } else {
						$shortcode_args[ 'timeframe' ] = ( $timeframe == 'custom' ) ?  $start_date : $timeframe;
						echo mycred_render_shortcode_leaderboard( $shortcode_args );
					} ?>
				</div>
				<?php
				remove_filter( 'mycred_ranking_classes', array( $this, 'edit_ranking_row_classes' ), 10 );
				remove_filter( 'mycred_ranking_row', array( $this, 'edit_ranking_row_layout' ), 10 );
			}

			echo $args['after_widget'];

		}

		/**
		 * Get row components choices
		 * @return array
		 */
		private function get_row_components() {
			$components = array(
				'position' => __( 'Position', 'gamify' ),
				'avatar' => __( 'Avatar', 'gamify' ),
				'name' => __( 'Name', 'gamify' ),
				'cred_f' => __( 'Creds Count', 'gamify' )
			);

			if( mycred_get_module( 'ranks' ) ) {
				$components[ 'rank' ] = __( 'Rank', 'gamify' );
			}

			return apply_filters( 'gfy/widget/leaderboard/row_components', $components );
		}

		/**
		 * Outputs the options form on admin
		 * @param array $instance
		 *
		 * @return string
		 */
		public function form( $instance ) {

			// Defaults
			$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : __( 'Leaderboard', 'gamify' );
			$type = isset( $instance[ 'type' ] ) ? $instance[ 'type' ] : MYCRED_DEFAULT_TYPE_KEY;
			$based_on = isset( $instance[ 'based_on' ] ) ? $instance[ 'based_on' ] : 'balance';

			$number = isset( $instance[ 'number' ] ) ? $instance[ 'number' ] : 5;
			$show_visitors = isset( $instance[ 'show_visitors' ] ) ? $instance[ 'show_visitors' ] : 0;
			$offset = isset( $instance[ 'offset' ] ) ? $instance[ 'offset' ] : 0;
			$order = isset( $instance[ 'order' ] ) ? $instance[ 'order' ] : 'DESC';
			$current = isset( $instance[ 'current' ] ) ? $instance[ 'current' ] : 0;
			$timeframe = isset( $instance[ 'timeframe' ] ) ? $instance[ 'timeframe' ] : '';
			$start_date = isset( $instance[ 'start_date' ] ) ? $instance[ 'start_date' ] : '';
			$timeframe_tabs = isset( $instance[ 'timeframe_tabs' ] ) ? $instance[ 'timeframe_tabs' ] : array();
			$row_components = isset( $instance['row_components'] ) ?
				$instance['row_components'] : array(
					'position',
					'avatar',
					'name',
					'cred_f',
					'rank'
				);

			$mycred = mycred( $type );
			$mycred_types = mycred_get_types();

			?>
			<p class="gfy-widget-field">
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
					<?php _e( 'Title', 'gamify' ); ?>:
				</label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				       name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				       type="text"
				       value="<?php echo esc_attr( $title ); ?>" class="widefat"/>
			</p>

			<?php if ( count( $mycred_types ) > 1 ) { ?>
				<p class="gfy-widget-field">
					<label for="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>">
						<?php _e( 'Point Type', 'gamify' ); ?>:
					</label><br/>
					<?php mycred_types_select_from_dropdown( $this->get_field_name( 'type' ), $this->get_field_id( 'type' ), $type, false,' class="widefat"' ); ?>
				</p>
			<?php } else {
				mycred_types_select_from_dropdown( $this->get_field_name( 'type' ), $this->get_field_id( 'type' ), $type );
			} ?>

			<p class="gfy-widget-field">
				<label for="<?php echo esc_attr( $this->get_field_id( 'based_on' ) ); ?>">
					<?php _e( 'Based On', 'gamify' ); ?>:
				</label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'based_on' ) ); ?>"
				       name="<?php echo esc_attr( $this->get_field_name( 'based_on' ) ); ?>"
				       type="text"
				       value="<?php echo esc_attr( $based_on ); ?>" class="widefat" />
				<small>
					<?php _e( 'Use "balance" to base the leaderboard on your users current balances or use a specific reference.', 'gamify' ); ?>
					<a href="http://codex.mycred.me/chapter-vi/log-references/" target="_blank" rel="noopener">
						<?php _e( 'Reference Guide', 'gamify' ); ?>
					</a>
				</small>
			</p>

			<p class="gfy-widget-field">
				<label for="<?php echo esc_attr( $this->get_field_id( 'show_visitors' ) ); ?>">
					<input type="checkbox"
						name="<?php echo esc_attr( $this->get_field_name( 'show_visitors' ) ); ?>"
						id="<?php echo esc_attr( $this->get_field_id( 'show_visitors' ) ); ?>"
						value="1"<?php checked( $show_visitors, 1 ); ?>
						class="checkbox"/> <?php _e( 'Visible to non-members', 'gamify' ); ?>
				</label>
			</p>

			<p class="gfy-widget-field">
				<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>">
					<?php _e( 'Number of users', 'gamify' ); ?>:
				</label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"
				       name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>"
				       type="text"
				       value="<?php echo absint( $number ); ?>" size="3" class="widefat" />
			</p>

			<p class="gfy-widget-field">
				<label><?php _e( 'Row Components', 'gamify' ); ?>:</label>

				<?php
				$field_name = esc_attr( $this->get_field_name( 'row_components' ) );
				$field_id = esc_attr( $this->get_field_id( 'row_components' ) );
				foreach( $this->get_row_components() as $value => $label ) { ?>
					<br/>
				<label for="<?php echo $field_id . '-' . $value; ?>">
					<input type="checkbox"
					       value="<?php echo $value; ?>"
					       <?php checked( in_array( $value, $row_components ), true ) ?>
					       id="<?php echo $field_id . '-' . $value; ?>"
					       name="<?php echo $field_name; ?>[]" />
					<?php echo $label; ?>
				</label>
				<?php } ?>
			</p>

			<p class="gfy-widget-field">
				<label for="<?php echo esc_attr( $this->get_field_id( 'offset' ) ); ?>">
					<?php _e( 'Offset', 'gamify' ); ?>:
				</label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'offset' ) ); ?>"
				       name="<?php echo esc_attr( $this->get_field_name( 'offset' ) ); ?>"
				       type="text"
				       value="<?php echo absint( $offset ); ?>"
				       size="3"
				       class="widefat" />
				<small><?php _e( 'Optional offset of order. Use zero to return the first in the list.', 'gamify' ); ?></small>
			</p>

			<p class="gfy-widget-field">
				<label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>">
					<?php _e( 'Order', 'gamify' ); ?>:
				</label><br/>
				<select name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>"
						class="widefat"
				        id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>">
					<?php

					$options = array(
						'ASC'  => __( 'Ascending', 'gamify' ),
						'DESC' => __( 'Descending', 'gamify' ),
					);

					foreach ( $options as $value => $label ) { ?>
						<option value="<?php echo $value; ?>" <?php selected( $order, $value ); ?>>
							<?php echo $label; ?>
						</option>
						<?php
					}
					?>
				</select>
			</p>

			<p class="gfy-widget-field">
				<label for="<?php echo esc_attr( $this->get_field_id( 'current' ) ); ?>">
					<input type="checkbox"
					       name="<?php echo esc_attr( $this->get_field_name( 'current' ) ); ?>"
					       id="<?php echo esc_attr( $this->get_field_id( 'current' ) ); ?>"
					       value="1"<?php checked( $current, 1 ); ?>
					       class="checkbox" /> <?php _e( 'Append current users position', 'gamify' ); ?>
				</label>
				<br/>
				<small><?php _e( 'If the current user is not in this leaderboard, you can select to append them at the end with their current position.', 'gamify' ); ?></small>
			</p>

			<?php
				$unique_id = uniqid();
			?>
			<p class="gfy-widget-field">
				<label for="<?php echo esc_attr( $this->get_field_id( 'timeframe' ) ); ?>">
					<?php _e( 'Timeframe', 'gamify' ); ?>:
				</label><br/>

				<select
					id="<?php echo esc_attr( $this->get_field_id( 'timeframe' ) ); ?>"
					class="widefat"
					name="<?php echo esc_attr( $this->get_field_name( 'timeframe' ) ); ?>"
					onchange="
						if( jQuery(this).val() == 'custom' ) {
						    jQuery( '#<?php echo $unique_id; ?>' ).css( { 'display' : 'block' } )
						} else {
							jQuery( '#<?php echo $unique_id; ?>' ).css( { 'display' : 'none' } )
						}"
					>
					<option value="" <?php selected( $timeframe, '' ); ?>><?php _e( 'All Time', 'gamify' ); ?></option>
					<option value="today" <?php selected( $timeframe, 'today' ); ?>><?php _e( 'Today', 'gamify' ); ?></option>
					<option value="this-week" <?php selected( $timeframe, 'this-week' ); ?>><?php _e( 'This Week', 'gamify' ); ?></option>
					<option value="this-month" <?php selected( $timeframe, 'this-month' ); ?>><?php _e( 'This Month', 'gamify' ); ?></option>
					<option value="custom" <?php selected( $timeframe, 'custom' ); ?>><?php _e( 'Custom', 'gamify' ); ?></option>
				</select><br/>

			</p>

			<p id="<?php echo $unique_id; ?>" style="display:<?php echo $timeframe == 'custom' ? 'block' : 'none'; ?>;">
				<label for="<?php echo esc_attr( $this->get_field_id( 'start_date' ) ); ?>">
					<?php _e( 'Start Date', 'gamify' ); ?>:
				</label><br/>

				<input id="<?php echo esc_attr( $this->get_field_id( 'start_date' ) ); ?>"
				       name="<?php echo esc_attr( $this->get_field_name( 'start_date' ) ); ?>"
				       type="text"
				       value="<?php echo esc_attr( $start_date ); ?>" size="3" class="widefat"/>
				<small><?php _e( 'Option to limit the leaderboard based on a specific timeframe.', 'gamify' ); ?></small>
			</p>

			<p class="gfy-widget-field">
				<label><?php _e( 'Timeframe Tabs', 'gamify' ); ?>:</label>
				<br/>
				<label for="<?php echo esc_attr( $this->get_field_id( 'timeframe_tabs' ) ); ?>-all-time">
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'timeframe_tabs' ) ); ?>-all-time" name="<?php echo esc_attr( $this->get_field_name( 'timeframe_tabs' ) ); ?>[]" value="all-time" <?php checked( in_array( 'all-time', $timeframe_tabs ), true ); ?> /> <?php esc_html_e( 'All Time', 'gamify' ); ?>
				</label>
				<br/>
				<label for="<?php echo esc_attr( $this->get_field_id( 'timeframe_tabs' ) ); ?>-today">
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'timeframe_tabs' ) ); ?>-today" name="<?php echo esc_attr( $this->get_field_name( 'timeframe_tabs' ) ); ?>[]" value="today" <?php checked( in_array( 'today', $timeframe_tabs ), true ); ?> /> <?php esc_html_e( 'Today', 'gamify' ); ?>
				</label>
				<br/>
				<label for="<?php echo esc_attr( $this->get_field_id( 'timeframe_tabs' ) ); ?>-this-week">
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'timeframe_tabs' ) ); ?>-this-week" name="<?php echo esc_attr( $this->get_field_name( 'timeframe_tabs' ) ); ?>[]" value="this-week" <?php checked( in_array( 'this-week', $timeframe_tabs ), true ); ?> /> <?php esc_html_e( 'Week', 'gamify' ); ?>
				</label>
				<br/>
				<label for="<?php echo esc_attr( $this->get_field_id( 'timeframe_tabs' ) ); ?>-this-month">
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'timeframe_tabs' ) ); ?>-this-month" name="<?php echo esc_attr( $this->get_field_name( 'timeframe_tabs' ) ); ?>[]" value="this-month" <?php checked( in_array( 'this-month', $timeframe_tabs ), true ); ?> /> <?php esc_html_e( 'Month', 'gamify' ); ?>
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

			$instance[ 'number' ] = absint( $new_instance[ 'number' ] );
			$instance[ 'title' ] = wp_kses_post( $new_instance[ 'title' ] );
			$instance[ 'type' ] = sanitize_key( $new_instance[ 'type' ] );
			$instance[ 'based_on' ] = sanitize_key( $new_instance[ 'based_on' ] );
			$instance[ 'show_visitors' ] = ( isset( $new_instance[ 'show_visitors' ] ) ) ? 1 : 0;
			$instance[ 'row_components' ] = $new_instance[ 'row_components' ];
			$instance[ 'offset' ] = sanitize_text_field( $new_instance[ 'offset' ] );
			$instance[ 'order' ] = sanitize_text_field( $new_instance[ 'order' ] );
			$instance[ 'current' ] = ( isset( $new_instance[ 'current' ] ) ) ? 1 : 0;
			$instance[ 'timeframe' ] = sanitize_text_field( $new_instance[ 'timeframe' ] );
			$instance[ 'start_date' ] = sanitize_text_field( $new_instance[ 'start_date' ] );
			$instance[ 'timeframe_tabs' ] = $new_instance[ 'timeframe_tabs' ];

			mycred_flush_widget_cache( 'widget_gfy_leaderboard' );

			return $instance;

		}

		/**
		 * Edit ranking row classes
		 * @param string $classes Current classes
		 *
		 * @return string
		 */
		public function edit_ranking_row_classes( $classes ) {
			$classes .= ' leaderboard-item';

			return $classes;
		}

		/**
		 * Edit ranking row instance
		 * @param string $layout Current layout
		 * @param string $template Template
		 * @param $user
		 * @param int $position User position
		 * @param myCRED_Query_Leaderboard $instance Current instance
		 *
		 * @return string;
		 */
		public function edit_ranking_row_layout( $layout, $template, $user, $position, $instance ) {
			$replace_pairs = array();

			/***** User avatar */
			if( strpos( $layout, '%gfy_widget_user_avatar%' ) !== false ) {
				$avatar = get_avatar( $user['ID'], 80 );
				$avatar = $avatar ? $avatar : '';

				$replace_pairs[ '%gfy_widget_user_avatar%' ] = $avatar;
			}

			/***** Rank logo */
			if( strpos( $layout, '%gfy_widget_rank_image%' ) !== false ) {
				$rank_logo = '';
				if( function_exists( 'mycred_get_rank_logo' ) ) {
					$rank_id = mycred_get_users_rank_id( $user['ID'] );
					if( $rank_id ) {
						$rank_logo = mycred_get_rank_logo( $rank_id, 'user_rank_logo', array(
							'title' => get_the_title( $rank_id )
						) );
					}
				}

				$replace_pairs[ '%gfy_widget_rank_image%' ] = $rank_logo;
			}

			if ( strpos( $layout, '%user_current_balance%' ) !== false && isset( $instance->args['type'] ) ) {
				$replace_pairs['%user_current_balance%'] = mycred_format_creds( mycred_get_users_balance( $user['ID'] ), $instance->args['type'] );
			}

			if( ! empty( $replace_pairs ) ) {
				$layout = strtr( $layout, $replace_pairs );
			}

			return $layout;
		}

	}

	function gfy_register_leaderboard_widget() {
		register_widget( 'GFY_Widget_Leaderboard' );
	}

	add_action( 'mycred_widgets_init', 'gfy_register_leaderboard_widget' );

}