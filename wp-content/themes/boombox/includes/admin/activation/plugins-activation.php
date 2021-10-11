<?php
/**
 * Register the required plugins for BoomBox theme.
 * @since 1.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Class Boombox_Plugin_Activation
 * @since 2.1.4
 */
class Boombox_Plugin_Activation {

	/**
	 * Get pro plugins
	 * @return array
	 * @since 2.1.4
	 * @version 2.1.4
	 */
	public static function get_pro_plugins(){
		return array(
			// AdSense Integration WP QUADS PRO
			array(
				'name'               => esc_html__( 'AdSense Integration WP QUADS PRO', 'boombox' ),
				'slug'               => 'wp-quads-pro',
				'source'             => BOOMBOX_ADMIN_PATH . 'activation/plugins/wp-quads-pro.zip',
				'required'           => false,
				'version'            => '1.4.2',
				'force_activation'   => false,
				'force_deactivation' => false,
				'external_url'       => '',
				'is_callable'        => ''
			),
			// BoomBox Theme Extensions
			array(
				'name'               => esc_html__( 'BoomBox Theme Extensions', 'boombox' ),
				'slug'               => 'boombox-theme-extensions',
				'source'             => BOOMBOX_ADMIN_PATH . 'activation/plugins/boombox-theme-extensions.zip',   // The plugin source.
				'required'           => true,
				'version'            => '1.6.8', // bbte-version: 1.6.8
				'force_activation'   => false,
				'force_deactivation' => false,
				'external_url'       => '',
				'is_callable'        => ''
			),
			// Easy Social Share Buttons
			array(
				'name'               => esc_html__( 'Easy Social Share Buttons', 'boombox' ),
				'slug'               => 'easy-social-share-buttons3',
				'source'             => BOOMBOX_ADMIN_PATH . 'activation/plugins/easy-social-share-buttons3.zip',
				'required'           => false,
				'version'            => '7.9.6',
				'force_activation'   => false,
				'force_deactivation' => false,
				'external_url'       => '',
				'is_callable'        => ''
			),
			// Gamify
			array(
				'name'               => esc_html__( 'Gamify', 'boombox' ),
				'slug'               => 'gamify',
				'source'             => BOOMBOX_ADMIN_PATH . 'activation/plugins/gamify.zip',
				'required'           => false,
				'version'            => '1.3.1', // gfy-version: 1.3.1
				'force_activation'   => false,
				'force_deactivation' => false,
				'external_url'       => '',
				'is_callable'        => ''
			),
			// Zombify
			array(
				'name'               => esc_html__( 'Zombify', 'boombox' ),
				'slug'               => 'zombify',
				'source'             => BOOMBOX_ADMIN_PATH . 'activation/plugins/zombify.zip',
				'required'           => false,
				'version'            => '1.6.5', // zf-version: 1.6.5
				'force_activation'   => false,
				'force_deactivation' => false,
				'external_url'       => '',
				'is_callable'        => ''
			),
			// WPBakery Page Builder
			array(
				'name'               => esc_html__( 'WPBakery Page Builder', 'boombox' ),
				'slug'               => 'js_composer',
				'source'             => BOOMBOX_ADMIN_PATH . 'activation/plugins/js_composer.zip',
				'required'           => false,
				'version'            => '6.7.0',
				'force_activation'   => false,
				'force_deactivation' => false,
				'external_url'       => '',
				'is_callable'        => ''
			),
		);
	}

	/**
	 * Get free plugins
	 * @return array
	 * @since 2.1.4
	 * @version 2.1.4
	 */
	public static function get_free_plugins() {
		return array(
			// Envato Market
			array(
				'name'               => esc_html__( 'Envato Market', 'boombox' ),
				'slug'               => 'envato-market',
				'source'             => BOOMBOX_ADMIN_PATH . 'activation/plugins/envato-market.zip',
				'required'           => true,
				'version'            => '2.0.5',
				'force_activation'   => false,
				'force_deactivation' => false,
				'external_url'       => '',
			),
			// AdSense Integration WP QUADS
			array(
				'name'     => esc_html__( 'AdSense Integration WP QUADS', 'boombox' ),
				'slug'     => 'quick-adsense-reloaded',
				'required' => false
			),
			// BuddyPress
			array(
				'name'     => esc_html__( 'BuddyPress', 'boombox' ),
				'slug'     => 'buddypress',
				'required' => false
			),
			// MailChimp for WordPress
			array(
				'name'     => esc_html__( 'MailChimp for WordPress', 'boombox' ),
				'slug'     => 'mailchimp-for-wp',
				'required' => false
			),
			// One Click Demo Import
			array(
				'name'     => esc_html__( 'One Click Demo Import', 'boombox' ),
				'slug'     => 'one-click-demo-import',
				'required' => false
			),
			// Wordpress Social Login
			array(
				'name'     => esc_html__( 'Wordpress Social Login', 'boombox' ),
				'slug'     => 'wordpress-social-login',
				'required' => false
			),
			// Facebook Comments
			array(
				'name'               => esc_html__( 'Facebook Comments', 'boombox' ),
				'slug'               => 'facebook-comments-plugin',
				'source'             => BOOMBOX_ADMIN_PATH . 'activation/plugins/facebook-comments-plugin.zip',
				'required'           => false,
				'version'            => '2.3.7',
				'force_activation'   => false,
				'force_deactivation' => false,
				'external_url'       => '',
			),
		);
	}

	/**
	 * Get all plugins
	 * @return array
	 * @since 2.1.4
	 * @version 2.1.4
	 */
	public static function get_plugins(){
		return array_merge( static::get_pro_plugins(), static::get_free_plugins() );
	}

	/**
	 * Register plugins
	 * @since 2.1.4
	 * @version 2.1.4
	 */
	public static function register_plugins() {
		if( 'tgmpa_register' != current_action() ) {
			return;
		}

		$config = array(
			'id'           => 'boombox',                    // Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '',                           // Default absolute path to bundled plugins.
			'menu'         => 'tgmpa-install-plugins',      // Menu slug.
			'has_notices'  => true,                         // Show admin notices or not.
			'dismissable'  => true,                        // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                           // If 'dismissible' is false, this message will be output at top of nag.
			'is_automatic' => false,                        // Automatically activate plugins after installation or not.
			'message'      => '',                           // Message to output right before the plugins table.
		);

		tgmpa( static::get_plugins(), $config );
	}

}

add_action( 'tgmpa_register', array( 'Boombox_Plugin_Activation', 'register_plugins' ) );