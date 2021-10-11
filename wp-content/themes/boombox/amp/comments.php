<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( comments_open() || get_comments_number() ) {
	do_action( 'boombox/amp/comments', $this );
} ?>