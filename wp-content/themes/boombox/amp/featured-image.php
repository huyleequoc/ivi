<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$featured_image = $this->get( 'featured_image' );

if ( empty( $featured_image ) ) {
	return;
}

$amp_html = $featured_image['amp_html'];
$caption = $featured_image['caption'];
?>

<figure class="post-featured-image m-b-sm wp-caption">
	<?php echo $amp_html; // amphtml content; no kses ?>
	<?php if ( $caption ) : ?>
		<p class="wp-caption-text">
			<?php echo wp_kses_data( $caption ); ?>
		</p>
	<?php endif; ?>
</figure>
