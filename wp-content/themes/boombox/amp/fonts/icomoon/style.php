<?php
// Root url
$root_url = get_template_directory_uri() . '/amp/';
$icon_fonts_url = $root_url.'fonts/icomoon/fonts/';
?>
<?php
// NOTE: Please do not forget to remove important styling from font-family: 'icomoon' and keep icon fonts correct url when adding new icons
//       Please do not forget to minify icon styles before copying them from css file
?>
@font-face {
  font-family: 'icomoon';
  src:  url('<?php echo $icon_fonts_url;?>icomoon.eot?hdep03');
  src:  url('<?php echo $icon_fonts_url;?>icomoon.eot?hdep03#iefix') format('embedded-opentype'),
    url('<?php echo $icon_fonts_url;?>icomoon.ttf?hdep03') format('truetype'),
    url('<?php echo $icon_fonts_url;?>icomoon.woff?hdep03') format('woff'),
    url('<?php echo $icon_fonts_url;?>icomoon.svg?hdep03#icomoon') format('svg');
  font-weight: normal;
  font-style: normal;
}
[class^="icon-"], [class*=" icon-"] {
  font-family: 'icomoon';
  speak: none;
  font-style: normal;
  font-weight: normal;
  font-variant: normal;
  text-transform: none;
  line-height: 1;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
.icon-tag:before{content:"\e924"}.icon-kakaotalk:before{content:"\e91f"}.icon-meneame:before{content:"\e900"}.icon-newsvine:before{content:"\e914"}.icon-aol:before{content:"\e915"}.icon-gmail:before{content:"\e913"}.icon-pocket:before{content:"\e90f"}.icon-flattr:before{content:"\e90a"}.icon-buffer:before{content:"\e90b"}.icon-messenger:before{content:"\e909"}.icon-viber:before{content:"\e908"}.icon-whatsapp:before{content:"\e907"}.icon-soundcloud:before{content:"\f1be"}.icon-mixcloud:before{content:"\f289"}.icon-quora:before{content:"\f2c4"}.icon-chevron-left:before{content:"\f053"}.icon-chevron-right:before{content:"\f054"}.icon-comment-o:before{content:"\f0e5"}.icon-snapchat:before{content:"\f2ab"}.icon-snapchat-square:before{content:"\f2ad"}.icon-globe:before{content:"\f0ac"}.icon-quote-left:before{content:"\f10d"}.icon-telegram:before{content:"\f2c6"}.icon-close:before{content:"\f00d"}.icon-share:before{content:"\f064"}.icon-twitter:before{content:"\f099"}.icon-facebook:before{content:"\f09a"}.icon-github:before{content:"\f09b"}.icon-pinterest:before{content:"\f0d2"}.icon-google-plus:before{content:"\f0d5"}.icon-caret-down:before{content:"\f0d7"}.icon-caret-up:before{content:"\f0d8"}.icon-envelope:before{content:"\f0e0"}.icon-linkedin:before{content:"\f0e1"}.icon-youtube:before{content:"\f167"}.icon-xing:before{content:"\f168"}.icon-stack-overflow:before{content:"\f16c"}.icon-instagram:before{content:"\f16d"}.icon-flickr:before{content:"\f16e"}.icon-tumblr:before{content:"\f173"}.icon-dribbble:before{content:"\f17d"}.icon-foursquare:before{content:"\f180"}.icon-vk:before{content:"\f189"}.icon-reddit:before{content:"\f1a1"}.icon-stumbleupon:before{content:"\f1a4"}.icon-delicious:before{content:"\f1a5"}.icon-digg:before{content:"\f1a6"}.icon-behance:before{content:"\f1b4"}.icon-deviantart:before{content:"\f1bd"}.icon-vine:before{content:"\f1ca"}.icon-codepen:before{content:"\f1cb"}.icon-jsfiddle:before{content:"\f1cc"}.icon-slideshare:before{content:"\f1e7"}.icon-twitch:before{content:"\f1e8"}.icon-yelp:before{content:"\f1e9"}.icon-lastfm:before{content:"\f202"}.icon-odnoklassniki:before{content:"\f263"}.icon-vimeo:before{content:"\f27d"}.icon-snapchat-ghost:before{content:"\f2ac"}.icon-mailru:before{content:"\e91a"}.icon-email:before{content:"\e923"}.icon-print:before{content:"\e922"}.icon-hackernews:before{content:"\e917"}.icon-yahoo:before{content:"\e912"}.icon-amazon:before{content:"\e911"}.icon-blogger:before{content:"\e910"}.icon-weibo:before{content:"\e90d"}.icon-yammer:before{content:"\e921"}.icon-livejournal:before{content:"\e920"}.icon-skype:before{content:"\e91e"}.icon-flipboard:before{content:"\e91c"}.icon-line:before{content:"\e91b"}.icon-viadeo:before{content:"\e919"}.icon-myspace:before{content:"\e918"}.icon-evernote:before{content:"\e916"}.icon-trending5:before{content:"\e90e"}.icon-popular:before{content:"\e902"}.icon-hot:before{content:"\e903"}.icon-trending3:before{content:"\e906"}.icon-trending2:before{content:"\e904"}.icon-trending:before{content:"\e905"}.icon-bars-cst:before{content:"\e901"}.icon-search:before{content:"\e955"}.icon-eye:before{content:"\e956"}
