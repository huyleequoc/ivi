jQuery(function ($) {
    'use strict';

    /**
     ** Perform AJAX register with facebook
     **/
    $( document ).on( 'click', '.facebook-login-button-js' , function(){

        if( ! boombox_fb_app_validity ) return false;

        var container    = $( this ).closest('.authentication');
        var _status = container.find( 'p.status-msg' );
        var is_nsfw_auth = container.hasClass( 'is-nsfw-auth' );

	    _status.removeClass( 'msg-success msg-error' ).addClass( 'msg-info' ).html( ajax_social_auth_object.loading_message ).show();

        FB.login( function( response ) {
            if( response.status == 'connected' ) {
	            _status.removeClass( 'msg-success msg-error' ).addClass( 'msg-info' ).html( ajax_social_auth_object.login_success_wait ).show();
                $.post(
                    ajax_social_auth_object.ajaxurl,
                    {
                        action: 'boombox_social_auth',
                        social_type: 'facebook',
                        redirect_url:ajax_social_auth_object.login_redirect_url,
                        access_token: response.authResponse.accessToken,
                        _nonce: ajax_social_auth_object.nonce

                    }, function( response ) {
                        if( response.success ) {
	                        _status.removeClass( 'msg-error msg-info' ).addClass( 'msg-success' ).html( response.data );
	                        document.location.href = ajax_social_auth_object.login_redirect_url;
                        } else {
	                        _status.removeClass( 'msg-success msg-info' ).addClass( 'msg-error' ).html( response.data );
                        }
                    }
                );
            }
            else {
	            _status.removeClass( 'msg-success msg-info' ).addClass( 'msg-error' ).html( ajax_social_auth_object.login_failed );
            }
        }, { scope: 'email' } );

        return false;
    });

    /**
     ** Perform AJAX register with Google
     **/
    $( document ).on( 'click', '.google-login-button-js' , function(){
        var google_params = {
            'clientid' : ajax_social_auth_object.google_oauth_id,
            'cookiepolicy' : 'single_host_origin',
            'callback' : 'OnGoogleAuth',
            'scope' : 'email profile'
        };
        gapi.auth.signIn( google_params );
        return false;
    });
    function GoggleOnLoad() {
        gapi.client.setApiKey( ajax_social_auth_object.google_api_key );
        gapi.client.load( 'plus', 'boombox', function(){} );
    }

});


function OnGoogleAuth( googleUser ) {
    var container    = jQuery( this ).closest('.authentication');
    var is_nsfw_auth = container.hasClass( 'is-nsfw-auth' );
    var redirect_url = ajax_social_auth_object.register_redirect_url;

    if(googleUser['status']['signed_in'] && googleUser['status']['method'] == 'PROMPT') {
        container.find( 'p.status').show().text( ajax_social_auth_object.loading_message );
        jQuery.post(
            ajax_social_auth_object.ajaxurl,
            {
                'action': 'boombox_social_auth',
                'social_type': 'google',
                'access_token': googleUser.access_token,
                '_nonce': ajax_social_auth_object.nonce
            }, function( response ) {
                var data = jQuery.parseJSON( response );
                if( data.error != '' )
                    return container.find( 'p.status' ).show().text( data.error );
                else{
                    if( is_nsfw_auth ){
                        redirect_url = ajax_social_auth_object.nsfw_redirect_url;
                    }

                    if( 'login' == data.action ){
                        redirect_url = ajax_social_auth_object.login_redirect_url;
                    }
                    document.location.href = redirect_url;
                }
            }
        );
    }
    else {
        if( googleUser['error'] != 'immediate_failed' )
            container.find( 'p.status' ).show().text( ajax_social_auth_object.login_failed );
    }
}