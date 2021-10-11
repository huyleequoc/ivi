<?php
/**
 * The template part for displaying the site trending navigation
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$boombox_queried_object = get_queried_object();
$boombox_trending_nav_items = boombox_get_trending_navigation_items();
$order = apply_filters( 'boombox/trending_nav_order', array( 'trending', 'hot', 'popular' ) );
if ( ! empty( $boombox_trending_nav_items ) ) { ?>
	<nav class="bb-trending-navigation">
		<ul>
			<?php foreach ( $order as $type ) {
			    if( ! isset( $boombox_trending_nav_items[ $type ] ) ) {
			        continue;
                }

				$item = $boombox_trending_nav_items[ $type ];
				$active = '';
				if (
					$boombox_queried_object
					&& ( 'page' == $boombox_queried_object->post_type )
					&& ( $item[ 'id' ] == $boombox_queried_object->ID )
				) {
					$active = 'active';
				} ?>
				<li class="<?php echo esc_attr( $active ); ?>">
					<a href="<?php echo esc_url( $item[ 'href' ] ); ?>">
						<i class="bb-icon bb-ui-icon-<?php echo esc_html( $item[ 'icon' ] ); ?>"></i>
						<?php echo esc_html( $item[ 'name' ] ); ?>
					</a>
				</li>
			<?php } ?>
		</ul>
	</nav>
<?php } ?>