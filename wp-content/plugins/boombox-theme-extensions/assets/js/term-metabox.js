(function(){

    // color picker
    $('.boombox-form-row-color input, .boombox-form-row-multicolor input').wpColorPicker({
        change: function (event, ui) {},
        clear: function () {},
        hide: true
    });

    // image
    (function(){

        // Media library open button functionality
        $( document ).on( 'click', '.boombox-form-row-image .button-upload', function ( event ) {

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

        });

        $( document ).on( 'click', '.boombox-form-row-image .placeholder', function(){
            $(this).closest( '.boombox-form-row-image' ).find( '.button-upload' ).trigger( 'click' );
        });

        // remove button functionality
        $( document ).on( 'click', '.boombox-form-row-image .button-remove', function ( event ) {
            var _this = $(this),
                _upload_wrapper = _this.closest( '.upload-wrapper' ),
                _hidden_field = _upload_wrapper.find( '.image_id' );

            event.preventDefault();

            _hidden_field.val( '' );
            _upload_wrapper.removeClass( 'has-image' );

        });

    })();

})(jQuery)