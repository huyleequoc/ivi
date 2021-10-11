<?php
/**
 * Boombox migration functions
 *
 * @package BoomBox_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Run required migrations
 */
function boombox_migrate() {

	// do not run migration if CRON or AJAX is processing
	if ( ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		return;
	}

	$db_version = get_option( 'boombox_db_version', 0 );
	$latest_version = 11;

	// do nothing if is up to date ( latest 'case' in 'switch' below )
	if ( ( $db_version + 1 ) > $latest_version ) {
		return;
	}

	add_action( 'boombox/migration_fail', function( $version ){
		add_action( 'wp_title', function() {
			return __( 'Boombox', 'boombox' );
		}, 999999 );
		$support_link = sprintf( '<a href="%1$s" target="_blank" rel="noopener">%1$s</a>', 'https://pxlab.ticksy.com' ); ?>
		<html>
			<head>
				<meta charset="UTF-8">
				<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
				<meta http-equiv="X-UA-Compatible" content="ie=edge">
				<title><?php wp_title(); ?></title>
				<style type="text/css">
					.bb-container {
						text-align: center;
					}
					.bb-migration-message {
						font-size: 1.1em;
					}
				</style>
			</head>
			<body>
				<div class="bb-container">
					<h1><img src="<?php echo get_template_directory_uri(); ?>/images/logo.png" alt="<?php bloginfo( 'name' ); ?>"/></h1>
					<div>
						<p class="bb-migration-message"><?php printf( esc_html__( 'Could not implement migration - %d.', 'boombox' ), $version ); ?></p>
						<p class="bb-migration-message"><?php printf( __( 'Please contact support at %s', 'boombox' ), $support_link ); ?></p>
					</div>
				</div>
			</body>
		</html>
		<?php

		die;
	} );

	switch ( $db_version + 1 ) {
		case 1:
			require_once( BOOMBOX_INCLUDES_PATH . 'migration/migrations/migration_25092016.php' );
			$status = migration_25092016::up();

			$version = 1;
			if ( $status ) {
				update_option( 'boombox_db_version', $version );
				do_action( 'boombox/migrated', $version );
			} else {
				do_action( 'boombox/migration_fail', $version );
				break;
			}
		case 2:
			require_once( BOOMBOX_INCLUDES_PATH . 'migration/migrations/migration_12062017.php' );
			$status = migration_12062017::up();

			$version = 2;
			if ( $status ) {
				update_option( 'boombox_db_version', $version );
				do_action( 'boombox/migrated', $version );
			} else {
				do_action( 'boombox/migration_fail', $version );
				break;
			}
		case 3:
			require_once( BOOMBOX_INCLUDES_PATH . 'migration/migrations/migration_15082017.php' );
			$status = migration_15082017::up();

			$version = 3;
			if ( $status ) {
				update_option( 'boombox_db_version', $version );
				do_action( 'boombox/migrated', $version );
			} else {
				do_action( 'boombox/migration_fail', $version );
				break;
			}
		case 4:
			require_once( BOOMBOX_INCLUDES_PATH . 'migration/migrations/migration_13092017.php' );
			$status = migration_13092017::up();

			$version = 4;
			if ( $status ) {
				update_option( 'boombox_db_version', $version );
				do_action( 'boombox/migrated', $version );
			} else {
				do_action( 'boombox/migration_fail', $version );
				break;
			}
		case 5:
			require_once( BOOMBOX_INCLUDES_PATH . 'migration/migrations/migration_13112017.php' );
			$status = migration_13112017::up();

			$version = 5;
			if ( $status ) {
				update_option( 'boombox_db_version', $version );
				do_action( 'boombox/migrated', $version );
			} else {
				do_action( 'boombox/migration_fail', $version );
				break;
			}
		case 6:
			require_once( BOOMBOX_INCLUDES_PATH . 'migration/migrations/migration_28122017.php' );
			$status = migration_28122017::up();

			$version = 6;
			if ( $status ) {
				update_option( 'boombox_db_version', $version );
				do_action( 'boombox/migrated', $version );
			} else {
				do_action( 'boombox/migration_fail', $version );
				break;
			}
		case 7:
			require_once( BOOMBOX_INCLUDES_PATH . 'migration/migrations/migration_25012018.php' );
			$status = migration_25012018::up();

			$version = 7;
			if ( $status ) {
				update_option( 'boombox_db_version', $version );
				do_action( 'boombox/migrated', $version );
			} else {
				do_action( 'boombox/migration_fail', $version );
				break;
			}
		case 8:
			require_once( BOOMBOX_INCLUDES_PATH . 'migration/migrations/migration_08022017.php' );
			$status = migration_08022017::up();

			$version = 8;
			if ( $status ) {
				update_option( 'boombox_db_version', $version );
				do_action( 'boombox/migrated', $version );
			} else {
				do_action( 'boombox/migration_fail', $version );
				break;
			}
		case 9:
			require_once( BOOMBOX_INCLUDES_PATH . 'migration/migrations/migration_05032018.php' );
			$status = migration_05032018::up();

			$version = 9;
			if ( $status ) {
				update_option( 'boombox_db_version', $version );
				do_action( 'boombox/migrated', $version );
			} else {
				do_action( 'boombox/migration_fail', $version );
				break;
			}
		case 10:
			require_once( BOOMBOX_INCLUDES_PATH . 'migration/migrations/migration_28042018.php' );
			$status = migration_28042018::up();

			$version = 10;
			if ( $status ) {
				update_option( 'boombox_db_version', $version );
				do_action( 'boombox/migrated', $version );
			} else {
				do_action( 'boombox/migration_fail', $version );
				break;
			}
		case 11:
			require_once( BOOMBOX_INCLUDES_PATH . 'migration/migrations/migration_10072018.php' );
			$status = migration_10072018::up();

			$version = 11;
			if ( $status ) {
				update_option( 'boombox_db_version', $version );
				do_action( 'boombox/migrated', $version );
			} else {
				do_action( 'boombox/migration_fail', $version );
				break;
			}
	}

	do_action( 'boombox/after_migration' );

	// after the last step we need to redirect
	wp_redirect( boombox_get_current_url( true ) ); exit();

}

boombox_migrate();