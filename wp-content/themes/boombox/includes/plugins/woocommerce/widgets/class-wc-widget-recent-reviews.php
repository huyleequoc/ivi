<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Recent Reviews Widget.
 *
 * @author   WooThemes
 * @category Widgets
 * @package  WooCommerce/Widgets
 * @version  2.3.0
 * @extends  WC_Widget
 */
class Boombox_WC_Widget_Recent_Reviews extends WC_Widget_Recent_Reviews {

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	 public function widget( $args, $instance ) {

        global $comments, $comment;

        if ( $this->get_cached_widget( $args ) ) {
            return;
        }

        ob_start();

        $number   = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : $this->settings['number']['std'];
        $comments = get_comments( array( 'number' => $number, 'status' => 'approve', 'post_status' => 'publish', 'post_type' => 'product' ) );

        if ( $comments ) {
            $this->widget_start( $args, $instance );

            $html = '<ul class="product_list_widget">';

            foreach ( (array) $comments as $comment ) {

                $_product = wc_get_product( $comment->comment_post_ID );
                $comment_link = esc_url( get_comment_link( $comment->comment_ID ) );
                $reviewer = sprintf( '<span class="reviewer">' . _x( 'by %1$s', 'by comment author', 'woocommerce' ) . '</span>', get_comment_author() );

                $html .= sprintf('
                    <li>
                        <a href="%1$s" class="product-thumb">%2$s</a>
                        <div class="product-content">
                            %4$s
                            <a href="%1$s" class="product-title">%3$s</a>
                            %5$s
                        </div>
                    </li>',
                    $comment_link,
                    $_product->get_image(),
                    $_product->get_title(),
                    wc_get_rating_html( intval( get_comment_meta( $comment->comment_ID, 'rating', true ) ) ),
                    $reviewer
                );
            }

            $html .= '</ul>';

            echo $html;

            $this->widget_end( $args );
        }

		$content = ob_get_clean();

		echo $content;

		$this->cache_widget( $args, $content );
	}
}
