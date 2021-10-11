<?php
/**
 * The template part for displaying the login form.
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
$options_set       = boombox_get_theme_options_set( array(
	'extra_authentication_login_popup_title',
	'extra_authentication_login_popup_text',
	'extra_authentication_enable_remember_me',
	'extra_authentication_registration_popup_title',
	'extra_authentication_enable_login_captcha',
	'extra_authentication_google_recaptcha_site_key',
	'extra_authentication_google_v3_recaptcha_site_key',
	'extra_authentication_registration_custom_url',
) );
?>

<!-- Modal content -->
<div id="sign-in" class="light-modal sign-in authentication">
	<a href="#" class="modal-close"><i class="bb-icon bb-ui-icon-close"></i></a>
	<div class="modal-body wrapper">
		<div class="content-wrapper">
			<header class="content-header">
				<?php if ( $options_set['extra_authentication_login_popup_title'] ): ?>
					<h3 class="title"><?php esc_html_e( $options_set['extra_authentication_login_popup_title'], 'boombox' ); ?></h3>
				<?php endif; ?>

				<?php if ( $options_set['extra_authentication_login_popup_text'] ): ?>
					<div class="intro"><?php echo wp_kses_post( esc_html__( $options_set['extra_authentication_login_popup_text'], 'boombox' ) ); ?></div>
				<?php endif; ?>
			</header>
			<div class="content-body">
				<?php if ( boombox_plugin_management_service()->is_plugin_active( 'wordpress-social-login/wp-social-login.php' ) ) { ?>
					<?php do_action( 'wordpress_social_login' ); ?>
				<?php } else { ?>
					<div class="clearfix"><?php do_action( 'boombox_before_login_form', 'login' ); ?></div>
				<?php } ?>

				<p class="status-msg bb-txt-msg"></p>

				<form id="boombox-login" class="ajax-auth" action="login" method="post">
					<?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
					<div class="input-field">
						<input type="text" name="useremail" class="required" placeholder="<?php esc_html_e( 'Your username or e-mail', 'boombox' ); ?>">
					</div>
					<div class="input-field">
						<input type="password" name="password" class="required" placeholder="<?php esc_html_e( 'Your password', 'boombox' ); ?>">
					</div>

					<?php if( $options_set['extra_authentication_enable_remember_me'] ) { ?>
					<div class="input-field row-remember-me bb-row-check-label">
						<input type="checkbox" name="rememberme" id="rememberme" class="form-input">
						<label for="rememberme" class="form-label"><?php esc_html_e( 'Remember me', 'boombox' ); ?></label>
					</div>
					<?php } ?>

					<?php if ( $options_set['extra_authentication_enable_login_captcha'] ) { ?>

						<?php if ( $auth_captcha_type === 'image' ) { ?>
							<div class="input-field captcha-container loading">
								<div class="form-captcha">
									<img src="" alt="Captcha!" class="captcha">
									<a href="#refresh-captcha" class="auth-refresh-captcha refresh-captcha"></a>
								</div>
								<input type="text" class="required bb-captcha-field" name="captcha-code"
									   placeholder="<?php esc_html_e( 'Enter captcha', 'boombox' ); ?>">
							</div>
						<?php } elseif ( in_array( $auth_captcha_type, array( 'google', 'google_v3' ) ) ) { ?>
                            <div class="input-field text-center">
								<div class="google-captcha-code" id="boombox-login-captcha"
									 data-boombox-sitekey="<?php echo esc_attr( $options_set['extra_authentication_' . $auth_captcha_type . '_recaptcha_site_key'] ); ?>"></div>
							    <?php if( 'google_v3' === $auth_captcha_type ) { ?>
                                <input type="hidden" id="g-recaptcha-response-login" name="g-recaptcha-response" />
                                <?php } ?>
							</div>
						<?php } ?>
					<?php } ?>

					<div class="input-field">
						<button class="bb-btn" type="submit"><?php esc_html_e( 'log in', 'boombox' ); ?></button>
					</div>
					<div class="input-field row-forgot-password">
						<a class="forgot-password-link js-authentication" href="#forgot-password"><?php esc_html_e( 'Forgot password?', 'boombox' ); ?></a>
					</div>
				</form>
				<?php do_action( 'boombox_after_login_form' ); ?>
			</div>
			<?php if ( boombox_user_can_register() ): ?>
				<div class="content-footer">
					<div class="bottom">
						<div class="text"><?php esc_html_e( 'Don\'t have an account?', 'boombox' ); ?></div>
						<?php
						$custom_registration_url = $options_set['extra_authentication_registration_custom_url'];
						$registration_url        = '#registration';
						if ( $custom_registration_url && filter_var( $custom_registration_url, FILTER_VALIDATE_URL ) ) {
							$registration_url = $custom_registration_url;
						}
						$btn_class = ( $registration_url == '#registration' ) ? 'js-authentication' : '';
						?>
						<a class="bb-btn bb-btn-default <?php echo $btn_class; ?>" href="<?php echo $registration_url; ?>"><?php esc_html_e( $options_set['extra_authentication_registration_popup_title'], 'boombox' ); ?></a>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
