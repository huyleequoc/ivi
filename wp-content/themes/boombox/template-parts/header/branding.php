<?php
/**
 * The template part for displaying the site logo and tagline
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$logo = boombox_get_logo();
$site_name = get_bloginfo( 'name' ); ?>

<div class="branding">

	<p class="site-title">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php if ( ! empty( $logo ) ) {
				$logo_attrs = array(
					'src="' . esc_url( $logo[ 'src' ] ) . '"',
					'alt="' . esc_attr( $site_name ) . '"',
				);

				$width = absint( $logo[ 'width' ] );
				if ( $width ) {
					$logo_attrs[] = 'width="' . $width . '"';
				}

				$height = absint( $logo[ 'height' ] );
				if ( $height ) {
					$logo_attrs[] = 'height="' . $height . '"';
				}

				if ( isset( $logo[ 'src_2x' ] ) && $logo[ 'src_2x' ] ) {
					$logo_attrs[] = 'srcset="' . esc_attr( $logo[ 'src_2x' ] ) . '"';
				}
				$logo_attrs = implode( ' ', $logo_attrs ); ?>
				<img class="site-logo" <?php echo $logo_attrs; ?> />
				<?php
			} else {
				echo esc_html( $site_name );
			}
			?>
		</a>
	</p>

	<?php if ( boombox_get_theme_option( 'branding_show_tagline' ) && ( $tagline = get_bloginfo( 'description' ) ) ) { ?>
		<p class="site-description"><?php echo esc_html( $tagline ); ?></p>
	<?php } ?>
</div>