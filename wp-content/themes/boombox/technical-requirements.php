<?php
/**
 * The template for displaying theme technical requirements
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title><?php wp_title(); ?></title>
	</head>

	<body>
		<style>
			html, body {
				margin: 0;
				padding: 0;
				font-size: 16px;
				line-height: 1.1em;
				font-family: Geneva, Arial, Helvetica, sans-serif;
			}

			.container {
				width: 1000px;
				margin: 0 auto;
				max-width: 100%;
				padding: 50px 20px;
				box-sizing: border-box;
			}

			.text-center {
				text-align: center;
			}

			img {
				max-width: 95%
			}

			.text {
				max-width: 600px;
				margin: 15px auto
			}

			.text-success {
				background-color: #5cb85c;
				padding: 0 5px;
				color: #fff;
			}

			.text-error {
				background-color: #c9302c;
				padding: 0 5px;
				color: #fff;
			}
		</style>
		<div class="container text-center">
			<img src="<?php echo get_template_directory_uri(); ?>/images/logo.png" alt="Boombox"/>

			<?php
			$php_version = phpversion();
			$php_ok = ( version_compare( $php_version, '5.4' ) >= 0 );
			?>
			<p class="text"><?php esc_html_e( 'Boombox requires PHP 5.4 or higher', "boombox" ); ?> ( <span
						class="text-<?php echo $php_ok ? 'success' : 'error'; ?>"><?php printf( esc_html__( 'Your Version %s', 'boombox' ), $php_version ); ?></span>
				)</p>
		</div>
	</body>

</html>