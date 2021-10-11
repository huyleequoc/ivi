<?php
if( is_file( zombify()->locate_template( zombify()->quiz_view_dir($post_type.DIRECTORY_SEPARATOR.'share.php')) ) ){

    include zombify()->locate_template( zombify()->quiz_view_dir($post_type.DIRECTORY_SEPARATOR.'share.php'));

} else {

    zf_share_html();

}
?>