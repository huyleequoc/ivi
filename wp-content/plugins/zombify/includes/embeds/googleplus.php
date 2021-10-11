<?php
/**
 * Zombify GooglePlus Class
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !class_exists("Zombify_GooglePlus_Embed") ) {

    class Zombify_GooglePlus_Embed extends Zombify_Embed {

        public static $unique_html = false;

        public static function createEmbed( $url, $host, $cached_data, $view, $ajax, $post_id )
        {
            if( !parent::createEmbed( $url, $host, $cached_data, $view, $ajax, $post_id ) ) return false;

            $embed     = [];
            $thumbnail = '';

            if( !$cached_data ) {

                $url                = trim( $url );
                $url                = rtrim($url, '/');
                $url_array          = explode( '/', $url );
                $video_id           = end( $url_array );
                $video_id           = str_replace( "+", "", $video_id );
                $video_type         = 'GooglePlus';
                $elem_id            = uniqid() . $video_id;

                $file_contents      = file_get_contents("{$url}");
                preg_match('/property="og:image" content="(.*?)"/', $file_contents, $matches);
                $thumbnail          = ($matches[1]) ? $matches[1] : '';

            } else {

                $embed_variables    = zf_decode_data($cached_data['embed_variables']);
                $video_id           = $embed_variables['video_id'];
                $elem_id            = $embed_variables['elem_id'];
                $video_type         = $cached_data['embed_type'];

            }

            if( zombify()->amp()->is_amp_endpoint() ) {

                $embed['html'] = '<a target="_blank" href="' . trim( $url ) . '" rel="noopener">' . trim( $url ) . '</a>';

            } else {

                if( !self::$unique_html ) {

                    $embed['unique_html']   = '<script src="https://apis.google.com/js/platform.js?onload=onLoadCallback">{"parsetags": "explicit"}</script>';
                    self::$unique_html      = true;

                } else {

                    $embed['unique_html']   = '';

                }

                if( $ajax ) {

                   $embed['html']           = $embed['unique_html'] .
                                                '<div class="zf-embed-cont">
                                                    <div class="zf-google-plus-cont" id="' . $elem_id . '"></div>
                                                </div>
                                                <script>
                                                    window.onLoadCallback = function(){

                                                        try {
                                                            gapi.post.render("' . $elem_id . '", {"href" : "' . $url . '"} );
                                                        } catch (e) {
                                                            console.log(e);
                                                        }

                                                    }
                                                </script>';
                } else {

                    $embed['html']          = $embed['unique_html'] .
                                                '<div class="zf-embed-cont">
                                                    <div class="zf-google-plus-cont" id="' . $elem_id . '"></div>
                                                </div>
                                                <script>
                                                    try {
                                                        gapi.post.render("' . $elem_id . '", {"href" : "' . $url . '"} );
                                                    } catch (e) {
                                                        console.log(e);
                                                    }
                                                </script>';

                }

            }

            $embed['thumbnail']     = $thumbnail;
            $embed['type']          = $video_type;
            $embed['variables']     = zf_encode_data( ['elem_id' => $elem_id, 'video_id' => $video_id] );
            $embed['url']           = $url;

            if( !$cached_data && $view ) parent::saveEmbedCode( $embed );

            return $embed;
        }

    }

}