(function( $ ){

    /**
     * Set reaction image URL
     */
    $( '.bbte-custom-reaction-icon select' ).on( 'change', function(){
        var _this = $(this);
        _this.closest( '.bbte-custom-reaction-icon' ).find( '.reaction-thumb img' ).attr( 'src', _this.find( 'option:selected' ).data( 'url' ) );
    } );

    /**
     * Color scheme
     */
    $( '#term_icon_color_scheme' ).on( 'change', function(){
        if( 'default' == $(this).val() ) {
            var _color = $('#reaction_icon_file_name_wrapper').data( 'default-color' );
        } else {
            var _color = $("#term_icon_background_color").val();
        }

        $('#reaction_icon_file_name_wrapper .reaction-thumb').css({ backgroundColor : _color });

    } ).trigger( 'change' );

    /**
     * Color picker
     */
    $( '#term_icon_background_color' ).wpColorPicker( 'option', 'change', function( event, ui ){
        $('#reaction_icon_file_name_wrapper .reaction-thumb').css({ backgroundColor : ui.color.toCSS() });
    } );

})(jQuery);