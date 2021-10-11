<?php
$tw_url = 'https://twitter.com/intent/tweet?text='.esc_attr( __( 'I voted', 'zombify' ) ).'%20zombifyResult%20%22'.$question["question"].'%22%20+'.urlencode(get_permalink());
$fb_url = 'http://www.facebook.com/share.php?u='.urlencode(get_permalink()).'&quote='.esc_attr( __( 'I voted', 'zombify' ) ).'%20zombifyResult%20%22'.$question["question"].'%22%20';

zf_share_html( $tw_url, $fb_url );
