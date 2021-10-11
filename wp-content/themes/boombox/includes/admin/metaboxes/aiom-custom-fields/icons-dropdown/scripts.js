(function( $ ){

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
	 */
	$( '.bb-custom-icons-dropdown select' ).select2({
		width : '100%',
		templateResult    : render_markup,
		templateSelection : render_markup,
		escapeMarkup      : function( m ) {
			return m;
		}
	});

})(jQuery);