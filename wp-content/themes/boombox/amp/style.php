<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/* Options and Theme Colors */

// General Options from AMP Customizer
$header_txt_clr = $this->get_customizer_setting( 'header_color' );                                                  // header text color
$header_bg_clr = $this->get_customizer_setting( 'header_background_color' );                                        // header bg color
$light_mode = ( $this->get_customizer_setting( 'color_scheme' ) == 'light' );                                       // color scheme mode

// Logo Width
$logo = boombox_amp()->get_logo( (int)$this->get_customizer_setting( 'boombox_logo' ), (int)$this->get_customizer_setting( 'boombox_logo_hdpi' ) );
$logo_width = false;
if( ! empty( $logo ) )
	$logo_width = absint( $logo['width'] );
$logo_max_width = ($logo_width > 400 ? 400 : $logo_width)."px";
$logo_max_width_mobile = ($logo_width > 200 ? 200 : $logo_width)."px";

// Options from Global Customizer
$primary_clr = $this->get_customizer_setting( 'design_primary_color' );                                             // primary color
$primary_txt = $this->get_customizer_setting( 'design_primary_text_color' );                                        // primary text color
$link_clr = $this->get_customizer_setting( 'design_link_text_color' );                                              // link color
$badge_clr = $this->get_customizer_setting( 'extras_badges_reactions_background_color' );                           // badges background color
$global_brd_radius = $this->get_customizer_setting( 'design_border_radius' );                                       // border-radius
$global_input_btn_brd_radius = $this->get_customizer_setting( 'design_inputs_buttons_border_radius' );              // input / btn border-radius
$constant_clrs = $this->get_customizer_setting( 'constants' );		                                                // predefined constant colors

// General Options
$footer_bg_clr = $constant_clrs['footer_bg_clr'];

// Dark Mode Options
$dark_bg_clr = $constant_clrs['dark_bg_clr'];
$dark_border_clr = $constant_clrs['dark_border_clr'];
$dark_txt_clr = $constant_clrs['dark_txt_clr'];
$dark_sec_txt_clr = $constant_clrs['dark_sec_txt_clr'];
$dark_sec_bg_clr = $constant_clrs['dark_sec_bg_clr'];

// Light Mode Options
$light_bg_clr = $constant_clrs['light_bg_clr'];
$light_border_clr = $constant_clrs['light_border_clr'];
$light_txt_clr = $constant_clrs['light_txt_clr'];
$light_sec_txt_clr = $constant_clrs['light_sec_txt_clr'];
$light_sec_bg_clr = $constant_clrs['light_sec_bg_clr'];
$light_sidebar_border_clr = $constant_clrs['light_sidebar_border_clr'];

/* Content Width */
$content_max_width = "758px";

/* Spacings */
$container_spacing = "10px";
$spacing_md = "30px";
$spacing_md_1 = "15px"; // For the case if we want to achieve md spacing with two spacings
$spacing_md_2 = "25px"; // For the case when element itself has spacing
$spacing_sm = "16px";
$spacing_lg ="60px";
$spacing_post_content ="40px";

/* Breakpoints */
// xxs- [0, 479], xs- [480, 767], sm- [768, 991], md- [992, 1199], lg- [1200, +]
$screen_xxs_max = "479px";
$screen_xs_min = "480px";
$screen_xs_max = "767px";
$screen_sm_min = "768px";
$screen_boxed_max = "700px";

?>

<?php // NOTE: Please pay attention that the last style in css class doesn't have separator. This is specific style for AMP for reducing css size  ?>

/** Boombox AMP Styles **/

/** 1.0 - Fonts **/
<?php include "fonts/icomoon/style.php";?>

/** 2.0 - Reset **/
*, *:before, *:after {
	-webkit-box-sizing: border-box;
	   -moz-box-sizing: border-box;
	        box-sizing: border-box
}
*:focus, *:active {
	outline: 0
}
* {
	-webkit-tap-highlight-color: transparent
}

/** 3.0 - Editor Styles **/
<?php include "editor-styles.php";?>

/** 4.0 - Containers and Spacing **/
html, body {
	width: 100%;
	height: 100%;
	font-family: 'Open Sans', sans-serif;
	font-size: 17px;
	line-height: normal;
	-webkit-overflow-scrolling: touch;
	  -webkit-text-size-adjust: 100%;
	      -ms-text-size-adjust: 100%;
	letter-spacing: 0px;
	line-height: 1.5
}
.container {
    max-width: <?php echo $content_max_width; ?>;
    padding: 0 <?php echo $container_spacing?>;
    margin: auto auto;
	width: 100%
}
.row {
	margin-left: -10px;
	margin-right: -10px
}
.col-main {
	padding: 0 15px;
	width: 100%;
	float: left
}

.col-sec-wrapper-sm, .col-sec-wrapper-sm .col-sec {
	width: 100%;
	float: left;
	text-align: left
}

.col-sec-wrapper {
	display: table;
	width: 100%;
	height: 100%
}
.col-sec-wrapper .col-sec {
	display: table-cell;
	height: 100%;
	width: 50%
}
.col-sec-wrapper .col-sec-2 {
	text-align: right
}
@media (min-width: <?php echo $screen_sm_min; ?>) {
	.col-main {
		width: 50%
	}
	.col-main:nth-child(2n+1) {
		clear: both
	}

	.col-sec-wrapper-sm, .col-sec-wrapper-sm .col-sec {
		float: none
	}
	.col-sec-wrapper-sm {
		display: table;
		width: 100%;
		height: 100%
	}
	.col-sec-wrapper-sm .col-sec  {
		display: table-cell;
		height: 100%
	}
	.col-sec-wrapper-sm .col-sec-1 {
		width: 66.66667%
	}
	.col-sec-wrapper-sm .col-sec-2 {
		width: 33.33333%;
		text-align: right
	}
}

/** 5.0 - Form **/
input[type='search'], input[type='text'], select {
	-webkit-appearance: none;
	outline: none;
	display: block;
	width: 100%;
	padding: 10px 24px;
	font-size: 14px;
	border: 1px solid transparent
}

/** 6.0 - Components **/
/* 6.1 - Badge List */
.bb-badge-list {
	margin: 0 -3px
}
.bb-badge-list .badge {
	width: 40px;
	height: 40px;
	line-height: 40px;
	font-size: 26px;
	vertical-align: middle;
	text-align: center;
	margin: 0 3px
}

/* 6.2 - Category Links */
.bb-cat-links {
	text-transform: uppercase;
	margin: 0
}
.bb-cat-links.links-lg {
	font-size: 18px;
	line-height: 20px;
	font-weight: 700
}
.bb-cat-links.links-sm {
	font-size: 12px;
	line-height: 13px
}
.bb-cat-links a {
	display: inline-block;
	margin: 0 10px 3px 0;
	font-size: inherit
}

/* 6.3 - Author Vcard Mini */
.bb-author-vcard-mini {
	display: inline-block
}
.bb-author-vcard-mini .author-name {
	display: inline
}
.bb-author-vcard-mini .avatar, .bb-author-vcard-mini .author-name, .bb-author-vcard-mini .posted-on, .bb-author-vcard-mini .byline {
	vertical-align: middle
}
.bb-author-vcard-mini.vcard-sm {
	font-size: 12px;
	line-height: 15px
}
.bb-author-vcard-mini.vcard-sm .author-name {
	font-size: 13px;
	line-height: 13px
}
.bb-author-vcard-mini.vcard-lg {
	font-size: 14px
}
.bb-author-vcard-mini.vcard-lg .author-name {
	font-size: 18px;
    line-height: 24px
}

/* 6.4 - Author Vcard */
.bb-author-vcard {
	text-align: center
}
.bb-author-vcard .author-vcard-inner {
	border: 1px solid <?php echo $light_border_clr;?>;
	overflow: hidden
}
.bb-author-vcard .avatar, .bb-author-vcard .website-url {
	display: inline-block
}
.bb-author-vcard .avatar {
	margin: 15px 15px 0;
	padding-top: 0;
	width: 133px;
	height: 133px;
	-webkit-box-shadow: -1px 2px 2px rgba(0, 0, 0, 0.18) ;
       -moz-box-shadow: -1px 2px 2px rgba(0, 0, 0, 0.18) ;
	        box-shadow: -1px 2px 2px rgba(0, 0, 0, 0.18)
}
.bb-author-vcard .header-info {
	overflow: hidden;
	padding: 15px
}
.bb-author-vcard .byline {
	display: block;
	font-size: 12px;
	line-height: 1.5;
	font-weight: normal
}
.bb-author-vcard .author-name {
	display: block;
	font-size: 24px;
	line-height: 26px;
	font-weight: 600;
	text-transform: none;
	margin: 0 0 5px 0
}
.bb-author-vcard .website-url {
	font-size: 14px;
	text-decoration: underline;
	margin-bottom: 5px
}
.bb-author-vcard .bb-social {
	width: 100%
}

.bb-author-vcard .author-info {
	padding: 25px 30px 30px;
	text-align: justify
}
@media (min-width: <?php echo $screen_sm_min; ?>) {
	.bb-author-vcard, .bb-author-vcard .author-info {
		text-align: left
	}
	.bb-author-vcard .avatar {
		position: relative;
		margin: 0 15px;
		bottom: -20px
	}
	.bb-author-vcard .header-info {
		padding: 17px 15px 15px 30px
	}
	.bb-author-vcard .author-info {
		padding: 40px 40px 25px
	}
}

.bb-author-vcard.no-author-info .author-info {
	display: none
}
@media (min-width: <?php echo $screen_sm_min;?>) {
	.bb-author-vcard.no-author-info .avatar {
		bottom: 0;
		margin: 15px
	}
}

/* 6.5 - Social */
.bb-social ul, .bb-social li, .bb-social.circle a {
	display: inline-block
}
.bb-social ul {
	margin: 0;
	padding: 0
}
.bb-social li {
	margin: 0 10px;
	list-style: none;
	line-height: 16px
}
.bb-social li, .bb-social li a {
	vertical-align: middle
}
.bb-social.circle a {
	width: 42px;
	height: 42px;
	font-size: 18px;
	text-align: center;
	line-height: 42px
}
.bb-social.circle li {
	margin: 0 5px 10px 5px
}
.bb-social.default li {
	margin: 0 10px;
	line-height: 16px
}
@media (min-width: <?php echo $screen_sm_min;?>) {
	.bb-social.default li {
		margin: 0 20px 0 0
	}
	.bb-social.default li:last-child {
		margin-right: 0
	}
}

/* 6.6 - Views */
.bb-meta-itm {
	display: inline-block
}
.bb-meta-itm.itm-lg {
	font-size: 22px;
	font-weight: bold
}
.bb-meta-itm.itm-lg .icon {
	position: relative;
	top: 1px;
	font-size: 24px;
	line-height: 24px;
	vertical-align: middle
}
.bb-meta-itm.itm-lg .count {
	vertical-align: middle;
	line-height: 21px
}

.bb-meta-itm.itm-sm {
	font-size: 12px;
	line-height: 16px;
	vertical-align: middle
}
.bb-meta-itm.itm-sm .icon {
	font-size: 16px
}

/* 6.7 - Tags */
.bb-tags a {
	float: left;
	margin: 0 3px 5px 0;
	padding: 7px 13px 7px;
	font-size: 13px;
	line-height: 15px;
	font-weight: 700;
	color: inherit;
	text-transform: uppercase;
	border: 3px solid <?php echo $light_border_clr; ?>
}

/* 6.8 - Buttons and Icons */
.bb-btn {
	display: inline-block;
	font-weight: bold;
	text-transform: uppercase
}
.bb-btn.btn-default {
	padding: 13px 50px;
	font-size: 15px
}
.bb-btn.btn-default.btn-xs {
	padding: 6px 12px;
	font-size: 14px
}
.bb-btn.btn-primary {
	height: 48px;
	position: relative;
	padding: 0 30px
}
.bb-btn.btn-primary .icon {
	font-size: 25px;
	position: absolute;
	top: 12px
}
.bb-btn.btn-primary .icon.icn-left {
	left: 20px
}
.bb-btn.btn-primary .icon.icn-right {
	right: 20px
}
.bb-btn.btn-primary .text {
	font-size: 18px;
	line-height: 48px;
	padding: 0 20px
}
.icn-lg {
	font-size: 25px
}
.icn-sm {
	font-size: 14px
}
.icn-xs {
	font-size: 12px
}

/* 6.9 - Labels */
.bb-label {
	display: inline-block;
	padding: 1px 4px;
	line-height: 10px;
	font-size: 8px;
	border-radius: 2px;
	text-transform: uppercase;
	letter-spacing: 1px;
	font-weight: 600
}


/* 6.10 - Headings */
.bb-entry-header {
	padding-bottom: 16px;
	font-size: 1.5rem;
	font-weight: normal;
	margin: 0 0 40px 0
}
.bb-entry-header, .bb-entry-header a {
	text-transform: uppercase
}

/* 6.11 - Price Block */
.bb-price-block {
	display: inline-block;
	line-height: 18px
}
.bb-price-block .current-price-txt {
	font-size: 18px;
	font-weight: 700
}
.bb-price-block .old-price {
	font-size: 12px;
	font-weight: 400;
	text-decoration: line-through;
	padding-left: 24px
}
.bb-price-block .icon {
	font-size: 16px
}

/** 7.0 - General Styles **/
/* 7.1 - Main Header */
.main-header {
	padding: 20px 0
}
.main-header .logo-txt {
	font-weight: 600
}
.main-header .toggle-menu {
	width: 55px;
	text-align: right
}
.main-header .toggle-icon {
	font-size: 2.2rem
}
.main-header .logo {
	font-size: 1.5rem
}
<?php if($logo_width) { ?>
.main-header .logo amp-img {
	max-width: <?php echo $logo_max_width_mobile;?>
}
<?php }?>
@media (min-width: <?php echo $screen_sm_min; ?>) {
	<?php if($logo_width){ ?>
	.main-header .logo amp-img {
		max-width: <?php echo $logo_max_width;?>
	}
	<?php } ?>
	.main-header .logo {
		font-size: 2.3rem
	}
	.main-header .toggle-icon {
		font-size: 2.5rem
	}
}

/* 7.2 - Post */
.post .post-header *:last-child {
	margin-bottom: 0
}
.post .post-content {
	margin-bottom: <?php echo $spacing_post_content;?>
}
.post .post-content amp-img {
	height: auto
}
.post .post-summary {
	font-size: 1.25rem
}

@media screen and (max-width: <?php echo $screen_boxed_max; ?>) {
	.post .post-featured-image {
		max-width: inherit;
	}
	.post-featured-image amp-img {
		border-radius: 0px
	}
}

/* 7.3 - Page Navigation */
.page-nav .page-nav-list {
	list-style: none
}
.page-nav .page-nav-itm .header {
	padding-bottom: 12px;
	margin: 0;
	font-weight: bold;
	font-size: 14px;
	text-transform: uppercase
}
.page-nav .page-nav-itm .content {
	padding: 20px 0
}
.page-nav .page-nav-itm .title {
	font-size: 18px
}
.page-nav .page-nav-itm .author-name {
	margin: 5px 0 0 0
	font-size: 13px
}
.page-nav .page-nav-itm .page-info {
	overflow: hidden;
	padding-left: 25px
}

/* 7.4 - Post List: Two Col Layout */
.post-list.two-col-layout .post-thumbnail {
	margin-bottom: 15px;
	position: relative
}
.post-list.two-col-layout .bb-cat-links, .post-list.two-col-layout .post-title {
	margin: 0 0 12px 0
}
.post-list.two-col-layout .post-title {
	font-size: 1.375rem
}
.post-list.two-col-layout .post-summary p {
	margin: 0 0 8px 0
}
.post-list.two-col-layout .divide-h {
	margin: 0 0 5px 0
}
.post-list.two-col-layout .bb-meta-itm {
	padding: 4px 14px;
	border-radius: 25px
}

.post-list.two-col-layout .post-meta {
	position: absolute;
	right: 15px;
	bottom: 15px;
	z-index: 100
}

/* 7.5 - Post List: One Col Layout */
.post-list.one-col-layout .post-itm {
	width: 100%
}
.post-list.one-col-layout .bb-cat-links {
	margin: 0 0 8px 0
}
.post-list.one-col-layout .post-title {
	margin: 0
}

/* 7.6 - Sidebar */
.sidebar {
	width: 300px
}
.sidebar .btn-close {
	display: inline-block
}
.sidebar .content {
	padding: 0 20px 30px 20px
}
.sidebar .header {
	padding: 15px
}
.sidebar .bb-social {
	text-align: center
}
@media (min-width: <?php echo $screen_sm_min; ?>) {
	.sidebar {
		width: 464px
	}
	.sidebar .content {
		padding: 0 30px 40px 40px
	}
}

/* 7.7 - Search Form */
.search-form .search-control {
	width: 80%
}
.search-form .search-btn {
	width: 20%;
	padding: 5px 15px
}
.search-form .btn-search {
	background: transparent;
	border: none;
	font-size: 25px
}

/* 7.8 - Main Navigation */
.main-nav .main-menu {
	font-size: 18px;
	line-height: 26px;
	padding: 0;
	margin: 0
}
.main-nav .divide-h {
	margin: 15px 0
}
.main-nav .main-menu li {
	padding: 0;
	list-style-type: none;
	margin: 7px 0
}
.main-nav .sub-menu {
	padding-left: 20px;
	font-size: 14px;
	line-height: 22px;
	margin: 0
}

/* 7.9 - Main Footer */
.main-footer {
	padding: 20px 15px
}
.main-footer p {
	margin: 0
}

/* 7.10 - Next/ Prev Pagination */
.next-prev-pagination ul {
	margin: 0
}
.next-prev-pagination li.page-nav {
	list-style: none;
	width: 40%
}
.next-prev-pagination .nav-link .text {
	display: none
}
.next-prev-pagination .pages {
	line-height: 48px;
	font-weight: bold
}
.next-prev-pagination .all-pages {
	font-size: 20px
}
.next-prev-pagination .cur-page {
	font-size: 25px
}
.next-prev-pagination .pages .text {
	line-height: 25px
}
@media screen and (min-width: <?php echo $screen_sm_min; ?>) {
	.next-prev-pagination .nav-link .text {
		display: inline
	}
	.next-prev-pagination .nav-link {
		width: 100%
	}
}

/** 8.0 - Widgets **/

/* 8.1 - Mashshare */
.mashsb-container.mashsb-main {
	padding: 0;
	margin: <?php echo $spacing_md;?>
}
@media (max-width: <?php echo $screen_xs_max; ?>) {
	.mashsb-container .mashsb-count {
		font-size: 30px
	}
}
@media (min-width: <?php echo $screen_sm_min; ?>) {
	.mashsb-container .mashsb-buttons {
		display: -webkit-flex;
		display: -ms-flexbox;
		display: flex;
		-webkit-flex-wrap: wrap;
            -ms-flex-wrap: wrap;
		        flex-wrap: wrap;
	-webkit-align-items: flex-start;
	     -ms-flex-align: start;
            align-items: flex-start
	}
	.mashsb-container .mashsb-buttons a {
	 	-webkit-flex: 1 1 auto;
	    	-ms-flex: 1 1 auto;
			    flex: 1 1 auto;
		min-width: 0
	}
}

/* 8.2 - Easy Social Share */
.essb_t_nb_after, .essb_links .essb_network_name {
	margin: 0 0 0 7px
}
<?php
if( boombox_plugin_management_service()->is_plugin_active( 'easy-social-share-buttons3/easy-social-share-buttons3.php' ) ) {
	include "easy-social-plugin-styles.php";
} ?>

/** 9.0 - Hover and Shadow Effects **/
.hvr-btm-shadow, .hvr-opacity a, a.hvr-opacity {
	-webkit-transition: all .1s ease-out;
	   -moz-transition: all .1s ease-out;
	        transition: all .1s ease-out
}
.hvr-btm-shadow:hover, .hvr-btm-shadow:focus, .btm-shadow {
	-webkit-box-shadow: 1px 1px 2px rgba(0,0,0,.18);
	   -moz-box-shadow: 1px 1px 2px rgba(0,0,0,.18);
	        box-shadow: 1px 1px 2px rgba(0,0,0,.18)
}
.hvr-opacity a:hover, .hvr-opacity a:focus, a.hvr-opacity:hover, a.hvr-opacity:focus {
	opacity: 0.8
}
.bb-cat-links a:hover, .post .post-content a:hover, .bb-author-vcard-mini .author-name:hover, .bb-author-vcard .author-info a:hover, .bb-entry-header a:hover {
	text-decoration: underline
}
.post .post-content .instagram-media a:hover {
	text-decoration: none
}

/** 10.0 - Helper Classes **/
/* Making col table-cell */
.row-to-table {
	display: table;
	width: 100%;
	table-layout: fixed;
	height: 100%
}
.col-to-cell {
	display: table-cell;
	height: 100%
}
@media screen and (max-width: 768px) {
    .bb-table-responsive tr, .bb-table-responsive td, .bb-table-responsive th {
    display: block;
    width: 100%;
    }
    .bb-table-responsive thead, .bb-table-responsive tfoot {
    display: none;
    }
}
    .responsive-table {
    white-space: nowrap;
    overflow-x: auto;
    }
    .responsive-table table {
    table-layout: auto;
    }

    /* Making Col Inline Block */
.col-inl-blck {
	display: inline-block
}

/* Floating */
.pull-left {
	float: left
}
.pull-right {
	float: right
}
@media (min-width: <?php echo $screen_sm_min;?>) {
	.pull-left-sm {
		float: left
	}
	.pull-right-sm {
		float: right
	}
}

/* Alignments */
.vmiddle {
	vertical-align: middle
}
.text-left {
	text-align: left
}
.text-right {
	text-align: right
}
.text-center {
	text-align: center
}

/* Clearfix */
.clearfix {
	clear: both
}
.clearfix:after {
	clear: both;
	content: "";
	display: block
}

/* Borders */
.border-circle {
	-webkit-border-radius: 50%;
	        border-radius: 50%
}

/* Widths */
.w-full {
	width: 100%
}

/* Spacings */
.m-r-xs {
	margin-right: 5px
}
.m-l-xs {
	margin-left: 5px
}
.m-r-sm {
	margin-right: 10px
}
.m-l-md {
	margin-left: 15px
}

.m-b-md {
	margin-bottom: <?php echo $spacing_md;?>
}
.m-b-md-1 {
	margin-bottom: <?php echo $spacing_md_1;?>
}
.m-b-md-2 {
	margin-bottom: <?php echo $spacing_md_2;?>
}
.m-b-sm {
	margin-bottom: <?php echo $spacing_sm;?>
}
.m-b-sm-1 {
	margin: 0 0 <?php echo $spacing_sm;?> 0
}
.m-b-lg {
	margin-bottom: <?php echo $spacing_lg;?>
}

/** 11.0 - Options and Colors **/
/* Badge and Label Colors */
.bb-label, .bb-badge-list .badge.trending, .post-list.two-col-layout .post-view-count {
	color: #fff
}
.bb-label.hot, .bb-badge-list .badge.trending {
	background-color: #f43748
}
.bb-label.new {
	background-color: #2fa949
}
.post-list.two-col-layout .post-view-count {
	background-color: rgba(0, 0, 0, 0.5)
}
.post-list.two-col-layout .post-share-count {
	background-color: rgba(255, 255, 255, 0.5);
	color: #1f1f1f
}

/* Global Options */
.main-header {
	background-color: <?php echo $header_bg_clr; ?>
}
.main-header a {
	color: <?php echo $header_txt_clr;?>
}
a {
	color: <?php echo $link_clr; ?>
}
.bb-btn, blockquote:not(.instagram-media):before {
	background-color: <?php echo $primary_clr; ?>;
	color: <?php echo $primary_txt; ?>
}
.bb-price-block .icon {
	color: <?php echo $primary_clr; ?>
}
.sidebar {
	background-color: <?php echo $dark_bg_clr;?>
}
.main-footer {
	background-color: <?php echo $footer_bg_clr;?>
}
.sidebar, .sidebar .icon, .main-nav a, .main-footer, .search-form .btn-search {
	color: <?php echo $dark_txt_clr;?>
}
.bb-badge-list .badge.reaction {
	background: <?php echo $badge_clr; ?>
}
amp-img, amp-anim, .bb-tags a, .bb-author-vcard .author-vcard-inner {
	-webkit-border-radius: <?php echo $global_brd_radius; ?>;
            border-radius: <?php echo $global_brd_radius; ?>
}
input[type='search'], input[type='text'], select, .bb-btn {
	-webkit-border-radius: <?php echo $global_input_btn_brd_radius; ?>;
            border-radius: <?php echo $global_input_btn_brd_radius; ?>
}

/* Light Mode Colors */
<?php if ($light_mode) { ?>
	body, .bb-author-vcard-mini .author-name a, .bb-author-vcard .author-name a, .page-nav-itm a, .post-list .post-itm a,.bb-social.default a, .bb-author-vcard .website-url {
		color: <?php echo $light_txt_clr;?>
	}
	body {
		background-color: <?php echo $light_bg_clr;?>
	}
	.main-nav .divide-h {
		background-color: <?php echo $light_sidebar_border_clr;?>
	}
	hr {
		background-color: <?php echo $light_border_clr; ?>
	}
	.border-btm {
		border-bottom: 2px solid <?php echo $light_border_clr;?>
	}
	.bb-cat-links, .bb-cat-links a, .byline, .post-summary, .posted-on, .bb-price-block .old-price {
		color: <?php echo $light_sec_txt_clr; ?>
	}
	.bb-author-vcard .author-header {
		background-color: <?php echo $light_sec_bg_clr;?>
	}
<?php }
else {?>
	/* Dark Mode Colors */
	body, .bb-author-vcard-mini .author-name a, .bb-author-vcard .author-name a, .page-nav-itm a, .post-list .post-itm a, .bb-social.default a, .bb-author-vcard .website-url, blockquote.instagram-media a {
		color: <?php echo $dark_txt_clr;?>
	}
	body {
		background-color: <?php echo $dark_bg_clr;?>
	}
	hr, .main-nav .divide-h {
		background-color: <?php echo $dark_border_clr; ?>
	}
	.bb-tags a, .bb-author-vcard .author-vcard-inner {
		border-color: <?php echo $dark_border_clr; ?>
	}
	.border-btm {
		border-bottom: 2px solid <?php echo $dark_border_clr;?>
	}
	.bb-cat-links, .bb-cat-links a, .byline, .post-summary, .posted-on, .bb-price-block .old-price {
		color: <?php echo $dark_sec_txt_clr; ?>
	}
	.bb-author-vcard .author-header {
		background-color: <?php echo $dark_sec_bg_clr;?>
	}
<?php }

/* Badge Personal Colors */
echo Boombox_Design_Scheme::get_instance()->get_terms_personal_styles();

?>

/** 12.0 - Social Colors **/
<?php include "social-colors.php"; ?>

<?php
// Allow third part to add styles
do_action( 'boombox/amp/additional_css', 'ltr', $this );
?>