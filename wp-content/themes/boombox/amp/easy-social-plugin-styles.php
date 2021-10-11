<?php // NOTE: Please pay attention that the last style in css class doesn't have separator. This is specific style for AMP for reducing css size  ?>
.essb_links {
	margin: 0 0 <?php echo $spacing_sm;?> 0;
	clear: both
}
.essb_links_list {
	margin: 0
}
.essb_links_list li {
	list-style: none;
	display: inline-block;
	text-align: center
}
.essb-subscribe-form, li.essb_link_more, li.essb_link_less, .essb_links_list .essb_counter_hidden,
[data-essb-button-style="button_name"] .essb_icon {
	display: none
}
.essb_links_list li.essb_item a {
	min-width: 45px;
	-webkit-transition: all .1s ease-out;
	   -moz-transition: all .1s ease-out;
	        transition: all .1s ease-out
}
.essb_links_list li.essb_item a:hover {
	text-decoration: none;
	opacity: 0.8
}
.essb_links_list li a, .essb_links_list li > span {
	margin: 0 3px 5px 0;
	padding: 0 13px;
	font-size: 13px;
	min-height: 28px;
	line-height: 28px;
	border-radius: 3px;
	display: inline-block;
	font-size: 13px;
	vertical-align: middle
}
.essb_icon {
	vertical-align: middle
}
.essb_network_name {
    display: inline-block
}
.essb_network_name, .essb_icon {
	line-height: 1em
}
.essb_network_name:first-child,
[data-essb-button-style="icon"].essb_links .essb_network_name,
[data-essb-button-style="button_name"].essb_links .essb_network_name {
	margin: 0
}

/* Icons */
<?php // NOTE: The code is written in this minified way specifically for reducing size of the css ?>
.essb_icon:before {font-family: 'icomoon'}
.essb_icon.essb_icon_facebook:before {content: "\f09a"}
.essb_icon.essb_icon_twitter:before {content: "\f099"}
.essb_icon.essb_icon_google:before {content: "\f0d5"}
.essb_icon.essb_icon_pinterest:before {content: "\f0d2"}
.essb_icon.essb_icon_whatsapp:before {content: "\e907"}
.essb_icon.essb_icon_viber:before {content: "\e908"}
.essb_icon.essb_icon_telegram:before {content: "\f2c6"}
.essb_icon.essb_icon_linkedin:before {content: "\f0e1"}
.essb_icon.essb_icon_digg:before {content: "\f1a6"}
.essb_icon.essb_icon_del:before {content: "\f1a5"}
.essb_icon.essb_icon_stumbleupon:before {content: "\f1a4"}
.essb_icon.essb_icon_tumblr:before {content: "\f173"}
.essb_icon.essb_icon_vk:before {content: "\f189"}
.essb_icon.essb_icon_reddit:before {content: "\f1a1"}
.essb_icon.essb_icon_xing:before {content: "\f168"}
.essb_icon.essb_icon_ok:before {content: "\f263"}
.essb_icon.essb_icon_messenger:before {content: "\e909"}
.essb_icon.essb_icon_mail:before {content: "\e923"}
.essb_icon.essb_icon_flattr:before {content: "\e90a"}
.essb_icon.essb_icon_buffer:before {content: "\e90b"}
.essb_icon.essb_icon_weibo:before {content: "\e90d"}
.essb_icon.essb_icon_pocket:before {content: "\e90f"}
.essb_icon.essb_icon_meneame:before {content: "\e900"}
.essb_icon.essb_icon_blogger:before {content: "\e910"}
.essb_icon.essb_icon_amazon:before {content: "\e911"}
.essb_icon.essb_icon_yahoomail:before {content: "\e912"}
.essb_icon.essb_icon_gmail:before {content: "\e913"}
.essb_icon.essb_icon_aol:before {content: "\e915"}
.essb_icon.essb_icon_newsvine:before {content: "\e914"}
.essb_icon.essb_icon_hackernews:before {content: "\e917"}
.essb_icon.essb_icon_evernote:before {content: "\e916"}
.essb_icon.essb_icon_myspace:before {content: "\e918"}
.essb_icon.essb_icon_mailru:before {content: "\e91a"}
.essb_icon.essb_icon_line:before {content: "\e91b"}
.essb_icon.essb_icon_flipboard:before {content: "\e91c"}
.essb_icon.essb_icon_skype:before {content: "\e91e"}
.essb_icon.essb_icon_kakaotalk:before {content: "\e91f"}
.essb_icon.essb_icon_livejournal:before {content: "\e920"}
.essb_icon.essb_icon_yammer:before {content: "\e921"}