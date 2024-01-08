function DeviceEvents() {
    var
        _this = this,
        items = [],
        loadFails = 0;

    _this.events = function() {
        $(document).on('keyup', '#events_search_field', $.debounce(500, function () {
            _this.list();
        }));
    };

    _this.init = function() {
        _this.events();
    };

    _this.list = function() {
        var dataType = 'html';

        dd('events.list');

        var $container = $('#ajax-events');

        $.ajax({
            type: 'GET',
            dataType: dataType,
            url: app.urls.events,
            data: {
                search: $('#events_search_field').val()
            },
            beforeSend: function() {
                loader.add( $container );
            },
            success: function(response) {
                dd('events.list.success');

                $container.html(response);

                initComponents( $container );
            },
            complete: function() {
                loader.remove( $container );
            },
            error: function(jqXHR, textStatus, errorThrown) {
                handlerFail(jqXHR, textStatus, errorThrown);

                loadFails++;

                if ( loadFails >= 5 ) {
                    loadFails = 0;
                    $('#getObejctsFailed').css('display', 'block');
                }
                else {
                    _this.list();
                }
            }
        });
    };

    _this.get = function( id ) {
        var _item = items[ id ];

        if ( typeof _item === "EventSys" )
            return null;

        return _item;
    };

    _this.parse = function(all) {
        $.each(all , function( index, data ) {
            _this.add(data, true);
        });
    };

    _this.addMulti = function(all) {
        $.each(all , function( index, data ) {
            _this.add(data);
        });
    };

    _this.add = function(data, fresh) {
        data = data || {};

        if ( typeof data === 'string' ) {
            data = JSON.parse(data);
        }

        if ( ! data ) {
            return;
        }

        if (typeof items[ data.id ] === 'undefined' ) {
            items[ data.id ] = new EventSys(data);
        } else {
            items[ data.id ].update(data);
        }

        if ( fresh ) {
            items[ data.id ].notice();

            $( items[ data.id ].html() ).prependTo( $("#ajax-events") );
        }
    };

    _this.select = function(event_id) {
        dd('events.select');

        if ( ! items[event_id] )
            return;

        $('#ajax-events [data-event-id]').removeClass('active');
        $('#ajax-events [data-event-id="'+event_id+'"]').addClass('active');

        items[event_id].popup();
    };
}