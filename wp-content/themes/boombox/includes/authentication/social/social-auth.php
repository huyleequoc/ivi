<?php
/**
 * Boombox social authentication
 *
 * @package BoomBox_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Enqueue Social Authentication scripts
 * @since 1.0.0
 * @version 2.0.0
 */
function boombox_social_auth_scripts() {
	$min = boombox_get_minified_asset_suffix();
	global $wp;

	$set = boombox_get_theme_options_set( array(
		'extra_authentication_facebook_app_id',
		'extra_authentication_google_oauth_id',
		'extra_authentication_google_api_key'
	) );
	/**
	 * Facebook SDK
	 */
	if ( $set['extra_authentication_facebook_app_id'] ) {
		wp_enqueue_script(
			'facebook-jssdk',
			'https://connect.facebook.net/' . get_locale() . '/sdk.js',
			array(),
			boombox_get_assets_version(),
			true
		);
		wp_add_inline_script(
			'facebook-jssdk',
			'
			var boombox_fb_app_validity = false;

			window.fbAsyncInit = function () {
				FB.init({
					appId: "' . esc_html( $set['extra_authentication_facebook_app_id'] ) . '",
					xfbml: true,
					version: "v2.8"
				});
				FB.getLoginStatus(function(response) {
					boombox_fb_app_validity = true;
				})
			};
			',
			'before'
		);
		wp_add_inline_script( 'jquery',
			"if( !jQuery( 'body' ).find( '#fb-root' ) ){
				jQuery( 'body' ).append( '<div id=\"fb-root\"></div>' );
			};"
		);
	}
	/**
	 * Google SDK
	 */
	$enabled_google_auth = boombox_google_auth_enabled();
	if ( $enabled_google_auth ) {
		wp_enqueue_script(
			'boombox-google-platform',
			'//apis.google.com/js/platform.js',
			array(),
			boombox_get_assets_version(),
			true
		);
		wp_enqueue_script(
			'boombox-google-client',
			'https://apis.google.com/js/client.js?onload=GoggleOnLoad',
			array(),
			boombox_get_assets_version(),
			true
		);
	}

	if ( ! wp_script_is( 'boombox-validate-scripts', 'enqueued' ) ) {
		wp_enqueue_script(
			'boombox-validate-scripts',
			BOOMBOX_INCLUDES_URL . 'authentication/assets/js/jquery.validate' . $min . '.js',
			array( 'jquery' ),
			boombox_get_assets_version(),
			true
		);
	}

	wp_enqueue_script(
		'boombox-social-auth-scripts',
		BOOMBOX_INCLUDES_URL . 'authentication/social/js/social-auth-scripts' . $min . '.js',
		array( 'jquery' ),
		boombox_get_assets_version(),
		true
	);

	$current_url = esc_url( home_url( add_query_arg( array(), $wp->request ) ) );
	$ajax_auth_object = array(
		'url'                   => BOOMBOX_THEME_URL,
		'ajaxurl'               => admin_url( 'admin-ajax.php' ),
		'login_redirect_url'    => apply_filters( 'boombox_auth_login_redirect_url', $current_url ),
		'register_redirect_url' => apply_filters( 'boombox_auth_register_redirect_url', $current_url, 'social' ),
		'loading_message'       => boombox_get_auth_loading_message( 'social' ),
		'login_success_wait'    => esc_html__( 'Please wait...', 'boombox' ),
		'login_failed'          => esc_html__( 'Login failed', 'boombox' ),
		'google_oauth_id'       => $set['extra_authentication_google_oauth_id'],
		'google_api_key'        => $set['extra_authentication_google_api_key'],
		'nonce'                 => wp_create_nonce( 'social_ajax_nonce' )
	);

	wp_localize_script( 'boombox-social-auth-scripts', 'ajax_social_auth_object', $ajax_auth_object );
}
add_action( 'wp_enqueue_scripts', 'boombox_social_auth_scripts' );
add_action( 'wp_ajax_nopriv_boombox_social_auth', 'boombox_social_auth' );

/**
 * Ajax Social Authentication
 */
function boombox_social_auth() {
	check_ajax_referer( 'social_ajax_nonce', '_nonce' );

	$data     = array();
	$response = '';
	$action   = 'login';
	$status   = false;

	$social_type  = $_POST['social_type'];
	$access_token = $_POST['access_token'];
	$redirect_url = $_POST['redirect_url'];
	if ( 'facebook' === $social_type ) {
		$data                  = wp_remote_get( 'https://graph.facebook.com/me?fields=' . urlencode( 'first_name,last_name,email,picture.width(100).height(100),cover' ) . '&access_token=' . $access_token );
		$first_name_field_name = 'first_name';
		$last_name_field_name  = 'last_name';
	} else if ( 'google' === $social_type ) {
		$data                  = wp_remote_get( 'https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token=' . $access_token );
		$first_name_field_name = 'given_name';
		$last_name_field_name  = 'family_name';
	}

	if ( ! empty( $data ) ) {

		if ( $data['response']['code'] == 200 ) {
			$data = json_decode( $data['body'], true );
			if ( empty( $data['id'] ) || empty( $data['email'] ) ) {
				$response = esc_html__( 'Email missing', 'boombox' );
			} else {

				$id         = $data['id'];
				$email      = $data['email'];
				$first_name = $data[ $first_name_field_name ];
				$last_name  = $data[ $last_name_field_name ];
				$picture    = '';

				$user = get_user_by( 'email', $email );
				if ( empty( $user ) ) {
					$user = get_users( array( 'meta_key' => "boombox_{$social_type}_id", 'meta_value' => $id, 'number' => 1 ) );
					$user = end( $user );
				}

				if ( empty( $user ) ) {
					$action = 'register';
					if ( get_option( 'users_can_register' ) ) {
						//create new user
						$user_id = boombox_create_user( '', $email, '', "boombox_{$social_type}_id", $id );
						if ( $user_id === false || ! is_numeric( $user_id ) ) {
							$response = $user_id;
						} else {
							$user = get_user_by( 'id', $user_id );
						}
					} else {
						$response = esc_html__( 'Registering new users is currently not allowed.', 'boombox' );
					}
				}

				if ( ! empty( $user ) ) {
					wp_set_current_user( $user->ID );
					wp_set_auth_cookie( $user->ID );
					do_action( 'wp_login', $user->user_login, $user );
					if ( ! empty( $first_name ) && ! empty( $last_name ) ) {
						wp_update_user( array(
							'ID'         => $user->ID,
							'first_name' => $first_name,
							'last_name'  => $last_name
						) );
					}
					if ( ! empty( $data['picture'] ) ) {
						if ( 'facebook' === $social_type && $data['picture']['data']['is_silhouette'] == false ) {
							$picture = @$data['picture']['data']['url'];
						} else if ( 'google' === $social_type ) {
							$picture = @$data['picture'];
						}
						$picture = $picture ? boombox_download_avatar( $picture, $user->ID ) : '';
					}
					if ( ! empty( $picture ) ) {
						boombox_add_update_user_meta( $user->ID, 'boombox_avatar', $picture );
					}

					$status = true;

				}

			}
		} else {
			$response = esc_html__( 'Failed to validate token', 'boombox' );
		}
	}

	if( $status ) {
		do_action( 'boombox_after_user_login_success', $user, $redirect_url );
		if( ! $response ) {
			if( $action == 'login' ) {
				$action = esc_html__( 'Login', 'boombox' );
			} elseif( $action == 'register' ) {
				$action = esc_html__( 'Registration', 'boombox' );
			}

			$response = $action . esc_html__( ' successful, redirecting...', 'boombox' );
		}

		wp_send_json_success( $response );
	} else {
		wp_send_json_error( $response );
	}
}

/**
 * Create new user
 *
 * @param $username
 * @param $email
 * @param $password
 * @param string $meta_key
 * @param string $meta_value
 *
 * @return int|WP_Error
 */
function boombox_create_user( $username, $email, $password, $meta_key = '', $meta_value = '' ) {
	if ( empty( $username ) ) {
		$username = strtok( $email, '@' );
	}
	if ( username_exists( $email ) ) {
		return esc_html__( 'Email already exists', 'boombox' );
	}

	if ( empty( $password ) ) {
		$password = wp_generate_password( 12, false );
	}
	$user_id = wp_create_user( $email, $password, $email );
	if ( is_wp_error( $user_id ) ) {
		return false;
	}

	if ( ! empty( $meta_key ) && ! empty( $meta_value ) ) {
		add_user_meta( $user_id, $meta_key, $meta_value );
	}

	wp_update_user(
		array(
			'ID'                   => $user_id,
			'nickname'             => $username,
			'display_name'         => $username,
			'user_nicename'        => $username,
			'role'                 => 'contributor',
			'show_admin_bar_front' => false
		)
	);

	return $user_id;
}

/**
 * Download avatar
 *
 * @param $url
 * @param $user_id
 *
 * @return bool|string
 */
function boombox_download_avatar( $url, $user_id ) {
	global $wp_filesystem;
	$upload_dir = WP_CONTENT_DIR . '/uploads/avatar';
	if ( ! is_dir( $upload_dir ) ) {
		wp_mkdir_p( $upload_dir );
	}

	$data = wp_remote_get( $url, array( 'sslverify' => false ) );

	if ( is_wp_error( $data ) ) {
		return false;
	}

	if ( $data['response']['code'] != 200 ) {
		return false;
	}

	$body      = $data['body'];
	$imagename = md5( $user_id ) . '.jpg';
	$image     = $upload_dir . '/' . $imagename;

	if( empty( $wp_filesystem ) ) {
		//require_once( ABSPATH .'/wp-admin/includes/file.php' );
		if( function_exists( 'WP_Filesystem' ) ){
			WP_Filesystem();
		}
	}

	if( $wp_filesystem ) {
		$wp_filesystem->put_contents( $image, $body, FS_CHMOD_FILE );
	}

	list( $w, $h ) = getimagesize( $image );
	if ( $w && $h ) {
		return WP_CONTENT_URL . '/uploads/avatar/' . $imagename;
	}

	@unlink( $image );

	return false;
}

/**
 * Add/Update user meta
 *
 * @param $user_id
 * @param $meta_key
 * @param $meta_value
 */
function boombox_add_update_user_meta( $user_id, $meta_key, $meta_value ) {
	if ( ! add_user_meta( $user_id, $meta_key, $meta_value, true ) ) {
		update_user_meta( $user_id, $meta_key, $meta_value );
	}
}

/**
 * Detect if facebook auth is enabled
 *
 * @return bool
 */
function boombox_facebook_auth_enabled() {
	$facebook_app_id = boombox_get_theme_option( 'extra_authentication_facebook_app_id' );

	return $facebook_app_id ? true : false;
}

/**
 * Detect if google auth is enabled
 *
 * @return bool
 */
function boombox_google_auth_enabled() {
	$set = boombox_get_theme_options_set( array(
		'extra_authentication_google_oauth_id',
		'extra_authentication_google_api_key'
	) );

	return ( $set['extra_authentication_google_oauth_id'] && $set['extra_authentication_google_api_key'] );
}