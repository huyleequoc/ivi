<?php
/**
 * The template part for displaying "Next/Prev buttons" pagination.
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$has_prev = ( NULL !== get_previous_posts_link() );
$has_next = ( NULL !== get_next_posts_link() );

if ( $has_prev || $has_next ) { ?>
	<nav class="bb-next-prev-pagination page-next-prev-pg pg-lg" role="navigation">
		<ul class="pg-list">
			
			<?php
				$url = $has_prev ? get_previous_posts_page_link() : '#';
				$class = 'pg-item page-nav prev-page';
				if( ! $has_prev ) {
					$class .= ' bb-disabled';
				} ?>
			<li class="<?php echo esc_attr( $class ); ?>">
		        <a href="<?php echo esc_url( $url ); ?>" class="prev-page-link page-link" rel="prev">
		            <i class="bb-icon bb-ui-icon-chevron-left"></i>
		            <span class="text big-text"><?php esc_html_e( 'Previous', 'boombox' ); ?></span>
		            <span class="text small-text"><?php esc_html_e( 'Previous', 'boombox' ); ?></span>
		        </a>
		    </li>
			
			<?php
			$url = $has_next ? get_next_posts_page_link() : '#';
			$class = 'pg-item page-nav next-page';
			if( ! $has_next ) {
				$class .= ' bb-disabled';
			} ?>
			<li class="<?php echo esc_attr( $class ); ?>">
				<a href="<?php echo esc_url( $url ); ?>" class="next-page-link page-link" rel="next">
		            <i class="bb-icon bb-ui-icon-chevron-right"></i>
		            <span class="text big-text"><?php esc_html_e( 'Next', 'boombox' ); ?></span>
		            <span class="text small-text"><?php esc_html_e( 'Next', 'boombox' ); ?></span>
		        </a>
		    </li>
			
		</ul>
	</nav>
<?php } ?>