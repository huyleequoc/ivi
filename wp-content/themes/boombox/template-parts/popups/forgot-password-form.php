<?php
/**
 * The template part for displaying the forgot password form.
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.5.6
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$boombox_forgot_password_popup_heading = boombox_get_theme_option( 'extra_authentication_forgot_password_popup_title' );
$boombox_forgot_password_popup_text = boombox_get_theme_option( 'extra_authentication_forgot_password_popup_text' );
$boombox_login_popup_heading = boombox_get_theme_option( 'extra_authentication_login_popup_title' ); ?>

<!-- Modal content -->
<div id="forgot-password" class="light-modal authentication">
	<a href="#" class="modal-close"><i class="bb-icon bb-ui-icon-close"></i></a>
	<div class="modal-body wrapper">
		<div class="content-wrapper">
			<header class="content-header">
				<?php if ( $boombox_forgot_password_popup_heading ) { ?>
					<h3 class="title"><?php esc_html_e( $boombox_forgot_password_popup_heading, 'boombox' ); ?></h3>
				<?php } ?>

				<?php if ( $boombox_forgot_password_popup_text ) { ?>
					<div class="intro"><?php echo wp_kses_post( esc_html__( $boombox_forgot_password_popup_text, 'boombox' ) ); ?></div>
				<?php } ?>
			</header>
			<div class="content-body">
				<p class="status-msg bb-txt-msg"></p>
				<form id="boombox_forgot_password" class="ajax-auth" action="forgot_password" method="post">
					<?php wp_nonce_field( 'ajax-forgot-nonce', 'forgotsecurity' ); ?>
					<div class="input-field">
						<input type="text" name="userlogin" class="required"
						       placeholder="<?php esc_html_e( 'Your username or e-mail', 'boombox' ); ?>">
					</div>
					<div class="input-field">
						<button class="bb-btn" type="submit"><?php esc_html_e( 'reset', 'boombox' ); ?></button>
					</div>
				</form>
			</div>
			<div class="content-footer">
				<div class="bottom">
					<div class="text"><?php esc_html_e( 'Back to ', 'boombox' ); ?></div>
					<a class="bb-btn bb-btn-default js-authentication"
					   href="#sign-in"><?php esc_html_e( $boombox_login_popup_heading, 'boombox' ); ?></a>
				</div>
			</div>
		</div>
	</div>
</div>


