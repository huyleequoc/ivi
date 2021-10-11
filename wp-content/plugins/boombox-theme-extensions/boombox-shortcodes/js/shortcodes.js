jQuery( function( $ ) {
	'use strict';

	var boombox_contact_form_selector = '.bb-contact-form';
	if ( $( boombox_contact_form_selector ).length ) {
		var contact_form_captcha = null;
		var contact_captcha_container = $( boombox_contact_form_selector ).find( '#boombox-contact-captcha' );

		if ( 'image' === params.captcha_type ) {
			refresh_captcha( $( boombox_contact_form_selector ), $( boombox_contact_form_selector ).attr( 'action' ) );
		} else if ( 'google' === params.captcha_type ) {
			$( 'body' ).on( 'boombox/grecaptcha_loaded', function() {
				if ( contact_form_captcha === null ) {
					contact_form_captcha = grecaptcha.render( contact_captcha_container.attr( 'id' ), {
						sitekey: contact_captcha_container.data( 'boombox-sitekey' ),
						theme  : 'light'
					} );
				} else {
					grecaptcha.reset( contact_form_captcha );
				}
			} );
		} else if ( 'google_v3' === params.captcha_type ) {
			grecaptcha.ready( function() {
				grecaptcha.execute( contact_captcha_container.data( 'boombox-sitekey' ) )
					.then( function( token ) {
						document.getElementById( 'g-recaptcha-response-contact-form' ).value = token;
					} );
			} );
		}
	}

	/**
	 * Contact Form Submit Event
	 */
	$( 'body' ).on( 'submit', boombox_contact_form_selector, function( e ) {
		e.preventDefault();

		var _this             = $( this ),
		    name              = _this.find( '[name=boombox_name]' ),
		    email             = _this.find( '[name=boombox_email]' ),
		    comment           = _this.find( '[name=boombox_comment]' ),
		    gdpr              = _this.find( '[name=boombox_gdpr]' ),
		    captcha_code      = null,
		    message_container = _this.parent().find( '.bb-contact-form-msg' ),
		    submit_btn        = _this.find( '[name=submit]' );

		if ( params.captcha_type === 'image' ) {
			captcha_code = _this.find( '[name="boombox_captcha_code"]' );
		} else if ( 'google' === params.captcha_type || 'google_v3' === params.captcha_type ) {
			captcha_code = _this.find( '[name="g-recaptcha-response"]' );
		}
		var check_captcha = (captcha_code && captcha_code.length) ? 1 : 0;

		message_container.html( '' ).removeClass( 'msg-error msg-success' );
		_this.find( '.error' ).removeClass( 'error' );
		submit_btn.attr( 'disabled', 'disabled' );

		var data = {
			action       : 'contact_form_submit',
			name         : name.val(),
			email        : email.val(),
			comment      : comment.val(),
			check_captcha: check_captcha
		};
		if ( gdpr.length ) {
			data.gdpr = gdpr.is( ':checked' ) ? 1 : 0;
		}
		if ( check_captcha ) {
			data.captcha = captcha_code.val();
		}

		$.post(
			params.ajax_url,
			data,
			function( response ) {
				var data = $.parseJSON( response );

				if ( !$.isEmptyObject( data.valid ) ) {
					var error_message = data.message ? data.message : params.error_message;
					message_container.addClass( 'msg-error' ).html( error_message );
					if ( check_captcha ) {
						reset_captcha();
					}
					show_form_errors( data.valid, name, email, comment, captcha_code );
				} else if ( data.sent ) {
					message_container.addClass( 'msg-success' ).html( params.success_message );
					_this[0].reset();

					if ( check_captcha ) {
						reset_captcha();
					}
				} else {
					message_container.addClass( 'msg-error' ).html( params.wrong_message );
				}
				submit_btn.removeAttr( 'disabled' );
			}
		);

	} );

	/**
	 * Refresh Captcha
	 */
	$( 'body' ).on( 'click', '.boombox-refresh-captcha', function( e ) {
		e.preventDefault();
		var form = $( this ).closest( 'form' );
		var type = form.attr( 'action' );
		refresh_captcha( form, type );
	} );

	/**
	 * Add to Contact Forms Fields 'error' class
	 *
	 * @param is_valid
	 * @param name
	 * @param email
	 * @param comment
	 * @param captcha_code
	 */
	function show_form_errors( is_valid, name, email, comment, captcha_code ) {
		if ( is_valid.hasOwnProperty( 'name' ) && !is_valid.name ) {
			name.addClass( 'error' );
		}

		if ( is_valid.hasOwnProperty( 'email' ) && !is_valid.email ) {
			email.addClass( 'error' );
		}

		if ( is_valid.hasOwnProperty( 'comment' ) && !is_valid.comment ) {
			comment.addClass( 'error' );
		}

		if ( is_valid.hasOwnProperty( 'captcha' ) && !is_valid.captcha_code ) {
			captcha_code.addClass( 'error' );
		}
	}

	/**
	 * Reset captcha if it possible
	 * @returns {boolean}
	 */
	function reset_captcha() {
		if ( 'image' === params.captcha_type ) {
			refresh_captcha( $( boombox_contact_form_selector ), $( boombox_contact_form_selector ).attr( 'action' ) );
		} else if ( 'google' === params.captcha_type ) {
			if ( contact_form_captcha === null ) {
				contact_form_captcha = grecaptcha.render( contact_captcha_container.attr( 'id' ), {
					sitekey: contact_captcha_container.data( 'sitekey' ),
					theme  : 'light'
				} );
			} else {
				grecaptcha.reset( contact_form_captcha );
			}
		} else {
			return false;
		}

		return true;
	}

	/**
	 * Refresh Captcha Function
	 *
	 * @param selector
	 */
	function refresh_captcha( selector, type ) {
		selector.find( '.captcha' ).attr( 'src', params.captcha_file_url + '?' + Math.random() + '&type=' + type ).closest( '.captcha-container' ).removeClass( 'loading' );
	}
} );