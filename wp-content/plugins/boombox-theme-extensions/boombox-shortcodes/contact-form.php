<?php
/**
 * Shortcode for Contact Form
 *
 * @package BoomBox_Theme_Extensions
 *
 */

// Prevent direct script access
if ( ! defined( 'ABSPATH' ) ) {
	die ( 'No direct script access allowed' );
}

add_shortcode( 'boombox_contact_form', 'boombox_contact_form' );

/**
 * Hooks
 */
add_action( 'wp_ajax_contact_form_submit', 'boombox_contact_form_submit_callback' );
add_action( 'wp_ajax_nopriv_contact_form_submit', 'boombox_contact_form_submit_callback' );
add_action( 'wp_enqueue_scripts', 'boombox_contact_form_scripts' );
add_filter( 'boombox/gdpr_checkbox_visibility_choices', 'boombox_contact_form_edit_gdpr_checkbox_visibility_choices' );

/**
 * Enqueue scripts for contact form
 */
function boombox_contact_form_scripts() {
	wp_enqueue_script( 'boombox-shortcodes', BBTE_PLUGIN_URL . '/boombox-shortcodes/js/shortcodes.min.js', array( 'jquery' ), '20160609', true );
	wp_localize_script( 'boombox-shortcodes', 'params', array(
		'ajax_url'         => admin_url( 'admin-ajax.php' ),
		'success_message'  => apply_filters( 'bbte_contact_form_success_message', esc_html__( 'Your message successfully submitted!', 'boombox-theme-extensions' ) ),
		'error_message'    => apply_filters( 'bbte_contact_form_error_message', esc_html__( 'Please fill all required fields!', 'boombox-theme-extensions' ) ),
		'wrong_message'    => apply_filters( 'bbte_contact_form_wrong_message', esc_html__( 'Something went wrong, please try again!', 'boombox-theme-extensions' ) ),
		'captcha_file_url' => esc_url( BBTE_PLUGIN_URL . '/boombox-shortcodes/captcha.php' ),
		'captcha_type'     => boombox_get_auth_captcha_type(),
	) );
}

/**
 * Contact Form Shortcode
 *
 * @param $atts
 *
 * @return string
 */
function boombox_contact_form( $atts ) {
	$a = shortcode_atts( array(
		'message_placeholder' => esc_html__( 'Your Message', 'boombox-theme-extensions' ),
		'label_submit'        => esc_html__( 'Submit Your Message', 'boombox-theme-extensions' ),
		'captcha'             => 1,
	), $atts );

	$boombox_auth_captcha_type = boombox_get_auth_captcha_type();

	ob_start();
	?>
	<div class="bb-contact-form-block">
		<div class="bb-contact-form-msg bb-txt-msg"></div>
		<form class="bb-contact-form" action="contact_form">
			<div class="bb-form-block">
				<div class="row">
					<div class="input-field col-lg-6 col-sm-6">
						<input name="boombox_name" type="text" placeholder="<?php echo esc_html__( 'Name', 'boombox-theme-extensions' ); ?> *">
					</div>
					<div class="input-field col-lg-6 col-sm-6">
						<input name="boombox_email" type="email" placeholder="<?php echo esc_html__( 'Email', 'boombox-theme-extensions' ); ?> *">
					</div>
					<div class="col-lg-12 input-field">
						<textarea name="boombox_comment" placeholder="<?php echo esc_attr( $a['message_placeholder'] ); ?> *"></textarea>
					</div>
				</div>
			</div>

			<?php if ( in_array( 'contact_form', (array) boombox_get_theme_option( 'extras_gdpr_visibility' ) ) && ( $dbpr_message = boombox_get_gdpr_message() ) ) { ?>
				<div class="input-field row-gdpr-agreement bb-row-check-label">
					<input type="checkbox" name="boombox_gdpr" id="boombox-gdpr" class="form-input" value="1" />
					<label for="boombox-gdpr" class="form-label"><?php echo $dbpr_message; ?></label>
				</div>
			<?php } ?>

			<div class="bb-form-row-actions">
				<?php if ( (bool) absint( $a['captcha'] ) && $boombox_auth_captcha_type ) { ?>
					<div class="captcha-col">
						<?php if ( 'image' === $boombox_auth_captcha_type ) { ?>

							<div class="captcha-container loading">
								<div class="form-captcha">
									<img src="" alt="Captcha!" class="captcha" />
									<a href="#refresh-captcha" class="boombox-refresh-captcha refresh-captcha"></a>
								</div>
								<input type="text" name="boombox_captcha_code" class="required" placeholder="<?php echo esc_html__( 'Enter captcha', 'boombox-theme-extensions' ); ?>">
							</div>

						<?php } elseif ( in_array( $boombox_auth_captcha_type, array( 'google', 'google_v3' ) ) ) { ?>

							<div class="google-captcha-code" id="boombox-contact-captcha" data-boombox-sitekey="<?php echo esc_attr( boombox_get_theme_option( 'extra_authentication_' . $boombox_auth_captcha_type . '_recaptcha_site_key' ) ); ?>"></div>

							<?php if ( 'google_v3' === $boombox_auth_captcha_type ) { ?>
								<input type="hidden" id="g-recaptcha-response-contact-form" name="g-recaptcha-response" />
							<?php }

						} ?>
					</div>
				<?php } ?>
				<div class="btn-col">
					<div class="input-field form-submit">
						<input name="submit" type="submit" id="submit" class="submit pull-right" value="<?php echo esc_attr( esc_html( $a['label_submit'] ) ); ?>" />
					</div>
				</div>
			</div>

		</form>
	</div>
	<?php

	return ob_get_clean();
}

/**
 * Contact form submit callback
 */
function boombox_contact_form_submit_callback() {
	$valid   = array();
	$sent    = false;
	$message = array();

	$name          = sanitize_text_field( $_POST['name'] );
	$email         = sanitize_email( $_POST['email'] );
	$comment       = esc_textarea( $_POST['comment'] );
	$check_captcha = (bool) sanitize_text_field( $_POST['check_captcha'] );

	$admin_email = apply_filters( 'boombox_contact_form_admin_email', get_option( 'admin_email' ) );

	if ( '' == $name ) {
		$valid['name'] = false;
		$message[]     = esc_html__( 'Please enter the name.', 'boombox-theme-extensions' );
	}

	if ( ! is_email( $email ) ) {
		$valid['email'] = false;
		$message[]      = esc_html__( 'Please enter valid email address.', 'boombox-theme-extensions' );
	}

	if ( '' == $comment ) {
		$valid['comment'] = false;
		$message[]        = esc_html__( 'Please fill the comment.', 'boombox-theme-extensions' );
	}

	$options_set = boombox_get_theme_options_set( array(
		'extras_gdpr_visibility',
		'extra_authentication_terms_of_use_page',
		'extra_authentication_privacy_policy_page'
	) );
	if ( in_array( 'contact_form', (array) $options_set['extras_gdpr_visibility'] ) ) {
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

			$valid['gdpr'] = false;
			$message[]     = sprintf( esc_html__( 'You must agree to %s before signing up', 'boombox' ), $links );
		}
	}

	// Check the captcha
	if ( $check_captcha ) {
		$boombox_auth_captcha_type = boombox_get_auth_captcha_type();

		if ( $boombox_auth_captcha_type === 'image' ) {
			// image captcha validation
			if ( ! boombox_validate_image_captcha( 'captcha', 'contact_form' ) ) {
				$valid['captcha'] = false;
				$message[]        = esc_html__( 'Invalid Captcha! Please, try again.', 'boombox-theme-extensions' );
			}
			session_write_close();

		} elseif ( in_array( $boombox_auth_captcha_type, array( 'google', 'google_v3' ) ) ) { //google captcha validation
			// google captcha validation
			$gcaptcha = boombox_validate_google_captcha( 'captcha' );

			if ( ! $gcaptcha['success'] ) {
				$valid['captcha'] = false;
				$message[]        = esc_html__( 'Invalid Captcha! Please, try again.', 'boombox-theme-extensions' );
			}
		}
	}

	if ( empty( $valid ) ) {

		$site_name = get_bloginfo( 'name' );
		$subject   = sprintf( '%1$s %2$s', $site_name, esc_html__( 'Contact Form', 'boombox-theme-extensions' ) );
		$subject   = apply_filters( 'boombox_contact_form_subject', $subject );
		$headers[] = "From: {$name} <{$email}>";
		$headers[] = 'content-type: text/html';

		$body = sprintf( '<b>%1$s:</b> %2$s<br/>  <b>%3$s:</b> %4$s<br/> <b>%5$s:</b> %6$s',
			esc_html__( 'Name', 'boombox-theme-extensions' ), $name,
			esc_html__( 'Email', 'boombox-theme-extensions' ), $email,
			esc_html__( 'Message', 'boombox-theme-extensions' ), $comment
		);

		$sent = wp_mail( $admin_email, $subject, $body, $headers );
	}

	$result = array(
		'valid'   => $valid,
		'message' => ! empty( $message ) ? implode( '<br />', $message ) : '',
		'sent'    => $sent
	);

	echo json_encode( $result );

	wp_die();
}

/**
 * Edit customizer "GDPR" checkbox visibility choices
 *
 * @param array $choices Current choices
 *
 * @return array
 * @since   1.5.5
 * @version 1.5.5
 */
function boombox_contact_form_edit_gdpr_checkbox_visibility_choices( $choices ) {
	$choices['contact_form'] = __( 'Contact Form (shortcode)', 'boombox-theme-extensions' );

	return $choices;
}

if ( ! function_exists( 'boombox_validate_google_captcha' ) ) {
	/**
	 * Validate google captcha response
	 *
	 * @param $key The key in $_POST array where response is set
	 *
	 * @return array
	 */
	function boombox_validate_google_captcha( $key ) {

		add_filter( 'http_request_timeout', 'boombox_recaptcha_http_request_timeout', 9999, 1 );

		$gcaptcha = array(
			'success'  => false,
			'message'  => '',
			'response' => wp_remote_retrieve_body( wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', array(
				'body' => array(
					'secret'   => boombox_get_theme_option( 'extra_authentication_' . boombox_get_auth_captcha_type() . '_recaptcha_secret_key' ),
					'response' => isset( $_POST[ $key ] ) ? $_POST[ $key ] : ''
				),
			) ) )
		);

		if ( ! is_wp_error( $gcaptcha['response'] ) ) {
			$gcaptcha['response'] = json_decode( $gcaptcha['response'], true );
			if ( isset( $gcaptcha['response']['success'] ) && $gcaptcha['response']['success'] ) {
				$gcaptcha['success'] = true;
			}
		}

		remove_filter( 'http_request_timeout', 'boombox_recaptcha_http_request_timeout', 9999 );

		return $gcaptcha;
	}
}

if ( ! function_exists( 'boombox_recaptcha_http_request_timeout' ) ) {

	/**
	 * Set optimal duration of HTTP request timeout for google recaptcha validating
	 *
	 * @param $val
	 *
	 * @return int
	 */
	function boombox_recaptcha_http_request_timeout( $val ) {
		return 5; // seconds
	}

}