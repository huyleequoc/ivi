( function( $ ) {
	'use strict';

	// Hide unnecessary elements from amp customizer preview
	$( '.wp-video' ).css( 'display', 'none' );

	// Header text color
	wp.customize( 'amp_customizer[header_color]', function( value ) {
		value.bind( function( header_clr ) {
			$( '.main-header a' ).css( 'color', header_clr );
		} );
	} );

	// Header background color
	wp.customize( 'amp_customizer[header_background_color]', function( value ) {
		value.bind( function( header_bg_clr ) {
			$( '.main-header' ).css( 'background-color', header_bg_clr );
		} );
	} );

	// AMP color scheme
	wp.customize( 'amp_customizer[color_scheme]', function( value ) {
		value.bind( function( color_scheme ) {
			// Get light mode
			var light_mode = ( color_scheme == 'light' );

			// General options
			var footer_bg_clr = bamp_customizer_design.constants.footer_bg_clr;

			// Dark mode options
			var dark_bg_clr = bamp_customizer_design.constants.dark_bg_clr;
			var dark_border_clr = bamp_customizer_design.constants.dark_border_clr;
			var dark_txt_clr = bamp_customizer_design.constants.dark_txt_clr;
			var dark_sec_txt_clr = bamp_customizer_design.constants.dark_sec_txt_clr;
			var dark_sec_bg_clr = bamp_customizer_design.constants.dark_sec_bg_clr;

			// Light mode options
			var light_bg_clr = bamp_customizer_design.constants.light_bg_clr;
			var light_border_clr = bamp_customizer_design.constants.light_border_clr;
			var light_txt_clr = bamp_customizer_design.constants.light_txt_clr;
			var light_sec_txt_clr = bamp_customizer_design.constants.light_sec_txt_clr;
			var light_sec_bg_clr = bamp_customizer_design.constants.light_sec_bg_clr;
			var light_sidebar_border_clr = bamp_customizer_design.constants.light_sidebar_border_clr;

			if(light_mode)
			{
				$('body, .bb-author-vcard-mini .author-name a, .bb-author-vcard .author-name a, .page-nav-itm a, .post-list .post-itm a, .bb-social.default a, .bb-author-vcard .website-url').css('color',light_txt_clr);
				$( 'body' ).css( 'background-color', light_bg_clr );
				$( '.main-nav .divide-h' ).css( 'background-color', light_sidebar_border_clr );
				$( 'hr' ).css( 'background-color', light_border_clr );
				$( '.bb-tags a, .bb-author-vcard .author-vcard-inner' ).css( 'border-color', light_border_clr );
				$( '.border-btm' ).css( 'border-color', light_border_clr );
				$( '.bb-cat-links, .bb-cat-links a, .byline, .post-summary, .posted-on, .bb-price-block .old-price' ).css( 'color', light_sec_txt_clr );
				$( '.bb-author-vcard .author-header' ).css( 'background-color', light_sec_bg_clr );
			}
			else
			{
				$('body, .bb-author-vcard-mini .author-name a, .bb-author-vcard .author-name a, .page-nav-itm a, .post-list .post-itm a, .bb-social.default a, .bb-author-vcard .website-url').css('color',dark_txt_clr);
				$( 'body' ).css( 'background-color', dark_bg_clr );
				$( 'hr, .main-nav .divide-h' ).css( 'background-color', dark_border_clr );
				$( '.bb-tags a, .bb-author-vcard .author-vcard-inner' ).css( 'border-color', dark_border_clr );
				$( '.border-btm' ).css( 'border-color', dark_border_clr );
				$( '.bb-cat-links, .bb-cat-links a, .byline, .post-summary, .posted-on, .bb-price-block .old-price' ).css( 'color', dark_sec_txt_clr );
				$( '.bb-author-vcard .author-header' ).css( 'background-color', dark_sec_bg_clr );
			}

		} );
	} );

	// Hide elements
	wp.customize( 'amp_customizer[boombox_hide_elements]', function( value ) {
        value.bind( function( hide_elements ) {
            hide_elements = hide_elements.split(',');

            var elements = [];
            for( var i = 0; i < hide_elements.length; i++ ) {
            	if( hide_elements[ i ] ) {
                    elements.push('.bb-customizer-toggle-' + hide_elements[i]);
                }
            }

            $('.bb-customizer-toggle').css( {'display': 'block'} );
            if( elements.length ) {
                $( elements.join(',') ).css( {'display': 'none'} );
            }
        } );
	} );

} )( jQuery );
