<?php
/**
 * The template part for displaying the registration form.
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.5.6
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$auth_captcha_type = boombox_get_auth_captcha_type();
$options_set = boombox_get_theme_options_set( array(
	'extra_authentication_registration_popup_title',
	'extra_authentication_registration_popup_text',
	'extra_authentication_login_popup_title',
	'extra_authentication_enable_registration_captcha',
	'extra_authentication_google_recaptcha_site_key',
	'extra_authentication_google_v3_recaptcha_site_key',
	'extras_gdpr_visibility'
) ); ?>

<!-- Modal content -->
<div id="registration" class="light-modal authentication">
	<a href="#" class="modal-close"><i class="bb-icon bb-ui-icon-close"></i></a>
	<div class="modal-body wrapper">
		<div class="content-wrapper">
			<header class="content-header">
				<?php if ( $options_set[ 'extra_authentication_registration_popup_title' ] ): ?>
					<h3 class="title"><?php esc_html_e( $options_set[ 'extra_authentication_registration_popup_title' ], 'boombox' ); ?></h3>
				<?php endif; ?>

				<?php if ( $options_set[ 'extra_authentication_registration_popup_text' ] ): ?>
					<div class="intro"><?php echo wp_kses_post( esc_html__( $options_set[ 'extra_authentication_registration_popup_text' ], 'boombox' ) ); ?></div>
				<?php endif; ?>
			</header>
			<div class="content-body">

				<?php if ( boombox_plugin_management_service()->is_plugin_active( 'wordpress-social-login/wp-social-login.php' ) ) { ?>
					<?php do_action( 'wordpress_social_login' ); ?>
				<?php } else { ?>
					<div class="clearfix"><?php do_action( 'boombox_before_register_form', 'register' ); ?></div>
				<?php } ?>

				<p class="status-msg bb-txt-msg"></p>

				<form id="boombox-register" class="ajax-auth" action="register" method="post">
					<?php wp_nonce_field( 'ajax-register-nonce', 'signonsecurity' ); ?>
					<div class="input-field">
						<input type="email" name="signonemail" class="required"
						       placeholder="<?php esc_html_e( 'Your e-mail address', 'boombox' ); ?>" value="">
					</div>
					<div class="input-field">
						<input type="text" name="signonusername" class="required"
						       placeholder="<?php esc_html_e( 'Your username', 'boombox' ); ?>" value="">
					</div>
					<div class="input-field">
						<input type="password" name="signonpassword" class="required"
						       placeholder="<?php esc_html_e( 'Your password', 'boombox' ); ?>" value="" autocomplete="new-password">
					</div>
					<div id="bb-register-pass-strength-result"></div>
					<?php if ( in_array( 'sign_up', (array)$options_set['extras_gdpr_visibility'] ) && ( $dbpr_message = boombox_get_gdpr_message() ) ) { ?>
					<div class="input-field row-gdpr-agreement bb-row-check-label">
						<input type="checkbox" name="signongdpr" id="signongdpr" class="form-input" value="1" />
						<label for="signongdpr" class="form-label"><?php echo $dbpr_message; ?><span class="bb-clr-danger asterisk">*</span></label>
					</div>
					<?php }

					if ( $options_set[ 'extra_authentication_enable_registration_captcha' ] ) {
						if ( $auth_captcha_type === 'image' ) { ?>
							<div class="input-field captcha-container loading">
								<div class="form-captcha">
									<img src="" alt="Captcha!" class="captcha">
									<a href="#refresh-captcha" class="auth-refresh-captcha refresh-captcha"></a>
								</div>
								<input type="text" name="signoncaptcha" class="required bb-captcha-field"
								       placeholder="<?php esc_html_e( 'Enter captcha', 'boombox' ); ?>">
							</div>
						<?php } else if ( in_array( $auth_captcha_type, array( 'google', 'google_v3' ) ) ) { ?>
							<div class="input-field text-center">
								<div class="google-captcha-code" id="boombox-register-captcha"
								     data-boombox-sitekey="<?php echo esc_attr( $options_set[ 'extra_authentication_' . $auth_captcha_type . '_recaptcha_site_key' ] ); ?>"></div>
                                <?php if( 'google_v3' === $auth_captcha_type ) { ?>
                                <input type="hidden" id="g-recaptcha-response-register" name="g-recaptcha-response" />
                                <?php } ?>
							</div>
						<?php }
					} ?>

					<div class="input-field">
						<button class="bb-btn" type="submit"><?php esc_html_e( 'sign up', 'boombox' ); ?></button>
					</div>
				</form>
				<?php do_action( 'boombox_after_register_form' ); ?>
			</div>
			<div class="content-footer">
				<div class="bottom">
					<div class="text"><?php esc_html_e( 'Back to ', 'boombox' ); ?></div>
					<a class="bb-btn bb-btn-default js-authentication"
					   href="#sign-in"><?php esc_html_e( $options_set[ 'extra_authentication_login_popup_title' ], 'boombox' ); ?></a>
				</div>
			</div>
		</div>
	</div>
</div>
