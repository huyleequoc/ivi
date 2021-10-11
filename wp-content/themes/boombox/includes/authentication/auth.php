<?php
/**
 * Theme authentication
 *
 * @package BoomBox_Theme
 * @since 1.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}


remove_all_actions( 'authenticate', 101 );

if ( boombox_is_auth_allowed() ) {

	add_action( 'boombox_before_login_form', 'boombox_social_login_form' );
	add_action( 'boombox_before_register_form', 'boombox_social_login_form' );

	function boombox_social_login_form( $auth_type ) {
		$social_auth = boombox_get_theme_option( 'extra_authentication_enable_social_auth' );
		if ( $social_auth ) {
			$enabled_facebook_auth = boombox_facebook_auth_enabled();
			$enabled_google_auth = boombox_google_auth_enabled();
			if ( $enabled_facebook_auth || $enabled_google_auth ) {
				if ( $enabled_facebook_auth ) { ?>
					<a class="button _facebook facebook-login-button-js" href="#">
						<i class="bb-icon bb-ui-icon-facebook"></i> <?php esc_html_e( 'Facebook', 'boombox' ); ?>
					</a>
					<?php
				}
				if ( $enabled_google_auth ) { ?>
					<a class="button _google google-login-button-js" href="#">
						<i class="bb-icon bb-ui-icon-google-plus"></i> <?php esc_html_e( 'Google', 'boombox' ); ?>
					</a>
					<?php
				} ?>

				<div class="_or"><?php esc_html_e( 'or', 'boombox' ); ?></div>
				<?php
			}
		}
	}

	/**
	 * Get auth loading message
	 *
	 * @param $type
	 * @return string
	 * @since 1.0.0
	 * @version 2.0.0
	 */
	function boombox_get_auth_loading_message( $type ) {
		return apply_filters( 'boombox/auth/' . $type . '/loading_message', esc_html__( 'Sending user info, please wait...', 'boombox' ) );
	}

	/**
	 * Get auth form invalid message
	 *
	 * @param $type
	 * @return string
	 * @since 1.0.0
	 * @version 2.0.0
	 */
	function boombox_get_auth_invalid_message( $type ) {
		return apply_filters( 'boombox/auth/' . $type . '/invalid_message', esc_html__( 'Please fix all invalid fields.', 'boombox' ) );
	}

	/**
	 * Require global authentication
	 */
	require_once( BOOMBOX_INCLUDES_PATH . 'authentication/default/default-auth.php' );

	/**
	 * Require social authentication
	 */
	if ( boombox_get_theme_option( 'extra_authentication_enable_social_auth' ) ) {
		require_once( BOOMBOX_INCLUDES_PATH . 'authentication/social/social-auth.php' );
	}

}