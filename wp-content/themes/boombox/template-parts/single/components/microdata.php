<?php
/**
 * Template part to for single post microdata
 * @since 2.5.0
 * @version 2.5.0
 * @var $helper Boombox_Single_Post_Template_Helper Template helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$microdata = Boombox_Template::get_clean( 'microdata' );
if( empty( $microdata ) ) {
	return;
} ?>

<div class="s-post-microdata mf-hide">
	
	<?php if( $microdata[ 'thumbnail' ][ 'url' ] || $microdata[ 'thumbnail' ][ 'width' ] || $microdata[ 'thumbnail' ][ 'height' ] ) { ?>
	<span itemprop="image" itemscope="" itemtype="https://schema.org/ImageObject">
		<!-- featured image -->
		<meta itemprop="url" content="<?php echo esc_attr( esc_url( $microdata[ 'thumbnail' ][ 'url' ] ) ); ?>">
		<!-- featured image W -->
		<meta itemprop="width" content="<?php echo esc_attr( $microdata[ 'thumbnail' ][ 'width' ] ); ?>">
		<!-- featured image H -->
		<meta itemprop="height" content="<?php echo esc_attr( $microdata[ 'thumbnail' ][ 'height' ] ) ?>">
	</span>
	<?php } ?>

	<span itemprop="publisher" itemscope="" itemtype="https://schema.org/Organization">
		<?php if( $microdata[ 'publisher' ][ 'logo' ] ) { ?>
		<span itemprop="logo" itemscope="" itemtype="https://schema.org/ImageObject">
			<meta itemprop="url" content="<?php echo esc_attr( esc_url( $microdata[ 'publisher' ][ 'logo' ] ) ); ?>">
		</span>
		<?php } ?>
		<meta itemprop="name" content="<?php echo esc_attr( $microdata[ 'publisher' ][ 'blogname' ] ) ?>">
		<meta itemprop="url" content="<?php echo esc_attr( esc_url( $microdata[ 'publisher' ][ 'permalink' ] ) ); ?>">
	</span>
	
	<time itemprop="datePublished" datetime="<?php echo esc_attr( $microdata[ 'post' ][ 'published' ] ); ?>"><?php echo booombox_get_single_post_date( 'published' ); ?></time>
	<time itemprop="dateModified" datetime="<?php echo esc_attr( $microdata[ 'post' ][ 'updated' ] ); ?>"><?php echo booombox_get_single_post_date( 'modified' ); ?></time>
	<meta itemscope="" content="" itemprop="mainEntityOfPage" itemtype="https://schema.org/WebPage" itemid="<?php echo esc_attr( esc_url( $microdata[ 'post' ][ 'permalink' ] ) ); ?>">
</div>