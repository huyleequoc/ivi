<?php
// NOTE: Please pay attention that the last style in css class doesn't have separator. This is specific style for AMP for reducing css size
// Styles that are specifically modified for rtl are marked as  /* -- Rtl Specific -- */ in php
?>
figure, button, input, iframe {
    margin: 0
}
amp-img, amp-video, amp-audio, iframe, amp-iframe {
    max-width: 100%
}
.amp-wp-enforced-sizes {
    max-width: 100%;
    margin: 0 auto
}
/* Temp Solution */
amp-video {
    height: 250px
}
@media (min-width: 600px) {
    amp-video {
        height: 450px
    }
}

button {
    cursor: pointer;
    padding: 0
}
ol, ul {
    margin: 0 0 25px 20px;
    padding: 0
}
p {
    margin: 0 0 20px
}
a {
    text-decoration: none
}

h1, h2, h3, h4, h5, h6,.h1, .h2, .h3, .h4, .h5, .h6 {
    line-height: 1.2;
    font-weight: 600
}
.h1, .h2, .h3, h1, h2, h3 {
    margin: 40px 0 20px
}
.h4, .h5, .h6, h4, h5, h6 {
    margin: 15px 0 10px
}
h1:first-child, h2:first-child, h3:first-child, h4:first-child, h5:first-child, h6:first-child {
    margin-top: 0
}
h1 {
    font-size: 1.7rem
}
h2 {
    font-size: 1.5rem
}
h3 {
    font-size: 1.375rem
}
h4 {
    font-size: 1.25rem
}
h5 {
    font-size: 1.1rem
}
h6 {
    font-size: 1rem
}
@media (min-width: <?php echo $screen_sm_min; ?>) {
    h1 {
        font-size: 2.5rem
    }
    h2 {
        font-size: 2.2rem
    }
    h3 {
        font-size: 1.876rem
    }
    h4 {
        font-size: 1.6rem
    }
}

hr {
    margin: 0 0 <?php echo $spacing_sm;?> 0;
    height: 2px;
    border: 0
}

.small, small {
    font-size: 85%;
    line-height: 1;
    color: #828282
}

.bold, strong, b {
    font-weight: bold
}

sup,
sub {
    font-size: 75%;
    height: 0;
    line-height: 0;
    position: relative;
    vertical-align: baseline
}

sup {
    bottom: 1ex
}

sub {
    top: .5ex
}

small {
    font-size: 75%
}

big {
    font-size: 125%
}

dfn, cite, em, i, var {
    font-style: italic
}

s, strike, del {
    text-decoration: line-through
}

u, ins {
    text-decoration: underline
}

/* General Blockquote Styling */
<?php /* -- Rtl Specific -- */ ?>
/* General Blockquote Styling */
blockquote:not(.instagram-media) {
    position: relative;
    font-family: Cabin,sans-serif;
    line-height: 1.3;
    font-size: 21px;
    text-align: right;
    margin: 25px;
    padding-right: 60px
}
blockquote:not(.instagram-media):before {
    display: inline-block;
    font-family: icomoon;
    content: "\f10d";
    position: absolute;
    right: 0;
    top: -7.33px;
    font-style: inherit;
    width: 40px;
    height: 40px;
    line-height: 40px;
    border-radius: 50%;
    font-size: 16px;
    text-align: center;
    box-shadow: 1px 2px 2px rgba(0,0,0,.18)
}
blockquote:not(.instagram-media) cite, blockquote:not(.instagram-media) small, blockquote:not(.instagram-media) footer {
    display: block;
    padding-top: 10px;
    color: #a6a6a6;
    font-size: 16px;
    line-height: 18px;
    font-weight: 400;
    text-transform: none
}
blockquote:not(.instagram-media) p:last-child {
    margin: 0
}
blockquote.instagram-media {
    margin: 0
}

.mark, mark {
    padding: .1em .3em;
    background: #ff0;
    color: #1f1f1f
}

abbr[data-original-title], abbr[title] {
    cursor: help;
    border-bottom: 1px dotted #777
}

address {
    margin-bottom: 20px;
    font-style: normal;
    line-height: 1.42857143
}

ul, ol {
    margin: 0 0 25px 20px
}
ul ul, ul ol, ol ul, ol ol {
    margin-bottom: 0
}

dl {
    margin-top: 0;
    margin-bottom: 20px
}
dt {
    font-weight: 700
}
dd, dt {
    line-height: 1.42857143
}
dd {
    margin-left: 0
}

kbd {
    padding: 2px 4px;
    font-size: 90%;
    color: #fff;
    background-color: #333;
    border-radius: 3px;
    -webkit-box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.25);
            box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.25)
}

pre {
    display: block;
    padding: 9px;
    margin: 0 0 10px;
    font-size: 13px;
    line-height: 1.42857143;
    color: #333;
    word-break: break-all;
    word-wrap: break-word;
    background-color: #f5f5f5;
    border: 1px solid #ccc;
    border-radius: 4px;
    white-space: pre-wrap;
    word-wrap: break-word
}
pre.pre-scrollable {
    max-height: 340px;
    overflow-y: scroll
}

code {
    padding: 2px 4px;
    font-size: 90%;
    color: #c7254e;
    background-color: #f9f2f4;
    border-radius: 4px
}

code, kbd, pre, samp {
    font-family: Menlo,Monaco,Consolas,"Courier New",monospace
}

table {
    border-collapse: separate;
    border-spacing: 0;
    border: none;
    border-width: 0;
    margin: 0 0 25px 0;
    width: 100%
}
table thead th {
    text-transform: uppercase;
    color: #000000;
    font-size: 12px;
    line-height: 26px
}
table tbody tr:nth-child(2n+1) th, table tbody tr:nth-child(2n+1) td {
    background-color: #f7f7f7
}
table th, table td {
    padding: 9px 20px;
    vertical-align: middle
}
table caption, table th, table td {
    font-weight: normal;
    text-align: left
}
table th {
    font-weight: 700
}
table td {
    font-size: 16px;
    line-height: 20px;
    color: inherit;
    font-weight: 400
}

/** Alignments **/
.alignleft, .amp-wp-enforced-sizes.alignleft {
    display: inline;
    float: left
}
.alignright, .amp-wp-enforced-sizes.alignright  {
    display: inline;
    float: right
}
.aligncenter,.amp-wp-enforced-sizes.aligncenter {
    display: block;
    margin-right: auto;
    margin-left: auto
}
blockquote.alignleft,
.wp-caption.alignleft,
.amp-wp-enforced-sizes.alignleft {
    margin: 0.4em 1.6em 1.6em 0
}
blockquote.alignright,
.wp-caption.alignright,
.amp-wp-enforced-sizes.alignright {
    margin: 0.4em 0 1.6em 1.6em
}
blockquote.aligncenter,
.wp-caption.aligncenter,
.amp-wp-enforced-sizes.aligncenter {
    clear: both;
    margin-top: 0.4em;
    margin-bottom: 1.6em
}
.wp-caption.alignleft,
.wp-caption.alignright,
.wp-caption.aligncenter {
    margin-bottom: 1.2em
}

/** Caption **/
.wp-caption {
    background: transparent;
    border: none;
    color: #737373;
    font-family: "Cabin", sans-serif;
    margin: 0 0 28px 0;
    max-width: 100%;
    padding: 0;
    font-size: 14px;
    line-height: 20px
}
.wp-caption.alignleft {
    margin: 20px 25px 30px 0
}
.wp-caption.alignright {
    margin: 20px 0 30px 25px
}
.wp-caption.aligncenter {
    margin: 7px auto
}
.wp-caption .wp-caption-text,
.wp-caption-dd {
    position: relative;
    font-size: 14px;
    line-height: 20px;
    padding: 20px 0
}
