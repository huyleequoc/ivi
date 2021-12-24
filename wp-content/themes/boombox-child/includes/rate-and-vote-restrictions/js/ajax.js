(function ($) {
    "use strict";

    var boombox_body = $('body');
    /**
     * Points
     */
    boombox_body.on('click', '.js-post-point .point-btn', function (e) {
        e.preventDefault();
        var _this               = $(this);
        var action              = _this.data( 'action' );
        var post_id             = _this.closest( '.js-post-point' ).data( 'post_id' );
        var container           = $( '.js-post-point[data-post_id=' + post_id + ']' );
        var mobile_container    = container.siblings( '.mobile-info' );
        _this           = container.find( '.point-btn[data-action=' + action + ']' );

        if (!post_id) {
            return;
        }

        _this.attr( 'disabled', 'disabled' );
        container.find( '.count').addClass( 'loading' );

        if ( _this.hasClass( 'active' ) ) {
            _this.removeClass( 'active' );
            $.post(
                boombox_ajax_params.ajax_url,
                {
                    action      : 'boombox_ajax_point_discard',
                    sub_action  : action,
                    id          : post_id
                },
                function ( response ) {
                    var data = $.parseJSON( response );
                    if ( data.status == true ) {
                        container.find( '.count .text' ).text( data.point_count );
                        mobile_container.find( '.mobile-votes-count').text( data.point_count );
                    }
                    container.find( '.count').removeClass( 'loading' );
                   _this.removeAttr( 'disabled' );
                }
            );
        } else {
            container.find( '.active' ).removeClass( 'active' );
            _this.addClass( 'active' );
            $.post(
                boombox_ajax_params.ajax_url,
                {
                    action      : 'boombox_ajax_point',
                    sub_action  : action,
                    id          : post_id
                },
                function ( response ) {
                    var data = $.parseJSON( response );
                    if ( data.status == true ) {
                        container.find( '.count .text' ).text( data.point_count );
                        mobile_container.find( '.mobile-votes-count').text( data.point_count );
                    }
                    container.find( '.count').removeClass( 'loading' );
                    _this.removeAttr( 'disabled' );
                }
            );
        }
    });

    /**
     * Reactions
     */
    boombox_body.on('click', '.js-reaction-item .reaction-vote-btn', function (e) {
        e.preventDefault();
        var disabled_class = 'disabled';
        var _this            = $(this);
        if( _this.hasClass( disabled_class ) ){
            return;
        }
        _this.addClass( disabled_class );
        _this.parent().addClass( 'voted' );

        var reaction     = _this.closest( '.reaction-item' ).data( 'reaction_id' );
        var container       = _this.closest( '.reaction-sections' );
        var post_id         = container.data( 'post_id' );

        if ( !post_id || !reaction ) {
            return;
        }

        $.post(
            boombox_ajax_params.ajax_url,
            {
                action      : 'boombox_ajax_reaction_add',
                post_id     : post_id,
                reaction_id : reaction
            },
            function ( response ) {
                _this.removeClass( disabled_class );
                var data = $.parseJSON( response );
                if ( data.status == true ) {
                    container.find( '.reaction-item' ).each( function(){
                        var reaction_id = $( this ).data( 'reaction_id' );

                        var reaction_total = data.reaction_total[ reaction_id ];
                        if( reaction_total ){
                            $( this ).find( '.reaction-stat' ).height( reaction_total.height + '%' );
                            $( this ).find( '.reaction-stat-count' ).text( reaction_total.total );
                        }

                        var reaction_restriction = data.reaction_restrictions[ reaction_id ];
                        if( reaction_restriction && false === reaction_restriction.can_react ){
                            $( this ).find( '.reaction-vote-btn' ).addClass( disabled_class );
                        }
                    } );
                }
            }
        );
    });

    /**
     * An object that keeps storing data that expires after 24 hours
     * It tries to use local browser storage if possible, or the cookie storage otherwise
     * @type {{get, set}}
     */
    var track_view_daily_cache_storage = (function(){
        var day_in_milliseconds = 1000 * 60 * 60 * 24;
        if( !! localStorage ){
            return {
                get: function( key ){
                    var expired = localStorage.getItem( '_expiration_' + key );

                    if ( expired === null ){
                        localStorage.removeItem( key );
                        return false;
                    } else {
                        var date = new Date();
                        if( date.getTime() >= expired ){
                            localStorage.removeItem( '_expiration_' + key );
                            localStorage.removeItem( key );
                            return false;
                        }
                    }

                    return localStorage.getItem( key );
                },
                set: function( key, value ) {
                    var date = new Date();
                    localStorage.setItem( '_expiration_' + key, date.getTime() + day_in_milliseconds );
                    localStorage.setItem( key, value );
                }
            }
        } else {
            return {
                get: function( key ){
                    return jQuery.cookie( key );
                },
                set: function( key, value ){
                    var date = new Date();
                    date.setTime( date.getTime() + ( 1000 * 60 * 60 * 24 ) );
                    jQuery.cookie( key, value, { expires: date } );
                }
            }
        }
    })();
    /**
     * Track single post views
     */
    (function(){
        if( !parseInt( boombox_ajax_params.track_view ) ) {
            return;
        }
        var post_id = $('article.single').data('post-id');
        if( !post_id ) {
            return;
        }

        // checking the cache to prevent sending unnecessary requests
        var is_cached = false;
        // should we start tracking or not

        var start_tracking = false;

        // holds the number of request that we can allow to do,
        // actually this is the restriction daily value for session or ip
        //
        // actually the value will be 0 or 1 for session restriction,
        // for greater values it must also contain information when we can send requests again
        // or we should track all the action on front too
        var track_view_request_cache = parseInt( boombox_ajax_params.track_view_request_cache );

        if( track_view_request_cache > 0 ){
            var cached_val = track_view_daily_cache_storage.get( post_id );
            if( !!cached_val ){
                cached_val = parseInt( cached_val );
                if( track_view_request_cache <= cached_val ){
                    is_cached = true;
                } else {
                    track_view_daily_cache_storage.set( post_id, cached_val + 1 );
                }
            } else {
                start_tracking = true;
            }
        }
        if( is_cached ){
            return;
        }


        // sending request to up view count
        (function(_post_id, _start_tracking){
            $.post(
                boombox_ajax_params.ajax_url,
                {
                    action: 'boombox_ajax_track_view',
                    post_id: _post_id
                },
                function (response) {
                    response = JSON.parse( response );
                    // if system returns false as tracking status, then we don't know when we should start tracking again,
                    // even if there's no cached info at all, we'll start tracking again later when the status comes true
                    if( _start_tracking && response.status !== false ){
                        track_view_daily_cache_storage.set( post_id, 1 );
                    }
                }
            );
        })(post_id, start_tracking);
    })();
})(jQuery);