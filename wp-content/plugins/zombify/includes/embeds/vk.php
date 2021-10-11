<?php
/**
 * Zombify VK Class
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !class_exists("Zombify_VK_Embed") ) {

    class Zombify_VK_Embed extends Zombify_Embed {

        public static function createEmbed( $url, $host, $cached_data, $view, $ajax, $post_id )
        {
        	$embed     = [];
            $embed['thumbnail'] = 'https://hms.lostcut.net/vk/logo.png';

            $url        = trim( $url );
            $url        = rtrim($url, '/');
        	$video_type = 'VK';

        	if( strpos( $url, 'hash' ) !== false ) {

                if( zombify()->amp()->is_amp_endpoint() ) {

                    $embed['html'] = '<a target="_blank" href="' . trim( $url ) . '" rel="noopener">' . trim( $url ) . '</a>';

                } else {

                    $embed['html'] = '<div class="zf-embed-cont"><iframe src="' . $url . '" width="600px" height="400px" frameborder="0" allowfullscreen></iframe></div>';

                }


        	} else {
		        esc_attr( __("http://example.com", "zombify"));
        		$embed['html'] 			= '<p class="zf-note">'
		                                    . esc_attr( __( 'Seems like you are trying to embed video form', 'zombify' ) )
		                                    . ' <a href="vk.com" target="_blank" rel="noopener">vk.com</a>, '
		                                    . esc_attr( __( 'please use embed code instead of direct URL', 'zombify' ) )
		                                    . '.</p>';
                $embed['thumbnail']     = '';
        		$embed['reset'] 	    = true;

        	}

        	$embed['type'] 			= $video_type;
        	$embed['variables']     = '';
            $embed['url']           = $url;

        	return $embed;
        }

    }

}