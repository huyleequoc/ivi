var zf_isMobile;

jQuery( document ).ready( function( $ ) {

	jQuery( document ).on( 'change', '.zombify_quiz input[type="file"]', function( e ) {
		if ( !ZombifyBuilder.validateField( jQuery( this ) ) ) {
			jQuery( this ).val( '' );
		} else {

			zombify_virtual_unsaved_form = true;

			if ( jQuery( this ).attr( "data-zombify-field-type" ) == 'file' ) {

				jQuery( this ).attr( "data-zf-file-browsed", "1" );
				jQuery( this ).closest( ".zf-form-group" ).find( ".zombify_uploaded_image_item" ).remove();

				ZombifyBuilder.virtualSave();

			}

		}
	} );


	jQuery( document ).on( "click", ".zf-submit_url", function() {

		ZombifyBuilder.virtualSave();

	} );

	jQuery( document ).on( 'click', ".zf-uploader .js-zf-get_url", function( e ) {
		e.stopPropagation();
		e.preventDefault();
		jQuery( this ).parents( '.zf-uploader' ).find( '.zf-get-url-popup' ).addClass( 'zf-open' );
		jQuery( this ).parents( '.zf-uploader' ).find( '.zf-image_url,.zf-start-image_url' ).focus();
	} );
	jQuery( document ).on( 'click', ".zf-uploader .zf-popup-close", function( e ) {
		e.stopPropagation();
		e.preventDefault();
		jQuery( this ).parents( '.zf-uploader' ).find( '.zf-get-url-popup' ).removeClass( 'zf-open' );
	} );
	jQuery( document ).on( 'click', ".zf-uploader .zf-submit_url", function( e ) {
		jQuery( this ).parents( '.zf-uploader' ).find( '.zf-preview-gif' ).hide();
		zf_get_video_by_url( this );
	} );

	jQuery( ".zf-uploader .zf-submit_url" ).each( function() {

		zf_get_video_by_url( this );

	} );


	/** Comments load more  */
	jQuery( document ).on( 'click', '.zf-comments_load_more a', function( e ) {
		e.preventDefault();
		var _this = jQuery( this ),
		    page  = parseInt( _this.attr( 'data-page' ) ),
		    count = _this.data( 'pages-count' );

		_this.parent().addClass( 'zf-loading' );

		jQuery.ajax( {
			url     : zf.ajaxurl,
			type    : 'POST',
			data    : { post_id: _this.data( 'post-id' ), page: page, action: 'zombify_get_post_comments' },
			dataType: 'json',
			success : function( data ) {
				jQuery( '[data-post-id=' + _this.data( 'post-id' ) + '] .zf-comments-box' ).append( data.comments );

				_this.attr( 'data-page', page + 1 );

				if ( count + 1 <= page + 1 ) _this.parent().addClass( 'zf-hide' );
				_this.parent().removeClass( 'zf-loading' );
			}
		} );
	} );

	/** Tabs */
	var tabActive = jQuery( '.tabs-menu>li.active' );
	if ( tabActive.length > 0 ) {
		for ( var i = 0; i < tabActive.length; i++ ) {
			var tab_id = jQuery( tabActive[i] ).children().attr( 'href' );

			jQuery( tab_id ).addClass( 'active' ).show();
		}
	}

	jQuery( document ).on( 'click', '.zf-tabs-menu a', function( e ) {
		var tab = jQuery( this );
		var tab_id = tab.attr( 'href' );
		var tab_wrap = tab.closest( '.zf-tabs' );
		var tab_content = tab_wrap.find( '.zf-tab-content' );

		tab.parent().addClass( "zf-active" );
		tab.parent().siblings().removeClass( 'zf-active' );
		tab_content.not( tab_id ).removeClass( 'zf-active' ).hide();
		jQuery( tab_id ).addClass( 'zf-active' ).fadeIn( 500 );

		e.preventDefault();
	} );


	/**
	 *
	 * Zombify Post Types
	 *
	 * ***/

	// Personality Quiz Post Type
	jQuery( '.zf-personality_quiz' ).zombifyPersonalQuiz();

	// Trivia Quiz Post Type
	jQuery( '.zf-trivia_quiz' ).zombifyTriviaQuiz();

	// Poll Post Type
	jQuery( '.zf-poll-item' ).zombifyPoll();

	// Open Post Type new content upload
	openListUploader();

	// Before - After Slider Post Type
	beforeAfterPostType();

	jQuery( document ).on( "change", ".zf-image_url", function() {

		jQuery( this ).closest( ".zf-uploader" ).closest( ".zf-form-group" ).find( ".zf-help" ).remove();

	} );
} );

jQuery.fn.zombifyPersonalQuiz = function() {

	return this.each( function() {

		var zf_this           = jQuery( this ),
		    zf_personalAllow  = true,
		    zf_questionsCount = zf_this.data( 'question_count' ),
		    zf_resultsCount   = zf_this.data( 'result_count' ),
		    zf_results        = [];
		zf_actionAnswerBtn = zf_this.find( '.js-zf-answer' );

		var ZF = {

			showPersonalResult: function( obj ) {
				if ( zf_this.find( '.zf-choice' ).length == zf_questionsCount ) {
					zf_personalAllow = false;

					for ( var i = 0; i <= zf_resultsCount; i++ ) {
						var qnt = zf_this.find( '.zf-selected[data-personality_index=' + i + ']' ).length;
						zf_results.push( qnt );
					}

					var max      = zf_results[0],
					    maxIndex = 0;

					for ( var i = 1; i < zf_results.length; i++ ) {
						if ( zf_results[i] > max ) {
							maxIndex = i;
							max = zf_results[i];
						}
					}
					zf_this.find( ".zf-quiz_results" ).show();
					zf_this.find( ".zf-quiz_results ol li:nth-child(" + (maxIndex + 1) + ")" ).show();

					var pos = zf_this.find( '.zf-quiz_results' ).offset();

					ZombifyPageAnimate( pos.top - 150, 500 );

					//if you set gif in result and gif control is scroll, its not working
					// need window resize trigger to call scroll
					jQuery( window ).resize();

				} else if ( obj.parent().parent().index() + 1 == zf_questionsCount ) {

					var pos = zf_this.find( '.zf-quiz_answer' ).not( '.zf-done' ).offset();

					ZombifyPageAnimate( pos.top, 500 );

				} else {
					var pos = obj.parent().parent().next().offset();

					ZombifyPageAnimate( pos.top - 150, 500 );
				}

			}
		};


		// Personal Quiz Actions

		// when user click on image credit not select answer
		zf_this.find( '.zf-answer_credit' ).on( 'click', function( e ) {
			e.stopPropagation();
		} );

		jQuery( zf_actionAnswerBtn ).on( 'click', function( e ) {

			var zf_parent = jQuery( this ).parent();

			if ( zf_personalAllow ) {

				zf_parent.parent().find( '.zf-selected' ).removeClass( 'zf-selected' );
				zf_parent.parent().find( '.zf-deactivated' ).removeClass( 'zf-deactivated' );
				zf_parent.addClass( 'zf-selected' );
				zf_parent.parent().find( 'li' ).not( '.zf-selected' ).addClass( 'zf-deactivated' );
				zf_parent.parent().addClass( 'zf-choice' );
				zf_parent.parent().parent().addClass( 'zf-done' );

				ZF.showPersonalResult( jQuery( this ).parent() );
			}
		} );

	} );
};

jQuery.fn.zombifyTriviaQuiz = function() {

	return this.each( function() {

		var zf_this            = jQuery( this ),
		    zf_triviaAllow     = true,
		    zf_questionsCount  = zf_this.data( 'question_count' ),
		    zf_answeredCount   = 0,
		    zf_actionAnswerBtn = zf_this.find( '.js-zf-answer' ),
		    zf_actionGuessBtn  = zf_this.find( '.js-zf-quiz-guess-btn' ),
		    zf_actionGiveUpBtn = zf_this.find( '.js-zf-quiz-giveup-btn' ),
		    zf_actionInput     = zf_this.find( '.js-zf-quiz-input' ),
		    zf_showAnswer      = zf_this.hasClass( 'zf-show-answer' ) ? true : false,
		    zf_resultMatch     = false;

		var ZF = {

			showRevealAnswer: function( obj ) {
				if ( obj.data( 'correct' ) == 1 ) {
					obj.parent().parent().find( '.zf-quiz_reveal .zf-reveal_header' ).addClass( 'zf_correct' );
				} else {
					obj.parent().parent().find( '.zf-quiz_reveal .zf-reveal_header' ).addClass( 'zf_wrong' );
				}
				obj.parent().parent().find( '.zf-quiz_reveal' ).show();
			},
			showTriviaResult: function( obj ) {
				if ( zf_answeredCount == zf_questionsCount ) {

					zf_this.addClass( 'zf-quiz-done' );
					zf_triviaAllow = false;

					var zf_correctAnswerCount = zf_this.find( 'li.zf-selected[data-correct="1"]' ).length;

					zf_this.find( '.zf-quiz_results li' ).each( function() {
						var rangeStart = parseInt( jQuery( this ).data( 'range_start' ) ),
						    rangeEnd   = parseInt( jQuery( this ).data( 'range_end' ) );

						if ( zf_correctAnswerCount >= rangeStart && zf_correctAnswerCount <= rangeEnd ) {
							zf_resultMatch = true;
							jQuery( this ).find( '.zf-score_count' ).text( zf_correctAnswerCount + '/' + zf_questionsCount );
							jQuery( this ).find( '.zf-score_count_correct' ).text( zf_correctAnswerCount );
							jQuery( this ).show();
							// add result to Twitter share
							var tw = jQuery( this ).find( '.zf-share.zf_twitter' ).attr( 'href' );
							var new_tw = tw.replace( "zombifyResult", zf_correctAnswerCount + '/' + zf_questionsCount );
							jQuery( this ).find( '.zf-share.zf_twitter' ).attr( 'href', new_tw );
							// add result to Facebook share
							var fb = jQuery( this ).find( '.zf-share.zf_facebook' ).attr( 'href' );
							var new_fb = fb.replace( "zombifyResult", zf_correctAnswerCount + '/' + zf_questionsCount );
							jQuery( this ).find( '.zf-share.zf_facebook' ).attr( 'href', new_fb );

							// Show correct result
							zf_this.find( '.zf-quiz_results' ).show();
							var pos = zf_this.find( '.zf-quiz_results' ).offset();

							ZombifyPageAnimate( pos.top, 500 );
						}
					} );

					if ( !zf_resultMatch ) {

						jQuery( '.zf-default-result' ).find( '.zf-score_count' ).text( zf_correctAnswerCount + '/' + zf_questionsCount );
						jQuery( '.zf-default-result' ).find( '.zf-score_count_correct' ).text( zf_correctAnswerCount );
						jQuery( '.zf-default-result' ).show();
						// add result to Twitter share
						var tw = jQuery( '.zf-default-result' ).find( '.zf-share.zf_twitter' ).attr( 'href' );
						var new_tw = tw.replace( "zombifyResult", zf_correctAnswerCount + '/' + zf_questionsCount );
						jQuery( '.zf-default-result' ).find( '.zf-share.zf_twitter' ).attr( 'href', new_tw );
						// add result to Facebook share
						var fb = jQuery( '.zf-default-result' ).find( '.zf-share.zf_facebook' ).attr( 'href' );
						var new_fb = fb.replace( "zombifyResult", zf_correctAnswerCount + '/' + zf_questionsCount );
						jQuery( '.zf-default-result' ).find( '.zf-share.zf_facebook' ).attr( 'href', new_fb );

						// Show correct result
						zf_this.find( '.zf-quiz_results' ).show();
						var pos = zf_this.find( '.zf-quiz_results' ).offset();

						ZombifyPageAnimate( pos.top, 500 );

						//if you set gif in result and gif control is scroll, its not working
						// need window resize trigger to call scroll
						jQuery( window ).resize();

					}
				} else if ( obj.parent().parent().index() + 1 == zf_questionsCount ) {

					var pos = zf_this.find( '.zf-quiz_question' ).not( '.zf-done' ).offset();

					ZombifyPageAnimate( pos.top - 150, 500 );
				}

			}
		};

		// Trivia Quiz Actions

		// when user click on image credit not select answer
		zf_this.find( '.zf-answer_credit' ).on( 'click', function( e ) {
			e.stopPropagation();
		} );

		//click on trivia answer ( 3col,  2col, text)
		jQuery( zf_actionAnswerBtn ).on( 'click', function( e ) {

			var zf_parent = jQuery( this ).parent();

			if ( zf_triviaAllow && !zf_parent.parent().hasClass( 'zf-choice' ) ) {

				zf_parent.parent().find( '.zf-selected' ).removeClass( 'zf-selected' );
				zf_parent.parent().find( '.zf-deactivated' ).removeClass( 'zf-deactivated' );
				zf_parent.addClass( 'zf-selected' );
				zf_parent.parent().find( 'li' ).not( '.zf-selected' ).addClass( 'zf-deactivated' );
				zf_parent.parent().addClass( 'zf-choice' );
				zf_parent.parent().parent().addClass( 'zf-done' );

				zf_answeredCount++;

				if ( zf_showAnswer ) {
					ZF.showRevealAnswer( jQuery( this ).parent() );
				}
				ZF.showTriviaResult( jQuery( this ).parent() );
			}
		} );

		//click on trivia input guess button
		jQuery( zf_actionGuessBtn ).on( 'click', function( e ) {
			e.preventDefault();
			var zf_parent = jQuery( this ).parent();
			if ( zf_triviaAllow && !zf_parent.parent().hasClass( 'zf-choice' ) ) {

				var zf_input    = zf_parent.find( 'input' ),
				    zf_tryCount = zf_input.data( 'try' ),
				    zf_word     = zf_input.val(),
				    zf_answers  = zf_input.data( 'answers' ),
				    zf_limit    = zf_input.data( 'limit' );

				// Change array values to lowercase
				jQuery.each( zf_answers, function( index, item ) {
					zf_answers[index] = item.toLowerCase();
				} );

				var zf_answer = zf_answers.indexOf( zf_word.toLowerCase() );

				if ( zf_answer < 0 ) {

					zf_tryCount++;
					zf_input.data( 'try', zf_tryCount );

					zf_input.addClass( 'zf-error zf-animate zf-shake' );
					var animate = setTimeout( function() {
						zf_input.removeClass( 'zf-error zf-animate zf-shake' );
					}, 200 );

					if ( zf_tryCount == 1 ) {
						zf_parent.addClass( 'zf-first-try' );
					}

					if ( zf_tryCount >= zf_limit ) {
						clearTimeout( animate );
						zf_input.addClass( 'zf-error' );

						zf_parent.addClass( 'zf-selected' ).attr( 'data-correct', '0' );
						zf_parent.parent().addClass( 'zf-choice' );
						zf_parent.parent().parent().addClass( 'zf-done' );
						zf_input.val( zf_answers[0] );
						zf_input.attr( 'disabled', 'disabled' );

						zf_answeredCount++;

						if ( zf_showAnswer ) {
							ZF.showRevealAnswer( jQuery( this ).parent() );
						}

						ZF.showTriviaResult( jQuery( this ).parent() );
					}
				} else {
					zf_input.addClass( 'zf-true' );

					zf_parent.addClass( 'zf-selected' ).attr( 'data-correct', '1' );
					zf_parent.parent().addClass( 'zf-choice' );
					zf_parent.parent().parent().addClass( 'zf-done' );
					zf_input.attr( 'disabled', 'disabled' );

					zf_answeredCount++;

					if ( zf_showAnswer ) {
						ZF.showRevealAnswer( jQuery( this ).parent() );
					}

					ZF.showTriviaResult( jQuery( this ).parent() );
				}
			}
		} );

		//enter on trivia input
		jQuery( zf_actionInput ).on( 'keypress', function( e ) {

			if ( e.which === 13 ) {
				jQuery( this ).parent().find( '.js-zf-quiz-guess-btn' ).trigger( 'click' );
			}

		} );

		//click on trivia input give up button
		jQuery( zf_actionGiveUpBtn ).on( 'click', function( e ) {
			e.preventDefault();
			var zf_parent = jQuery( this ).parent();
			if ( zf_triviaAllow && !zf_parent.parent().hasClass( 'zf-choice' ) ) {

				var zf_input   = zf_parent.find( 'input' ),
				    zf_answers = zf_input.data( 'answers' );

				zf_input.addClass( 'zf-error' );
				zf_parent.addClass( 'zf-selected' ).attr( 'data-correct', '0' );
				zf_parent.parent().addClass( 'zf-choice' );
				zf_parent.parent().parent().addClass( 'zf-done' );
				zf_input.val( zf_answers[0] );
				zf_input.attr( 'disabled', 'disabled' );

				zf_answeredCount++;

				if ( zf_showAnswer ) {
					ZF.showRevealAnswer( jQuery( this ).parent() );
				}
				ZF.showTriviaResult( jQuery( this ).parent() );
			}
		} );
	} );
};

jQuery.fn.zombifyPoll = function() {

	return this.each( function() {

		var zf_this            = jQuery( this ),
		    zf_pollAllow       = true,
		    zf_totalVoted      = zf_this.data( 'voted_count' ),
		    zf_actionAnswerBtn = zf_this.find( '.js-zf-answer' );

		var ZF = {
			showPollStat: function( obj, total ) {
				obj.find( '[data-voted]' ).each( function() {
					var count = jQuery( this ).attr( 'data-voted' );
					var percent = Math.round( (count * 100) / total );
					jQuery( this ).find( '.zf-poll-stat_count' ).html( percent + '%' );
					jQuery( this ).find( '.zf-poll-stat' ).css( {
						'height': percent + '%',
						'width' : percent + '%'
					} );
				} );
			}
		};

		if ( zf_this.hasClass( 'zf-poll-done' ) ) {
			zf_pollAllow = false;
			ZF.showPollStat( zf_this.find( '.zf-quiz_answer' ), zf_totalVoted );
		}

		// Poll Actions

		// when user click on image credit not select answer
		zf_this.find( '.zf-answer_credit' ).on( 'click', function( e ) {
			e.stopPropagation();
		} );

		jQuery( zf_actionAnswerBtn ).on( 'click', function( e ) {
			if ( zf_pollAllow ) {
				var _this       = jQuery( this ).parent(),
				    singleVoted = _this.data( 'voted' ),
				    id          = _this.data( 'id' ),
				    post_id     = _this.data( 'post-id' ),
				    group_id    = _this.data( 'group-id' );

				_this.addClass( 'zf-selected' );

				zf_totalVoted++;
				_this.attr( 'data-voted', singleVoted + 1 );
				zf_this.find( '.voted-count' ).html( zf_totalVoted );

				ZF.showPollStat( _this.parent(), zf_totalVoted );

				zf_this.addClass( 'zf-poll-done' );

				var shareText = jQuery.trim( zf_this.find( '.zf-selected .zf-answer_text' ).text() );
				// add result to Twitter share
				var tw = zf_this.find( '.zf-share.zf_twitter' ).attr( 'href' );
				var new_tw = tw.replace( "zombifyResult", shareText );
				zf_this.find( '.zf-share.zf_twitter' ).attr( 'href', new_tw );
				// add result to Facebook share
				var fb = zf_this.find( '.zf-share.zf_facebook' ).attr( 'href' );
				var new_fb = fb.replace( "zombifyResult", shareText );
				zf_this.find( '.zf-share.zf_facebook' ).attr( 'href', new_fb );


				jQuery.ajax( {
					url     : zf.ajaxurl + '?action=poll_vote',
					type    : 'POST',
					data    : { id: id, post_id: post_id, group_id: group_id, action: 'zombify_poll_vote' },
					dataType: 'json',
					success : function( data ) {
					}
				} );
				zf_pollAllow = false;
			}
		} );

		var shareText = jQuery.trim( zf_this.find( '.zf-selected .zf-answer_text' ).text() );

		if ( shareText != '' ) {

			// add result to Twitter share
			var tw = zf_this.find( '.zf-share.zf_twitter' ).attr( 'href' );
			var new_tw = tw.replace( "zombifyResult", shareText );
			zf_this.find( '.zf-share.zf_twitter' ).attr( 'href', new_tw );
			// add result to Facebook share
			var fb = zf_this.find( '.zf-share.zf_facebook' ).attr( 'href' );
			var new_fb = fb.replace( "zombifyResult", shareText );
			zf_this.find( '.zf-share.zf_facebook' ).attr( 'href', new_fb );

		}
	} );
};


function openListUploader() {
	jQuery( '.zf-media-uploader.zf-openlist' ).each( function() {
		var openListUploader = jQuery( this );

		openListUploader.find( 'input[type="file"]' ).on( 'change', function() {
			openListUploader.find( '.zf-hide' ).removeClass( 'zf-hide' );
		} );

		openListUploader.find( '.zf-image_url' ).on( 'input', function() {
			openListUploader.find( '.zf-hide' ).removeClass( 'zf-hide' );
		} );

		openListUploader.find( '.zombify_embed_url_textarea' ).on( "input", function() {
			openListUploader.find( '.zf-hide' ).removeClass( 'zf-hide' );
		} );
	} );
}

function beforeAfterPostType() {

	jQuery( '.zf-js-before-after-slider' ).each( function() {
		var _this     = jQuery( this ),
		    contWidth = _this.width(),
		    after     = _this.find( '.zf-after' ),
		    before    = _this.find( '.zf-before' );

		_this.find( 'img' ).css( 'max-width', contWidth + 'px' );

		_this.on( 'mousemove', function( e ) {
			e.preventDefault();
			moveHandler( _this, e );
		} );

		var lastTouchX = null;

		_this.on( 'touchstart', function( e ) {
			var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];

			lastTouchX = touch.pageX;
		} );

		_this.on( 'touchend', function( e ) {
			lastTouchX = null;
		} );

		_this.on( 'touchmove', function( e ) {
			var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];

			var deltaX = touch.pageX - parseInt( lastTouchX, 10 );

			if ( deltaX ) {
				moveHandler( _this, touch );
			}
		} );

	} );
}


String.prototype.splice = function( idx, rem, str ) {
	return this.slice( 0, idx ) + str + this.slice( idx + Math.abs( rem ) );
};

function ZombifyPageAnimate( pos, speed ) {
	jQuery( 'body,html' ).animate( {
		scrollTop: pos
	}, speed );
}

function moveHandler( obj, e ) {
	var offset = obj.offset();
	var iTopWidth = (e.pageX - offset.left);

	// set width of bottomimage div
	obj.find( '.zf-before' ).width( iTopWidth );
	obj.find( '.zf-hint' ).remove();
}

// ZombifyOnAjax when using dynamic content
// This function call all functions that need to recall after dynamic content load
function ZombifyOnAjax() {

	// Personality Quiz Post Type
	jQuery( '.zf-personality_quiz' ).zombifyPersonalQuiz();

	// Trivia Quiz Post Type
	jQuery( '.zf-trivia_quiz' ).zombifyTriviaQuiz();

	// Poll Post Type
	jQuery( '.zf-poll-item' ).zombifyPoll();

	openListUploader();
}


function parseEmbedURL( obj, url, width, height, sources, chng ) {

	if ( url === '' ) return false;

	var getSrc = '';
	var srcMatch = [];

	if ( /<iframe /i.test( url ) ) {
		getSrc = /src="([^"]*)"/;
		srcMatch = getSrc.exec( url );
	} else if ( /<blockquote /i.test( url ) ) { //instagram embed
		getSrc = /data-instgrm-permalink="([^?]*)([^"]*)"/;
		srcMatch = getSrc.exec( url );
	}

	if ( srcMatch && srcMatch[1] !== undefined ) {
		url = srcMatch[1];
		jQuery( obj ).val( url );
	}

	if ( url.match( /(http:|https:|)\/\/(www.)?[A-Za-z0-9._%-]{1,}.[A-Za-z0-9._%-+]{1,}[/A-Za-z0-9._%-]{1,}([?][^ "]{1,})?/g ) !== null ) {

		jQuery( obj ).closest( ".zf-embed" ).find( ".zf-embed-video" ).html( '<p class="zf-note text-center">' + zf.fetching_text + '...</p>' );

		jQuery.ajax( {
			url    : zf.ajaxurl,
			type   : 'GET',
			data   : { action: 'zombify_embed_from_url', url: url, post_id: zf.post_id },
			success: function( data ) {

				if ( data !== '' ) {

					ZombifyBuilder.updateShowDependency();

					data = JSON.parse( data );

					jQuery( obj ).closest( ".zf-embed" ).find( ".zf-embed-video" ).html( data.html );
					jQuery( obj ).closest( ".zf-embed" ).find( "input[data-zombify-field-name='embed_thumb']" ).val( data.thumbnail );
					jQuery( obj ).closest( ".zf-embed" ).find( "input[data-zombify-field-name='embed_type']" ).val( data.type );
					jQuery( obj ).closest( ".zf-embed" ).find( "input[data-zombify-field-name='embed_variables']" ).val( data.variables );

					if ( data.reset )
						jQuery( obj ).val( '' );

				} else {

					jQuery( obj ).val( '' );
					jQuery( obj ).closest( ".zf-embed" ).find( ".zf-embed-video" ).html( '' );

				}

			},
			error  : function( error ) {

				console.log( error );

			}
		} );

	} else {

		jQuery( obj ).val( 'https://' + url );

	}
}

function zf_get_video_by_url( obj ) {

	var url = jQuery( obj ).parent().find( '.zf-image_url' ).val();

	if ( url ) {
		if ( url.split( '.' ).pop() === 'mp4' ) {
			jQuery( obj ).parents( '.zf-uploader' ).find( ".zf-preview-gif-mp4" ).find( 'source' ).attr( 'src', url );
			jQuery( obj ).parents( '.zf-uploader' ).find( ".zf-preview-gif-mp4" ).show();
			jQuery( obj ).parents( '.zf-uploader' ).find( ".zf-preview-gif-mp4" ).find( 'video' )[0].load();
			jQuery( obj ).parents( '.zf-uploader' ).find( ".zf-preview-gif-mp4" ).find( 'video' )[0].play();
		} else {
			jQuery( obj ).parents( '.zf-uploader' ).find( ".zf-preview-img" ).attr( 'src', url ).show();
		}

		jQuery( obj ).closest( ".zf-embed" ).find( ".zf-embed-video" ).html( "" );

		var videoObj = parseEmbedURL( jQuery( obj ).parent().find( '.zf-image_url' ), jQuery( obj ).parent().find( '.zf-image_url' ).val(), '100%', 500, 'youtube,mp4', 1 );

		jQuery( obj ).parents( '.zf-uploader' ).find( '.zf-get-url-popup' ).removeClass( 'zf-open' );
		jQuery( obj ).parents( '.zf-uploader' ).addClass( "zf-uploader-uploaded" );

	} else {
		jQuery( this ).parents( '.zf-uploader' ).find( '.zf-get-url-popup' ).removeClass( 'zf-open' );
	}

}