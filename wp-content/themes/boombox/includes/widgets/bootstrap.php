<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * "Recent Posts" widget
 */
locate_template( 'includes/widgets/class-boombox-widget-recent-posts.php', true );

/**
 * "Trending Posts" widget
 */
locate_template( 'includes/widgets/class-boombox-widget-trending-posts.php', true );

/**
 * "Picked posts" widget
 */
locate_template( 'includes/widgets/class-boombox-widget-picked-posts.php', true );

/**
 * "Sticky sidebar" widget
 */
locate_template( 'includes/widgets/class-boombox-widget-sticky-sidebar.php', true );

/**
 * "Sidebar Footer" widget
 */
locate_template( 'includes/widgets/class-boombox-widget-sidebar-footer.php', true );

/**
 * "Create Post" widget
 */
locate_template( 'includes/widgets/class-boombox-widget-create-post.php', true );

/**
 * "Related posts" widget
 */
locate_template( 'includes/widgets/class-boombox-widget-related-posts.php', true );

/**
 * "Sidebar Navigation" widget
 */
locate_template( 'includes/widgets/class-boombox-widget-sidebar-nav.php', true );