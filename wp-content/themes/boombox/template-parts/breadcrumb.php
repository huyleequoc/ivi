<?php

/**
 * @var Boombox_Breadcrumb_Template_Helper $template_helper
 */
$template_helper = Boombox_Template::init( 'breadcrumb' );
$template_options = $template_helper->get_options();

$show_home = ( $template_options[ 'home' ][ 'label' ] || $template_options[ 'home' ][ 'icon' ] );

$home = $template_options[ 'home' ];
$items = ! empty( $template_options[ 'items' ] ) ?  $template_options[ 'items' ] : array();
$tail = $template_options[ 'tail' ];
$separator = $template_options[ 'separator' ] ? $template_options[ 'separator' ] : false;
$position = 0;

if( empty( $items ) && ! $tail ) {
	return;
}

echo Boombox_Template::get_clean( 'before' ); ?>
	<div class="breadcrumb-inner" role="navigation" aria-label="Breadcrumb">
		<ol itemscope itemtype="http://schema.org/BreadcrumbList">
			<?php if ( $show_home ) { ?>
				<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">

					<a class="ordinal-item" itemprop="item" href="<?php echo esc_url( $home[ 'url' ] ); ?>" <?php if ( $home[ 'icon' ] ) { echo 'title="' . $home[ 'label' ] . '"'; } ?>>
						<?php if ( $home[ 'icon' ] ) {
							echo $home[ 'icon' ];
						}

						if ( $home[ 'label' ] ) { ?>
							<span itemprop="name"><?php echo $home[ 'label' ]; ?></span>
						<?php } else { ?>
							<meta itemprop="name" content="<?php echo $home[ 'label' ]; ?>">
						<?php } ?>
					</a>
					<meta itemprop="position" content="<?php echo ++ $position; ?>" />
				</li>
			<?php }

			foreach ( $items as $i => $item ) { ?>
				<li class="ordinal-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
					<?php
					if( ( $show_home || ( !$show_home && 0 != $i ) ) && $separator ) {
						echo $separator;
					} ?>
					<a itemprop="item" href="<?php echo esc_url( $item[ 'url' ] ); ?>">
						<span itemprop="name"><?php echo ucfirst( $item[ 'label' ] ); ?></span>
					</a>
					<meta itemprop="position" content="<?php echo ++ $position; ?>" />
				</li>
			<?php }

			if ( $tail ) { ?>
				<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
					<?php echo $separator; ?>
					<a itemprop="item" href="<?php the_permalink(); ?>" class="last-item"><span itemprop="name"><?php echo $tail; ?></span></a>
					<meta itemprop="position" content="<?php echo ++ $position; ?>">
				</li>
			<?php } ?>

		</ol>
	</div>
<?php echo Boombox_Template::get_clean( 'after' ); ?>