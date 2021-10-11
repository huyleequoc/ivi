<?php
/**
 * Boombox theme status class
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.7.9
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

final class Boombox_Theme_Status {

	/**
	 * Holds class single instance
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * Get instance
	 * @return Boombox_Theme_Status|null
	 */
	public static function get_instance() {

		if ( null == static::$_instance ) {
			static::$_instance = new self();
		}

		return static::$_instance;

	}

	/**
	 * Holds admin page slug
	 * @var string
	 */
	private $slug = 'status-msg';

	/**
	 * Get admin page slug
	 * @return string
	 */
	public function get_admin_slug() {
		return $this->slug;
	}

	/**
	 * Boombox_Theme_Status constructor.
	 */
	private function __construct() {
		$this->hooks();
	}

	/**
	 * A dummy magic method to prevent Boombox_Theme_Status from being cloned.
	 */
	public function __clone() {
		throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
	}

	/**
	 * Setup Hooks
	 */
	public function hooks() {
		add_action( 'admin_menu', array( $this, 'register_status_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_init', array( $this, 'init' ) );
	}

	/**
	 * Callback function to initialize
	 */
	public function init() {
		add_action(
			'load-' . get_plugin_page_hook( $this->get_admin_slug(), 'admin.php' ),
			array( $this, 'process_request' )
		);
	}

	/**
	 *
	 */
	public function process_request() {
		$action_query_arg = 'bb_action';
		$key_query_arg = 'bb_key';

		$action = ( isset( $_REQUEST[ $action_query_arg ] ) && $_REQUEST[ $action_query_arg ] ) ? $_REQUEST[ $action_query_arg ] : '';

		if ( ! $action ) {
			return;
		}

		switch ( $action ) {
			case 'restart_prs':

				$status = array(
					'type'    => 'error',
					'message' => ''
				);
				$nonce  = isset( $_REQUEST[ $key_query_arg ] ) ? $_REQUEST[ $key_query_arg ] : '';
				if ( wp_verify_nonce( $nonce, 'bb-restart-prs' ) ) {
					$query_status = boombox_clear_rate_schedule();

					if ( $query_status === false ) {
						$status['message'] = __( 'Error processing the request. Please try again.', 'boombox' );
					} else {
						$status['type']    = 'success';
						$status['message'] = __( 'Post ranking system successfully restarted.', 'boombox' );
					}
				} else {
					$status['type']    = 'error';
					$status['message'] = __( 'Invalid request.', 'boombox' );
				}

				add_action( 'admin_notices', function () use ( $status ) {
					$class = 'notice notice-' . $status['type'] . ' is-dismissible';
					printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $status['message'] ) );
				} );

				break;
			case 'reset_views':

				$nonce = isset( $_REQUEST[ $key_query_arg ] ) ? $_REQUEST[ $key_query_arg ] : '';
				if ( wp_verify_nonce( $nonce, 'bb-reset-views' ) ) {
					$query_status = boombox_reset_views();

					if ( $query_status === false ) {
						$status['message'] = __( 'Error processing the request. Please try again.', 'boombox' );
					} else {
						$status['type']    = 'success';
						$status['message'] = __( 'Views ranking is reset successfully.', 'boombox' );
					}
				} else {
					$status['type']    = 'error';
					$status['message'] = __( 'Invalid request.', 'boombox' );
				}

				add_action( 'admin_notices', function () use ( $status ) {
					$class = 'notice notice-' . $status['type'] . ' is-dismissible';
					printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $status['message'] ) );
				} );

				break;
			case 'reset_upvotes':

				$nonce = isset( $_REQUEST[ $key_query_arg ] ) ? $_REQUEST[ $key_query_arg ] : '';
				if ( wp_verify_nonce( $nonce, 'bb-reset-upvotes' ) ) {
					$query_status = boombox_reset_points();

					if ( $query_status === false ) {
						$status['message'] = __( 'Error processing the request. Please try again.', 'boombox' );
					} else {
						$status['type']    = 'success';
						$status['message'] = __( 'Upvotes ranking is reset successfully.', 'boombox' );
					}
				} else {
					$status['type']    = 'error';
					$status['message'] = __( 'Invalid request.', 'boombox' );
				}

				add_action( 'admin_notices', function () use ( $status ) {
					$class = 'notice notice-' . $status['type'] . ' is-dismissible';
					printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $status['message'] ) );
				} );

				break;

			default:
				do_action( 'boombox/theme_status_handle_' . $action . '_request' );
		}

		return;
	}

	/**
	 * Register status page
	 */
	public function register_status_page() {
		add_theme_page(
			__( 'Boombox Status', 'boombox' ),
			__( 'Boombox Status', 'boombox' ),
			'manage_options',
			$this->get_admin_slug(),
			array( $this, 'render_status_page' )
		);
	}

	/**
	 * Enqueue assets
	 */
	public function enqueue_scripts() {
		$min = boombox_get_minified_asset_suffix();

		wp_enqueue_style(
			'boombox-admin-theme-status',
			BOOMBOX_ADMIN_URL . 'status/assets/css/style' . $min . '.css',
			array(),
			boombox_get_assets_version()
		);
	}

	/**
	 * Scan the template files.
	 *
	 * @param  string $template_path Path
	 *
	 * @return array
	 */
	private function scan_template_files( $template_path ) {
		$files = @scandir( $template_path );
		$result = array();

		if ( ! empty( $files ) ) {

			foreach ( $files as $key => $value ) {

				if ( ! in_array( $value, array( ".", ".." ) ) ) {

					if ( is_dir( $template_path . DIRECTORY_SEPARATOR . $value ) ) {
						$sub_files = $this->scan_template_files( $template_path . DIRECTORY_SEPARATOR . $value );
						foreach ( $sub_files as $sub_file ) {
							$result[] = $value . DIRECTORY_SEPARATOR . $sub_file;
						}
					} else {
						$result[] = $value;
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Retrieve metadata from a file. Based on WP Core's get_file_data function.
	 *
	 * @param  string $file Path to the file
	 *
	 * @return string
	 */
	private function get_file_version( $file ) {

		// Avoid notices if file does not exist
		if ( ! file_exists( $file ) ) {
			return '';
		}

		// We don't need to write to the file, so just open for reading.
		$fp = fopen( $file, 'r' );

		// Pull only the first 8kiB of the file in.
		$file_data = fread( $fp, 8192 );

		// PHP will close file handle, but we are good citizens.
		fclose( $fp );

		// Make sure we catch CR-only line endings.
		$file_data = str_replace( "\r", "\n", $file_data );
		$version = '';

		if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( '@version', '/' ) . '(.*)$/mi', $file_data, $match ) && $match[ 1 ] ) {
			$version = _cleanup_header_comment( $match[ 1 ] );
		}

		return $version;
	}

	/**
	 * Get theme information
	 * @return array
	 */
	private function get_theme_info() {
		$active_theme = wp_get_theme();
		$is_child_theme = is_child_theme();

		/***** Get parent theme info if this theme is a child theme, otherwise pass empty info in the response. */
		if ( $is_child_theme ) {
			$parent_theme = wp_get_theme( $active_theme->Template );
			$parent_theme_info = array(
				'parent_name'       => $parent_theme->Name,
				'parent_version'    => $parent_theme->Version,
				'parent_author_url' => $parent_theme->{'Author URI'},
			);
		} else {
			$parent_theme_info = array(
				'parent_name'       => '',
				'parent_version'    => '',
				'parent_author_url' => '',
			);
		}

		/***** Scan child theme directory for all templates to see if it overrides any of them. */
		$override_files = array();

		if ( $is_child_theme ) {
			$child_root = get_stylesheet_directory();

			$root_files = array(
				'404.php',
				'archive.php',
				'attachment.php',
				'author.php',
				'comments.php',
				'footer.php',
				'header.php',
				'index.php',
				'page.php',
				'page-no-sidebar.php',
				'page-trending-result.php',
				'page-with-left-sidebar.php',
				'search.php',
				'searchform.php',
				'single.php',
			);
			$scan_files = $this->scan_template_files( $child_root . '/template-parts/' );
			array_walk( $scan_files, function ( &$file ) {
				$file = 'template-parts/' . $file;
			} );

			$files = array_merge( $root_files, $scan_files );
			if ( ! empty( $files ) ) {

				$parent_root = get_template_directory();
				$ignore_list = array(
					'template-parts/footer/index.php',
					'template-parts/header/index.php',
					'template-parts/pagination/index.php',
					'template-parts/popups/index.php',
					'template-parts/index.php',
				);

				foreach ( $files as $file ) {

					$file = strtr( $file, array( DIRECTORY_SEPARATOR => '/' ) );
					if ( in_array( $file, $ignore_list ) ) {
						continue;
					}

					$child_file = strtr( $child_root . '/' . $file, array( DIRECTORY_SEPARATOR => '/' ) );
					if ( ! file_exists( $child_file ) ) {
						$child_file = false;
					}

					if ( $child_file ) {
						$parent_file = strtr( $parent_root . '/' . $file, array( DIRECTORY_SEPARATOR => '/' ) );

						$parent_version = $this->get_file_version( $parent_file );
						if ( ! $parent_version ) {
							$parent_version = '---';
						}
						$child_version = $this->get_file_version( $child_file );
						if ( ! $child_version ) {
							$child_version = '---';
						}

						$override_files[] = array(
							'file'           => strtr( $file, array( DIRECTORY_SEPARATOR => '/' ) ),
							'child_version'  => $child_version,
							'parent_version' => $parent_version,
						);
					}
				}
			}
		}

		$active_theme_info = array(
			'name'           => $active_theme->Name,
			'version'        => $active_theme->Version,
			'author_url'     => esc_url_raw( $active_theme->{'Author URI'} ),
			'is_child_theme' => $is_child_theme,
			'overrides'      => $override_files,
		);

		return array_merge( $active_theme_info, $parent_theme_info );
	}

	/**
	 * Render status page
	 */
	public function render_status_page() {
		$theme = $this->get_theme_info(); ?>
		<h2><?php _e( 'Boombox Status', 'boombox' ); ?></h2>
		<table class="bb_status_table widefat" cellspacing="0" id="templates" style="table-layout: fixed">
			<thead>
			<tr>
				<th colspan="3"><h2><?php _e( 'Theme', 'boombox' ); ?></h2></th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td><?php _e( 'Version', 'boombox' ); ?>:</td>
				<td class="help">&nbsp;</td>
				<td><?php echo $theme[ 'version' ]; ?></td>
			</tr>
			<tr>
				<td><?php _e( 'Author URL', 'boombox' ); ?>:</td>
				<td class="help">&nbsp;</td>
				<td><?php echo $theme[ 'author_url' ]; ?></td>
			</tr>
			<tr>
				<td><?php _e( 'Child theme', 'boombox' ); ?>:</td>
				<td class="help">&nbsp;</td>
				<td>
					<?php if ( $theme[ 'is_child_theme' ] ) { ?>
						<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
					<?php } else { ?>
						<mark class="no"><span class="dashicons dashicons-no-alt"></span></mark>
						<?php
						printf( __( 'See: <a href="%s" target="_blank">How to create a child theme</a>', 'boombox' ), 'https://codex.wordpress.org/Child_Themes' );
					}
					?>
				</td>
			</tr>
			<?php if ( $theme[ 'is_child_theme' ] ) { ?>
				<tr>
					<td><?php _e( 'Parent theme version', 'boombox' ); ?>:</td>
					<td class="help">&nbsp;</td>
					<td><?php echo $theme[ 'parent_version' ]; ?></td>
				</tr>
				<tr>
					<td><?php _e( 'Parent theme author URL', 'boombox' ); ?>:</td>
					<td class="help">&nbsp;</td>
					<td><?php echo $theme[ 'parent_author_url' ]; ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>

		<?php if ( $theme[ 'is_child_theme' ] ) { ?>
			<table class="bb_status_table widefat" cellspacing="0" id="template-overrides" style="table-layout: fixed">
				<thead>
				<tr>
					<th colspan="2"><h2><?php _e( 'Template Overrides', 'boombox' ); ?></h2></th>
					<th><h2><?php _e( 'Current Version', 'boombox' ); ?></h2></th>
					<th><h2><?php _e( 'Core Version', 'boombox' ); ?></h2></th>
				</tr>
				</thead>
				<tbody>
				<?php if ( ! empty( $theme[ 'overrides' ] ) ) {
					foreach ( $theme[ 'overrides' ] as $data ) { ?>
						<tr>
							<td><?php echo $data[ 'file' ]; ?></td>
							<td class="help">&nbsp;</td>

							<?php
							$child_status_class = 'danger';
							if ( ( $data[ 'parent_version' ] != '---' ) && ( $data[ 'child_version' ] == $data[ 'parent_version' ] ) ) {
								$child_status_class = 'ok';
							} ?>
							<td class="bb-version bb-version-<?php echo $child_status_class; ?>">
								<?php echo $data[ 'child_version' ] ? $data[ 'child_version' ] : '---'; ?>
							</td>
							<?php
							$parent_status_class = 'ok';
							if ( $data[ 'parent_version' ] == '---' ) {
								$parent_status_class = 'danger';
							} ?>
							<td class="bb-version bb-version-<?php echo $parent_status_class; ?>">
								<?php echo $data[ 'parent_version' ]; ?>
							</td>
						</tr>
					<?php } ?>
				<?php } else { ?>
					<tr>
						<td colspan="4"><?php _e( 'There are no overwrite templates.', 'boombox' ); ?></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
			<?php
		}

		if ( boombox_module_management_service()->is_module_active( 'prs' ) ) { ?>
			<table class="bb_status_table widefat" cellspacing="0" id="prs" style="table-layout: fixed">
				<thead>
				<tr>
					<th colspan="3"><h2><?php esc_html_e( 'Post Ranking System', 'boombox' ); ?></h2></th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td colspan="2">
						<p><strong><?php esc_html_e( 'Regenerate posts ranked lists (trending, hot, popular).', 'boombox' ); ?></strong></p>
					</td>
					<td>
						<?php
						$restart_prs_url = add_query_arg( array(
							'page'      => $this->get_admin_slug(),
							'bb_action' => 'restart_prs',
							'bb_key'    => wp_create_nonce( 'bb-restart-prs' ),
						), admin_url( 'themes.php' ) ); ?>
						<a href="<?php echo esc_url( $restart_prs_url ); ?>"
						   class="button-primary"><?php esc_html_e( 'Regenerate', 'boombox' ); ?></a>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<p>
							<strong><?php esc_html_e( "Reset will affect current ranked lists (trending, hot, popular). After reset you need to regenerate the lists, or it will automatically regenerate within 24 hours. Posts total views and upvotes won't be affected.", 'boombox' ) ?></strong>
						</p>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<p><?php esc_html_e( 'Reset views', 'boombox' ); ?></p>
					</td>
					<td>
						<?php
						$reset_views_url = add_query_arg( array(
							'page'      => $this->get_admin_slug(),
							'bb_action' => 'reset_views',
							'bb_key'    => wp_create_nonce( 'bb-reset-views' ),
						), admin_url( 'themes.php' ) ); ?>
						<a href="<?php echo esc_url( $reset_views_url ); ?>"
						   class="button-primary"><?php esc_html_e( 'Reset', 'boombox' ); ?></a>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<p><?php esc_html_e( 'Reset upvotes', 'boombox' ); ?></p>
					</td>
					<td>
						<?php
						$reset_upvotes_url = add_query_arg( array(
							'page'      => $this->get_admin_slug(),
							'bb_action' => 'reset_upvotes',
							'bb_key'    => wp_create_nonce( 'bb-reset-upvotes' ),
						), admin_url( 'themes.php' ) ); ?>
						<a href="<?php echo esc_url( $reset_upvotes_url ); ?>"
						   class="button-primary"><?php esc_html_e( 'Reset', 'boombox' ); ?></a>
					</td>
				</tr>
				</tbody>
			</table>
		<?php }

		do_action( 'boombox/theme_status_sections' );

	}

}