jQuery(function( $ ){
    'use strict';

    /**
     * Enable UI Sortable to social items
     *
     * @type {*|jQuery|HTMLElement}
     */
    var boombox_social = $('.boombox-social');
    if( boombox_social.length > 0 ){
        boombox_social.sortable({
            placeholder: "ui-state-highlight"
        });
    }

});