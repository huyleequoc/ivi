<?php
/**
 * The template part for displaying a message that posts cannot be found
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
} ?>

<section class="no-results not-found">
	<header class="page-header">
		<h1 class="page-title"><?php esc_html_e( 'Nothing Found', 'boombox' ); ?></h1>
	</header>

	<div class="page-content">
		<?php if ( is_search() ) { ?>

			<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'boombox' ); ?></p>

		<?php } else { ?>

			<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. ', 'boombox' ); ?></p>

		<?php } ?>
	</div>
</section>
