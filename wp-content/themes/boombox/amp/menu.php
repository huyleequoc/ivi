<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
?>

<!-- Sidebar -->
<amp-sidebar id="sidebar" layout="nodisplay" side="<?php echo is_rtl() ? 'left' : 'right'; ?>" class="sidebar">
	<!-- Sidebar Header -->
	<div class="header">
		<a href="#" class="btn-close icn-lg" on="tap:sidebar.close"><span class="icon icon-close"></span></a>
	</div>

	<!-- Sidebar Content -->
	<div class="content">
		<!-- Sidebar Search Form -->
		<form method="GET" class="search-form clearfix m-b-md" action="<?php echo esc_url( home_url( '/' ) ); ?>"
		      target="_top">
			<div class="search-control pull-left">
				<input type="search" placeholder="<?php _e( 'Search...', 'boombox' ); ?>" name="s"
				       value="<?php echo esc_attr( get_search_query() ); ?>"/>
			</div>
			<div class="search-btn pull-left text-right">
				<button type="submit" class="icon icon-search btn-search icn-lg"></button>
			</div>
		</form>

		<?php
		if ( has_nav_menu( 'top_header_nav' ) || has_nav_menu( 'bottom_header_nav' ) || has_nav_menu( 'burger_top_nav' ) || has_nav_menu( 'burger_bottom_nav' ) ) {
			?>
			<nav class="main-nav m-b-md">
				<?php
				if ( has_nav_menu( 'top_header_nav' ) ) {
					wp_nav_menu( array(
						'theme_location' => 'top_header_nav',
						'container'      => false,
						'menu_class'     => 'main-menu',
						'walker'         => new Boombox_AMP_Nav_Menu(),
					) );
				}

				if ( has_nav_menu( 'bottom_header_nav' ) ) {
					wp_nav_menu( array(
						'theme_location' => 'bottom_header_nav',
						'container'      => false,
						'menu_class'     => 'main-menu',
						'walker'         => new Boombox_AMP_Nav_Menu(),
					) );
				}
				?>

				<?php if ( ( has_nav_menu( 'top_header_nav' ) || has_nav_menu( 'bottom_header_nav' ) ) && ( has_nav_menu( 'burger_top_nav' ) || has_nav_menu( 'burger_bottom_nav' ) ) ) { ?>
					<hr class="divide-h"/>
				<?php } ?>

				<?php
				if ( has_nav_menu( 'burger_top_nav' ) ) {
					wp_nav_menu( array(
						'theme_location' => 'burger_top_nav',
						'container'      => false,
						'menu_class'     => 'main-menu',
						'walker'         => new Boombox_AMP_Nav_Menu(),
					) );
				}

				if ( has_nav_menu( 'burger_bottom_nav' ) ) {
					wp_nav_menu( array(
						'theme_location' => 'burger_bottom_nav',
						'container'      => false,
						'menu_class'     => 'main-menu',
						'walker'         => new Boombox_AMP_Nav_Menu(),
					) );
				}
				?>
			</nav>
		<?php } ?>

		<?php
		$set = boombox_get_theme_options_set( array(
			'header_layout_button_link',
			'header_layout_button_text',
			'header_layout_button_plus_icon'
		) );

		if ( boombox_is_auth_allowed() && $set['header_layout_button_link'] ) { ?>
			<div class="text-center m-b-md">
				<?php echo boombox_get_create_post_button(
					array( 'bb-btn btn-default', 'hvr-btm-shadow' ),
					esc_html__( $set['header_layout_button_text'], 'boombox' ),
					$set['header_layout_button_plus_icon'],
					$set['header_layout_button_link']
				);
				?>
			</div>
		<?php } ?>

		<?php if ( function_exists( 'boombox_get_social_links' ) ) {
			$social_links = boombox_get_social_links( array( 'link_classes' => 'border-circle' ) ); ?>
			<!-- Sidebar Social -->
			<div class="bb-social circle hvr-opacity"><?php echo $social_links; ?></div>
		<?php } ?>
	</div>
	<!-- / Sidebar Content -->
</amp-sidebar>