<?php
/**
 * Boombox_Widget_Sidebar_Footer class
 *
 * @package BoomBox_Theme
 * @since 1.0.0
 * @version 2.1.3
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}


if ( ! class_exists( 'Boombox_Widget_Sidebar_Footer' ) ) {
	/**
	 * Core class used to implement a Sidebar Footer widget.
	 *
	 * @see WP_Widget
	 */
	class Boombox_Widget_Sidebar_Footer extends WP_Widget {

		/**
		 * Sets up a new Sidebar Footer widget instance.
		 *
		 * @since 2.8.0
		 * @access public
		 */
		public function __construct() {
			$widget_ops = array(
				'classname'                   => 'widget_sidebar_footer',
				'description'                 => esc_html__( 'Your site&#8217;s footer.', 'boombox' ),
				'customize_selective_refresh' => true,
			);
			parent::__construct( 'boombox-sidebar-footer', esc_html__( 'Boombox Sidebar Footer', 'boombox' ), $widget_ops );
			$this->alt_option_name = 'widget_sidebar_footer';
		}

		/**
		 * Outputs the content for the current Sidebar Footer widget instance.
		 *
		 * @param array $args Display arguments including 'before_title', 'after_title',
		 *                        'before_widget', and 'after_widget'.
		 * @param array $instance Settings for the current Sidebar Footer widget instance.
		 */
		public function widget( $args, $instance ) {
			if ( ! isset( $args['widget_id'] ) ) {
				$args['widget_id'] = $this->id;
			}

			// Get menu
			$nav_menu       = ! empty( $instance['nav_menu'] ) ? wp_get_nav_menu_object( $instance['nav_menu'] ) : false;
			$show_copyright = isset( $instance['show_copyright'] ) ? $instance['show_copyright'] : false;

			echo $args['before_widget'];

			if ( ! empty( $instance['title'] ) ) {
				echo $args['before_title'] . $instance['title'] . $args['after_title'];
			}

			if ( $nav_menu ) {
				$nav_menu_args = array(
					'fallback_cb' => '',
					'menu'        => $nav_menu,
					'menu_class'  => '',
					'depth'       => 1,
					'container'   => 'nav'
				);

				/**
				 * Filter the arguments for the Custom Menu widget.
				 *
				 * @since 4.2.0
				 * @since 4.4.0 Added the `$instance` parameter.
				 *
				 * @param array $nav_menu_args {
				 *     An array of arguments passed to wp_nav_menu() to retrieve a custom menu.
				 *
				 * @type callable|bool $fallback_cb Callback to fire if the menu doesn't exist. Default empty.
				 * @type mixed $menu Menu ID, slug, or name.
				 * }
				 *
				 * @param stdClass $nav_menu Nav menu object for the current menu.
				 * @param array $args Display arguments for the current widget.
				 * @param array $instance Array of settings for the current widget.
				 */
				echo '<div class="footer-nav">';
				wp_nav_menu( apply_filters( 'widget_nav_menu_args', $nav_menu_args, $nav_menu, $args, $instance ) );
				echo '</div>';
			}

			if ( $show_copyright ) {
				?>
				<div class="text">&copy;
					<?php printf( '%1$s %2$s.',
						date( 'Y' ),
						wp_kses_post( esc_html__( boombox_get_theme_option( 'footer_general_text' ), 'boombox' ) )
					); ?>
				</div>
			<?php
			}

			echo $args['after_widget'];
		}

		/**
		 * Handles updating the settings for the current Sidebar Footer widget instance.
		 *
		 * @param array $new_instance New settings for this instance as input by the user via
		 *                            WP_Widget::form().
		 * @param array $old_instance Old settings for this instance.
		 *
		 * @return array Updated settings to save.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			if ( ! empty( $new_instance['nav_menu'] ) ) {
				$instance['nav_menu'] = (int) $new_instance['nav_menu'];
			}
			$instance['show_copyright'] = isset( $new_instance['show_copyright'] ) ? (bool) $new_instance['show_copyright'] : false;

			return $instance;
		}

		/**
		 * Outputs the settings form for the Sidebar Footer widget.
		 *
		 * @param array $instance
		 *
		 * @return string|void
		 */
		public function form( $instance ) {
			$nav_menu       = isset( $instance['nav_menu'] ) ? $instance['nav_menu'] : '';
			$show_copyright = isset( $instance['show_copyright'] ) ? (bool) $instance['show_copyright'] : false;

			// Get menus
			$menus = wp_get_nav_menus();

			// If no menus exists, direct the user to go and create some.
			?>
			<p class="nav-menu-widget-no-menus-message" <?php if ( ! empty( $menus ) ) { echo ' style="display:none" '; } ?>>
				<?php
				if ( isset( $GLOBALS['wp_customize'] ) && $GLOBALS['wp_customize'] instanceof WP_Customize_Manager ) {
					$url = 'javascript: wp.customize.panel( "nav_menus" ).focus();';
				} else {
					$url = admin_url( 'nav-menus.php' );
				}
				?>
				<?php echo sprintf( wp_kses_post( 'No menus have been created yet. <a href="%s">Create some</a>.' ), esc_attr( $url ) ); ?>
			</p>

			<p <?php if ( empty( $menus ) ) { echo ' style="display:none" '; } ?> >
				<label for="<?php echo $this->get_field_id( 'nav_menu' ); ?>"><?php esc_html_e( 'Select Menu', 'boombox' ); ?>:</label>
				<select id="<?php echo $this->get_field_id( 'nav_menu' ); ?>"
				        name="<?php echo $this->get_field_name( 'nav_menu' ); ?>">
					<option value="0">&mdash; <?php esc_html_e( 'Select', 'boombox' ); ?> &mdash;</option>
					<?php foreach ( $menus as $menu ) : ?>
						<option
							value="<?php echo esc_attr( $menu->term_id ); ?>" <?php selected( $nav_menu, $menu->term_id ); ?>>
							<?php echo esc_html( $menu->name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</p>

			<p><input class="checkbox" type="checkbox"<?php checked( $show_copyright ); ?>
			          id="<?php echo $this->get_field_id( 'show_copyright' ); ?>"
			          name="<?php echo $this->get_field_name( 'show_copyright' ); ?>"/>
				<label
					for="<?php echo $this->get_field_id( 'show_copyright' ); ?>"><?php esc_html_e( 'Show Copyright Text', 'boombox' ); ?></label>
			</p>

		<?php
		}
	}
}

/**
 * Register Boombox Sidebar Footer Widget
 */
if( ! function_exists( 'boombox_load_sidebar_footer_widget' ) ) {
	
	function boombox_load_sidebar_footer_widget() {
		register_widget( 'Boombox_Widget_Sidebar_Footer' );
	}
	
	add_action( 'widgets_init', 'boombox_load_sidebar_footer_widget' );
 
}