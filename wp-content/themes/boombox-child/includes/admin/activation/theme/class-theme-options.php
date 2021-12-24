<?php
/**
 * Boombox admin functions
 *
 * @package BoomBox_Theme
 * @since   .0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

final class Boombox_Theme_Options {

	/**
	 * @var Boombox_Theme_Options
	 */
	private static $instance;

	/**
	 * Get instance
	 * @return Boombox_Theme_Options
	 */
	public static function get_instance() {
		if ( ! static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * The admin page name
	 * @var string
	 */
	private $page_name = '';

	/**
	 * Get page name
	 * @return string
	 */
	public function get_page_name() {
		return $this->page_name;
	}

	/**
	 * The admin menu name
	 * @var string
	 */
	private $menu_name = '';

	/**
	 * Get admin menu name
	 * @return string
	 */
	public function get_menu_name() {
		return $this->menu_name;
	}

	/**
	 * The admin menu slug
	 * @var string
	 */
	private $menu_slug = '';

	/**
	 * Get admin menu slug
	 * @return string
	 */
	public function get_menu_slug() {
		return $this->menu_slug;
	}

	/**
	 * Holds theme options page tabs
	 * slug => name
	 *
	 * @var array<string, string>
	 */
	private $tabs = array();

	/**
	 * @return string
	 */
	private function get_current_tab_slug() {
		$slugs = array_keys( $this->get_tabs() );
		if ( ! empty( $_GET[ 'tab' ] ) ) {
			$tab = $_GET[ 'tab' ];
			if ( in_array( $tab, $slugs ) ) {
				return $tab;
			}
		}

		return $slugs[ 0 ];
	}

	/**
	 * @return array<string, string>
	 */
	private function get_tabs() {
		return $this->tabs;
	}

	/**
	 * @var string
	 */
	private $option_id = 'boombox_theme_activation';

	/**
	 * @return string
	 */
	public function get_option_id() {
		return $this->option_id;
	}

	/**
	 * Boombox_Theme_Options constructor.
	 */
	private function __construct() {
		$this->tabs = array(
			'activation' => __( 'Activation', 'boombox' ),
		);
		$this->page_name = __( 'Boombox Activation', 'boombox' );
		$this->menu_name = __( 'Boombox Activation', 'boombox' );
		$this->menu_slug = 'boombox-activation';

		$this->hooks();
	}

	/**
	 * A dummy magic method to prevent Boombox_Theme_Options from being cloned.
	 */
	private function __clone() {}

	/**
	 * Setup Hooks
	 */
	private function hooks() {
		add_action( 'admin_menu', array( $this, 'add_theme_options_page' ) );
		add_action( 'admin_init', array( $this, 'setup_theme_options' ) );
		add_action( 'admin_notices', array( $this, 'setup_admin_notices' ) );
		add_action( 'admin_init', array( $this, 'maybe_deactivate_plugins' ) );

		$network_prefix = ( defined( 'WP_NETWORK_ADMIN' ) && WP_NETWORK_ADMIN ) ? 'network_admin_' : '';

		add_filter( 'tgmpa_' . $network_prefix . 'plugin_action_links', array( $this, 'edit_tgma_plugin_action_links' ), 10, 4 );
		add_filter( 'tgmpa_table_data_item', array( $this, 'edit_tgma_table_item_data' ), 10, 2 );
	}

	/**
	 * Register theme options page
	 */
	public function add_theme_options_page() {
		add_theme_page(
			esc_html__( $this->get_page_name(), 'boombox' ),
			esc_html__( $this->get_menu_name(), 'boombox' ),
			'manage_options',
			$this->get_menu_slug(),
			array(                              // Callback, to output the content for this page.
				$this,
				'render_theme_options_page_content',
			)
		);
	}

	/**
	 * Setup theme options
	 */
	public function setup_theme_options() {
		register_setting(
			$this->get_option_id(),
			$this->get_option_id()
		);
		Boombox_Theme_Options_Registration::get_instance( $this->get_menu_slug() );
	}

	/**
	 * Callback to setup theme activation admin error message
	 */
	public function setup_admin_notices() {
		if ( ! boombox_is_registered() ) {
			$class = 'notice notice-error';
			$activation_page_url = add_query_arg( array( 'page' => $this->get_menu_slug() ), admin_url( 'themes.php' ) );
			$message = sprintf( __( '<strong>Please activate your copy of Boombox. <a href="%s">Activate Now</a></strong>', 'boombox'
			), $activation_page_url );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
		}
	}

	/**
	 * Force deactivate plugins if theme is not registered
	 */
	public function maybe_deactivate_plugins() {

		if( ! boombox_is_registered() ) {
			$root = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR;

			$plugins = array(
				/***** "BoomBox Theme Extensions" */
				$root . 'boombox-theme-extensions/boombox-theme-extensions.php',
				/***** "Easy Social Share Buttons" */
				$root . 'easy-social-share-buttons3/easy-social-share-buttons3.php',
				/***** "AdSense Integration WP QUADS PRO" */
				$root . 'wp-quads-pro/wp-quads-pro.php',
				/***** "Zombify" */
				$root . 'zombify/zombify.php',
				/***** "Gamify" */
				$root . 'gamify/gamify.php',
				/***** "WPBakery Page Builder" */
				$root . 'js_composer/js_composer.php'
			);

			deactivate_plugins( $plugins );
		}
	}

	/**
	 * Edit plugins actions links in "tgma" plugin activation screen
	 *
	 * @param array $action_links Current action links
	 * @param string $slug Plugin slug
	 * @param array $item Plugin data
	 * @param string $context View context.
	 *                        One of: 'all', 'install', 'update', 'activate'
	 *
	 * @return array
	 */
	public function edit_tgma_plugin_action_links( $action_links, $slug, $item, $context ) {
		if( ! boombox_is_registered() ) {

			$protected_plugins = wp_list_pluck( Boombox_Plugin_Activation::get_pro_plugins(), 'name', 'slug' );

			if( array_key_exists( $slug, $protected_plugins ) ) {

				$action_links = array(
					'theme_activation' => sprintf( '<a href="%s" style="color:#a00;">%s</a>',
						add_query_arg( array( 'page' => $this->get_menu_slug() ), admin_url( 'themes.php' ) ),
						__( 'Please register your copy of "Boombox" to unlock', 'boombox' )
					)
				);

			}

		}
		return $action_links;
	}

	/**
	 * Edit tgma plugins list table plugin row
	 * @param string $item Current row item data
	 * @param string $plugin Current plugin data
	 *
	 * @return mixed
	 */
	public function edit_tgma_table_item_data( $item, $plugin ) {
		if( ! boombox_is_registered() ) {
			$item[ 'source' ] = '---';
		}

		return $item;
	}

	/**
	 * Render theme options page content
	 */
	public function render_theme_options_page_content() {
		$current_tab = $this->get_current_tab_slug(); ?>

		<div class="wrap">
			<div id="icon-themes" class="icon32"></div>
			<h2 class="nav-tab-wrapper">
				<?php foreach ( $this->get_tabs() as $tab_slug => $tab_name ): ?>
					<a href="?page=theme-options&tab=<?php echo $tab_slug; ?>"
					   class="nav-tab <?php echo $current_tab == $tab_slug ? 'nav-tab-active' : ''; ?>">
						<?php echo $tab_name; ?>
					</a>
				<?php endforeach; ?>
			</h2>
			<form action="options.php" method="post">
				<?php settings_fields( $this->get_option_id() );
				$this->render_settings_sections( $this->get_menu_slug() );
				if( ! boombox_is_registered() ) {
					submit_button();
				} ?>
			</form>
		</div>

		<?php
	}

	/**
	 * Render settings
	 *
	 * @param string $page Settings page.
	 */
	public function render_settings_sections( $page ) {
		global $wp_settings_sections, $wp_settings_fields;

		if ( ! isset( $wp_settings_sections[ $page ] ) ) {
			return;
		}

		foreach ( (array)$wp_settings_sections[ $page ] as $section ) {
			// Skip if there are no fields.
			if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section[ 'id' ] ] ) ) {
				continue;
			}
			?>
			<div><?php do_settings_fields( $page, $section[ 'id' ] ); ?></div>
			<?php
		}
	}

}
