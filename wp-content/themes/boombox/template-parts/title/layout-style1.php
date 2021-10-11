<?php
/**
 * Template for displaying template header's "Style 1" template
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.5.0
 *
 * @var $template_helper Boombox_Title_Template_Helper Template helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$template_helper = Boombox_Template::init( 'title' );
$template_options = $template_helper->get_options();

if(
	$template_options[ 'title' ]
	|| $template_options[ 'badge' ]
	|| $template_options[ 'sub_title' ]
	|| $template_options[ 'filters' ]
	|| $template_options[ 'breadcrumb' ]
) { ?>
<header class="container bb-page-header style-corner-narrow <?php echo $template_options[ 'class' ][ 'primary' ]; ?>">
	<div class="container-bg rmv-b-r-mobile <?php echo $template_options[ 'class' ][ 'secondary' ]; ?>">
		<div class="container-inner">

			<?php
			// Breadcrumb
			if( $template_options[ 'breadcrumb' ] ) {
				boombox_get_template_part( 'template-parts/breadcrumb', '', array(
					'before' => '<nav class="header-breadcrumb bb-breadcrumb mb-xs bb-mb-el clr-style1">',
					'after'  => '</nav>'
				) );
			}

			if( $template_options[ 'title' ] || $template_options[ 'badge' ] || $template_options[ 'sub_title' ] || $template_options['trending_nav'] || $template_options[ 'filters' ] ) { ?>
			<!-- Header Content -->
			<div class="header-content">
				<div class="header-content-layout">
					<div class="row-col col1">
						<?php if ( $template_options[ 'title' ] || $template_options[ 'badge' ] ) { ?>
							<div class="page-title-block">
								<?php if ( $template_options[ 'title' ] ) { ?>
									<h1 class="page-title"><?php echo $template_options[ 'title' ]; ?></h1>
								<?php } ?>
								<?php echo $template_options[ 'badge' ]; ?>
							</div>
						<?php }
						if ( $template_options[ 'sub_title' ] ) { ?>
							<h2 class="page-subtitle"><?php echo $template_options[ 'sub_title' ]; ?></h2>
						<?php } ?>
					</div>

					<?php if( $template_options['trending_nav'] ) { ?>
					<div class="row-col col2">
						<?php boombox_get_template_part( 'template-parts/title/navigation', 'trending' ); ?>
					</div>
					<?php } ?>

					<?php if ( $template_options[ 'filters' ] ) { ?>
						<div class="row-col col2">
							<?php boombox_get_template_part( 'template-parts/title/filter' ); ?>
						</div>
					<?php } ?>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</header>
<?php }