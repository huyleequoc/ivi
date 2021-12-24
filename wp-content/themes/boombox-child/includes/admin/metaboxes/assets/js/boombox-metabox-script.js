(function ($) {
	'use strict';

	/**
	 * Render dropdown item markup
	 * @param state
	 * @returns string
	 */
	function render_markup( state ) {
		// optgroup
		if ( ! state.id ) {
			return state.text;
		}
		return '<i class="bb-icon '+$( state.element ).data('class')+'"></i>  '+state.text;
	}

	/**
	 * Initialize menu icons dropdown
     * @var node Selected nodes
	 */
	function init_icons_dropdown( node ) {
		node.select2({
			width : '100%',
			templateResult    : render_markup,
			templateSelection : render_markup,
			escapeMarkup      : function( m ) {
				return m;
			}
		});
    }

	/**
	 * Initialize menu icons dropdown on new added menu items
	 */
	$( '#post-body-content .menu' ).bind( 'DOMSubtreeModified', function() {
		$( '.pending .edit-menu-item-icon' ).each( function(){
			if( ! $( this ).attr( 'data-select2-id' ) ) {
				init_icons_dropdown( $( this ) );
			}
		});
	});

	init_icons_dropdown( $( '.edit-menu-item-icon' ) );

})(jQuery);