(function ($) {
    'use strict';

    /** Fields dependency */
    var is_field_visible = function( selector, value, compare ) {
        var _show,
            _node = $(selector),
            _found_nodes = _node.length,
            _sel_val = ( _found_nodes == 0 ) ? 0 : ( _found_nodes == 1 ? _node.val() : ( _node.map(function() { return this.value; }).get() ) );

        _sel_val = typeof _sel_val == 'undefined' ? 0 : _sel_val;

        switch ( compare ) {
            case '===':
                _show = ( _sel_val === value );
                break;
            case '==':
            case '=':
                _show = ( _sel_val == value );
                break;
            case '!==':
                _show = ( _sel_val !== value );
                break;
            case '!=':
                _show = ( _sel_val != value );
                break;
            case '>=':
                _show = ( value >= _sel_val );
                break;
            case '<=':
                _show = ( value <= _sel_val );
                break;
            case '>':
                _show = ( value > _sel_val );
                break;
            case '<':
                _show = ( value < _sel_val );
                break;
            case 'IN':
                if( ! ( value instanceof Array ) ) {
                    value = value.split();
                }
                var intersected = value.filter( function( nth ) {
                    return ( _sel_val.indexOf( nth ) !== -1 );
                });

                _show = ( intersected.length > 0 );
                break;
            case 'NOT IN':
                if( ! ( value instanceof Array ) ) {
                    value = value.split();
                }
                var intersected = value.filter( function( nth ) {
                    return ( _sel_val.indexOf( nth ) !== -1 );
                });

                _show = ( intersected.length === 0 );
                break;
            default:
                _show = ( _sel_val == value );
        }

        return _show;
    }

    ////////////////////////////// Tabs //////////////////////////////

    $( '.aiom-admin-tabs-menu a' ).on( 'click', function( event ){
        event.preventDefault();

        var tab = $( this ),
            tab_list_item = tab.parent(),
            tab_id = tab.attr('href'),
            tab_wrap = tab.closest('.aiom-admin-tabs'),
            tab_content = tab_wrap.find('.aiom-admin-tab-content');

        $( '#aiom-active-tab-hash' ).val( tab_list_item.data( 'hash' ) );

        tab_list_item.addClass( 'active' )
            .siblings()
                .removeClass( 'active' );

        tab_content.not( tab_id )
            .removeClass('active').css( 'display', 'none' );

        $( tab_id )
            .addClass('active').css( 'display', 'block' );

    });

    /***** remove iframe from featured video url field */
    $( '.aiom-advanced-fields #aiom_video_url' ).on( 'change', function(){
        var _this = $(this),
            _value = _this.val();

        if( _value.indexOf( '<iframe' ) != '-1' ) {
            var matches = _value.match( /src="([^"]+)"/ );
            if( matches ) {
                _this.val( matches[1] );
            }
        }
    } );

    /***** Dependency for radio, checkbox && select */
    $( '.aiom-advanced-fields input[type="radio"], .aiom-advanced-fields input[type="checkbox"], .aiom-advanced-fields select' ).on( 'change', function(){

        var _node = $(this),
            _id = _node.attr( 'id' ),
            _depended_data_holders = $( '.aiom-advanced-fields .aiom-superior-' + _id );

        if( _depended_data_holders.length <= 0 ) {
            return false;
        }

        for( var i=0; i<_depended_data_holders.length; i++ ) {
            var
                _data_holder = $( _depended_data_holders[i] ),
                _wrapper = _data_holder.parent(),
                _json = JSON.parse( _data_holder.text() );


            if( _json ) {
                var _is_visible = ( _json.relation == 'AND' );
                for( var j=0; j<_json.fields.length; j++ ) {

                    var _show = is_field_visible( _json.fields[j].jq_selector, _json.fields[j].value, _json.fields[j].compare );
                    if( _json.relation == 'AND' ) {
                        _is_visible = _is_visible && _show;
                    } else {
                        _is_visible = _is_visible || _show;
                    }
                }

                if( _is_visible ) {
                    _wrapper.removeClass( 'aiom-hidden' );
                } else {
                    _wrapper.addClass( 'aiom-hidden' );
                }
            }
        }

    } );

    /***** Image radio */
    $( '.aiom-advanced-fields .aiom-form-row-radio-image .field-list-item' ).on( 'click', function(){
        var _this = $(this);

        if( _this.is( '.selected' ) ) {
            return;
        }

        _this.addClass('selected')
            .siblings().removeClass( 'selected' )
            .end().find( 'input[type="radio"]' ).prop( 'checked', true ).trigger( 'change' );
    } );

    /***** color */
    if( $('.aiom-form-row-color, .aiom-form-row-multicolor' ).length > 0 ) {
        $('.aiom-form-row-color input, .aiom-form-row-multicolor input').wpColorPicker({
            change: function () {},
            clear: function () {},
            hide: true
        });
    }

    // gallery
    wp.media.AIOM_libEditGallery = {

        open: function( element ) {
            this.frame( element ).open();
        },

        delete: function( element ) {
            var box = element.closest( '.upload-wrapper' ),
                gallery = box.find( '.field-list' ),
                input = box.find( '.image_ids' );

            box.removeData( 'aiom-lib-gallery' );
            gallery.html( '' );
            input.val( '' );
        },

        frame: function( element ) {
            var _this = this,
                box = element.closest( '.upload-wrapper' ),
                gallery = box.find( '.field-list' ),
                input = box.find( '.image_ids' ),
                selection = this.select( element );

            this._frame = box.data( 'aiom-lib-gallery' );

            if ( ! this._frame ) {
                this._frame = wp.media( {
                    id: 'my-frame',
                    frame: 'post',
                    state: 'gallery-edit',
                    title: wp.media.view.l10n.editGalleryTitle,
                    editing: true,
                    multiple: true,
                    selection:  selection
                } );

                this._frame.on( 'update', function( selection ){
                    var box = element.closest( '.upload-wrapper' ),
                        gallery = box.find( '.field-list' ),
                        input = box.find( '.image_ids' ),
                        images = selection.toJSON(),
                        ids = [];


                    gallery.html( '' );
                    for( var i=0; i<images.length; i++ ) {
                        var image = images[i],
                            url = image.url,
                            alt = image.name;

                        ids.push( image.id );
                        if( image.hasOwnProperty( 'sizes' ) ) {
                            if( image.sizes.hasOwnProperty( 'thumbnail' ) ) {
                                url = image.sizes.thumbnail.url;
                            } else if( image.sizes.hasOwnProperty( 'medium' ) ) {
                                url = image.sizes.medium.url;
                            } else if( image.sizes.hasOwnProperty( 'full' ) ) {
                                url = image.sizes.full.url;
                            }
                        }

                        $( '<div class="field-list-item"><img src="'+url+'" alt="'+alt+'" /></div>' ).appendTo( gallery );
                    }
                    input.val( ids.join(',') );
                } );

                box.data( 'aiom-lib-gallery', this._frame );
            }

            return this._frame;
        },

        select: function( element ) {

            var ids = element.closest( '.upload-wrapper' ).find( '.image_ids' ).attr('value'),
                shortcode = ids ? wp.shortcode.next('gallery', '[gallery ids=\'' + ids + '\]' ) : '',
                defaultPostId = wp.media.gallery.defaults.id,
                attachments,
                selection;

            // Bail if we didn't match the shortcode or all of the content.
            if ( ! shortcode )
                return;

            // Ignore the rest of the match object.
            shortcode = shortcode.shortcode;

            if ( _.isUndefined( shortcode.get('id') ) && ! _.isUndefined( defaultPostId ) ) {
                shortcode.set( 'id', defaultPostId );
            }

            if ( _.isUndefined( shortcode.get('ids') ) && ids ) {
                shortcode.set( 'ids', ids );
            }

            if ( _.isUndefined( shortcode.get('ids') ) ) {
                shortcode.set( 'ids', '0' );
            }

            attachments = wp.media.gallery.attachments( shortcode )

            selection = new wp.media.model.Selection( attachments.models, {
                props:    attachments.props.toJSON()
                , multiple: true
            })

            selection.gallery = attachments.gallery

            // Fetch the query's attachments, and then break ties from the query to allow for sorting.
            selection.more().done( function () {
                selection.props.set({ query: false });
                selection.unmirror();
                selection.props.unset('orderby');
            });

            return selection;
        }

    };

    // image
    $( document )

        // Media library open button functionality
        .on( 'click', '.aiom-form-row-image .button-upload', function ( event ) {

            var _this = $(this),
                _upload_wrapper = _this.closest( '.upload-wrapper' ),
                _hidden_field = _upload_wrapper.find( '.image_id' ),
                _image_wrapper = _upload_wrapper.find( '.image-wrapper' ),
                _image_holder = _image_wrapper.find( '.image-holder' ),
                _buttons_wraper = _upload_wrapper.find( '.buttons-wrapper' ),
                _file_frame = _upload_wrapper.data( 'file_frame' );

            event.preventDefault();

            // If the media frame already exists, reopen it.
            if ( _file_frame ) {
                _file_frame.open();
                return;
            }

            // Create the media frame.
            _file_frame = wp.media.frames.downloadable_file = wp.media({
                multiple: false
            });

            // When an image is selected, run a callback.
            _file_frame.on( 'select', function () {
                var
                    _image_data = _file_frame.state().get( 'selection' ).first().toJSON(),
                    _old_image = _image_holder.find( 'img' ),
                    _alt = _image_data.name,
                    _url = _image_data.url,
                    _new_image = '';

                if( _image_data.hasOwnProperty( 'sizes' ) ) {
                    if( _image_data.sizes.hasOwnProperty( 'medium' ) ) {
                        _url = _image_data.sizes.medium.url;
                    } else if( _image_data.sizes.hasOwnProperty( 'thumbnail' ) ) {
                        _url = _image_data.sizes.thumbnail.url;
                    } else if( _image_data.sizes.hasOwnProperty( 'full' ) ) {
                        _url = _image_data.sizes.full.url;
                    }
                }

                _new_image = '<img src="' + _url + '" alt="' + _alt + '" />';

                _hidden_field.val( _image_data.id );
                if( _old_image.length > 0 ) {
                    _old_image.replaceWith( _new_image );
                } else {
                    _image_holder.append( _new_image );
                }
                _upload_wrapper.addClass( 'has-image' );

            });

            $.data( _upload_wrapper, '_file_frame', _file_frame );

            // Finally, open the modal.
            _file_frame.open();

        } )

        // image placeholder
        .on( 'click', '.aiom-form-row-image .placeholder', function() {
            $(this).closest( '.aiom-form-row-image' ).find( '.button-upload' ).trigger( 'click' );
        } )

        // remove button functionality
        .on( 'click', '.aiom-form-row-image .button-remove', function ( event ) {
            var _this = $(this),
                _upload_wrapper = _this.closest( '.upload-wrapper' ),
                _hidden_field = _upload_wrapper.find( '.image_id' );

            event.preventDefault();

            _hidden_field.val( '' );
            _upload_wrapper.removeClass( 'has-image' );

        } )

        .on( 'click', '.aiom-form-row-gallery .button-upload', function ( event ) {
            event.preventDefault();

            wp.media.AIOM_libEditGallery.open( $(this) );
        } )

        .on( 'click', '.aiom-form-row-gallery .button-remove', function ( event ) {
            event.preventDefault();

            wp.media.AIOM_libEditGallery.delete( $(this) );
        }) ;

	// date
	if( $('.aiom-form-row-date').length > 0 ) {
		$('.aiom-form-row-date input[type="text"]').each( function(){
			var _this = $(this),
				config = _this.attr( 'js-config' );

			_this.removeAttr( 'js-config' );
			config = JSON.parse( config );
			if ( ( 'minDate' in config ) && $.isNumeric( config.minDate ) ) {
				var minDate = new Date( config.minDate * 1000 );
				if( minDate.getTime() > 0 ) {
					config.minDate = minDate;
				}
			}

			if ( ( 'maxDate' in config ) && $.isNumeric( config.maxDate ) ) {
				var maxDate = new Date( config.maxDate * 1000 );
				if( maxDate.getTime() > 0 ) {
					config.maxDate = maxDate;
				}
			}

			$(this).datepicker( config );
		} );
	}

})(jQuery);