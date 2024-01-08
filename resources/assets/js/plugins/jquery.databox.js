(function( $ ) {
    "use strict";
    $.databox = function( element, options ) {

        var
            plugin = this,
            $element = $( element );

        var settings = $.extend({
            onBeforeLoad: function(){},
            onAfterLoad: function(){},
            onComplete: function(){},
            onError: function(){}
        }, options );

        plugin.init = function () {
            dd( 'databox.init' );

            plugin.url = $element.attr( 'data-url' );
            plugin.box = $( $element.attr( 'data-target' ) );

            _events();
        };

        var _events = function() {
            $element.on('click', function (e) {
                dd( 'databox.click' );
                plugin.load();
            });
        };

        plugin.beforeLoad = function() {
            settings.onBeforeLoad( plugin, plugin.box );
        };

        plugin.afterLoad = function() {
            settings.onAfterLoad( plugin, plugin.box );
        };

        plugin.complete = function() {
            settings.onComplete( plugin, plugin.box );
        };

        plugin.setContent = function( html ) {
            plugin.box.html( html );
        };

        plugin.load = function( url ) {
            if ( typeof url == "undefined" )
                url = plugin.url;

            dd( 'databox.load: ' + url );

            $.ajax({
                url: url,
                success: function( html ){
                    plugin.setContent( html );

                    plugin.afterLoad();
                },
                beforeSend: function(){
                    plugin.beforeLoad();
                },
                complete: function(){
                    plugin.complete();
                },
                error: function (request, status, error) {
                    plugin.onError(request, status, error);
                }
            });
        };

        plugin.reload = function() {
            plugin.load( plugin.url );
        };

        //Auto initialize
        plugin.init();

    };
    $.fn.databox = function( options ) {

        return this.each(function() {

            if ( undefined === $( this ).data( 'databox' ) ) {

                var plugin = new $.databox( this, options );
                $( this ).data( 'databox', plugin );
            }
        });
    };
})( jQuery );
