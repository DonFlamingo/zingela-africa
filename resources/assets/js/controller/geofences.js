function Geofences() {
    var
        _this = this,
        items = [],
        tmpItem = null,

        draw = null,

        loadFails = 0;

    _this.events = function() {
        $('#ajax-geofences')
            .on('multichanged', 'input[data-toggle="checkbox"]', function(e, data){
                dd('multichanged', e, data);

                _this.activeMulti( $( this ).val(), data.values, $( this ).is(':checked') );
            })
            .on('multichange', 'input[type="checkbox"]', function(e, data){});

        $(document).on('change', '#geofences_export select[name="export_type"]', function() {
            dd( 'geofences_export.export_type.change' );

            var $container = $('#geofences_export');

            $.ajax({
                type: 'GET',
                url: app.urls.geofencesExportType,
                data: {
                    type: $(this).val()
                },
                success: function (res) {
                    $('.geofences-export-input', $container).html(res);

                    initComponents('#geofences_export');
                }
            });
        });

        $(document)
            .on('hidden.bs.tab', '[data-toggle="tab"][href="#geofencing_create"]', function (e) {
                _this.cancelEditing();
            })
            .on('hidden.bs.tab', '[data-toggle="tab"][href="#geofencing_edit"]', function (e) {
                _this.cancelEditing();
            });

        $(document).on('geofence.created geofence.updated', function(e, geofence){
            dd( 'geofence', e, geofence );

            if ( ! geofence.isLayerVisible () )
                return;

            var polygon = geofence.getLayer();

            if ( polygon )
                app.map.addLayer( polygon );
        });

        app.map.on(L.Draw.Event.CREATED, function (e) {
            dd('geofences.draw:created');

            if ( ! tmpItem )
                return;

            var type = e.layerType,
                layer = e.layer;

            if (type === 'polygon') {
                dd('app.drawnItems.addLayer');

                var _polygon = tmpItem.setLayer(layer);

                app.map.addLayer(_polygon);

                tmpItem.enableEdit();
            }
        });

        $('#geofencing_tab').on('keyup', 'input[name="search"]', $.debounce(100, function(){
            sidebarSearch( $(this).val().toLowerCase(), items, 'data-geofence-id', '#ajax-geofences');
        }));
    };

    _this.init = function() {
        _this.events();
    };

    _this.list = function() {
        var dataType = 'html';

        dd('geofences.list');

        var $container = $('#ajax-geofences');

        $.ajax({
            type: 'GET',
            dataType: dataType,
            url: app.urls.geofences,
            beforeSend: function() {
                loader.add( $container );
            },
            success: function(response) {
                dd('geofences.list.success');

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
            items[ data.id ] = new Geofence(data);
        } else {
            items[ data.id ].update(data);
        }
    };

    _this.addMulti = function(all) {

        $.each(all , function( index, data ) {
            _this.add(data);
        });
    };

    _this.active = function(geofence_id, value) {
        var _item = items[geofence_id];

        if ( !_item )
            return;

        _item.active( value );

        if (value) {
            if ( _item.isLayerVisible() )
                app.map.addLayer( _item.getLayer() );
        } else {
            app.map.removeLayer( _item.getLayer() );
        }

        //_item.refreshlayer();

        _this.changeActive( geofence_id, value );
    };

    _this.activeMulti = function(group_id, changeItems, value) {
        $.each( changeItems, function(index, geofence_id) {
            var _geofence = items[geofence_id];

            if ( !_geofence )
                return;

            _geofence.active( value );

            if (value) {
                if ( _geofence.isLayerVisible() )
                    app.map.addLayer( _geofence.getLayer() );
            } else {
                app.map.removeLayer( _geofence.getLayer() );
            }
        });

        _this.changeActive(changeItems, value);
    };

    _this.changeActive = function( id, status ) {
        dd( 'geofences.changeActive', id, status );

        $.ajax({
            type: 'POST',
            url: app.urls.geofenceChangeActive,
            data: {
                id: id,
                active: status
            },
            error: handlerFail
        });
    };

    _this.toggleGroup = function( id ) {
        dd( 'geofences.toggleGroup', id );

        $.ajax({
            type: 'GET',
            url: app.urls.geofenceToggleGroup,
            data: {
                id: id
            }
        });
    };

    _this.create = function() {
        tmpItem = new Geofence();

        draw = new L.Draw.Polygon( app.map );
        draw.enable();

        _this.initForm(tmpItem);

        app.openTab('geofencing_create');
    };

    _this.store = function() {
        var modal = $('#geofencing_create');
        var form = modal.find('form');
        var url = form.attr('action');
        var method = form.find('input[name="_method"]').val();
        var data = form.serializeArray();

        data.push({
            name: 'polygon',
            value: JSON.stringify( tmpItem.getLatLngs() )
        });

        method = (typeof method != 'undefined' ? method : 'POST');

        $modal.postData(url, method, modal, data);
    };

    _this.edit = function(id) {
        tmpItem = items[id];

        tmpItem.enableEdit();

        _this.initForm(tmpItem);

        app.map.fitBounds( tmpItem.getBounds() );

        app.openTab( 'geofencing_edit' );
    };

    _this.update = function() {
        var modal = $('#geofencing_edit');
        var form = modal.find('form');
        var url = form.attr('action');
        var method = form.find('input[name="_method"]').val();
        var data = form.serializeArray();
        data.push({
            name: 'polygon',
            value: JSON.stringify( tmpItem.getLatLngs() )
        }, {
            name: 'id',
            value: tmpItem.id()
        });

        method = (typeof method != 'undefined' ? method : 'POST');

        $modal.postData(url, method, modal, data);
    };

    _this.delete = function(id, confirmed) {
        if ( ! confirmed ) {
            $('#deleteGeofence button[onclick]').attr('onclick', 'app.geofences.delete('+id+', true);');

            return;
        }

        _this.remove( id );

        $modal.postData(
            app.urls.geofenceDelete,
            'DELETE',
            $('#geofencing_edit'),
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
        document.getElementById("upload_file").addEventListener("change", _this.importFile, false);
        document.getElementById("upload_file").click()
    };

    _this.importFile = function(el) {
        var file = el.target.files;
        var fileReader = new FileReader();

        fileReader.onload = function (fun) {
            $.ajax({
                type: 'POST',
                url: app.urls.geofencesImport,
                data: {
                    content: fun.target.result
                },
                cache: false,
                success: function (res) {
                    if (res.status == 1) {
                        app.notice.success(res.message);
                        app.geofences.list();
                    } else {
                        app.notice.error(res.error);
                    }
                }
            });
            document.getElementById('upload_file').value = '';
        };

        fileReader.readAsText(file[0], 'UTF-8');
        this.removeEventListener('change', _this.importFile, false);
    };

    _this.tmpUpdate = function(data) {
        if ( ! tmpItem )
            return;

        dd( 'geofences.tmpUpdate' );

        var $container;

        if ( tmpItem.id() ) {
            $container = $('#geofencing_edit');
        } else {
            $container = $('#geofencing_create');
        }

        var _options = {
            name: $container.find('input[name="name"]').val(),
            polygon_color: $container.find('input[name="polygon_color"]').val(),
        };

        _options = $.extend({}, _options, data || {});
        tmpItem.update(_options);

        $( '[name="coordinates"]', $container ).val( JSON.stringify( tmpItem.getBounds() ) );

        dd('geofences.map.click.data', _options);
    };

    _this.initForm = function( item ) {
        var $container;

        if ( item.id() ) {
            $container = $('#geofencing_edit');
        } else {
            $container = $('#geofencing_create');
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
        $.each(items , function( geofence_id, geofence ) {
            if ( ! geofence )
                return;

            geofence.removeLayer();
        });
    };

    _this.showLayers = function() {
        $.each(items , function( geofence_id, geofence ) {
            if ( ! geofence )
                return;

            if ( ! geofence.isLayerVisible() )
                return;

            app.map.addLayer( geofence.getLayer() );
        });
    };

    _this.cancelEditing = function() {
        if ( draw ) {
            draw.disable();
            app.map.removeLayer(draw);
        }

        var $container;

        if ( tmpItem.id() ) {
            $container = $('#geofencing_edit');
        } else {
            $container = $('#geofencing_create');
        }

        tmpItem.removeLayer();
        tmpItem = null;

        $('input[name="name"]', $container).val('');
        $('input[name="polygon_color"]', $container).val('');
    };
}

function geofencing_create_modal_callback(res) {
    if (res.status == 1) {
        app.notice.success( window.lang.successfully_created_geofence );

        app.openTab('geofencing_tab');
        app.geofences.list();
    }
}

function geofencing_edit_modal_callback(res) {
    if (res.status == 1) {
        app.notice.success(window.lang.successfully_updated_geofence);

        app.openTab('geofencing_tab');
        app.geofences.list();
    }
}