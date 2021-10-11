<?php
/**
 * Template part to render single post title
 * @since 1.0.0
 * @version 2.5.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
the_title( '<h1 class="entry-title s-post-title bb-mb-el" itemprop="headline">', '</h1>' );