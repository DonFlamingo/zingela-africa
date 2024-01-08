function Routes() {
    var
        _this = this,
        items = [],
        tmpItem = null,

        draw = null,

        loadFails = 0;

    _this.events = function() {
        $(document)
            .on('hidden.bs.tab', '[data-toggle="tab"][href="#routes_create"]', function (e) {
                _this.cancelEditing();
            })
            .on('hidden.bs.tab', '[data-toggle="tab"][href="#routes_edit"]', function (e) {
                _this.cancelEditing();
            });

        $(document).on('route.created route.updated', function(e, route){
            dd( 'route', e, route );

            if ( ! route.isLayerVisible () )
                return;

            var layer = route.getLayer();

            if ( layer )
                app.map.addLayer( layer );
        });

        app.map.on(L.Draw.Event.CREATED, function (e) {
            dd('routes.draw:created');

            if ( ! tmpItem )
                return;

            var type = e.layerType,
                layer = e.layer;

            if (type === 'polyline') {
                dd('app.drawnItems.addLayer');

                var _layer = tmpItem.setLayer( layer );

                app.map.addLayer( _layer );

                tmpItem.enableEdit();
            }
        });

        $('#routes_tab').on('keyup', 'input[name="search"]', $.debounce(100, function(){
            sidebarSearch( $(this).val().toLowerCase(), items, 'data-route-id', '#ajax-routes' );
        }));
    };

    _this.init = function() {
        _this.events();
    };

    _this.list = function() {
        var dataType = 'html';

        dd('routes.list');

        var $container = $('#ajax-routes');

        $.ajax({
            type: 'GET',
            dataType: dataType,
            url: app.urls.routes,
            beforeSend: function() {
                loader.add( $container );
            },
            success: function(response) {
                dd('routes.list.success');

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

    _this.add = function(data){
        data = data || {};

        if ( typeof data === 'string' ) {
            data = JSON.parse(data);
        }

        if ( !data ) {
            return;
        }

        if (typeof items[ data.id ] === 'undefined' ) {
            items[ data.id ] = new Route(data);
        } else {
            items[ data.id ].update(data);
        }
    };

    _this.addMulti = function(all) {

        $.each(all , function( index, data ) {
            _this.add(data);
        });
    };

    _this.active = function(route_id, value) {
        var _item = items[route_id];

        if ( !_item )
            return;

        _item.active( value );

        if (value) {
            if ( _item.isLayerVisible() )
                app.map.addLayer( _item.getLayer() );
        } else {
            app.map.removeLayer( _item.getLayer() );
        }

        _this.changeActive( route_id, value );
    };

    _this.changeActive = function( id, status ) {
        dd( 'routes.changeActive', id, status );

        $.ajax({
            type: 'POST',
            url: app.urls.routeChangeActive,
            data: {
                id: id,
                active: status
            },
            error: handlerFail
        });
    };

    _this.create = function() {
        tmpItem = new Route();

        draw = new L.Draw.Polyline( app.map );
        draw.enable();

        _this.initForm(tmpItem);

        app.openTab('routes_create');
    };

    _this.store = function() {
        var modal = $('#routes_create');
        var form = modal.find('form');
        var url = form.attr('action');
        var method = form.find('input[name="_method"]').val();
        var data = form.serializeArray();

        data.push({
            name: 'polyline',
            value: placesRouteLatLngsToPointsString( tmpItem.getLatLngs() )
        });

        method = (typeof method !== 'undefined' ? method : 'POST');

        $modal.postData(url, method, modal, data);
    };

    _this.edit = function(id) {
        tmpItem = items[id];

        tmpItem.enableEdit();

        _this.initForm(tmpItem);

        app.map.fitBounds( tmpItem.getBounds() );

        app.openTab( 'routes_edit' );
    };

    _this.update = function() {
        var modal = $('#routes_edit');
        var form = modal.find('form');
        var url = form.attr('action');
        var method = form.find('input[name="_method"]').val();
        var data = form.serializeArray();
        data.push({
            name: 'polyline',
            value: placesRouteLatLngsToPointsString( tmpItem.getLatLngs() )
        }, {
            name: 'id',
            value: tmpItem.id()
        });

        method = (typeof method !== 'undefined' ? method : 'POST');

        $modal.postData(url, method, modal, data);
    };

    _this.delete = function(id, confirmed) {
        if ( ! confirmed ) {
            $('#deleteRoute button[onclick]').attr('onclick', 'app.routes.delete('+id+', true);');

            return;
        }

        _this.remove( id );

        $modal.postData(
            app.urls.routeDelete,
            'DELETE',
            $('#routes_edit'),
            {
                id: id,
                _method: 'DELETE'
            }
        );
    };

    _this.remove = function( id ) {
        var _item = items[id];

        if ( !_item )
            return;

        if ( _item.isLayerVisible() )
            app.map.removeLayer( _item.getLayer() );

        delete items[_item];
    };

    _this.tmpUpdate = function(data) {
        if ( ! tmpItem )
            return;

        dd( 'routes.tmpUpdate' );

        if ( tmpItem.id() ) {
            $container = $('#routes_edit');
        } else {
            $container = $('#routes_create');
        }

        var _options = {
            name: $container.find('input[name="name"]').val(),
            polygon_color: $container.find('input[name="polygon_color"]').val(),
        };

        _options = $.extend({}, _options, data || {});
        tmpItem.update(_options);

        $( '[name="coordinates"]', $container ).val( JSON.stringify( tmpItem.getBounds() ) );

        dd('routes.map.click.data', _options);
    };

    _this.initForm = function( item ) {
        if ( item.id() ) {
            $container = $('#routes_edit');
        } else {
            $container = $('#routes_create');
        }

        if ( ! item.id() ) {
            item.update({
                name: $container.find('input[name="name"]').val(),
                polygon_color: $container.find('input[name="polygon_color"]').val(),
            });
        }

        $( '[name="id"]', $container ).val( item.options().id );
        $( '[name="name"]', $container ).val( item.options().name );
        $( '[name="polygon_color"]', $container ).val( item.options().polygon_color );
        $( '[name="coordinates"]', $container ).val( JSON.stringify( item.getBounds() ) );
    };

    _this.hideLayers = function() {
        $.each(items , function( id, item ) {
            if ( ! item )
                return;

            item.removeLayer();
        });
    };

    _this.showLayers = function() {
        $.each(items , function( id, item ) {
            if ( ! item )
                return;

            if ( ! item.isLayerVisible() )
                return;

            app.map.addLayer( item.getLayer() );
        });
    };

    _this.cancelEditing = function() {
        if ( draw ) {
            draw.disable();
            app.map.removeLayer(draw);
        }

        var $container;

        if ( tmpItem.id() ) {
            $container = $('#routes_edit');
        } else {
            $container = $('#routes_create');
        }

        tmpItem.removeLayer();
        tmpItem = null;

        $('input[name="name"]', $container).val('');
        $('input[name="polygon_color"]', $container).val('');
    };
}

function routes_create_modal_callback(res) {
    if (res.status == 1) {
        app.notice.success( window.lang.successfully_created_route );

        app.openTab('routes_tab');
        app.routes.list();
    }
}

function routes_edit_modal_callback(res) {
    if (res.status == 1) {
        app.notice.success(window.lang.successfully_updated_route);

        app.openTab('routes_tab');
        app.routes.list();
    }
}