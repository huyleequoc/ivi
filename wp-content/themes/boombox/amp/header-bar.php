<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

$home_url     = $this->get( 'home_url' );
$boombox_logo = boombox_amp()->get_logo( (int)$this->get_customizer_setting( 'boombox_logo' ), (int)$this->get_customizer_setting( 'boombox_logo_hdpi' ) );
$shadow_position = boombox_get_theme_option( 'header_layout_shadow_position' );
$header_classes = array( 'main-header', 'm-b-md' );
if( $shadow_position !== 'none' ) {
    $header_classes[] = 'btm-shadow';
}
?>
<!-- Main Header -->
<header class="<?php echo implode( ' ', $header_classes ); ?>">
    <div class="container row-to-table">
        <div class="logo col-to-cell vmiddle">
            <?php
                if ( ! empty( $boombox_logo ) ) {

                    $image_atts = array(
                        'src'    => esc_url( $boombox_logo['src'] ),
                        'alt'    => $this->get( 'blog_name' ),
                        'layout' => 'responsive'
                    );

                    $width  = absint( $boombox_logo['width'] );
                    if( $width ) {
                        $image_atts['width'] = $width;
                    }

                    $height = absint( $boombox_logo['height'] );
                    if( $height ) {
                        $image_atts['height'] = $height;
                    }
                    
                    if( $boombox_logo['src_2x'] ) {
	                    $image_atts['srcset'] = $boombox_logo['src_2x'];
                    }

                    printf( '<a href="%1$s">%2$s</a>', $home_url, boombox_amp()->render_image( $image_atts, false ) );
                } else {
                    printf( '<a href="%1$s" class="logo-txt hvr-opacity">%2$s</a>', $home_url, $this->get( 'blog_name' ) );
                }
            ?>
        </div>
        <div class="toggle-menu col-to-cell vmiddle hvr-opacity">
            <a href="#" on="tap:sidebar.toggle"><span class="icon toggle-icon icon-bars-cst"></span></a>
        </div>
    </div>
</header>
