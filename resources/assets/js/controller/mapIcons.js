function MapIcons() {
    var
        _this = this,
        items = [],
        tmpItem = null,

        loadFails = 0;

    _this.events = function() {
        $(document)
            .on('hidden.bs.tab', '[data-toggle="tab"][href="#map_icons_create"]', function (e) {
                _this.cancelEditing();
            })
            .on('hidden.bs.tab', '[data-toggle="tab"][href="#map_icons_edit"]', function (e) {
                _this.cancelEditing();
            });

        $('#map_icons_create').on('change', 'input[name="map_icon_id"]', function(){
            _this.tmpUpdate();
        });
        $('#map_icons_edit').on('change', 'input[name="map_icon_id"]', function(){
            _this.tmpUpdate();
        });

        app.map.on('click', function (e) {
            _this.tmpUpdate({
                coordinates: {
                    lat: e.latlng.lat,
                    lng: e.latlng.lng
                }
            });
        });

        $(document).on('mapicon.created mapicon.updated', function(e, mapicon){
            dd( 'mapicon', e, mapicon );

            if ( ! mapicon.isLayerVisible() )
                return;

            var layer = mapicon.getLayer();

            if ( layer )
                app.map.addLayer( layer );
        });

        $('#map_icons_tab').on('keyup', 'input[name="search"]', $.debounce(100, function(){
            sidebarSearch( $(this).val().toLowerCase(), items, 'data-mapicon-id', '#ajax-map-icons');
        }));
    };

    _this.init = function() {
        _this.events();
    };

    _this.list = function() {
        var dataType = 'html';

        dd('mapIcons.list');

        var $container = $('#ajax-map-icons');

        $.ajax({
            type: 'GET',
            dataType: dataType,
            url: app.urls.mapIcons,
            beforeSend: function() {
                loader.add( $container );
            },
            success: function(response) {
                dd('mapIcons.list.success');

                $container.html(response);

                initComponents( $container );
            },
            complete: function() {
                loader.remove( $container );
            },
            error: function(jqXHR, textStatus, errorThrown) {
                handlerFail(jqXHR, textStatus, errorThrown);

                loadFails++;

                if (loadFails >= 5) {
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

        if ( typeof data == 'string' ) {
            data = JSON.parse(data);
        }

        if ( !data ) {
            return;
        }

        if (typeof items[ data.id ] == 'undefined' ) {
            items[ data.id ] = new MapIcon(data);
        } else {
            items[ data.id ].update(data);
        }
    };

    _this.addMulti = function(all) {

        $.each(all , function( index, data ) {
            _this.add(data);
        });
    };

    _this.active = function(poi_id, value) {
        var _item = items[poi_id];

        if ( !_item )
            return;

        _item.active( value );

        if (value) {
            if ( _item.isLayerVisible() )
                app.map.addLayer( _item.getLayer() );
        } else {
            app.map.removeLayer( _item.getLayer() );
        }

        _this.changeActive( poi_id, value );
    };

    _this.changeActive = function( id, status ) {
        dd( 'mapicon.changeActive', id, status );

        $.ajax({
            type: 'POST',
            url: app.urls.mapIconsChangeActive,
            data: {
                id: id,
                active: status
            },
            error: handlerFail
        });
    };

    _this.create = function() {
        tmpItem = new MapIcon();

        _this.initForm(tmpItem);

        app.openTab('map_icons_create');
    };

    _this.store = function() {
        var modal = $('#map_icons_create');
        var form = modal.find('form');
        var url = form.attr('action');
        var method = form.find('input[name="_method"]').val();
        var data = form.serializeArray();

        method = (typeof method != 'undefined' ? method : 'POST');

        $modal.postData(url, method, modal, data);
    };

    _this.edit = function(id) {
        tmpItem = items[id];

        _this.initForm(tmpItem);

        app.map.setView( tmpItem.getLatLng() );

        app.openTab( 'map_icons_edit' );
    };

    _this.update = function() {
        var modal = $('#map_icons_edit');
        var form = modal.find('form');
        var url = form.attr('action');
        var method = form.find('input[name="_method"]').val();
        var data = form.serializeArray();

        method = (typeof method != 'undefined' ? method : 'POST');

        $modal.postData(url, method, modal, data);
    };

    _this.delete = function(id, confirmed) {
        if ( ! confirmed ) {
            $('#deleteMapIcon button[onclick]').attr('onclick', 'app.mapIcons.delete('+id+', true);');

            return;
        }

        _this.remove( id );

        $modal.postData(
            app.urls.mapIconsDelete,
            'DELETE',
            $('#map_icons_edit'),
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

    _this.import = function() {
        var modal = $('#map_icons_import');
        var form = modal.find('form');
        var url = form.attr('action');
        var method = form.find('input[name="_method"]').val();
        var data = new FormData(form['0']);

        method = (typeof method != 'undefined' ? method : 'POST');

        $modal.postData(url, method, modal, data, true);
    };

    _this.tmpUpdate = function(data) {
        if ( ! tmpItem )
            return;

        dd( 'mapicons.tmpUpdate' );

        if ( tmpItem.id() ) {
            $container = $('#map_icons_edit');
        } else {
            $container = $('#map_icons_create');
        }

        var checked = $container.find('.icon-list input[name="map_icon_id"]:checked');

        if (!checked.length) {
            checked = $container.find('.icon-list input[name="map_icon_id"]:first');
        }

        var _options = {
            name: $container.find('input[name="name"]').val(),
            description: $container.find('textarea[name="description"]').val(),
            map_icon_id: checked.val(),
            map_icon: {
                url: checked.parent().find('img').attr('src'),
                width: checked.data('width'),
                height: checked.data('height')
            }
        };

        _options = $.extend({}, _options, data || {});
        tmpItem.update(_options);

        $( '[name="coordinates"]', $container ).val( JSON.stringify( tmpItem.getLatLng() ) );

        dd('mapIcons.map.click.data', _options);
    };

    _this.initForm = function( item ) {
        if ( item.id() ) {
            $container = $('#map_icons_edit');
        } else {
            $container = $('#map_icons_create');
        }

        var checked = $container.find('.icon-list input[name="map_icon_id"]:checked');

        if (!checked.length) {
            checked = $container.find('.icon-list input[name="map_icon_id"]:first');
        }

        if ( ! item.id() ) {
            item.update({
                name: $container.find('input[name="name"]').val(),
                description: $container.find('textarea[name="description"]').val(),
                map_icon_id: checked.val(),
                map_icon: {
                    url: checked.parent().find('img').attr('src'),
                    width: checked.data('width'),
                    height: checked.data('height')
                }
            });
        }

        $( '[name="id"]', $container ).val( item.options().id );
        $( '[name="name"]', $container ).val( item.options().name );
        $( '[name="description"]', $container ).val( item.options().description );
        $( '[name="map_icon_id"][value="'+item.options().map_icon_id+'"]', $container).prop('checked', true);
        $( '[name="coordinates"]', $container ).val( JSON.stringify( item.getLatLng() ) );
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
        var $container;

        if ( tmpItem.id() ) {
            $container = $('#map_icons_edit');
        } else {
            $container = $('#map_icons_create');
        }

        tmpItem.removeLayer();
        tmpItem = null;

        $('input[name="name"]', $container).val('');
        $('textarea[name="description"]', $container).val('');
        $('input:radio[name="map_icon_id"]', $container).removeAttr('checked');
    };
}

function map_icons_create_modal_callback(res) {
    if (res.status == 1)
        app.notice.success( window.lang.successfully_created_marker );

    app.openTab('map_icons_tab');
    app.mapIcons.list();
}

function map_icons_edit_modal_callback(res) {
    if (res.status == 1)
        app.notice.success( window.lang.successfully_updated_marker );

    app.openTab('map_icons_tab');
    app.mapIcons.list();
}

function map_icons_import_modal_callback(res) {
    app.notice.success(res.message);

    app.openTab('map_icons_tab');
    app.mapIcons.list();
}