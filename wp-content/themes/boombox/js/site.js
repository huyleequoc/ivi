/**
 * ************ Dom Ready ************
 */
(function ($) {
    "use strict";

    /* Functions */
    $('.has-full-post-button .post-list.standard .post-thumbnail img').each(function(){
        ShowFullPost($(this));
    });

    /* BB Side Navigation */
    bbSideNav();

    /* Post Gallery  */
    $('.bb-post-gallery').bbPostGallery();

    /* Sticky Sidebar  */
    $('.sticky-sidebar').bbStickySidebar();

    /* Masonry Post   */
    postMasonry();

    /* Tabs */
    initializeTabs();

    /* Set Placeholders */
    setFormPlaceholders('.woocommerce','.form-row');

    /* Featured Carousel */
    bbFeaturedCarousel();

    /* LightModal Popup Plugin */
    $('.js-inline-popup').lightModal ({});

    /* Scroll Area Plugin */
    $('.bb-scroll-area.arrow-control').bbScrollableArea({});

    /* Mobile Navigation */
    bbMobileNavigation();

    /* Shows and Hides Some Elements on Scroll */
    showHideElementsOnScroll();

    /* Page Top Animation */
    animationPageTop();

    /* Disabled Links Behaviour */
    disabledLinksBehaviour();

    /* Hyena GIF  */
    HyenaGIF();


    // Post Featured Video autoplay
    if (bb.html.hasClass('video')) {
        $('.post-thumbnail video').not('.gif-video').each(function () {
            var video = $(this)[0];
            featuredVideo(video);
        });
  
        $(' video.gif-video').each(function () {
            var video = $(this)[0];
            GIFvideo(video);
        });

        $(' img.gif-image').each(function () {
            var img = $(this)[0];
            GIFtoVideo(img);
        });
    }

    /* ************ Ends - Gif and Video Functionality ************ */


    /**
     * ************ Load More Content ************
     */
    if ($('#load-more-button').length) {

        var load_more_btn = $('#load-more-button');
        var loading = false;
        var firstClick = false;
        var loadType = load_more_btn.data('scroll');


        $('#load-more-button').on("click", function () {
            if (loading) return;

            loading = true;

            var next_page_url = load_more_btn.attr('data-next_url');

            load_more_btn.parent().addClass('loading');
            jQuery.post(next_page_url, {},
                function (response) {
                    var html = $(response);
                    var container = html.find('#post-items');
                    var articles = container.find('.post-item').addClass('item-added');
                    var more_btn = html.find('#load-more-button');

                    $('#post-items').append(articles);


                    // load new content
                    $('body').trigger( 'bbNewContentLoaded', [ articles ] );

                    // Post Featured Video autoplay
                    if ($("html").hasClass('video')) {
                        $('#post-items  .item-added video').not('.gif-video').each(function () {
                            var video = $(this)[0];
                            featuredVideo(video);
                        });
                        $('#post-items  .item-added video.gif-video').each(function () {
                            var video = $(this)[0];
                            GIFvideo(video);
                        });

                        $('#post-items  .item-added img.gif-image').each(function () {
                            var img = $(this)[0];
                            GIFtoVideo(img);
                        });
                    }

                    $('.has-full-post-button .post-list.standard .item-added .post-thumbnail img').each(function(){
                        ShowFullPost($(this));
                    });

                    $('#post-items  .item-added').removeClass('item-added');

                    load_more_btn.parent().removeClass('loading');

                    if (more_btn.length > 0) {
                        var next_url = more_btn.data('next_url');
                        load_more_btn.attr('data-next_url', next_url);
                    } else {
                        load_more_btn.parent().remove();
                    }

                    loading = false;
                    firstClick = true;
                    if (loadType === 'on_demand' || loadType === 'infinite_scroll') {
                        infiniteScroll();
                    }
                }
            );

        });

        var lm_scrollPos;
        var lm_buttonPos;
        var infiniteScroll = function () {

            if(!$('#load-more-button').length) {
                return;
            }
            if (loadType === 'on_demand' && !firstClick) {
                return false;
            }

            lm_scrollPos = $(window).scrollTop();
            lm_buttonPos = $('#load-more-button').offset();

            $(window).scroll(function () {
                var scroll = $(window).scrollTop();

                if (scroll > lm_scrollPos) {
                    if (scroll >= lm_buttonPos.top - bb.windowHeight) {
                        load_more_btn.trigger("click");
                    }
                }
            });
        }

        if (loadType === 'infinite_scroll') {
            infiniteScroll();
        }

    }

    $("body").on( "alnp-post-loaded", function(){
        $("div#balnp_content_container  .item-added video").not(".gif-video").each(function () {
            var video = $(this)[0];
            featuredVideo(video);
        });

        $("div#balnp_content_container  .item-added video.gif-video").each(function () {
            var video = $(this)[0];
            GIFvideo(video);
        });

        $("div#balnp_content_container  .item-added img.gif-image").each(function () {
            var img = $(this)[0];
            GIFtoVideo(img);
        });


        if (typeof ZombifyOnAjax !== 'undefined' && ZombifyOnAjax) {
            ZombifyOnAjax();
        }

        $("div#balnp_content_container .item-added").removeClass("item-added");
    } );

    /* ************ Ends - Load More Content ************ */

})(jQuery);

/**
 * ************ Window Load ************
 */
jQuery(window).load(function () {
    /* Sticky Navbar */
    jQuery('.bb-sticky.sticky-smart').bbSticky({
        scrollStyle: 'smart',
        fixedOffsetFunc: function(){
            return  getSetAdminBars();
        },
        animation: true
    });
    jQuery('.bb-sticky.sticky-classic').bbSticky({
        fixedOffsetFunc: function(){
            return  getSetAdminBars();
        }
    });

    /* Sticky Fixed Next Page */
    jQuery('.bb-sticky.bb-floating-navbar').bbSticky({
        scrollStyle: 'classic',
        topOffsetFunc: function() {
            return getHeaderAreaHeight();
        },
        keepWrapperHeight: false,
        fullWidth: true,
        animation: true
    });

    jQuery('.bb-sticky.bb-floating-navbar').bbSticky({
        scrollStyle: 'classic',
        topOffsetFunc: function() {
            return getHeaderAreaHeight();
        },
        keepWrapperHeight: false,
        fullWidth: true,
        animation: true
    });

    /* Toggle Functionality */
    jQuery.bbToggle();

    /* Focus Functionality */
    jQuery('.bb-focus').bbFocus();

    /* Floating Pagination */
    setFormPlaceholders('.woocommerce','.form-row');

    /* Sticky Bottom Functionality */
    jQuery('.bb-sticky-btm').bbStickyBottom();
});


