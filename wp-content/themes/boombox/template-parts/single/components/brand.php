<?php
/**
 * Template part to render single post sponsor section
 * @since 2.5.0
 * @version 2.5.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$brand = boombox_get_post_brand();
if( ! $brand ) {
	return;
}

$logo = boombox_get_brand_logo( $brand );
$logo_attrs = array(
	'src="' . esc_url( $logo[ 'src' ] ) . '"',
	'title="' . esc_attr( $brand->name ) . '"',
	'alt="' . esc_attr( $brand->name ) . '"',
);

$height = absint( $logo[ 'height' ] );
if ( $logo[ 'width' ] ) {
	$logo_attrs[] = 'width="' . absint( $logo[ 'width' ] ) . '"';
}
if ( $logo[ 'height' ] ) {
	$logo_attrs[] = 'height="' . absint( $logo[ 'height' ] ) . '"';
}
if ( isset( $logo[ 'src_2x' ] ) && $logo[ 'src_2x' ] ) {
	$logo_attrs[] = 'srcset="' . esc_attr( $logo[ 'src_2x' ] ) . '"';
}

$logo_html = '<img ' . join( ' ', $logo_attrs ) . '/>';
$url = boombox_get_term_meta( $brand->term_id, 'brand_url' );
if( $url ) {
	$logo_html = sprintf( '<a href="%s" target="_blank" rel="nofollow noopener">%s</a>', esc_url( $url ), $logo_html );
}
$presentation_text = boombox_get_theme_option( 'single_post_sponsored_articles_label' );

if( $presentation_text || $logo_html ) {
	echo Boombox_Template::get_clean( 'before' ); ?>
	<div class="brand-content">
		<?php if( $presentation_text ) { ?>
		<div class="brand-content-col byline-col">
			<span class="brand-byline bb-sec-label"><?php esc_html_e( $presentation_text, 'boombox' ); ?></span>
		</div>
		<?php } ?>
		<div class="brand-content-col logo-col">
			<?php echo $logo_html; ?>
		</div>

		<?php
		$description = term_description( $brand, 'brand' );
		if( $description ) { ?>
		<div class="brand-content-col desc-col"><?php echo $description; ?></div>
		<?php } ?>
	</div>
	<?php echo Boombox_Template::get_clean( 'after' );
}