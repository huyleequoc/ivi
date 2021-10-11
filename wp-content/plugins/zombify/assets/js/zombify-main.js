jQuery(document).ready(function ($) {
	zfContainerWidth();

	// Zombify popup open / close
    jQuery(document).on('click', '.zf-create-popup', function (e) {
	    e.preventDefault();
        jQuery('.zombify-create-popup').addClass('zf-open');
	});

    jQuery(document).on('click', '.zombify-create-popup .zf-popup_close', function (e) {
	    e.preventDefault();
        jQuery('.zombify-create-popup').removeClass('zf-open');
	});

	// worked only for Boombox (close create popup for opening authentication popup (in boombox))
    jQuery(document).on('click', '.js-authentication', function (e) {
	    e.preventDefault();
        jQuery('.zombify-create-popup').removeClass('zf-open');
	});

    jQuery(document).on('click', '.zombify-submit-popup .zf-popup_close', function (e) {
	    e.preventDefault();
        jQuery('.zombify-submit-popup').removeClass('zf-open');
	});

    // Close zombify popups by clicking outside of it
    jQuery(document).mouseup(function(e) {
        var container = jQuery('.zombify-popup_body');

        if (!container.is(e.target) && container.has(e.target).length === 0) {
            if( container.parent('div').hasClass('zf-open') ) {
                container.parent('div').removeClass('zf-open');
            }
        }
    });

	// submission page single post  actions Toggle
    jQuery(document).on('click', '.js-zf-actions-toggle', function (e) {
	    e.preventDefault();
	    e.stopPropagation();
        jQuery(this).parent().toggleClass('zf-open');
	});
    jQuery(document).on('click', 'body', function () {
        jQuery('.zf-actions.zf-open').removeClass('zf-open');
	});

	//Buddypress filter
    // $(document).on('change', '#submissions-filter-by', function() {
     //    var _this = $(this),
     //        _form = _this.closest( 'form' );
    //
     //    _form.trigger( 'submit' );
    // });

    /** Open list/Ranked List voting  (up) */
    jQuery(document).on("click", ".zf-vote_up", function(){

        jQuery(this).parent().addClass('zf-loading');

        var zf_post_id = jQuery(this).parent().attr("data-zf-post-id");
        var zf_post_parent_id = jQuery(this).parent().attr("data-zf-post-parent-id");

        jQuery.ajax({
            url: zf_main.ajaxurl,
            type: 'POST',
            data: {post_id: zf_post_id, post_parent_id: zf_post_parent_id, vote_type: 'up', action: 'zombify_post_vote'},
            dataType: 'json',
            success: function (data) {
                jQuery(".zf-vote_count[data-zf-post-id='"+data.post_id+"'] .zf-vote_number").html( data.votes );
                jQuery(".zf-vote_count[data-zf-post-id='"+data.post_id+"']").parent().removeClass('zf-loading');
            }
        });
    });

    /** Open list/Ranked List voting  (down) */
    jQuery(document).on("click", ".zf-vote_down", function(e){

        jQuery(this).parent().addClass('zf-loading');

        var zf_post_id = jQuery(this).parent().attr("data-zf-post-id");
        var zf_post_parent_id = jQuery(this).parent().attr("data-zf-post-parent-id");

        jQuery.ajax({
            url: zf_main.ajaxurl,
            type: 'POST',
            data: {post_id: zf_post_id, post_parent_id: zf_post_parent_id, vote_type: 'down', action: 'zombify_post_vote'},
            dataType: 'json',
            success: function (data) {
                jQuery(".zf-vote_count[data-zf-post-id='"+data.post_id+"'] .zf-vote_number").html( data.votes );
                jQuery(".zf-vote_count[data-zf-post-id='"+data.post_id+"']").parent().removeClass('zf-loading');
            }
        });
    });
});

jQuery( window ).resize(function() {
    zfContainerWidth();
});

function zfContainerWidth() {
    var container = jQuery('.zombify-screen'),
        screenWidth = container.width();

    switch (true) {
        case screenWidth > 850:
            container.removeClass(function (index, css) {
                return (css.match(/(^|\s)zf-screen-\S+/g) || []).join(' ');
            });
            container.addClass('zf-screen-lg');
            break;

        case screenWidth <= 850 && screenWidth > 700:
            container.removeClass(function (index, css) {
                return (css.match(/(^|\s)zf-screen-\S+/g) || []).join(' ');
            });
            container.addClass('zf-screen-md');
            break;

        case screenWidth <= 700 && screenWidth > 550:
            container.removeClass(function (index, css) {
                return (css.match(/(^|\s)zf-screen-\S+/g) || []).join(' ');
            });
            container.addClass('zf-screen-sm');
            break;

        case screenWidth <= 550:
            container.removeClass(function (index, css) {
                return (css.match(/(^|\s)zf-screen-\S+/g) || []).join(' ');
            });
            container.addClass('zf-screen-xs');
            break;
    }

    /** Detect Device Type */
    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        zf_isMobile = true;
        jQuery('html').addClass('zf-mobile');
    } else {
        zf_isMobile = false;
        jQuery('html').addClass('zf-desktop');
    }
}