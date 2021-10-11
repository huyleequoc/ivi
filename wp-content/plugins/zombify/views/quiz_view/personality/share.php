<?php
$title = get_the_title();

$tw_url = 'https://twitter.com/intent/tweet?text='
          . esc_attr( __( 'I got', 'zombify' ) )
          . '%20%22'
          . $result["result"]
          . '%22%20%7C%20'
          . zf_get_post_title_for_share( $title )
          . '+'
          . urlencode( get_permalink() );

$fb_url = 'http://www.facebook.com/share.php?u='
          . urlencode(get_permalink())
          . '&quote='
          . esc_attr( __( 'I got', 'zombify' ) )
          . '%20%22'
          . $result["result"]
          . '%22%20%7C%20'
          . zf_get_post_title_for_share( $title );

zf_share_html( $tw_url, $fb_url );
