( function( $ ) {

    // update form elements attributes
    var update_form_elements_attributes = function( $node, $search, $replace ){

        $node.find( 'input, select, textarea, label' ).each(function() {

            if( this.hasAttribute( 'name' ) ) {
                this.name = this.name.replace( '[row_' + $search + ']', '[row_' + $replace + ']' );
            }

            if( this.hasAttribute( 'id' ) ) {
                this.setAttribute( 'id', this.getAttribute( 'id' ).replace( 'row-' + $search, 'row-' + $replace ) );
            }

            if( this.hasAttribute( 'for' ) ) {
                this.setAttribute( 'for', this.getAttribute( 'for' ).replace( 'row-' + $search, 'row-' + $replace ) );
            }

            if( this.hasAttribute( 'data-dynamic' ) ) {
                this.innerHTML = this.getAttribute( 'data-dynamic' ).replace( '{N}', $replace + 1 );
            }

        });
    };

    // add repeater row
    $( document ).on( 'click', '.mycred-repeater-add', function(){

        var _this = $(this),
            _repeater_id = _this.data( 'target' ),
            _repeater = $( '.mycred-repeater' + _repeater_id ),
            _cloneable = _repeater.find( '.mycred-repeater-item.cloneable' ),
            _clone = _cloneable.clone( true ),
            _index = _repeater.find( '.mycred-repeater-item' ).length;

        _index = Math.max( 1, _index );

        // set form element attributes
        update_form_elements_attributes( _clone, 0, _index );

        //set index
        _clone.data( 'index', _index );

        _clone.removeClass( 'cloneable' ).addClass( 'removable' ).appendTo( _repeater ).prepend( '<a href="#" class="delete-row dashicons dashicons-no"></a>' );

        return false;

    } );

    // remove repeater row
    $( document ).on( 'click', '.mycred-repeater .delete-row', function(){

        var _this = $(this),
            _removable = _this.closest( '.mycred-repeater-item' ),
            _update_elements = _removable.nextAll( '.mycred-repeater-item' );

        _update_elements.each( function() {
            var _search_index = $( this ).data( 'index' ),
                _replace_index = _search_index - 1;

            $( this ).data( 'index', _replace_index );

            update_form_elements_attributes( $( this ), _search_index, _replace_index );
        } );

        _removable.remove();



        return false;

    } );

} )( jQuery );