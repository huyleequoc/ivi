<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}
?>
        <!-- Main Footer -->
        <footer class="main-footer text-center">
            <p class="copy-right">&copy;
                <?php printf( __( '%1$s %2$s', 'boombox' ),
                    date( 'Y' ),
                    wp_kses_post( $this->get( 'boombox_footer_settings' )->footer_text )
                ); ?></p>
        </footer>

        <?php do_action( 'amp_post_template_footer', $this ); ?>

    </body>
</html>