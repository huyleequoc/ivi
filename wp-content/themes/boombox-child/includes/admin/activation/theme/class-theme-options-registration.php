<?php
/**
 * Boombox admin functions
 *
 * @package BoomBox_Theme
 * @since .0.0
 * @version 2.0.0
 */

final class Boombox_Theme_Options_Registration {
	/**
	 * @var Boombox_Theme_Options_Registration
	 */
	private static $instance;

	/**
	 * @param string $page_slug admin page slug
	 *
	 * @return Boombox_Theme_Options_Registration
	 */
	public static function get_instance( $page_slug ) {
		if ( ! static::$instance ) {
			static::$instance = new static( $page_slug );
		}

		return static::$instance;
	}

	/**
	 * @var string $page_slug Admin page slug
	 */
	private $page_slug;

	/**
	 * Get admin page slug
	 * @return string
	 */
	private function get_page_slug() {
		return $this->page_slug;
	}

	/**
	 * @var string $section_id Current section ID
	 */
	private $section_id = 'registration';

	/**
	 * Boombox_Theme_Options_Registration constructor.
	 *
	 * @param $page_slug
	 */
	private function __construct( $page_slug ) {
		$this->page_slug = $page_slug;
		$this->register_section_and_fields();
	}

	/**
	 * A dummy magic method to prevent Boombox_Theme_Options_Registration from being cloned.
	 *
	 */
	private function __clone() {
	}

	/**
	 * Register options sections and fields
	 */
	private function register_section_and_fields() {
		add_settings_section(
			$this->section_id,                        // ID used to identify this section and with which to register options.
			'',        // Title to be displayed on the administration page.
			NULL,
			$this->get_page_slug()                   // Page on which to add this section of options.
		);

		add_settings_field(
			'boombox_theme_registration',
			'',
			array( $this, 'render_theme_registration_form' ),
			$this->get_page_slug(),
			$this->section_id
		);
	}

	/**
	 * Render theme registration admin page
	 */
	public function render_theme_registration_form() {
		if ( boombox_is_registered() ) {
			printf( '<h1>%s</h1>', __( 'Boombox is registered !!!', 'boombox' ) );

			return;
		}

		/**
		 * @var TGM_Plugin_Activation $tgmpa
		 */
		global $tgmpa;

		$action = '';
		$install_envato_market_plugin = '';

		if ( ! $tgmpa->is_plugin_active( 'envato-market' ) ) {
			if ( ! $tgmpa->is_plugin_installed( 'envato-market' ) ) {
				$action = 'install';
			} else if ( $tgmpa->can_plugin_activate( 'envato-market' ) ) {
				$action = 'activate';
			}

			$install_envato_market_plugin =
				wp_nonce_url(
					add_query_arg(
						array(
							'plugin'           => urlencode( 'envato-market' ),
							'tgmpa-' . $action => $action . '-plugin',
						),
						$tgmpa->get_tgmpa_url()
					),
					'tgmpa-' . $action,
					'tgmpa-nonce'
				);
		}
		?>
		<div class="about-wrap">
			<h1><?php esc_html_e( 'Activate your Boombox Copy', 'boombox' ); ?></h1>
			<br/>

			<p>
				<?php esc_html_e( 'Welcome and thank you for Choosing Boombox Theme! The Boombox theme need to be activated to enable all demos, features and auto updates. Please follow activation steps exactly.', 'boombox' ); ?>
			</p>

			<h3><?php esc_html_e( 'Activation process', 'boombox' ); ?>:</h3>

			<ol>
				<?php if ( $install_envato_market_plugin ) { ?>
				<li>
					<span class="envato-market-not-activated">
					<?php if ( 'install' === $action ) { ?>
						<?php printf( __( '<a href="%s" class="g1-install-envato-market">Install the Envato Market</a> plugin.', 'boombox' ), $install_envato_market_plugin ); ?>
					<?php } else { ?>
						<?php printf( __( '<a href="%s" class="g1-install-envato-market">Activate the Envato Market</a> plugin.', 'boombox' ), $install_envato_market_plugin ); ?>
					<?php } ?>
					</span>
							<span class="envato-market-installing" style="display: none;">
						<?php esc_html_e( 'Installing the Envato Market plugin...', 'boombox' ); ?>
					</span>
							<span class="envato-market-activated" style="display: none;">
						<?php esc_html_e( 'The Envato Market plugin ready to use. Go to next step.', 'boombox' ); ?>
					</span>
							<span class="envato-market-installation-failed" style="display: none;">
						<?php esc_html_e( 'The Envato Market plugin installation failed.', 'boombox' ); ?>
						<?php printf( __( '<a href="%s" target="_blank">See details</a>.', 'boombox' ), $install_envato_market_plugin ) ?>
					</span>
				</li>
				<?php } ?>
				<li>
					<?php echo sprintf( __( 'Create Envato token <a href="%s" target="_blank">here</a>. You should be logged into Envato account where Boombox theme was purchased.', 'boombox' ), esc_url( 'https://build.envato.com/create-token/?purchase:download=t&purchase:verify=t&purchase:list=t' ) ); ?>
				</li>
				<li>
					<?php esc_html_e( 'Enter a token name, leave default permissions as it is, check "I have read, understood and agree to the  Terms and Conditions" and then click the "Create Token" button.', 'boombox' ); ?>
				</li>
				<li>
					<?php echo sprintf( __( 'Insert the token in the <a href="%s" target="_blank">Envato Market->Settings</a> under "Global OAuth Personal Token" and click on "Save Changes" button. Please note that "Envato Market" plugin must be activated first.', 'boombox' ), esc_url( admin_url( '/admin.php?page=envato-market' ) ) ); ?>
				</li>
				<li>
					<?php esc_html_e( 'After successful token verification Boombox theme will be activated and all the features will be unlocked, you can return to this page and reload it to check activation status. If activation failed, please make sure that you followed the steps above correctly.', 'boombox' ); ?>
				</li>
			</ol>
		</div>
		<?php
	}
}