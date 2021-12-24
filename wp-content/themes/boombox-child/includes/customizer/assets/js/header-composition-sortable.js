wp.customize.bind('ready', function () {

    (function ($) {

        var update_value = function( object ) {
            var _control_id = object.data( 'control' ),
                _obj_location = object.data( 'location' ),
                _obj_components = object.sortable( 'toArray', { attribute: 'component-id' } ),
                _sibling = object.closest( '.bb-control-composition-sortable-slave' ).find( '.bb-composition-sortable' ).not( object ).first(),
                _sib_location = _sibling.data( 'location' ),
                _sib_components = _sibling.sortable( 'toArray', { attribute: 'component-id' } ),
                _update = {};

            _update[ _obj_location ] = _obj_components;
            _update[ _sib_location ] = _sib_components;

            wp.customize.control( _control_id ).setting.set( _update );
        }

        $('.bb-composition-sortable').sortable({
            cursor: 'move',
            items: 'li:not(.bb-disabled)',
            cancel: 'li.bb-disabled',
            opacity: 0.5,
            placeholder: 'bb-composition-sortable-item-placeholder',
            connectWith: '.bb-composition-sortable',
            update: function (event, ui) {
                if ( $(this).is('.bb-composition-slave') ) {
                    update_value( $(this) );
                } else if( ui.sender && ui.sender.is('.bb-composition-slave') ) {
                    update_value( ui.sender );
                }
            }
        }).disableSelection();

        for( var i=0; i<bb.components_dependencies.length; i++ ) {
            var _dependency = bb.components_dependencies[i];

            wp.customize( bb.option + '[' + _dependency.setting + ']', function( setting ) {
                setting.bind( function( value ) {
                    var
                        _component = $('#bb-composition-item-' + _dependency.component),
                        _sortable = _component.closest( '.bb-composition-sortable.bb-composition-slave' );

                    switch( _dependency.operator ) {
                        case '!=':
                        case '<>':
                            var _compare = _dependency.value != value;
                            break;
                        case '==':
                        case '=':
                        default:
                            var _compare = _dependency.value == value;
                            break;
                    }

                    if( _compare ) {
                        _component.addClass( 'bb-disabled' );
                    } else {
                        _component.removeClass( 'bb-disabled' );
                    }
                    $('.bb-composition-sortable').sortable('refresh');

                    if( _sortable.length ) {
                        update_value( _sortable );
                    }
                });
            } );
        }

    })(jQuery)

});