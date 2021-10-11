(function( $ ) {
	'use strict';

	var boombox_login_form            = $( 'form#boombox-login' ),
	    boombox_register_form         = $( 'form#boombox-register' ),
	    boombox_forgot_password_form  = $( 'form#boombox_forgot_password' ),
	    boombox_reset_password_form   = $( 'form#boombox_reset_password' ),
	    boombox_login_form_captcha    = null,
	    boombox_register_form_captcha = null;

	/**
	 * Refresh Captcha Function
	 *
	 * @param selector
	 */
	var boombox_refresh_captcha = function( selector, type ) {
		selector.find( '.captcha' ).attr( 'src', ajax_auth_object.captcha_file_url + '?' + Math.random() + '&type=' + type ).closest( '.captcha-container' ).removeClass( 'loading' );
		selector.find( '.bb-captcha-field' ).val( '' );
	};

	/**
	 * Manually open authentication modal
	 * @param string type Modal type: Accepts: login, register, forgot-password
	 */
	var boombox_open_auth_popup = function( type ) {
		switch ( type ) {
			case 'login':
				var selector = '.js-authentication[href="#sign-in"]';
				break;
			case 'register':
				var selector = '.js-authentication[href="#registration"]';
				break;
			case 'forgot-password':
			case 'fp':
				var selector = '.js-authentication[href="#forgot-password"]';
				break;
			case 'reset-password':
			case 'rp':
				var selector = '.js-authentication[href="#reset-password"]';
				break;
			default:
				var selector = false;
		}

		if ( selector && $( selector ).length > 0 ) {
			$( selector ).first().trigger( 'click' );
		}
	};

	/**
	 * Check password strength
	 * @param passField
	 * @param confirmPassField
	 * @param strengthNode
	 * @param blacklistArray
	 */
	var boombox_check_pass_strength = function( passField, confirmPassField, strengthNode, blacklistArray ) {
		var pass                          = passField.val(),
		    confirmPass                   = (confirmPassField && confirmPassField.length) ? confirmPassField.val() : pass,
		    strength,
		    strengthNodeText,
		    strengthNodeClass             = 'bb-pass-strength',
		    passStrengthHtmlTemplate,
		    passStrengthPgBarHtmlTemplate = '<div class="pass-progress-bar"></div>';

		if ( !pass ) {
			strengthNode.empty();
			return;
		}

		blacklistArray = blacklistArray.concat( wp.passwordStrength.userInputBlacklist() );
		passStrengthHtmlTemplate = '<span class="pass-msg-title">' + ajax_auth_object.messages.password_strength + ':</span>';

		strength = wp.passwordStrength.meter( pass, blacklistArray, confirmPass );

		switch ( strength ) {
			case -1:
				strengthNodeText = pwsL10n.unknown;
				strengthNodeClass += ' bb-weak-pass';
				break;
			case 2:
				strengthNodeText = pwsL10n.bad;
				strengthNodeClass += ' bb-weak-pass';
				break;
			case 3:
				strengthNodeText = pwsL10n.good;
				strengthNodeClass += ' bb-good-pass';
				break;
			case 4:
				strengthNodeText = pwsL10n.strong;
				strengthNodeClass += ' bb-strong-pass';
				break;
			case 5:
				strengthNodeText = ajax_auth_object.messages.password_mismatch;
				passStrengthHtmlTemplate = '';
				passStrengthPgBarHtmlTemplate = '';
				strengthNodeClass += ' bb-mismatch-pass';
				break;
			default:
				strengthNodeText = pwsL10n['short'];
				strengthNodeClass += ' bb-short-pass';
		}

		strengthNode.html( '<div class="' + strengthNodeClass + '"><p class="pass-status-msg">' + passStrengthHtmlTemplate + '<span class="pass-msg-value"> ' + strengthNodeText + '</span></p>' + passStrengthPgBarHtmlTemplate + '</div>' );
	};

	/**
	 * Initialize Authentication Popup
	 */
	var initAuthenticationPopup = function( elements ) {
		elements.lightModal( {
			beforeShow: function() {
				var is_nsfw = $( this ).hasClass( 'entry-nsfw' );
				if ( boombox_login_form.length > 0 ) {
					if ( is_nsfw ) {
						boombox_login_form.closest( '.authentication' ).addClass( 'is-nsfw-auth' );
					}
					boombox_login_form[0].reset();
					boombox_login_form.find( '.error' ).removeClass( 'error' );
					boombox_login_form.find( '.status-msg' ).html( '' );
					if ( ajax_auth_object.enable_login_captcha ) {
						if ( 'image' === ajax_auth_object.captcha_type ) {
							boombox_refresh_captcha( boombox_login_form, boombox_login_form.attr( 'action' ) );
						} else if ( 'google' === ajax_auth_object.captcha_type ) {
							var login_captcha_container = boombox_login_form.find( '#boombox-login-captcha' );
							if ( boombox_login_form_captcha === null ) {
								boombox_login_form_captcha = grecaptcha.render( login_captcha_container.attr( 'id' ), {
									sitekey: login_captcha_container.data( 'boombox-sitekey' ),
									theme  : 'light'
								} );
							} else {
								grecaptcha.reset( boombox_login_form_captcha );
							}
						} else if ( 'google_v3' === ajax_auth_object.captcha_type ) {
							var login_captcha_container = boombox_login_form.find( '#boombox-login-captcha' );

							grecaptcha.ready( function() {
								grecaptcha.execute( login_captcha_container.data( 'boombox-sitekey' ) )
									.then( function( token ) {
										document.getElementById( 'g-recaptcha-response-login' ).value = token;
									} );
							} );

						}
					}
				}
				if ( boombox_register_form.length > 0 ) {
					if ( is_nsfw ) {
						boombox_login_form.closest( '.authentication' ).addClass( 'is-nsfw-auth' );
					}
					boombox_register_form[0].reset();
					boombox_check_pass_strength( $( '[name="signonpassword"]' ), false, $( '#bb-register-pass-strength-result' ), [] );
					boombox_register_form.find( '.error' ).removeClass( 'error' );
					boombox_register_form.find( '.status-msg' ).html( '' );
					if ( ajax_auth_object.enable_registration_captcha ) {
						if ( 'image' === ajax_auth_object.captcha_type ) {
							boombox_refresh_captcha( boombox_register_form, boombox_register_form.attr( 'action' ) );
						} else if ( 'google' === ajax_auth_object.captcha_type ) {
							var register_captcha_container = boombox_register_form.find( '#boombox-register-captcha' );
							if ( boombox_register_form_captcha === null ) {
								boombox_register_form_captcha = grecaptcha.render( register_captcha_container.attr( 'id' ), {
									sitekey: register_captcha_container.data( 'boombox-sitekey' ),
									theme  : 'light'
								} );
							} else {
								grecaptcha.reset( boombox_register_form_captcha );
							}
						} else if ( 'google_v3' === ajax_auth_object.captcha_type ) {
							grecaptcha.ready( function() {
								grecaptcha.execute( login_captcha_container.data( 'boombox-sitekey' ) )
									.then( function( token ) {
										document.getElementById( 'g-recaptcha-response-register' ).value = token;
									} );
							} );
						}
					}
				}
				if ( boombox_forgot_password_form.length > 0 ) {
					boombox_forgot_password_form[0].reset();
					boombox_forgot_password_form.find( '.error' ).removeClass( 'error' );
					boombox_forgot_password_form.find( '.status-msg' ).html( '' );
				}
			}
		} );
	};

	/**
	 * Open auth popup
	 */
	if ( ajax_auth_object.trigger_action ) {
		if ( '-1' != $.inArray( ajax_auth_object.trigger_action, ['login', 'register', 'forgot-password', 'fp', 'reset-password', 'rp'] ) ) {
			$( window ).on( 'load', function() {
				boombox_open_auth_popup( ajax_auth_object.trigger_action );
			} );
		}
	}

	$.validator.addMethod( "loginRegex", function( value, element ) {
		return this.optional( element ) || /^[a-z0-9\-\_]+$/i.test( value );
	} );

	/**
	 * Authentication Popup
	 */
	initAuthenticationPopup( $( '.js-authentication' ) );
	$( 'body' ).on( 'bbNewContentLoaded', function( e, articles ) {
		initAuthenticationPopup( articles.find( '.js-authentication' ) );
	} );

	/**
	 * Client side form validation for "login" form
	 */
	if ( boombox_login_form.length > 0 ) {
		boombox_login_form.validate( {
			errorPlacement: function( error, element ) {
			}
		} );

	}

	/**
	 * Client side form validation for "registration" form
	 */
	if ( boombox_register_form.length > 0 ) {
		boombox_register_form.validate( {
			rules         : {
				signonusername: {
					loginRegex: true
				},
				signongdpr    : {
					required: true
				},
				password2     : {
					equalTo: '#signonpassword'
				}
			},
			invalidHandler: function( event, validator ) {
				console.log( $( this ) );
			},
			errorPlacement: function( error, element ) {
			},
			highlight     : function( element, errorClass ) {
				$( element ).addClass( errorClass );
				if ( $( element ).is( ':checkbox' ) ) {
					$( element ).closest( 'div' ).addClass( 'bb-chkb-err' );
				}
			},
			unhighlight   : function( element, errorClass ) {
				$( element ).removeClass( errorClass );

				if ( $( element ).is( ':checkbox' ) ) {
					$( element ).closest( 'div' ).removeClass( 'bb-chkb-err' );
				}
			}
		} );

		boombox_register_form.on( 'keyup', '[name="signonpassword"]', function( e ) {
			boombox_check_pass_strength( $( '[name="signonpassword"]' ), false, $( '#bb-register-pass-strength-result' ), [] );
		} );
	}

	/**
	 * Client side form validation for "forgot password" form
	 */
	if ( boombox_forgot_password_form.length > 0 ) {
		boombox_forgot_password_form.validate( {
			errorPlacement: function( error, element ) {
			}
		} );
	}

	/**
	 * Client side form validation for "reset password" form
	 */
	if ( boombox_reset_password_form.length ) {
		boombox_reset_password_form.validate( {
			rules         : {
				rppassword       : {
					required: true
				},
				rpconfirmpassword: {
					required: true,
					equalTo : '[name="rppassword"]'
				}
			},
			errorPlacement: function( error, element ) {
			}
		} );
		boombox_reset_password_form.on( 'keyup', '[name="rppassword"], [name="rpconfirmpassword"]', function() {
			boombox_check_pass_strength( $( '[name="rppassword"]' ), $( '[name="rpconfirmpassword"]' ), $( '#bb-rp-pass-strength-result' ), [] );
		} );
	}

	/**
	 * Perform AJAX login on form submit
	 */
	boombox_login_form.on( 'submit', function( e ) {
		var _this   = $( this ),
		    _status = _this.parent().find( 'p.status-msg' );

		if ( !_this.valid() ) {
			_status.removeClass( 'msg-success msg-info' ).addClass( 'msg-error' ).html( ajax_auth_object.invalid_message ).show();
			return false;
		}

		var user_email    = _this.find( '[name="useremail"]' ).val(),
		    password      = _this.find( '[name="password"]' ).val(),
		    rememberField = _this.find( '[name="rememberme"]' ),
		    security      = _this.find( '[name="security"]' ).val(),
		    is_nsfw_auth  = _this.closest( '.authentication' ).hasClass( 'is-nsfw-auth' ),
		    redirect_url  = is_nsfw_auth ? ajax_auth_object.nsfw_redirect_url : ajax_auth_object.login_redirect_url,
		    data          = {
			    action   : 'boombox_ajax_login',
			    useremail: user_email,
			    password : password,
			    security : security,
			    redirect : redirect_url
		    };

		if ( rememberField.length ) {
			data.remember = rememberField.is( ':checked' ) ? 1 : 0;
		}

		if ( ajax_auth_object.enable_login_captcha ) {
			if ( ajax_auth_object.captcha_type === 'image' ) {
				data.captcha = _this.find( '[name="captcha-code"]' ).val();
			} else if ( 'google' === ajax_auth_object.captcha_type || 'google_v3' === ajax_auth_object.captcha_type ) {
				data.captcha = _this.find( '[name="g-recaptcha-response"]' ).val();
			}
		}

		_status.removeClass( 'msg-success msg-error' ).addClass( 'msg-info' ).html( ajax_auth_object.loading_message ).show();

		$.post(
			ajax_auth_object.ajaxurl,
			data,
			function( response ) {
				if ( response.success == true ) {
					_status.removeClass( 'msg-error msg-info' ).addClass( 'msg-success' ).html( response.data.message );
					window.location.href = redirect_url;
				} else {
					_status.removeClass( 'msg-success msg-info' ).addClass( 'msg-error' ).html( response.data.message );
					if ( ajax_auth_object.enable_login_captcha ) {
						if ( ajax_auth_object.captcha_type === 'image' ) {
							boombox_refresh_captcha( _this, _this.attr( 'action' ) );
						} else if ( ajax_auth_object.captcha_type === 'google' ) {
							grecaptcha.reset( boombox_login_form_captcha );
						}
					}
				}
			}
		);
		e.preventDefault();
	} );

	/**
	 * Perform AJAX register on form submit
	 */
	boombox_register_form.on( 'submit', function( e ) {
		var _this   = $( this ),
		    _status = _this.parent().find( 'p.status-msg' );

		if ( !_this.valid() ) {
			_status.removeClass( 'msg-success msg-info' ).addClass( 'msg-error' ).html( ajax_auth_object.invalid_message ).show();
			return false;
		}

		var username     = _this.find( '[name="signonusername"]' ).val(),
		    useremail    = _this.find( '[name="signonemail"]' ).val(),
		    password     = _this.find( '[name="signonpassword"]' ).val(),
		    gdpr         = _this.find( '[name="signongdpr"]' ),
		    security     = _this.find( '[name="signonsecurity"]' ).val(),
		    is_nsfw_auth = _this.closest( '.authentication' ).hasClass( 'is-nsfw-auth' ),
		    redirect_url = is_nsfw_auth ? ajax_auth_object.nsfw_redirect_url : ajax_auth_object.register_redirect_url,
		    data         = {
			    action   : 'boombox_ajax_register',
			    username : username,
			    useremail: useremail,
			    password : password,
			    security : security,
			    redirect : redirect_url
		    };

		if ( gdpr.length ) {
			data.gdpr = gdpr.is( ':checked' ) ? 1 : 0;
		}

		if ( ajax_auth_object.enable_registration_captcha ) {
			if ( ajax_auth_object.captcha_type === 'image' ) {
				data.captcha = _this.find( '[name="signoncaptcha"]' ).val();
			} else if ( 'google' === ajax_auth_object.captcha_type || 'google_v3' === ajax_auth_object.captcha_type ) {
				data.captcha = _this.find( '[name="g-recaptcha-response"]' ).val();
			}
		}

		_status.removeClass( 'msg-success msg-error' ).addClass( 'msg-info' ).html( ajax_auth_object.loading_message ).show();

		$.post(
			ajax_auth_object.ajaxurl,
			data,
			function( response ) {
				if ( response.success == true ) {
					_status.removeClass( 'msg-error msg-info' ).addClass( 'msg-success' ).html( response.data.message );
					var _need_activation = response.data.need_activation || false;
					if ( _need_activation == false ) {
						window.location.href = redirect_url;
					} else {
						_this[0].reset();
					}
				} else {
					_status.removeClass( 'msg-success msg-info' ).addClass( 'msg-error' ).html( response.data.message );
					if ( ajax_auth_object.enable_registration_captcha ) {
						if ( ajax_auth_object.captcha_type === 'image' ) {
							boombox_refresh_captcha( _this, _this.attr( 'action' ) );
						} else if ( ajax_auth_object.captcha_type === 'google' ) {
							grecaptcha.reset( boombox_register_form_captcha );
						}
					}
				}
			}
		);
		e.preventDefault();
	} );

	/**
	 * Perform AJAX forget password on form submit
	 */
	boombox_forgot_password_form.on( 'submit', function( e ) {
		var _this   = $( this ),
		    _status = _this.parent().find( 'p.status-msg' );

		if ( !_this.valid() ) {
			_status.removeClass( 'msg-success msg-info' ).addClass( 'msg-error' ).html( ajax_auth_object.invalid_message ).show();
			return false;
		}

		var userlogin = _this.find( '[name="userlogin"]' ).val(),
		    security  = _this.find( '[name="forgotsecurity"]' ).val();

		_status.removeClass( 'msg-success msg-error' ).addClass( 'msg-info' ).html( ajax_auth_object.loading_message ).show();

		$.post(
			ajax_auth_object.ajaxurl,
			{
				action   : 'boombox_ajax_forgot_password',
				userlogin: userlogin,
				security : security
			},
			function( response ) {
				if ( response.success ) {
					_status.removeClass( 'msg-error msg-info' ).addClass( 'msg-success' ).html( response.data.message );
					_this.remove();
				} else {
					_status.removeClass( 'msg-success msg-info' ).addClass( 'msg-error' ).html( response.data.message );
				}
			}
		);
		e.preventDefault();
		return false;

	} );

	/**
	 * Perform AJAX reset password on form submit
	 */
	boombox_reset_password_form.on( 'submit', function( e ) {
		var _this   = $( this ),
		    _status = _this.parent().find( 'p.status-msg' ),
		    userlogin,
		    password,
		    confirm,
		    security;

		if ( !_this.valid() ) {
			_status.removeClass( 'msg-success msg-info' ).addClass( 'msg-error' ).html( ajax_auth_object.invalid_message ).show();
			return false;
		}

		userlogin = _this.find( '[name="rpuserlogin"]' ).val();
		password = _this.find( '[name="rppassword"]' ).val();
		confirm = _this.find( '[name="rpconfirmpassword"]' ).val();
		security = _this.find( '[name="resetpasswordsecurity"]' ).val();

		_status.removeClass( 'msg-success msg-error' ).addClass( 'msg-info' ).html( ajax_auth_object.loading_message ).show();

		$.post(
			ajax_auth_object.ajaxurl,
			{
				action   : 'boombox_ajax_reset_password',
				userlogin: userlogin,
				password : password,
				confirm  : confirm,
				security : security
			},
			function( response ) {
				if ( response.success ) {
					_status.removeClass( 'msg-error msg-info' ).addClass( 'msg-success' ).html( response.data.message );
					window.location.href = ajax_auth_object.reset_password_redirect_url;
				} else {
					_status.removeClass( 'msg-success msg-info' ).addClass( 'msg-error' ).html( response.data.message );
				}
			}
		);
		e.preventDefault();
		return false;

	} );

	/**
	 * Refresh Captcha
	 */
	$( 'body' ).on( 'click', '.auth-refresh-captcha', function( e ) {
		var form = $( this ).closest( 'form' ),
		    type = form.attr( 'action' );
		boombox_refresh_captcha( form, type );
		return false;
	} );

})( jQuery );