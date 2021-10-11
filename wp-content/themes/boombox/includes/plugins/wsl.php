<?php
/**
 * WSL plugin functions
 *
 * @package BoomBox_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( ! boombox_plugin_management_service()->is_plugin_active( 'wordpress-social-login/wp-social-login.php' ) ) {
	return;
}

if( ! class_exists( 'Boombox_WSL' ) ) {

    final class Boombox_WSL {

	    /**
	     * Holds class single instance
	     * @var null
	     */
	    private static $_instance = null;

	    /**
	     * Get instance
	     * @return Boombox_WSL|null
	     */
	    public static function get_instance() {

		    if (null == static::$_instance) {
			    static::$_instance = new self();
		    }

		    return static::$_instance;

	    }

	    /**
	     * Boombox_WSL constructor.
	     */
	    private function __construct() {
            $this->hooks();

		    do_action( 'boombox/wsl/wakeup', $this );
        }

	    /**
	     * A dummy magic method to prevent Boombox_WSL from being cloned.
	     *
	     */
	    public function __clone() {
		    throw new Exception('Cloning ' . __CLASS__ . ' is forbidden');
	    }

        /**
         * Setup Hooks
         */
        private function hooks() {
            add_filter( 'wsl_component_loginwidget_setup_alter_icon_sets', array( $this, 'add_icon_sets' ), 10, 1 );
            add_filter( 'wsl_render_auth_widget_alter_assets_base_url', array( $this, 'check_icon_sets_base_url' ), 10, 1 );
	        add_filter( 'wsl_hook_alter_provider_scope', array( $this, 'change_default_permissons' ), 10, 2 );

            if( !is_admin() ) {
                add_filter('wsl_render_auth_widget_alter_provider_icon_markup', array( $this, 'button_markup' ), 10, 3);
            }
        }

	    /**
	     * Add boombox social icon sets to wsl
	     *
	     * @param array $icon_sets Current icons sets
	     * @return array
	     */
        public function add_icon_sets( $icon_sets ) {

            $icon_sets[ 'boombox' ] = esc_html__( 'Boombox social icons', 'boombox' );

            return $icon_sets;
        }

	    /**
	     * Modify social icon sets url
	     *
	     * @param string $assets_base_url Current base URL
	     * @return string
	     */
        public function check_icon_sets_base_url( $assets_base_url ) {
            $social_icon_set = get_option( 'wsl_settings_social_icon_set' );

            if( 'boombox' == $social_icon_set ) {
                $assets_base_url = BOOMBOX_THEME_URL . 'images/social-icons/';
            }

            return $assets_base_url;
        }

	    /**
	     * Edit social icons markup
	     *
	     * @param string $provider_id Provider ID
	     * @param string $provider_name Provider name
	     * @param string $authenticate_url Authentication URL
	     * @return string
	     */
        public function button_markup($provider_id, $provider_name, $authenticate_url) {
            $icon_id = strtolower( $provider_id );
            $icons_rewrite_map = array(
                'vkontakte'        => 'vk',
                'stackoverflow'    => 'stack-overflow',
                'twitchtv'          => 'twitch',
                'mailru'            => 'at',
                'google'            => 'google-plus'
            );

            $icon_name = isset( $icons_rewrite_map[ $icon_id ] ) ? $icons_rewrite_map[ $icon_id ] : $icon_id;

            return sprintf(
                '<a rel="nofollow" href="%4$s" data-provider="%1$s" class="button _%2$s wp-social-login-provider wp-social-login-provider-%2$s">
                    <i class="bb-icon bb-ui-icon-%5$s"></i> %3$s
                </a>',
                $provider_id,
                $icon_id,
                apply_filters( 'boombox/wsl/button-text', $provider_name, $provider_id ),
                $authenticate_url,
                $icon_name
            );
        }

	    /**
	     * Edit default permissions by provider type
	     * @param string $provider_scope Current scope
	     * @param string $provider Provider ID
	     *
	     * @return string
	     * @since 2.5.7
	     * @version 2.5.7
	     */
	    public function change_default_permissons( $provider_scope, $provider ) {
		    if( 'facebook' == strtolower( $provider ) ) {
			    $provider_scope = 'email, public_profile';
		    }

		    return $provider_scope;
	    }

    }

	Boombox_WSL::get_instance();

}