<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}
?>

<!doctype html>
<html amp <?php echo AMP_HTML_Utils::build_attributes_string( $this->get( 'html_tag_attributes' ) ); ?>>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
        <meta http-equiv="origin-trial" data-feature="Web Share" data-expires="2017-04-04" content="Ajcrk411RcpUCQ3ovgC8le4e7Te/1kARZsW5Hd/OCnW6vIHTs5Kcq1PaABs7SzcrtfvT0TIlFh9Vdb5xWi9LiQsAAABSeyJvcmlnaW4iOiJodHRwczovL2FtcGJ5ZXhhbXBsZS5jb206NDQzIiwiZmVhdHVyZSI6IldlYlNoYXJlIiwiZXhwaXJ5IjoxNDkxMzM3MDEwfQ==">
        <?php do_action( 'amp_post_template_head', $this ); ?>
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">
        <style amp-custom>
            <?php do_action( 'amp_post_template_css', $this ); ?>
            <?php $this->load_parts( is_rtl() ? array( 'style-rtl' ) : array( 'style' ) ); ?>
        </style>
    </head>

    <body class="<?php echo esc_attr( $this->get( 'body_class' ) ); ?>">

        <?php $this->load_parts( array( 'header-bar', 'menu' ) ); ?>