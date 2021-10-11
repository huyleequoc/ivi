window.fbAsyncInit = function() {
    FB.init({
        appId: gfy.fb_app_id,
        xfbml: true,
        version: "v2.8"
    });

    window.gfyApiShareToFB = function( title, description, image, url ) {

        FB.login(
            function( response ) {
                if ( response.status === "connected" ) {
                    var shareData = {
                        "og:title":         title,
                        "og:description":   description,
                        "og:url":           url
                    };
                    if ( image ) {
                        shareData[ "og:image" ] = image;
                    }

                    FB.ui(
                        {
                            method: "share_open_graph",
                            action_type: "og.shares",
                            action_properties: JSON.stringify( {
                                object : shareData
                            } )
                        },
                        function( response ) {}
                    );
                }
            },
            { scope: "publish_actions" }
        );
    };
};

if ( typeof window.FB !== "undefined" ) {
    window.fbAsyncInit();
}

(function(){

    if( ! jQuery( 'body' ).find( "#fb-root" ) ){
        jQuery( 'body' ).append( '<div id="fb-root"></div>' );
    }

    /***** Facebook share */
    jQuery( '#gfy-share-trophy-fb' ).on( 'click', function( event ){
        event.preventDefault();

        var _dataHolder = jQuery( this ).closest( '.gfy-share-data' );
        if( _dataHolder.length ) {
            gfyApiShareToFB( _dataHolder.data('title'), _dataHolder.data('description'), _dataHolder.data('image'), _dataHolder.data('url') );
        }

    } );

    /***** Twiiter share */
    jQuery( '#gfy-share-trophy-tw' ).on( 'click', function( event ){
        event.preventDefault();

        var _dataHolder = jQuery( this ).closest( '.gfy-share-data' );
        if( _dataHolder.length ) {
            var
                url = 'https://twitter.com/intent/tweet?text=' + _dataHolder.data('description') + '&url=' + _dataHolder.data('url'),
                width = 570,
                height = 300,
                top = ( screen.height / 2 ) - ( height / 2 ),
                left = ( screen.width / 2 ) - ( width / 2 );

            window.open( url, '_blank', 'location=yes,scrollbars=yes,status=yes,width='+width+',height='+height+',top='+top+',left='+left );
        }
    } );

})();