<?php
/**
 * Boombox default authentication
 *
 * @package BoomBox_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}


/**
 * Hooks
 */

// Enable the user with no privileges to run ajax_login() in AJAX
add_action( 'wp_ajax_nopriv_boombox_ajax_login', 'boombox_ajax_login' );

// Enable the user with no privileges to run ajax_register() in AJAX
add_action( 'wp_ajax_nopriv_boombox_ajax_register', 'boombox_ajax_register' );

// Enable the user with no privileges to run ajax_forgotPassword() in AJAX
add_action( 'wp_ajax_nopriv_boombox_ajax_forgot_password', 'boombox_ajax_forgot_password' );

// Enable the user with no privileges to run ajax_resetPassword() in AJAX
add_action( 'wp_ajax_nopriv_boombox_ajax_reset_password', 'boombox_ajax_reset_password' );

// Enqueue scripts
add_action( 'wp_enqueue_scripts', 'boombox_default_auth_scripts' );


/**
 * Enqueue Global Authentication scripts
 */
function boombox_default_auth_scripts() {
	global $wp;
	// $min = boombox_get_minified_asset_suffix();
	$min = '';

	wp_enqueue_script(
		'boombox-validate-scripts',
		BOOMBOX_INCLUDES_URL . 'authentication/assets/js/jquery.validate' . $min . '.js',
		array( 'jquery' ),
		boombox_get_assets_version(),
		true
	);
	wp_enqueue_script(
		'boombox-default-auth-scripts',
		BOOMBOX_INCLUDES_URL . 'authentication/default/js/default-auth-scripts' . $min . '.js',
		array( 'jquery' ),
		boombox_get_assets_version(),
		true
	);

	$set              = boombox_get_theme_options_set( array(
		'extra_authentication_enable_login_captcha',
		'extra_authentication_enable_registration_captcha',
		'design_primary_color'
	) );
	$current_url      = esc_url( boombox_get_current_url( true ) );
	$ajax_auth_object = array(
		'ajaxurl'                     => admin_url( 'admin-ajax.php' ),
		'login_redirect_url'          => apply_filters( 'boombox_auth_login_redirect_url', $current_url ),
		'register_redirect_url'       => apply_filters( 'boombox_auth_register_redirect_url', site_url(), 'default' ),
		'reset_password_redirect_url' => apply_filters( 'boombox_auth_reset_password_redirect_url', site_url() ),
		'nsfw_redirect_url'           => apply_filters( 'boombox_auth_nsfw_redirect_url', $current_url ),
		'loading_message'             => boombox_get_auth_loading_message( 'default' ),
		'invalid_message'             => boombox_get_auth_invalid_message( 'default' ),
		'captcha_file_url'            => BOOMBOX_INCLUDES_URL . 'authentication/default/captcha/captcha-security-image.php',
		'captcha_type'                => boombox_get_auth_captcha_type(),
		'enable_login_captcha'        => $set['extra_authentication_enable_login_captcha'],
		'enable_registration_captcha' => $set['extra_authentication_enable_registration_captcha'],
		'site_primary_color'          => $set['design_primary_color'],
		'trigger_action'              => get_query_var( 'bb-action' ),
		'messages'                    => array(
			'password_strength' => __( 'Password strength', 'boombox' ),
			'password_mismatch' => __( 'Passwords don\'t match', 'boombox' )
		)
	);

	wp_localize_script( 'boombox-default-auth-scripts', 'ajax_auth_object', $ajax_auth_object );
}

/**
 * Ajax Login
 *
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_ajax_login() {

	// First check the nonce, if it fails the function will break
	if ( apply_filters( 'boombox_ajax_login_check_referer', true ) ) {
		check_ajax_referer( 'ajax-login-nonce', 'security' );
	}

	$has_error = false;
	$response  = '';

	$boombox_enable_login_captcha = boombox_get_theme_option( 'extra_authentication_enable_login_captcha' );
	if ( $boombox_enable_login_captcha ) {
		$boombox_auth_captcha_type = boombox_get_auth_captcha_type();

		if ( $boombox_auth_captcha_type === 'image' ) { // image captcha validation

			// Second check the captcha, if it fails the function will break
			if ( session_id() == '' || session_status() == PHP_SESSION_NONE ) {
				session_start();
			}
			if ( ! boombox_validate_image_captcha( 'captcha', 'login' ) ) {
				$has_error = true;
				$response  = esc_html__( 'Invalid Captcha. Please, try again.', 'boombox' );
			}
			session_write_close();

		} elseif ( in_array( $boombox_auth_captcha_type, array( 'google', 'google_v3' ) ) ) { // google captcha validation

			$gcaptcha = boombox_validate_google_captcha( 'captcha' );

			if ( ! $gcaptcha['success'] ) {
				$has_error = true;
				$response  = esc_html__( 'Invalid Captcha. Please, try again.', 'boombox' );
			}

		}
	}

	if ( $has_error ) {
		wp_send_json_error( array(
			'message' => $response,
		) );
	}

	// Call auth_user_login
	boombox_auth_user_login(
		sanitize_text_field( $_POST['useremail'] ),
		sanitize_text_field( $_POST['password'] ),
		filter_var( $_POST['remember'], FILTER_VALIDATE_BOOLEAN ),
		esc_html__( 'Login', 'boombox' )
	);
}

/**
 * Ajax Registration
 *
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_ajax_register() {

	// First check the nonce, if it fails the function will break
	if ( apply_filters( 'boombox_ajax_register_check_referer', true ) ) {
		check_ajax_referer( 'ajax-register-nonce', 'security' );
	}

	$has_error       = false;
	$need_activation = false;
	$response        = '';

	// Second check the captcha, if it fails the function will break
	$boombox_enable_registration_captcha = boombox_get_theme_option( 'extra_authentication_enable_registration_captcha' );
	if ( $boombox_enable_registration_captcha ) {
		$boombox_auth_captcha_type = boombox_get_auth_captcha_type();

		if ( $boombox_auth_captcha_type === 'image' ) { // image captcha validation

			if ( session_id() == '' || session_status() == PHP_SESSION_NONE ) {
				session_start();
			}
			if ( ! boombox_validate_image_captcha( 'captcha', 'register' ) ) {
				$has_error = true;
				$response  = esc_html__( 'Invalid Captcha. Please, try again.', 'boombox' );
			}
			session_write_close();

		} elseif ( in_array( $boombox_auth_captcha_type, array( 'google', 'google_v3' ) ) ) { // google captcha validation

			$gcaptcha = boombox_validate_google_captcha( 'captcha' );

			if ( ! $gcaptcha['success'] ) {
				$has_error = true;
				$response  = esc_html__( 'Invalid Captcha. Please, try again.', 'boombox' );
			}

		}
	}

	$options_set = boombox_get_theme_options_set( array(
		'extras_gdpr_visibility',
		'extra_authentication_terms_of_use_page',
		'extra_authentication_privacy_policy_page'
	) );
	if ( in_array( 'sign_up', (array) $options_set['extras_gdpr_visibility'] ) ) {
		$gdpr = isset( $_POST['gdpr'] ) ? absint( $_POST['gdpr'] ) : 0;
		if ( ! $gdpr ) {

			$links               = false;
			$terms_of_use_link   = $options_set['extra_authentication_terms_of_use_page'] ? sprintf( ' %1$s <a href="%2$s" target="_blank" rel="noopener">%3$s</a> ', esc_html__( 'the', 'boombox' ), get_permalink( $options_set['extra_authentication_terms_of_use_page'] ), apply_filters( 'boombox/signup/terms_of_use_title', esc_html__( 'terms of use', 'boombox' ) ) ) : false;
			$privacy_policy_link = $options_set['extra_authentication_privacy_policy_page'] ? sprintf( ' %1$s <a href="%2$s" target="_blank" rel="noopener">%3$s</a> ', esc_html__( 'the', 'boombox' ), get_permalink( $options_set['extra_authentication_privacy_policy_page'] ), apply_filters( 'boombox/signup/privacy_policy_title', esc_html__( 'privacy policy', 'boombox' ) ) ) : false;
			if ( $terms_of_use_link && $privacy_policy_link ) {
				$links = $terms_of_use_link . esc_html__( 'and', 'boombox' ) . $privacy_policy_link;
			} elseif ( $terms_of_use_link ) {
				$links = $terms_of_use_link;
			} elseif ( $privacy_policy_link ) {
				$links = $privacy_policy_link;
			}

			$has_error = true;
			$response  = sprintf( esc_html__( 'You must agree to %s before signing up', 'boombox' ), $links );
		}
	}

	// Nonce is checked, get the POST data and sign user on
	$info                  = array();
	$info['user_nicename'] = $info['nickname'] = $info['display_name'] = $info['first_name'] = $info['user_login'] = sanitize_user( $_POST['username'] );
	$info['user_pass']     = sanitize_text_field( $_POST['password'] );
	$info['user_email']    = sanitize_email( $_POST['useremail'] );
	$info['role']          = get_option( 'default_role', 'contributor' );

	// if there are no errors let's register the user
	if ( ! $has_error ) {

		$registration_function = apply_filters( 'boombox/auth/registration_callback', 'boombox_insert_user' );
		if ( is_callable( $registration_function ) ) {
			$callback_response = call_user_func( $registration_function, $info );

			$has_error       = isset( $callback_response['has_error'] ) ? $callback_response['has_error'] : true;
			$response        = ( isset( $callback_response['response'] ) && $callback_response['response'] ) ? $callback_response['response'] : esc_html__( 'Callback did not return response', 'boombox' );
			$need_activation = ( isset( $callback_response['need_activation'] ) && $callback_response['need_activation'] ) ? $callback_response['need_activation'] : $need_activation;

		} else {
			$has_error = true;
			$response  = esc_html__( 'Registration callback is not callable', 'boombox' );
		}

	}

	if ( $has_error ) {
		wp_send_json_error( array(
			'message' => $response,
		) );
	}

	if ( $need_activation ) {
		wp_send_json_success( array(
			'need_activation' => $need_activation,
			'message'         => apply_filters( 'boombox/buddypress/account_activation_message', esc_html__( 'You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.', 'boombox' ) ),
		) );
	}
	// registration passed successfully: let's login user
	boombox_auth_user_login( $info['user_email'], $info['user_pass'], false, esc_html__( 'Registration', 'boombox' ) );
}

/**
 * Auth user login
 *
 * @param $user_email
 * @param $password
 * @param $message
 */
function boombox_auth_user_login( $user_email, $password, $remember, $message ) {
	$info     = array();
	$success  = false;
	$response = '';

	$user = get_user_by( 'login', $user_email );
	if ( ! $user ) {
		$user = get_user_by( 'email', $user_email );
	}

	$user = apply_filters( 'boombox/auth/login/get_user', $user, $user_email, $password );
	if ( ! $user ) {
		$response = esc_html__( 'There is no user registered with that username or email address.', 'boombox' );
	} elseif ( is_wp_error( $user ) ) {
		$error_messages = $user->get_error_messages();
		$response       = $error_messages[0];
	} else {

		// User is found. Let's try to log in
		$user_signon  = wp_signon( array(
			'user_login'    => $user->user_login,
			'user_password' => $password,
			'remember'      => $remember
		), false );
		$redirect_url = ( isset( $_POST['redirect'] ) && $_POST['redirect'] ) ? $_POST['redirect'] : false;

		if ( is_wp_error( $user_signon ) ) {

			do_action( 'boombox_after_user_login_fail', $user_signon, $redirect_url );
			$response = esc_html__( 'Wrong username or password.', 'boombox' );

		} else {
			$user = wp_set_current_user( $user_signon->ID );
			wp_set_auth_cookie( $user->ID, $remember );

			do_action( 'boombox_after_user_login_success', $user, $redirect_url );

			$success  = true;
			$response = $message . esc_html__( ' successful, redirecting...', 'boombox' );
		}

	}

	if ( $success ) {
		$boombox_first_login = get_user_meta( $user->ID, 'boombox_first_login', true );

		if( $boombox_first_login ) {
			//delete usermeta first_login on first login and redirect to categories page
			delete_user_meta( $user->ID, 'boombox_first_login' );
			$redirect = home_url('world');
		} else {
			$redirect = home_url('/');
		}
		wp_send_json_success( array(
			'message' => $response,
			'redirectUrl' => $redirect,
		) );
	} else {
		wp_send_json_error( array(
			'message' => $response
		) );
	}
}

/**
 * Ajax Forgot Password
 */
function boombox_ajax_forgot_password() {

	// First check the nonce, if it fails the function will break
	check_ajax_referer( 'ajax-forgot-nonce', 'security' );
	$account = $_POST['userlogin'];
	$get_by  = 'email';

	$is_valid = true;
	$message  = '';

	if ( empty( $account ) ) {
		$is_valid = false;
		$message  = esc_html__( 'Enter an username or e-mail address.', 'boombox' );
	} else {
		if ( is_email( $account ) ) {
			if ( email_exists( $account ) ) {
				$get_by        = 'email';
				$user_property = 'user_email';
			} else {
				$is_valid = false;
				$message  = esc_html__( 'There is no user registered with that email address.', 'boombox' );
			}
		} elseif ( validate_username( $account ) ) {
			if ( username_exists( $account ) ) {
				$get_by        = 'login';
				$user_property = 'user_login';
			} else {
				$is_valid = false;
				$message  = esc_html__( 'There is no user registered with that username.', 'boombox' );
			}
		} else {
			$is_valid = false;
			$message  = esc_html__( 'Invalid username or e-mail address.', 'boombox' );
		}
	}

	if ( $is_valid ) {

		// Get user data by field and data, fields are id, slug, email and login
		$user_data = get_user_by( $get_by, $account );

		if ( $user_data ) {

			/********** Headers **********/
			$from = get_option( 'admin_email' );
			if ( ! ( isset( $from ) && is_email( $from ) ) ) {
				$sitename = strtolower( $_SERVER['SERVER_NAME'] );
				if ( substr( $sitename, 0, 4 ) == 'www.' ) {
					$sitename = substr( $sitename, 4 );
				}
				$from = 'admin@' . $sitename;
			}

			$headers = array(
				"Content-type: text/plain; charset=UTF-8",
				sprintf( 'From: %1$s <%2$s>', get_option( 'blogname' ), $from )
			);

			/********** Subject **********/
			if ( is_multisite() ) {
				$blogname = get_network()->site_name;
			} else {
				$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
			}
			$subject = sprintf( __( '[%s] Password Reset', 'boombox' ), $blogname );

			/********** Content **********/
			$user_login = $user_data->user_login;
			$key        = get_password_reset_key( $user_data );

			$content = __( 'Someone has requested a password reset for the following account:', 'boombox' ) . "\r\n\r\n";
			$content .= network_home_url( '/' ) . "\r\n\r\n";
			$content .= sprintf( __( 'Username: %s', 'boombox' ), $user_login ) . "\r\n\r\n";
			$content .= __( 'If this was a mistake, just ignore this email and nothing will happen.', 'boombox' ) . "\r\n\r\n";
			$content .= __( 'To reset your password, visit the following address:', 'boombox' ) . "\r\n\r\n";
			if ( apply_filters( 'boombox/use_front_password_reset', true ) ) {
				$content .= '< ' . network_site_url( "?bb-action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . " >\r\n";
			} else {
				$content .= '< ' . network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . " >\r\n";
			}

			$subject = apply_filters( 'retrieve_password_title', $subject, $user_login, $user_data );
			$content = apply_filters( 'retrieve_password_message', $content, $key, $user_login, $user_data );

			if ( wp_mail( $user_data->user_email, wp_specialchars_decode( $subject ), $content, $headers ) ) {
				$message = esc_html__( 'Confirmation email is sent to your email address', 'boombox' );
			} else {
				$is_valid = false;
				$message  = esc_html__( 'Oops! Something went wrong while updating your account.', 'boombox' );
			}

		}

	}

	$response = array( 'message' => $message );

	if ( $is_valid ) {
		wp_send_json_success( $response );
	} else {
		wp_send_json_error( $response );
	}

}

/**
 * Ajax Reset Password
 */
function boombox_ajax_reset_password() {
	// First check the nonce, if it fails the function will break
	check_ajax_referer( 'ajax-reset-password-nonce', 'security' );

	$login            = isset( $_POST['userlogin'] ) ? sanitize_text_field( $_POST['userlogin'] ) : '';
	$password         = isset( $_POST['password'] ) ? sanitize_text_field( $_POST['password'] ) : '';
	$confirm_password = isset( $_POST['confirm'] ) ? sanitize_text_field( $_POST['confirm'] ) : '';

	$has_error = false;
	$message   = '';

	while ( true ) {
		$user = get_user_by( 'login', $login );
		if ( ! $user ) {
			$has_error = true;
			$message   = __( 'Invalid user', 'boombox' );
			break;
		}

		if ( ! $password ) {
			$has_error = true;
			$message   = __( 'Password is required', 'boombox' );
			break;
		}

		if ( $password != $confirm_password ) {
			$has_error = true;
			$message   = __( 'Passwords do not match', 'boombox' );
			break;
		}

		break;
	}

	if ( $has_error ) {
		wp_send_json_error( array(
			'message' => $message
		) );
	}

	reset_password( $user, $password );
	boombox_auth_user_login(
		$user->user_login,
		$password,
		0,
		esc_html__( 'Login', 'boombox' )
	);
}

/**
 * Theme native functionality for user inserting
 *
 * @param $info
 *
 * @return array
 */
function boombox_insert_user( $info ) {

	$has_error = false;
	$response  = '';

	$user_register = wp_insert_user( $info );
	if ( is_wp_error( $user_register ) ) {
		$error = $user_register->get_error_codes();

		if ( in_array( 'empty_user_login', $error ) ) {
			$has_error = true;
			$response  = esc_html( $user_register->get_error_message( 'empty_user_login' ) );
		} elseif ( in_array( 'existing_user_email', $error ) || in_array( 'existing_user_login', $error ) ) {
			$has_error = true;
			$response  = esc_html__( 'This email address is already registered.', 'boombox' );
		}
	}

	return array(
		'has_error' => $has_error,
		'response'  => $response
	);

}