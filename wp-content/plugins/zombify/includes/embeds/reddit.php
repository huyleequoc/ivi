<?php
/**
 * Zombify Reddit Class
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !class_exists("Zombify_Reddit_Embed") ) {

    class Zombify_Reddit_Embed extends Zombify_Embed {

        public static function createEmbed( $url, $host, $cached_data, $view, $ajax, $post_id )
        {
            $embed      = [];
            $thumbnail  = 'http://www.wikipam.com/dataimage/imgs/reddit-logo.jpg';

            $url        = trim( $url );
            $url        = rtrim($url, '/');
            $video_type = 'Reddit';

            if( zombify()->amp()->is_amp_endpoint() ) {

                function zf_amp_reddit( $data ){
                    $scripts = array(
                        'amp-reddit' => 'https://cdn.ampproject.org/v0/amp-reddit-0.1.js',
                    );

                    $data["amp_component_scripts"] = array_merge($data["amp_component_scripts"], $scripts );
                    return $data;
                }

                add_filter('amp_post_template_data', 'zf_amp_reddit', 10, 2);

                $embed['html']      = '<div class="zf-embed-cont">
                                            <amp-reddit
                                                layout="responsive"
                                                width="150"
                                                height="150"
                                                data-embedtype="post"
                                                data-src="' . $url . '?ref=share&amp;ref_source=embed">
                                            </amp-reddit>
                                        </div>';

            } else {

                $embed['html']      = '<div class="zf-embed-cont">' . parent::getWpEmbedCode( $url, $ajax, $post_id, $view ) . '</div>';

            }

            $embed['thumbnail']     = $thumbnail;
            $embed['type']          = $video_type;
            $embed['variables']     = '';
            $embed['url']           = $url;

            return $embed;
        }

    }

}