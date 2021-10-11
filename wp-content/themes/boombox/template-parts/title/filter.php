<?php
/**
 * Template for displaying filter within title template
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 *
 * @var $template_helper Boombox_Title_Template_Helper Template helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$template_helper = Boombox_Template::init( 'title' );
$template_options = $template_helper->get_options();

if ( ! empty( $template_options[ 'filter_data' ][ 'choices' ] ) ) { ?>
	<div class="cat-dropdown bb-toggle bb-dropdown">
		<div class="element-toggle dropdown-toggle dropdown-sm" data-toggle="#title-filter">
			<?php echo $template_options[ 'filter_data' ][ 'choices' ][ $template_options[ 'filter_data' ][ 'current' ] ][ 'label' ]; ?>
		</div>
		<div id="title-filter" class="toggle-content dropdown-content">
			<ul>
				<?php foreach ( $template_options[ 'filter_data' ][ 'choices' ] as $key => $choice ) { ?>
					<li class="<?php echo $choice[ 'active' ] ? 'active' : ''; ?>">
						<a href="<?php echo $choice[ 'url' ]; ?>"><?php echo $choice[ 'label' ]; ?></a>
					</li>
				<?php } ?>
			</ul>
		</div>
	</div>
<?php } ?>