<?php
/**
 * The template part for generating header types.
 *
 * @package Boombox_Theme
 * @since   1.0.0
 * @version 2.5.0
 *
 * @var Boombox_Header_Template_Helper $template_helper Template helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$template_helper = Boombox_Template::init( 'header' );
$template_options = $template_helper->get_options();

if ( $template_options[ 'top' ][ 'enable' ] || $template_options[ 'bottom' ][ 'enable' ] ) {
	echo $template_options[ 'before' ]; ?>

	<header class="<?php echo $template_options[ 'class' ]; ?>">

		<?php if ( $template_options[ 'top' ][ 'enable' ] ) {
			$left_classes = $template_helper->get_components_wrapper_classes(
				$template_options[ 'top' ][ 'components' ][ 'left' ], array( 'h-component', 'l-1' ) );

			$right_classes = $template_helper->get_components_wrapper_classes(
				$template_options[ 'top' ][ 'components' ][ 'right' ], array( 'h-component', 'r-1' ) );

			echo $template_options[ 'top' ][ 'before' ]; ?>

			<div class="<?php echo $template_options[ 'top' ][ 'class' ]; ?>">
				<div class="container">

					<?php if( ! empty( $template_options[ 'top' ][ 'components' ][ 'left' ] ) ) { ?>
					<div class="<?php echo $left_classes; ?>">
						<?php
						$template_helper->set_component_location( 'left' );
						foreach ( $template_options[ 'top' ][ 'components' ][ 'left' ] as $component ) {
							$slug = $template_helper->get_composition_item_template_slug( $component );
							if ( $slug ) {
								boombox_get_template_part( $slug );
							} else {
								do_action( 'boombox/header/render_composition_item/' . $component, 'top', 'left' );
							}
						} ?>
					</div>
					<?php } ?>

					<?php if ( 'top' == $template_options[ 'logo_position' ] ) { ?>
						<div class="h-component m-1 logo"><?php boombox_get_template_part( 'template-parts/header/branding' ); ?></div>
					<?php }

					if ( $template_options[ 'top' ][ 'has_menu' ] || $template_options[ 'top' ][ 'has_ad' ] ) { ?>
						<div class="h-component m-2">

							<?php if ( $template_options[ 'top' ][ 'has_menu' ] ) { ?>
								<div class="bb-header-navigation header-item">
									<?php
										Boombox_Template::set( 'header_top', array(
											'include_more_menu' => true
										) );
									boombox_get_template_part( 'template-parts/header/navigation/header', 'top' ); ?>
								</div>
							<?php }

							if ( $template_options[ 'top' ][ 'has_ad' ] ) {
								boombox_the_advertisement( 'boombox-inside-header', array( 'class' => 'large bb-inside-header' ) );
							} ?>
						</div>
					<?php } ?>

					<?php if( ! empty( $template_options[ 'top' ][ 'components' ][ 'right' ] ) ) { ?>
					<div class="<?php echo $right_classes; ?>">
						<?php
						$template_helper->set_component_location( 'right' );
						foreach ( $template_options[ 'top' ][ 'components' ][ 'right' ] as $component ) {
							$slug = $template_helper->get_composition_item_template_slug( $component );
							if ( $slug ) {
								boombox_get_template_part( $slug );
							} else {
								do_action( 'boombox/header/render_composition_item/' . $component, 'top', 'right' );
							}
						} ?>
					</div>
					<?php } ?>

				</div>

				<?php if ( 'top' == $template_options[ 'pattern_position' ] ) {
					boombox_get_template_part( 'template-parts/header/pattern' );
				} ?>

			</div>
			<?php
			echo $template_options[ 'top' ][ 'after' ];
		} ?>

		<?php if ( $template_options[ 'bottom' ][ 'enable' ] ) {

			$left_classes = $template_helper->get_components_wrapper_classes(
				$template_options[ 'bottom' ][ 'components' ][ 'left' ], array( 'h-component', 'l-1' ) );

			$right_classes = $template_helper->get_components_wrapper_classes(
				$template_options[ 'bottom' ][ 'components' ][ 'right' ], array( 'h-component', 'r-1' ) );

			echo $template_options[ 'bottom' ][ 'before' ]; ?>

			<div class="<?php echo $template_options[ 'bottom' ][ 'class' ]; ?>">
				<div class="container">

					<?php if( ! empty( $template_options[ 'bottom' ][ 'components' ][ 'left' ] ) ) { ?>
					<div class="<?php echo $left_classes; ?>">
						<?php
						$template_helper->set_component_location( 'left' );
						foreach ( $template_options[ 'bottom' ][ 'components' ][ 'left' ] as $component ) {
							$slug = $template_helper->get_composition_item_template_slug( $component );
							if ( $slug ) {
								boombox_get_template_part( $slug );
							} else {
								do_action( 'boombox/header/render_composition_item/' . $component, 'bottom', 'left' );
							}
						} ?>
					</div>
					<?php } ?>

					<?php if ( 'bottom' == $template_options[ 'logo_position' ] ) { ?>
						<div class="h-component m-1 logo"><?php boombox_get_template_part( 'template-parts/header/branding' ); ?></div>
					<?php }

					if ( $template_options[ 'bottom' ][ 'has_menu' ] || $template_options[ 'bottom' ][ 'has_ad' ] ) { ?>
						<div class="h-component m-2">

							<?php if ( $template_options[ 'bottom' ][ 'has_menu' ] ) { ?>
								<div class="bb-header-navigation header-item">
									<?php
										Boombox_Template::set( 'header_bottom', array(
											'include_more_menu' => true
										) );
									boombox_get_template_part( 'template-parts/header/navigation/header', 'bottom' ); ?>
								</div>
							<?php }

							if ( $template_options[ 'bottom' ][ 'has_ad' ] ) {
								boombox_the_advertisement( 'boombox-inside-header', array( 'class' => 'large bb-inside-header' ) );
							} ?>
						</div>
					<?php } ?>

					<?php if( ! empty( $template_options[ 'bottom' ][ 'components' ][ 'right' ] ) ) { ?>
					<div class="<?php echo $right_classes; ?>">
						<?php
						$template_helper->set_component_location( 'right' );
						foreach ( $template_options[ 'bottom' ][ 'components' ][ 'right' ] as $component ) {
							$slug = $template_helper->get_composition_item_template_slug( $component );
							if ( $slug ) {
								boombox_get_template_part( $slug );
							} else {
								do_action( 'boombox/header/render_composition_item/' . $component, 'bottom', 'right' );
							}
						} ?>
					</div>
					<?php } ?>

				</div>

				<?php if ( 'bottom' == $template_options[ 'pattern_position' ] ) {
					boombox_get_template_part( 'template-parts/header/pattern' );
				} ?>

			</div>
			<?php
			echo $template_options[ 'bottom' ][ 'after' ];
		} ?>

	</header>

	<?php
	echo $template_options[ 'after' ];
}

boombox_get_template_part( 'template-parts/header/mobile/bootstrap' );