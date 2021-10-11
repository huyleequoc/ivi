<?php
/**
 * The template part for displaying the reset password form.
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.5.6
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$boombox_reset_password_popup_heading = boombox_get_theme_option( 'extra_authentication_reset_password_popup_title' );
$boombox_reset_password_popup_text = boombox_get_theme_option( 'extra_authentication_reset_password_popup_text' );

$key = wp_unslash( $_GET['key'] );
$login = wp_unslash( $_GET['login'] );
$user = check_password_reset_key( $key, $login ); ?>

<!-- Modal content -->
<div id="reset-password" class="light-modal authentication">
	<a href="#" class="modal-close"><i class="bb-icon bb-ui-icon-close"></i></a>
	<div class="modal-body wrapper">
		<div class="content-wrapper">
			<header class="content-header">
				<?php if ( $boombox_reset_password_popup_heading ) { ?>
					<h3 class="title"><?php esc_html_e( $boombox_reset_password_popup_heading, 'boombox' ); ?></h3>
				<?php } ?>

				<?php if ( $boombox_reset_password_popup_text ) { ?>
					<div class="intro"><?php echo wp_kses_post( esc_html__( $boombox_reset_password_popup_text, 'boombox' ) ); ?></div>
				<?php } ?>
			</header>
			<div class="content-body">
				<?php if( is_wp_error( $user ) ) { ?>
				<p><?php echo esc_html( $user->get_error_message() ); ?></p>
				<?php } else { ?>
				<p class="status-msg bb-txt-msg"></p>
				<form id="boombox_reset_password" class="ajax-auth" action="reset_password" method="post">
					<?php wp_nonce_field( 'ajax-reset-password-nonce', 'resetpasswordsecurity' ); ?>
					<input type="hidden" name="rpuserlogin" value="<?php echo $user->user_login; ?>">
					<div class="input-field">
						<input type="password" name="rppassword" class="required" placeholder="<?php esc_html_e( 'New Password', 'boombox' ); ?>">
					</div>
					<div class="input-field">
						<input type="password" name="rpconfirmpassword" class="required" placeholder="<?php esc_html_e( 'Confirm Password', 'boombox' ); ?>">
					</div>
					<div id="bb-rp-pass-strength-result"></div>
					<div class="input-field">
						<button class="bb-btn" type="submit"><?php esc_html_e( 'reset', 'boombox' ); ?></button>
					</div>
				</form>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<a href="#reset-password" class="js-authentication hidden"></a>


