
/**
 * THIS FILE SHOULD INCLUDE GLOBAL FUNCTIONS THAT CAN BE USED THROUGHOUT THE PROJECT
*/

/**
 *  Global Variables
 */
var bb = {
    isMobile : false,
    isRTL : false,
    html  : jQuery('html'),
    windowWidth : jQuery(window).width(),
    windowHeight : jQuery(window).height(),
    stickyBorder : jQuery('#sticky-border').offset().top,
    fixedHeader : 0,
    scrollTop : 0,
    floatingPagination : 0,
    adminBar : 0,
    stickyAdminBar : 0,
    videoOptions : boombox_global_vars.videoOptions
};


/**
 * Set Global Variables
 */
(function ($) {
    "use strict";

    if( bb_detect_mobile() ){
        bb.isMobile = true;
        bb.html.addClass('mobile');
        $('body').trigger('bbMobile');
    } else {
        bb.isMobile = false;
        bb.html.addClass('desktop');
        $('body').trigger('bbDesktop');
    }

    if($('body').hasClass('rtl')){
        bb.isRTL = true;
    }

    function bb_detect_mobile() {
        //var is_mobile = ( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test( navigator.userAgent ) );
        var is_mobile = $('html').hasClass('touchevents');
        return is_mobile;
    }

    if (bb.isMobile && boombox_global_vars.boombox_gif_event == 'hover') {
	    boombox_global_vars.boombox_gif_event = 'click';
    }

    function setSize(){
        bb.windowWidth = $(window).width();
        bb.windowHeight = $(window).height();
        $('.wh').css('height', bb.windowHeight +'px');
        $('.min-wh').css('min-height', bb.windowHeight +'px');
        $('.error404 .page-wrapper').css('min-height', bb.windowHeight);

        getSetAdminBars();
    }

    setSize();
    getSetFixedHeader();

    /* Global Window Load */
    $(window).load(function () {
        setSize();
        getSetFixedHeader();
        bb.html.addClass('page-loaded');
    });

    /* Global Window Resize */
    $(window).resize(function () {
        setSize();
    });

    /* Global Window Scroll */
    jQuery(window).scroll(function () {
        bb.scrollTop = jQuery(window).scrollTop();
        bb.stickyBorder = jQuery('#sticky-border').offset().top;
    });

})(jQuery);


/**
 *  Site Helper Functions
 **/
/* Set Height for Fixed Header */
function getSetFixedHeader(){
    if(jQuery('.bb-sticky.bb-sticky-nav').length && jQuery('.bb-sticky.bb-sticky-nav').is(":visible"))
        bb.fixedHeader = jQuery('.bb-sticky.bb-sticky-nav:visible').innerHeight();
    return bb.fixedHeader;
}

/* Set Height for Admin Bars */
function getSetAdminBars() {
    if(jQuery('#wpadminbar').length){
        bb.adminBar = jQuery('#wpadminbar').outerHeight(true);
        if(jQuery('#wpadminbar').css('position')=='fixed')
            bb.stickyAdminBar = jQuery('#wpadminbar').outerHeight(true);
        else
            bb.stickyAdminBar = 0;
    }
    return bb.stickyAdminBar;
}

/* Sets/Gets Height for Floating Pagination */
function getSetFloatingPagHeight() {
    if (jQuery('.bb-sticky.bb-floating-navbar').length) {
        bb.floatingPagination = jQuery('.bb-floating-navbar .floating-navbar-inner').outerHeight(true);
    }
    return bb.floatingPagination;
}

/* Get Header Height */
function getHeaderAreaHeight() {
    var deskHeader = jQuery('.bb-header.header-desktop');
    var mobileHeader = jQuery('.bb-header.header-mobile');
    if(deskHeader.is(":visible"))
        var headerSel = deskHeader;
    else
        var headerSel = mobileHeader;
    var headerH = 0;
    if(headerSel.length)
        headerH = headerSel.height();

    var headerOffset = headerSel.offset().top;
    return headerH + headerOffset;
}

/**
 *  BB Side Navigation
 **/
function bbSideNav() {

    var $selector = jQuery('.widget_bb-side-navigation .dropdown-toggle');

    $selector.on("touchstart click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        var $this = jQuery(this);
        var  $target = $this.parent();
        var $subMenu = $this.next('.sub-menu');

        if($target.hasClass('active-menu')) {
            $subMenu.stop( true, true ).slideUp(200, function(){
                $target.removeClass('active-menu');
            });
        }
        else {
            $subMenu.stop( true, true ).slideDown(200, function(){
                $target.addClass('active-menu');
            });
        }
    });
}


/**
 *  Shows Full Post
 **/
function ShowFullPost(obj) {

    // var $selector = jQuery('.post-list.standard .post-thumbnail img');
    //
    // if (!$selector.length) {
    //     return;
    // }

    var oW = obj.attr('width');
    var oH = obj.attr('height');

    if (oH / oW >= 3) {
        obj.parents('.post-thumbnail').addClass('show-short-media');
        obj.parents('.post').addClass('full-post-show');
    }
}

/**
 *  Sets Form Placeholders
 **/
function setFormPlaceholders(wrapperSel, rowSel){
    jQuery(wrapperSel + ' ' +rowSel).each(function(){
        if(jQuery(this).children('label').text()) {
            jQuery(this).find('input').attr('placeholder',jQuery(this).children('label').text());
        }
    });
}


/**
 *  Tabs
 **/
function initializeTabs() {
    var tabActive = jQuery('.bb-tabs .tabs-menu>li.active');
    if( tabActive.length > 0 ){
        for (var i = 0; i < tabActive.length; i++) {
            var tab_id = jQuery(tabActive[i]).children().attr('href');

            jQuery(tab_id).addClass('active').show();
        }
    }

    jQuery('.bb-tabs .tabs-menu a').on("click", function(e){
        var tab = jQuery(this);
        var tab_id = tab.attr('href');
        var tab_wrap = tab.closest('.bb-tabs');
        var tab_content = tab_wrap.find('.tab-content');

        tab.parent().addClass("active");
        tab.parent().siblings().removeClass('active');
        tab_content.not(tab_id).removeClass('active').hide();
        jQuery(tab_id).addClass('active').fadeIn(500);

        e.preventDefault();
    });
}


/**
 *  Sticky Sidebar
 **/
jQuery.fn.bbStickySidebar = function (action) {

    if(bb.isMobile) return; // not init in mobile

    return this.each(function () {

        //not init if sidebar height more than main content height
        if (jQuery(this).parent().outerHeight(true) >= jQuery('.site-main').outerHeight(true))  return;

        // Variables
        var $sticky = jQuery(this);
        var $parent = $sticky.parent();
        var $child = '<div class="bb-sticky-el"></div>';
        var stickyHeight = 1;
        var stickyWidth = 1;
        var stickyOffset = 25;

        var BB = {
            init: function () {
                BB.build();
                BB.calculate();
                BB.offset();
            },
            refresh: function () {
                BB.calculate();
                BB.offset();
                jQuery(window).scroll();
            },
            build: function () {
                jQuery($child).appendTo($sticky);
                var $next = $sticky.nextAll('.widget');
                jQuery($next).appendTo($sticky.find('.bb-sticky-el'));
            },
            calculate: function () {
                stickyHeight = $sticky.innerHeight(); //calculate sticky widget height
                stickyWidth = $parent.outerWidth(); //calculate sticky widget width
            },
            offset: function () {
                stickyOffset = getSetFloatingPagHeight() + getSetFixedHeader() + getSetAdminBars() + 25;
            },
            waypoint:function () {
                $sticky.bbSticky({
                    fixedOffsetFunc: function(){
                        return getSetFloatingPagHeight() + getSetFixedHeader() + getSetAdminBars() + 25;
                    }
                });
            },
            scroll: function () {
                if (bb.scrollTop >= (bb.stickyBorder - stickyHeight - stickyOffset)) {
                    if($sticky.find('.bb-sticky-el').css('position') === 'fixed') {

                        $sticky.addClass('non-fix');
                    }
                } else {
                    $sticky.removeClass('non-fix');
                }
            }
        };

        if (action === 'refresh') {
            BB.refresh();
            return;
        }

        // Plugin init
        BB.init();

        // Refresh when new content loaded
        jQuery('body').on('bbNewContentLoaded',function () {
            BB.refresh();
        });

        // Windows scroll coll plugin scroll
        jQuery(window).scroll(function () {
            BB.scroll();
        });

        // Windows resize coll plugin refresh for recolculation
        jQuery(window).resize(function () {
            BB.refresh();
        });

        // Windows load coll plugin refresh for recolculation
        jQuery(window).load(function () {
            BB.refresh();
            BB.waypoint();
        });
    });
};


/**
 *  Masonry
 **/
function postMasonry() {
    if (!jQuery.fn.isotope) {
        return;
    }

    var $selector = jQuery('.masonry-grid .post-items');

    if (!$selector.length) {
        return;
    }


    var $masonryGrid = $selector.isotope({
        itemSelector:   '.post-item',
        layoutMode:     'masonry'
    });

    if($selector.find('video').length) {
        var vid = $selector.find('video');
        var count = vid.length;

        jQuery(vid).on( 'load loadeddata', function() {
            -- count;
            if( 0 === count ) {
                $selector.isotope('layout');
            }
        });

        for (i = 0; i < vid.length; i++) {
            if ( vid[ i ].readyState === 4 ) {
                -- count;
            }
        }
        // todo -frontend: should we call $selector.isotope('layout'); if all the videos are already loaded here and 0 === count

        setTimeout(function(){ $selector.isotope('layout'); }, 3000);
    }


    jQuery('body').on( 'bbNewContentLoaded', function(e, newItems) {
        $selector.isotope('appended', newItems);

        if(newItems.find('video').length) {
            var vid = newItems.find('video');
            var count = vid.length;

            jQuery(vid).on( 'load loadeddata', function() {
                -- count;
                if( 0 === count ) {
                    $selector.isotope('layout');
                }
            });

            for (i = 0; i < vid.length; i++) {
                if ( vid[ i ].readyState === 4 ) {
                    -- count;
                }
            }

            setTimeout(function(){ $selector.isotope('layout'); }, 1500);
        }
    });

    // Windows load
    jQuery(window).load(function () {
        setTimeout(function(){ $selector.isotope('layout'); }, 1500);
    });
}

/**
 *  Toggle Functionality
 **/
jQuery.bbToggle = function() {
    var toggleElSel = ".bb-toggle .element-toggle";
    var toggleContentSel = ".bb-toggle .toggle-content";

    jQuery(toggleElSel).on('touchstart click', function(e){
       if(jQuery(this).hasClass('only-mobile') && !bb.isMobile){
           return;
       } else {
           e.preventDefault();
           jQuery(this).toggleClass('active');
           var toggleContent = jQuery(this).attr('data-toggle');
           jQuery(toggleContentSel).not(jQuery(toggleContent)).removeClass('active');
           jQuery(toggleContent).toggleClass('active');
       }
    });

    var closeToggleContent = function(event){
        var exceptElemsStr = toggleElSel  + ' , ' + toggleElSel + ' *' + ' , ' + toggleContentSel + ' , ' + toggleContentSel + ' *';
        if(!jQuery( event.target ).is(exceptElemsStr)) {
            jQuery(toggleElSel).removeClass('active');
            jQuery(toggleContentSel).removeClass('active');
        }

    }
    jQuery(document).on("click", 'body', function (e) {
        closeToggleContent(e);
    });
    jQuery(document).on("touchend", 'body', function (e) {
        closeToggleContent(e);
    });
};

/**
 *  Focus Functionality
 **/
jQuery.fn.bbFocus = function() {
    return this.each(function () {
        var _this = jQuery(this);
        var element = _this.find('.element-focus');
        var target = jQuery(element.attr('data-focus'));

        jQuery(element).on('touchstart click', function(e){
            e.preventDefault();
            setTimeout(function(){_this.find(target).focus(); }, 1000);
        });
    })
};


/**
 * Sticky Functionality
 **/
jQuery.fn.bbSticky = function( options ) {
    if(!jQuery(this).length) return;

    this.each(function(){
        /* **Parameters** */
        var settings = jQuery.extend({
            scrollStyle: 'classic', // 'smart', 'none'
            topOffsetFunc: null, // by default current element offset top will be used
            fixedOffset: null, // by default current element offset top will be used
            fullWidth: false, // by default sticky takes auto width
            animation: false, // by default no animation
            keepWrapperHeight: true, // This option keeps wrapper fictive height
            fixedOffsetFunc: function(){}, // by default current element offset top will be used
            scrollFunc: function(){}, // ex. jQuery(window).scroll(...), needs to be defined
            resizeFunc: function(){}
        }, options);

        /* **Variables** */
        var curElSel = this;
        var curEl = jQuery(this);
        var childEl = curEl.children('.bb-sticky-el');
        var offsetFromTop;
        var fixedClass = 'affix';
        var notFixedClass = 'no-affix';
        var posAnimateClass = 'pos-animate';
        var lastScrollTop = 0;
        var fixedOffset = settings.fixedOffsetFunc.call(this);

        /* **Functions** */
        /**
         * Sets sticky element size
         */
        var setStickySize = function() {
            /* Width Set */
            if(!settings.fullWidth) {
                curEl.css('width', 'auto');
                childEl.outerWidth(curEl.outerWidth());
            }
            else
                childEl.css('left',0);

            /* Height Set */
            if(settings.keepWrapperHeight) {
                curEl.css('height', 'auto');
                curEl.height(childEl.outerHeight(true));
            }

            /* Offset from Top */
            offsetFromTop = (settings.topOffsetFunc==null)? curEl.offset().top : settings.topOffsetFunc.call(curElSel);


            /* Fixed Elements Offset */
            offsetFromTop = (fixedOffset==null)? offsetFromTop : offsetFromTop - fixedOffset;
        };

        var smartStickyFunc = function(){
            var st = jQuery(this).scrollTop();
            if (st > lastScrollTop){ // if scrolled down
                if((fixedOffset!=null))
                    childEl.css('top',0);
                curEl.removeClass(fixedClass);
                if (jQuery(window).scrollTop() > offsetFromTop+curEl.height() && offsetFromTop >= 0) {
                    setTimeout(
                        function(){
                            curEl.addClass(notFixedClass);
                            curEl.removeClass(posAnimateClass);
                            lastScrollTop = jQuery(this).scrollTop();
                        },
                        50);
                }
                lastScrollTop = jQuery(this).scrollTop();
            }
            if(st < lastScrollTop) {
                if (jQuery(window).scrollTop() >= offsetFromTop) {
                    if(fixedOffset!=null)
                        childEl.css('top',fixedOffset);
                    curEl.addClass(fixedClass);
                    setTimeout(
                        function(){
                            curEl.removeClass(notFixedClass);
                            curEl.removeClass(posAnimateClass);
                            lastScrollTop = jQuery(this).scrollTop();
                        },
                        50);
                }
                else {
                    if(fixedOffset!=null) childEl.css('top',0);
                    curEl.removeClass(fixedClass);
                    curEl.addClass(posAnimateClass);
                }
                lastScrollTop = jQuery(this).scrollTop();
            }
            lastScrollTop = st;
        }

        var classicStickyFunc = function(){
            if (jQuery(window).scrollTop() >= offsetFromTop) {
                if(fixedOffset!=null)
                    childEl.css('top',fixedOffset);
                curEl.addClass(fixedClass);
                if(settings.animation)
                    setTimeout(
                        function(){
                            curEl.removeClass(posAnimateClass);
                        },
                        50);
            }
            else {
                if(fixedOffset!=null)
                    childEl.css('top',0);
                curEl.removeClass(fixedClass);
                if(settings.animation)
                    setTimeout(
                        function(){
                            curEl.addClass(posAnimateClass);
                        },
                        50);
            }
        }

        /* **Main Functionality** */
        setStickySize();
        jQuery(window).resize(function(){
            settings.resizeFunc.call(this);
            fixedOffset = settings.fixedOffsetFunc.call(this);
            setStickySize();

            /* Smart Scroll Functionality */
            if(settings.scrollStyle =='smart') {
                smartStickyFunc();
            }

            /* Classic Scroll Functionality */
            if(settings.scrollStyle =='classic') {
                classicStickyFunc();
            }
        });

        /* **Settings** */
        /* Animate */
        if(settings.animation)
            curEl.addClass('animated');

        /* Scroll Function */
        settings.scrollFunc.call(this);

        /* Smart Scroll Functionality */
        if(settings.scrollStyle =='smart') {
            jQuery(window).scroll(function(event){
                smartStickyFunc();
            });
        }

        /* Classic Scroll Functionality */
        if(settings.scrollStyle =='classic') {
            jQuery(window).scroll(function (event) {
                classicStickyFunc();
            });
        }
     })

};


/**
 *  Toggles Mobile Menu
 **/
function mobileMenuToggle(e, curEl) {
    e.preventDefault();
    e.stopPropagation();
    var targetSel =  curEl.next('.sub-menu');
    if(curEl.hasClass('toggled-on')) {
        targetSel.stop( true, true ).slideUp(300, function(){
            curEl.removeClass('toggled-on');
        });
    }
    else {
        targetSel.stop( true, true ).slideDown(300, function(){
            curEl.addClass('toggled-on');
        });
    }
}


/**
 *  Mobile Navigation
 **/
function bbMobileNavigation() {
    /* Mobile navigation sidebar open/close  */
    jQuery(document).on("click", '#menu-button', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var curEl = jQuery(this);
        var mbNavEl = jQuery('.bb-mobile-navigation');
        if(curEl.hasClass('pos-left')) {
            mbNavEl.addClass('pos-left');
            mbNavEl.removeClass('pos-right');
        }
        else {
            mbNavEl.addClass('pos-right');
            mbNavEl.removeClass('pos-left');
        }
        setTimeout(function(){
            bb.html.toggleClass('main-menu-open');
        }, 50);

    });
    jQuery(document).on("click", '#menu-close', function (e) {
        e.preventDefault();
        e.stopPropagation();
        setTimeout(function(){
            bb.html.toggleClass('main-menu-open');
        }, 50);

    });
    /* Mobile menu toggle */
    jQuery('.bb-mobile-navigation .dropdown-toggle').on('touchstart click',function(e){
        mobileMenuToggle(e, jQuery(this));
    });
    /* Mobile Nav Bg Click Events */
    var mbNavBgClickEvents = function(target) {
        jQuery('.toggled-on').removeClass('toggled-on');
        if (bb.html.hasClass('main-menu-open')) {
            target.preventDefault();
            bb.html.removeClass('main-menu-open');
        }
    }
    /* When closing something on background click, we need to set touchend and click events.
     Because otherwise when clicking on any target under which we have link, the link will be clicked and the page will redirect */
    jQuery(document).on("click", '#mobile-nav-bg', function (target) {
        mbNavBgClickEvents(target);
    });
    jQuery(document).on("touchend", '#mobile-nav-bg', function (target) {
        mbNavBgClickEvents(target);
    });
}


/**
 *  Shows and Hides Some Elements on Scroll
 **/
/* *** Shows/ hides go top button on scroll *** */
function showHideGoTopOnScroll() {
    jQuery(window).scroll(function () {
        if (bb.scrollTop >= 500) {
            jQuery('#go-top').addClass('show');
        } else {
            jQuery('#go-top').removeClass('show');
        }
    });
}
/* *** Shows/ hides fixed navigation on scroll *** */
function showHideFixedNavOnScroll() {
    if(jQuery('.bb-post-single').length) {
        var sPostContent = jQuery('.bb-post-single .s-post-content');
        // Set default offset from top
        var topOffset = 500 + bb.windowHeight;
        // If post has content, set post content offset as top offset
        if(sPostContent.length)
             topOffset = sPostContent.offset().top;

        jQuery(window).scroll(function () {
            // If footer sticky border and content are both visible, no need to hide fixed pagination when reaching footer
            if(bb.stickyBorder - topOffset < bb.windowHeight && bb.scrollTop > topOffset -bb.windowHeight )
                jQuery('.bb-fixed-pagination').removeClass('hide');
            else
            {
                // If content is visible and footer sticky border is not visible, show fixed pagination
                if(bb.scrollTop > topOffset -bb.windowHeight && bb.scrollTop < bb.stickyBorder - bb.windowHeight)
                    jQuery('.bb-fixed-pagination').removeClass('hide');
                else
                // If footer sticky border is visible, hide fixed pagination
                if (bb.scrollTop > bb.stickyBorder-bb.windowHeight)
                    jQuery('.bb-fixed-pagination').addClass('hide');
            }
        });
    }

}
function showHideElementsOnScroll() {
    showHideGoTopOnScroll();
    showHideFixedNavOnScroll();
}

/**
 *  Featured Carousel
 **/
function bbFeaturedCarousel(){
    jQuery(".featured-carousel").each(function(){

        var itemWidth = jQuery(this).hasClass('big-item')? 200 : 150;
        var containerWidth = jQuery(this).width();
        var slidesToShow = Math.round(containerWidth / itemWidth);

        if(slidesToShow < 1) return;

        jQuery(this).find('ul').slick({
            infinite: true,
            slidesToShow: slidesToShow,
            slidesToScroll: slidesToShow,
            prevArrow:'<a type="button" href="#" class="bb-arrow-prev"></a>',
            nextArrow:'<a type="button" href="#" class="bb-arrow-next"></a>',
            swipe: true,
            rtl: bb.isRTL,
            arrows: true,
            responsive: [{
                breakpoint: 768,
                settings: {
                    slidesToShow: Math.round(768 / itemWidth),
                    slidesToScroll: Math.round(768 / itemWidth)-1,
                }
            },{
                breakpoint: 480,
                settings: {
                    slidesToShow: Math.round(480 / itemWidth),
                    slidesToScroll: Math.round(480 / itemWidth)-1,
                }
            },]
        });
    });
}

/**
 *  Hyena GIF
 **/
function HyenaGIF() {

    var excluded_selectors = [
        '.gallery-item img[src*=".gif"]',
        '.regif_row_parent img[src*=".gif"]',
        '.next-prev-pagination img[src*=".gif"]',
        '.bb-no-play img[src*=".gif"]',
        '.bb-post-gallery-content img[src*=".gif"]',
        '.zf-result_media img[src*=".gif"]',
        'img.fr-fil[src*=".gif"]'
    ];
    excluded_selectors = excluded_selectors.concat( boombox_global_vars.single_post_animated_hyena_gifs_excluded_js_selectors );
    excluded_selectors = excluded_selectors.join( ', ' );

    var hyena_possible_nodes = jQuery('.bb-post-single .s-post-content .size-full[src*=".gif"], .bb-post-single .s-post-thumbnail img[src*=".gif"], .bb-media-playable img[src*=".gif"]').not( excluded_selectors );

    if( hyena_possible_nodes.length ) {
        hyena_possible_nodes.Hyena({
            "style": 1,
            "controls": false,
            "on_hover": (boombox_global_vars.boombox_gif_event == 'hover'),
            "on_scroll": (boombox_global_vars.boombox_gif_event == 'scroll')
        });
    }

    jQuery('body').on( 'bbNewContentLoaded', function(e, newItems) {

        var $selector = jQuery('.bb-media-playable .item-added  img[src*=".gif"]');

        if( $selector.length ) {
            $selector.Hyena({
                "style": 1,
                "controls": false,
                "on_hover": (boombox_global_vars.boombox_gif_event == 'hover'),
                "on_scroll": (boombox_global_vars.boombox_gif_event == 'scroll')
            });
        }

    });
}

/**
 * Plays featured video based on the options below
 * Options:
 bb.videoOptions = {
         playerControls: ( string ) mute | full_controls
         autoPlay: 		( string ) scroll | hover|none
         sound: 			( string ) muted | with_sound
         clickEvent: 	( string ) mute_unmute | play_pause
         loop: 			( int )    0 | 1 ( 0 if disabled, 1 if enabled )
         }
 */
function featuredVideo(video) {

    /* *** Variables *** */
    var $videoWrapper = jQuery(video).parent();
    var $btnVolume= $videoWrapper.find('.btn-volume');
    var durationBadge = $videoWrapper.find('.badge-duration');
    var durationTimeout;

    /* *** Helper Functions *** */
    // Play video
    var playVideo = function() {
        if (!$videoWrapper.hasClass('play')) {
            $videoWrapper.addClass('play');
            video.play();
            $btnVolume.removeClass('hidden');
            if(bb.videoOptions.playerControls =='mute')
                durationTimeout = setTimeout(function(){
                    durationBadge.addClass('hidden');
                }, 3000);
        }
    };

    // Pause video
    var pauseVideo = function() {
        if ($videoWrapper.hasClass('play')) {
            $videoWrapper.removeClass('play');
            video.pause();
            $btnVolume.addClass('hidden');
            clearTimeout(durationTimeout);
            if(bb.videoOptions.playerControls =='mute')

                durationBadge.removeClass('hidden');
        }
    };

    // Mute/Unmute click event
    var clickMuteUnmute = function() {
        var mutedVal = jQuery(video).prop('muted');
        if(mutedVal)
            jQuery(video).prop('muted', false);
        else
            jQuery(video).prop('muted', true);
        $videoWrapper.find('.btn-volume .bb-icon').toggleClass('hidden');
    };

    // Play/Pause click event
    var clickPlayPause = function() {
        if($videoWrapper.hasClass('play'))
            pauseVideo();
        else
            playVideo();
    };

    // Set video duration
    var setVideoDuration = function(){
        var durationInterval = window.setInterval(function(t){
            if(video.readyState > 0) {
                var videoDuration = numberToTwoDigits(Math.floor(video.duration));
                if (videoDuration < 60)
                    videoDuration = "00:" + videoDuration.toString();
                if (videoDuration > 60) {
                    var timeRoundPart = numberToTwoDigits(Math.floor(videoDuration / 60));
                    var timeReminder = numberToTwoDigits(Math.round(videoDuration % 60));
                    videoDuration = timeRoundPart + ":" + timeReminder;
                }

                durationBadge.text(videoDuration);
                durationBadge.removeClass('hidden');
                clearInterval(durationInterval);
            }
        },300);

    };

    /* *** General *** */
    $videoWrapper.find('.btn-volume').on("click", function (e) {
        e.stopPropagation();
        clickMuteUnmute();
    });
    // Video click play functionality by default
    $videoWrapper.on("click", function () {
        playVideo();
    });

    /* *** Options *** */
    // Video scroll autoplay/ pause functionality
    var videoView = new Waypoint.Inview({
        element: video,
        entered: function () {
            if(bb.videoOptions.autoPlay =='scroll' && !bb.isMobile)
                playVideo();
        },
        exited: function () {
            setTimeout(function () {
                pauseVideo();
            }, 150);
        }
    });

    // Video hover play functionality
    if(bb.videoOptions.autoPlay =='hover' && !bb.isMobile)
        $videoWrapper.on('mouseenter touchstart', function () {
            playVideo();
        })

    // Video click play/ pause functionality
    if(bb.videoOptions.clickEvent =='play_pause') {
        $videoWrapper.off('click');
        $videoWrapper.on("click", function (e) {
            clickPlayPause();
        });
    }

    // Video click mute/ unmute functionality
    if(bb.videoOptions.clickEvent =='mute_unmute') {
        $videoWrapper.off('click');
        $videoWrapper.on("click", function (e) {
            if($videoWrapper.hasClass('play'))
                clickMuteUnmute();
            else
                playVideo();
        });
    }

    // Video duration
    if(bb.videoOptions.playerControls =='mute') {
        setVideoDuration();
    }

    // Video Loop
    if(bb.videoOptions.loop == 0)
    {
        video.onended = function() {
            $videoWrapper.removeClass('play');
            if(bb.videoOptions.playerControls =='mute')
                durationBadge.removeClass('hidden');
        };
    }
}

/**
 *  This function works with GIF Cloud Converter
 **/
function GIFvideo(video) {

    video.pause();

    jQuery(video).attr('width', '100%').attr('height', 'auto');

    var $videoWrapper = jQuery(video).parent();
    var canPlay = true;


    if (bb.isMobile) {
        jQuery(video).attr('webkit-playsinline', 'playsinline');
        boombox_global_vars.boombox_gif_event = 'click';
    }
    if (boombox_global_vars.boombox_gif_event == 'hover') {

        $videoWrapper.on('mouseenter touchstart', function () {
            $videoWrapper.addClass('play');
            video.play();

        }).on('mouseleave touchend', function () {
            $videoWrapper.removeClass('play');
            video.pause();
        });

    } else if (boombox_global_vars.boombox_gif_event == 'scroll') {

        var videoView = new Waypoint.Inview({
            element: video,
            entered: function () {
                if (canPlay) {
                    $videoWrapper.addClass('play');
                    video.play();
                }

            },
            exited: function () {
                if (canPlay) {
                    setTimeout(function () {
                        $videoWrapper.removeClass('play');
                        video.pause();
                    }, 150);

                }
            }
        });
    }
    $videoWrapper.on('click', function (e) {
        if(!$videoWrapper.parents('.bb-post-collection').hasClass('masonry-grid')) e.stopPropagation();

        if (!$videoWrapper.hasClass('play')) {
            video.play();
            $videoWrapper.addClass('play');
        } else {
            video.pause();
            $videoWrapper.removeClass('play');
        }

        if(!$videoWrapper.parents('.bb-post-collection').hasClass('masonry-grid'))  return false;;
    });
}

/**
 *  This function works with GIF Cloud Converter
 *  Only in mobile
 **/
function GIFtoVideo(img) {
    var $videoWrapper = jQuery(img).parent();
    var imgUrl = jQuery(img).attr('src');
    var video;

    $videoWrapper[0].addEventListener('click', function () {

        if (!jQuery(this).hasClass('video-done')) {

            var videoUrl = jQuery(img).data('video');

            video = document.createElement("video");

            video.setAttribute("loop", true);
            video.setAttribute("poster", imgUrl);
            video.setAttribute("webkit-playsinline", "playsinline");

            var videoSrc = document.createElement("source");

            videoSrc.setAttribute("src", videoUrl);
            videoSrc.setAttribute("type", "video/mp4");

            video.appendChild(videoSrc);
            jQuery(this)[0].appendChild(video);

            toggleVideoPlaying(video);

            jQuery(this).find('img').remove();
            jQuery(this).addClass('video-done');

        } else {

            toggleVideoPlaying(video);
        }
    });

    var videoView = new Waypoint.Inview({
        element: $videoWrapper,
        exited: function () {
            if ($videoWrapper.hasClass('video-done')) {
                var img = '<img  src=' + imgUrl + ' alt="">';
                jQuery(img).appendTo($videoWrapper);
                $videoWrapper.find('video').remove();
                $videoWrapper.removeClass('play');
                $videoWrapper.removeClass('video-done');
            }
        }
    });
}

function toggleVideoPlaying(video) {
    if (video.paused) {

        var promise = video.play();

        // promise won�t be defined in browsers that don't support promisified play()
        if (promise === undefined) {

            //Promisified video play() not supported

            video.setAttribute("controls", true);

        } else {
            promise.then(function () {
                // Video playback successfully initiated, returning a promise
            }).catch(function (error) {
                // Error initiating video playback

                video.setAttribute("controls", true);
            });
        }

        jQuery(video).parent().addClass('play');

    } else {
        video.pause();
        jQuery(video).parent().removeClass('play');

    }
}

/**
 *  Animation to page top
 **/
function animationPageTop() {


    jQuery(document).on("click", '#go-top', function () {
        bbPageAnimate(0,500);
        return false;
    });
}


/**
 *  Disabled Links Behaviour
 **/
function disabledLinksBehaviour() {
    jQuery('.bb-disabled a').click(function(e){
        e.preventDefault();
    });
}

/**
 * Post gallery
 * @returns {*}
 */
jQuery.fn.bbPostGallery = function(){

	return this.each(function () {

		// Variables
		var $this = jQuery(this);
		var $link = $this.find('.bb-js-gallery-link');
		var	ID = $link.data('id');
		var	$popup = jQuery(ID);
		var topScroll = 0;

		var BB = {
			openPopup : function(id){
			    $popup = jQuery(id);
			    if($popup) {
                    $popup.addClass('bb-open');
                    BB.actions();
                    topScroll = bb.scrollTop;
                    jQuery('html').addClass('bb-gl-open');
                }
			},
			closePopup : function(){
                $popup.removeClass('bb-open');
				BB.clearLocation();
				BB.switchMode('slide');
                jQuery('html').removeClass('bb-gl-open');
                window.scrollTo(0, topScroll);
			},
			switchMode : function(mode){
				switch(mode) {
					case 'slide':
						$popup.removeClass('bb-mode-grid');
						$popup.addClass('bb-mode-slide');
						break;
					case 'grid':
						$popup.removeClass('bb-mode-slide');
						$popup.addClass('bb-mode-grid');
						break;
					default:
						$popup.removeClass('bb-mode-grid');
						$popup.addClass('bb-mode-slide');
				}
			},
			changeLocation: function(param){
				// Change Window Location
				window.location.hash = param;

				// Call function onLocationChange
				BB.onLocationChange(param);
			},
			getLocation: function(){
				return window.location.hash;
			},
			clearLocation: function(){
				// Clear window hash
				history.pushState("", document.title, window.location.pathname
					+ window.location.search);
			},
			onLocationChange : function(location){
				// Get img index from window hash
				imgNum = parseInt(location.split('_')[1]);

				// Call slide
				BB.slide(imgNum);

			},
			slide : function(index){
				$popup.find('.bb-gl-slide .bb-active').removeClass('bb-active');
				$popup.find('.bb-gl-slide').find('li').eq(index).addClass('bb-active');
			},
			actions:function(){
                $popup.find('.bb-js-gl-close').on('click', function(e){
                    e.preventDefault();
                    BB.closePopup(ID);
                });

                $popup.find('.bb-js-mode-switcher').on('click',function(e){
                    e.preventDefault();
                    var mode = jQuery(this).data('mode');
                    BB.switchMode(mode);
                });

                $popup.find('.bb-js-slide').on('click',function(e){
                    e.preventDefault();
                    var _this = jQuery(this);
                    var	hash = _this.attr('href');

                    BB.changeLocation(hash);
                });

                $popup.find('.bb-js-gl-item').on('click',function(e){
                    e.preventDefault();
                    var _this = jQuery(this);
                    var	hash = _this.attr('href');

                    BB.changeLocation(hash);
                    BB.switchMode('slide');
                });

                //Close gallery on click 'ESC'
                jQuery(document).keyup(function(e) {
                    if (e.keyCode === 27) {
                        BB.closePopup(ID);
                    }
                });

                //Slide gallery on click 'left'
                jQuery(document).keyup(function(e) {
                    if (e.keyCode === 37) {
                        jQuery('.bb-post-gallery-content.bb-open .bb-active .bb-js-slide.bb-gl-prev').trigger('click');
                    }
                });

                //Slide gallery on click 'right'
                jQuery(document).keyup(function(e) {
                    if (e.keyCode === 39) {
                        jQuery('.bb-post-gallery-content.bb-open .bb-active .bb-js-slide.bb-gl-next').trigger('click');
                    }
                });
            }
		};

		$this.on('click',function(e){
			e.preventDefault();
			//var _this = jQuery(this);
			var	hash = $link.attr('href');

			BB.openPopup(ID);
			BB.switchMode('slide');
			BB.changeLocation(hash);
		});

		// Windows load
        if(BB.getLocation()) {
            var url = BB.getLocation();
            //if it is gallery hash
           if(url.includes("post-gallery")){
               var location = BB.getLocation();
               var id = location.substring(0, location.indexOf('_'));
               if(!jQuery(id+'.bb-open').length) {
                   BB.openPopup(id);
                   BB.switchMode('slide');
                   BB.onLocationChange(BB.getLocation());
               }
           }
        }
	});
}

/**
 * Sticky Bottom Functionality
 */
;(function( window, document, undefined ){

    // Plugin constructor
    var bbStickyBottom = function( elem ){
        this.elem = elem;
        this.$elem = jQuery(elem);
        this.stickyBtmEl = this.$elem.find('> div');
        this.elHeight = this.stickyBtmEl.height();
    };

    // Plugin prototype
    bbStickyBottom.prototype = {
        init: function() {
            this.stickyBtmEl.addClass('bb-sticky-btm-el');
            this.setStickyElHeight();
            this.stickyBannerClose();
            this.resizeFunc();
            return this;
        },

        stickyBannerClose: function() {
            var self = this;
            jQuery(self.stickyBtmEl).prepend( "<a href='#' class='sticky-btm-close'>X</a>" );
            self.$elem.find('.sticky-btm-close').on('click', function(e){
                e.preventDefault();
                self.stickyBtmEl.css('opacity', 0);
                setTimeout(function(){
                    self.$elem.hide();
                },300);
            })
        },

        setStickyElHeight: function() {
            var self = this;
            self.$elem.height(self.elHeight);
        },

        resizeFunc: function() {
            var self = this;
            jQuery(window).resize(function(){
                self.setStickyElHeight();
            });

        }
    };

    jQuery.fn.bbStickyBottom = function() {
        return this.each(function() {
            new bbStickyBottom(this).init();
        });
    };

})( window , document );

