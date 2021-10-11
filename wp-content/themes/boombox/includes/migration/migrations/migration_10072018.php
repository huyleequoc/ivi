<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

class migration_10072018 {

	private static function update_customizer() {
		if ( ! class_exists( 'Boombox_Customizer' ) ) {
			require_once( BOOMBOX_INCLUDES_PATH . 'customizer' . DIRECTORY_SEPARATOR . 'class-boombox-customizer.php' );
		}

		$options = get_option( Boombox_Customizer::OPTION_NAME, array() );
		$map = array(
			'extra_authentication_reset_password_popup_title' => 'extra_authentication_forgot_password_popup_title',
			'extra_authentication_reset_password_popup_text'  => 'extra_authentication_forgot_password_popup_text'
		);

		foreach( $map as $old => $new ) {
			if( isset( $options[ $old ] ) ) {
				$options[ $new ] = $options[ $old ];
				unset( $options[ $old ] );
			}
		}

		update_option( Boombox_Customizer::OPTION_NAME, $options );

		return true;
	}

	/**
	 * Organize migration tasks
	 * @return bool
	 */
	public static function up() {
		return self::update_customizer();
	}

}