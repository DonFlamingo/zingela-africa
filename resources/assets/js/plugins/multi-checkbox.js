(function($){
    'use strict';
    $.multiCheckbox = function( element, options ){

        var
            _opt = {
                item: $( element ).find('[data-toggle="checkbox"]'),
                items: $( element ).find('input[type="checkbox"]')
            },
            _app = {},
            _this = this,
            $input = $( element ),
            $item,
            $items,
            disableChecking = false;

        _this.init = function(){
            _opt = $.extend({}, _opt, options),
                $item = _opt.item,
                $items = _opt.items.not( $item );

            _events();

            _checkParent();
        };

        var _events = function (){

            $item.on( 'change', function(){
                dd( 'multiCheckbox parent changed' );

                var _checked = $item.is(':checked'),
                    _items   = [],
                    _values  = [];

                $items.each( function(){
                    if ( $( this ).is(':checked') == _checked )
                        return;

                    _items.push( $( this ) );
                    _values.push( $( this ).val() );

                    $( this ).prop('checked', _checked);
                });

                $( this ).trigger('multichanged', { items: _items, values: _values });

                _checkParent();
/*
                $.each( _items, function(){
                    $( this ).trigger('multichange');
                });
*/
            });

            $items.on( 'multichange', function(){
                _this.disableChecking = true;
                $( this ).trigger( 'change' );
                _this.disableChecking = false;
            } );

            $items.on( 'change', function(e){
                dd( 'multiCheckbox child changed', e );
                _checkParent();
            } );
        };

        var _checkParent = function() {
            if ( _this.disableChecking )
                return;

            dd( 'multiCheckbox._checkParent' );

            if ($items.length == 0)
                return;

            $item.prop('checked', $items.length == $items.filter(':checked').length );
        };

        // init!
        _this.init();
    };
    $.fn.multiCheckbox = function( options ){

        return this.each(function(){

            if ( undefined === $( this ).data( 'multiCheckbox' ) ){

                var plugin = new $.multiCheckbox( this, options );
                $( this ).data( 'multiCheckbox', plugin );
            }
        });
    };
})(jQuery);