var app = {
    map: null,
    urls: {},
    lang: {},

    maps:       new MapTiles(),
    history:    new History(),
    devices:    new Devices(),
    geofences:  new Geofences(),
    mapIcons:   new MapIcons(),
    routes:     new Routes(),
    alerts:     new Alerts(),
    events:     new DeviceEvents(),
    sensors:    new Sensors(),

    listView:   new ListView(),

    rulerDraw: null,
    drawnItems: null,
    showPointMarker: null,
    showFindAssetMarker: null,

    checkTimeout: null,
    checkTimestamp: 0,
    checkFrequency: 5,
    offlineTimeout: 60,

    addressStack: {},

    settings: {
        fitBounds: true,
        clusterDevice: true,
        toggleSidebar: false,
        toggleWidgets: false,
        showDevice: true,
        showGeofences: true,
        showRoutes: true,
        showPoi: true,
        showTail: true,
        showNames: true,
        showTraffic: false,
        showHistoryRoute: true,
        showHistoryArrow: true,
        showHistoryStop: true,
        showHistoryEvent: true,

        mapCenter: [51.505, -0.09],
        mapZoom: 13,
        keys: {}
    },

    follow: function(data) {
        this.settings.showDevice = true;
        this.settings.clusterDevice = true;

        this.initSettings();
        this.initMap();

        data.active = true;
        this.devices.init();
        this.devices.following = data.id;
        this.devices.add(data);

        this.devices.updateLayers();
        this.devices.fitBounds();

        this.check();

        $('#loading').hide();
    },

    check: function() {
        $.ajax({
            type: 'GET',
            dataType: "json",
            url: app.urls.check,
            data: {
                time: app.checkTimestamp,
                user_id: app.settings.user_id
            },
            success: function(res){

                if (res.version != app.version)
                    return window.location.reload();

                app.checkTimestamp = res.time;

                if ( res.items != null && res.items ) {
                    app.devices.addMulti(res.items);
                    app.devices.updateLayers();
                    app.devices.refreshLayers();
                }

                if ( res.events != null ) {
                    app.events.parse( res.events );
                }

                app.devices.checkOfflineTimeout( app.checkTimestamp );

                app.devices.updateStopDuration();
            },
            complete: function(){
                clearTimeout(app.checkTimeout);
                app.checkTimeout = setTimeout(function(){
                    app.check();
                }, app.checkFrequency * 1000);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                handlerFail(jqXHR, textStatus, errorThrown);
            }
        });
    },

    stopCheck: function() {
        clearTimeout(app.checkTimeout);
    },

    ruler: function() {
        if ( this.rulerDraw === null ) {
            this.rulerDraw = L.control
                .ruler({
                    lengthUnit: {
                        factor: null,
                        display: 'km',
                        decimal: 2,
                    	label: 'Distance:'
                    }
                })
                .addTo(app.map);
        }

        this.rulerDraw._toggleMeasure();
    },

    zoomIn: function() {
        this.map.setView( this.map.getCenter(), this.map.getZoom() + 1 );
    },

    zoomOut: function() {
        this.map.setView( this.map.getCenter(), this.map.getZoom() - 1 );
    },

    mapFull: function() {
        this.settings.mapFull = !this.settings.mapFull;

        if (this.settings.mapFull) {
            $( '#header' ).addClass('hidden');
            $( '#sidebar' ).addClass('hidden');
            $( '#widgets' ).addClass('hidden');
            $( '#map-controls-layers' ).addClass('hidden');
            $( '#history-control-layers' ).addClass('hidden');
        } else {
            $( '#header' ).removeClass('hidden');
            $( '#sidebar' ).removeClass('hidden');
            $( '#widgets' ).removeClass('hidden');
            $( '#map-controls-layers' ).removeClass('hidden');
            $( '#history-control-layers' ).removeClass('hidden');
        }
    },

    showControls: function() {
        $('#map-controls-layers').show();
    },

    hideControls: function() {
        $('#map-controls-layers').hide();
    },

    init: function() {
        this.initSettings();
        this.initMap();

        this.devices.init();
        this.mapIcons.init();
        this.geofences.init();
        this.routes.init();
        this.history.init();
        this.events.init();

        this.devices.load(function(){
            app.devices.firstLoad();

            app.events.list();
            app.devices.list();
            app.mapIcons.list();
            app.geofences.list();
            app.routes.list();
            app.alerts.list();

            $('#loading').hide();
        });
    },

    initMap: function() {
        this.map = L.map('map', {zoomControl: false, maxZoom: 18}).setView( this.settings.mapCenter, this.settings.mapZoom);
        this.maps.init( this.map );

        this.drawnItems = new L.FeatureGroup();
        this.map.addLayer(this.drawnItems);

        this.map.on('popupclose', function(){
            $( '[role="tooltip"]' ).remove();
        });
    },

    getMapPadding: function() {
        var _offset  = 10,
            _left    = _offset,
            _bottom  = _offset,
            $sidebar = $('#sidebar'),
            $bottombar = $('#bottombar');

        if ( $sidebar.length ) {
            _left += $sidebar.width() + $sidebar.offset().left;
        }

        if ( $bottombar.length ) {
            _bottom += $bottombar.height();
        }

        dd( {
            paddingTopLeft: [_left,_offset],
            paddingBottomRight: [_bottom,_offset],
        } );

        return {
            paddingTopLeft: [_left,_offset],
            paddingBottomRight: [_bottom,_offset],
        };
    },

    initSettings: function() {
        var _parameters = [
            'clusterDevice',
            'fitBounds',
            'showDevice',
            'showGeofences',
            'showRoutes',
            'showPoi',
            'showTail',
            'showNames',
            'showTraffic',
            'showHistoryRoute',
            'showHistoryArrow',
            'showHistoryStop',
            'showHistoryEvent'
        ];

        for (var index = 0; index < _parameters.length; ++index) {
            var value = _parameters[index];

            $('#' + value).prop('checked', app.settings[value]);

            if ( app.settings[value] ) {
                $('#' + value).parent().addClass('active');
            }
        }
    },

    changeSetting: function(setting, value){
        dd('changing setting: ' + setting);

        switch (setting) {
            case 'clusterDevice':
                if ( this.settings.clusterDevice != value ) {
                    this.settings.clusterDevice = value;

                    this.devices.updateLayers();
                    this.devices.refreshLayers();
                }

                break;

            case 'toggleSidebar':
                value = ! this.settings.toggleSidebar;

                var $sidebar = $('#sidebar');

                if ( value ) {
                    $sidebar.addClass('collapsed');
                } else {
                    $sidebar.removeClass('collapsed');
                }

                this.settings.toggleSidebar = value;

                bottomAutoWidth();

                break;
            case 'toggleWidgets':
                value = ! this.settings.toggleWidgets;

                var $widgets = $('#widgets');

                if ( value ) {
                    $widgets.addClass('collapsed');
                } else {
                    $widgets.removeClass('collapsed');
                }

                this.settings.toggleWidgets = value;

                bottomAutoWidth();

                break;
            case 'showNames':
                if ( this.settings.showNames != value ) {
                    this.settings.showNames = value;

                    if ( this.settings.showNames )
                        $( this.map.getContainer() ).removeClass('hidden-device-name');
                    else
                        $( this.map.getContainer() ).addClass('hidden-device-name');
                }
                break;
            case 'showTail':
                if ( this.settings.showTail != value ) {
                    this.settings.showTail = value;

                    this.devices.updateLayers();
                }
                break;
            case 'showDevice':
                if ( this.settings.showDevice != value ) {
                    this.settings.showDevice = value;

                    this.devices.updateLayers();
                }
                break;

            case 'showPoi':
                if ( this.settings.showPoi != value ) {
                    this.settings.showPoi = value;

                    if ( this.settings.showPoi )
                        this.mapIcons.showLayers();
                    else
                        this.mapIcons.hideLayers();
                }

                break;
            case 'showGeofences':
                if ( this.settings.showGeofences != value ) {
                    this.settings.showGeofences = value;

                    if ( this.settings.showGeofences )
                        this.geofences.showLayers();
                    else
                        this.geofences.hideLayers();
                }

                break;
            case 'showRoutes':
                if ( this.settings.showRoutes != value ) {
                    this.settings.showRoutes = value;

                    if ( this.settings.showRoutes )
                        this.routes.showLayers();
                    else
                        this.routes.hideLayers();
                }

                break;
            case 'showTraffic':
                this.settings.showTraffic = value;

                var layer = $('input[name="leaflet-base-layers"]:checked').closest('label').find('span').text().trim();

                this.map
                    .removeLayer(this.maps.layers[layer])
                    .addLayer(this.maps.layers[layer]);

                break;

            case 'showHistoryRoute':
                this.settings.showHistoryRoute = value;
                this.history.parse();
                break;
            case 'showHistoryArrow':
                this.settings.showHistoryArrow = value;
                this.history.parse();
                break;
            case 'showHistoryStop':
                this.settings.showHistoryStop = value;
                this.history.parse();
                break;
            case 'showHistoryEvent':
                this.settings.showHistoryEvent = value;
                this.history.parse();
                break;
            default:
                break;
        }

        this.saveSetting(setting, this.settings[setting]);
    },

    saveSetting: function(setting, value) {
        dd('saving setting', setting, value);

        var _url = null,
            _data = null,
            _method = 'GET';

        switch (setting) {
            case 'showDevice':
            case 'showGeofences':
            case 'showRoutes':
            case 'showPoi':
            case 'showNames':
            case 'showTail':
            case 'showHistoryRoute':
            case 'showHistoryArrow':
            case 'showHistoryStop':
            case 'showHistoryEvent':
                var _values = {
                    "showDevice":"m_objects",
                    "showGeofences":"m_geofences",
                    "showRoutes":"m_routes",
                    "showPoi":"m_poi",
                    "showTail":"m_show_tails",
                    "showNames":"m_show_names",
                    "showHistoryRoute":"history_control_route",
                    "showHistoryArrow":"history_control_arrows",
                    "showHistoryStop":"history_control_stops",
                    "showHistoryEvent":"history_control_events"
                };
                _url = app.urls.changeMapSettings;
                _data = {
                    param: _values[setting],
                    value: value ? true : false
                };

                break;
            case 'map':
                _method = 'POST';
                _url = app.urls.changeMap;
                _data = {
                    selected: value
                };
                break;
        }

        if ( _url !== null ) {
            $.ajax({
                type: _method,
                url: _url,
                data: _data
            });
        }
    },

    getAddress: function(lat,lng) {
        $.ajax({
            type: 'GET',
            url: app.urls.geoAddress + '?lat=' + lat + '&lon=' + lng,
            success: function (res) {
                return res;
            },
            error: function(jqXHR, textStatus, errorThrown) {
                return textStatus;
            }
        });
    },

    clearQueue: function() {
        $.ajax({
            type: 'GET',
            url: app.urls.clearQueue,
            success: function (res) {
                $('.sms_queue_count').html('0');
            }
        });
    },

    showAddress: function () {
        var modal = $('#showAddress');
        var form = modal.find('form');
        var url = form.attr('action');
        var data = form.serializeArray();

        $.ajax({
            type: 'GET',
            data: data,
            url: url,
            beforeSend: function() {
                modal.find('.error').hide();
            },
            success: function (res) {
                if (res.status == 1) {
                    modal.modal('hide');
                    app.showPoint({lat: res.location.lat, lng: res.location.lng})
                }
                else {
                    modal.find('.error').html(res.error).show();
                }
            }
        });
    },

    findAssetsVehicle: function () {
        var _this = this;
          var modal = $('#findAssets');
          var form = $('#findAssets-vehicles-form');
          var url = form.attr('action');
          var data = form.serializeArray();
      
          $.ajax({
              type: 'GET',
              data: data,
              url: url,
              beforeSend: function() {
                  modal.find('.error').hide();
              },
              success: function (res) {
                      modal.modal('hide');
                      app.showFindAsset(res)
              },
              error: function (error) {
                modal.find('.error').html(res.error).show();
              }
          });
    },
      
    findAssetsPOI: function () {
        var _this = this;
        var modal = $('#findAssets');
        var form = $('#findAssets-poi-form');
          var url = form.attr('action');
          var data = form.serializeArray();
      
          $.ajax({
              type: 'GET',
              data: data,
              url: url,
              beforeSend: function() {
                  modal.find('.error').hide();
              },
              success: function (res) {
                      modal.modal('hide');
                      app.showFindAsset(res)
              },
              error: function (error) {
                modal.find('.error').html(res.error).show();
              }
          });
    },

    showFindAsset: function(latlng) {
        if ( this.showFindAssetMarker !== null )
          this.map.removeLayer( this.showFindAssetMarker );
        clearTimeout(app.checkTimeout);
        app.devices.removeAllLayers();
        var findAssetArray = [];
        for(var i = 0; i < latlng.length; i++) {
          var vehicleIconn = L.icon({
            iconUrl: latlng[i].icon.path,
            iconSize: [latlng[i].icon.width, latlng[i].icon.height]
          });
            findAssetArray.push(L.marker(latlng[i].location, {icon: vehicleIconn}).bindTooltip(latlng[i].name + ' (' + latlng[i].distance_text + ')', {permanent: true, offset: [0, ((latlng[i].icon.height + 10) / 2) * -1],  direction: 'top' }).bindPopup(latlng[i].name + '<br>Distance: ' + latlng[i].distance_text + '<br>Direction: ' + latlng[i].direction_text));
          }
        this.showFindAssetMarker = L.featureGroup(findAssetArray).addTo(this.map);
        this.map.fitBounds(this.showFindAssetMarker.getBounds(), {padding: [50, 50]});
    },

    showPoint: function (point) {
        if ( this.showPointMarker !== null )
            this.map.removeLayer( this.showPointMarker );

        this.showPointMarker = L.marker( point ).addTo( this.map );

        this.setView( point );
    },

    setView: function (point) {
        this.devices.disableFitBounds();
        this.map.setView( point );
    },

    openTab: function( tab_id ) {
        $('[data-toggle="tab"][href="#'+tab_id+'"]').trigger('click');
    },

    notice: {
        success: function(text){
            toastr.success(text);
            dd('app.notice.success', text);
        },
        error: function(text){
            toastr.error(text);
            dd('app.notice.error', text);
        },
        info: function(text){
            toastr.info(text);
            dd('app.notice.success', text);
        },
        warning: function(text){
            toastr.warning(text);
            dd('app.notice.warning', text);
        }
    },

    serviceCheck: function() {
        var $select = $('select[name="expiration_by"]'),
            $input = $('input[name="last_service"]');

        if ( $select.val() === 'days' ){
            $input.datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                weekStart: app.settings.weekStart
            });
        }
    }
};

function initComponents( container ) {
    container = container || 'body';

    dd('initComponents: ' + container);

    $container = $( container );

    $('.datepicker', $container).datepicker({
        format: 'yyyy-mm-dd',
        disableTouchKeyboard: true,
        autoclose: true,
        weekStart: typeof app.settings.weekStart !== "undefined" ? app.settings.weekStart : 0
    });
    $('.datetimepicker', $container).datetimepicker({
        format: 'yyyy-mm-dd hh:ii:ss',
        disableTouchKeyboard: true,
        autoclose: true,
        weekStart: typeof app.settings.weekStart !== "undefined" ? app.settings.weekStart : 0
    });
    $('select.form-control.multiexpand', $container).selectpicker({
        iconBase: '',
        tickIcon: '',
        size: false
    });
    $('select.form-control', $container).selectpicker({
        doneButton: true,
        iconBase: '',
        tickIcon: ''
    });

    $('.colorpicker', $container).colorpicker();


    $('[data-toggle="tooltip"]', $container).tooltip({
        container: 'body',
        delay: { "show": 200, "hide": 0 },
        trigger: "hover"
    });

    $('[rel="tooltip"]', $container).tooltip({
        container: 'body',
        delay: { "show": 200, "hide": 0 },
        trigger: "hover"
    });


    $('[data-toggle="multiCheckbox"]', $container).multiCheckbox();

    /*
    var $objects_tab = $('#objects_tab');
    if ( $objects_tab.find('.group-list > li').length > 500 ) {
        $objects_tab.on('show.bs.collapse','.collapse', function() {
            $objects_tab.find('.collapse.in').collapse('hide');
        });
    }
    */

    $_dataAddress = $('[data-device="address"]:visible', $container);

    if ( $_dataAddress.length ) {
        dd('geoAddress', $_dataAddress);
        $_dataAddress.each(function(){
            var _this = this,
                _lat = $( this ).attr('data-lat'),
                _lng = $( this ).attr('data-lng'),
                _key = '_a_' + _lat + _lng;

            if ( !_lat || !_lng )
                return;

            $( _this ).html( '<i class="loader small"></i>' );

            if (typeof app.addressStack[_key] !== "undefined")
                return;

            app.addressStack[_key] = true;

            $.ajax({
                type: 'GET',
                async: true,
                url: app.urls.geoAddress + '?lat=' + _lat + '&lon=' + _lng,
                success: function (res) {
                    $('[data-lat="'+_lat+'"][data-lng="'+_lng+'"][data-device="address"]:visible').html( res );
                },
                complete: function() {
                    delete app.addressStack[_key];
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $( _this ).html( textStatus );
                }
            });
        });
    }
   
    $_dataStopTime = $('[data-device="stoptime"]:visible', $container);
    if ( $_dataStopTime.length ) {
        dd('stopTime', $_dataStopTime);
        $_dataStopTime.each(function(){
            var _this = this,
                _id = $( this ).attr('data-id');

            if ( !_id )
                return;

            $.ajax({
                type: 'GET',
                url: app.urls.deviceStopTime + _id,
                beforeSend: function () {
                    $( _this ).html( '<i class="loader small"></i>' );
                },
                success: function (res) {
                    dd('_dataStopTime.success');

                    if (typeof res.time != 'undefined')
                        $( _this ).html( res.time );
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $( _this ).html( textStatus );
                }
            });
        });
    }
   

    $('[data-toggle="collapse"][data-url]').each( function() {
        var $source = $( this ),
            $target = $( $source.attr('data-target') );

        $target
            .off('show.bs.collapse')
            .on('show.bs.collapse', function () {
                $target.html('');
                remoteContent( $source.attr('data-url'), $target );
            });
    });

    //$('*', $container).dynamicElements();
    /*
    $( ".sidebar" ).resizable({
        handles: "e"
    });
    */

}

function remoteContent(url, target) {
    dd('remoteContent', url, target);

    $.ajax({
        url: url,
        success: function( html ){
            target.html( html );

            initComponents( target );
        },
        beforeSend: function(){
            loader.add( target );
        },
        complete: function(){
            loader.remove( target );
        },
        error: handlerFail
    });
}

function sidebarAutoHeight() {
    dd('sidebarAutoHeight');

    var $sidebar = $('#sidebar');
    var $tab_content = $('.tab-content', $sidebar);
    var $tab_panes = $('.tab-pane.active', $sidebar);

    if ( !$sidebar.length )
        return;

    if ( !$tab_content.length )
        return;

    var height = $sidebar.height() - ($tab_content.offset().top - $sidebar.offset().top);

    $tab_panes.each( function() {

        var $tab_pane = $( this );
        var $tab_pane_body = $('.tab-pane-body', $tab_pane);

        if ($tab_pane_body) {
            var body_height = height;

            var $tab_pane_header = $('.tab-pane-header', $tab_pane);
            var $tab_pane_footer = $('.tab-pane-footer', $tab_pane);

            if ($tab_pane_header.length) {
                body_height -= $tab_pane_header.outerHeight();
            }
            if ($tab_pane_footer.length) {
                body_height -= $tab_pane_footer.outerHeight();
            }

            $tab_pane_body.height( body_height );
        }
    });
}

function bottomAutoWidth() {
    setTimeout(function(){
        var _left = 0,
            _height = 0,
            _zindex = 997,
            $sidebar = $('#sidebar'),
            $bottombar = $('#bottombar');

        if ( $(window).width() > 500 ) {
            if ( $sidebar.length ) {
                _left = $sidebar.width();
            }
        } else {
            if ( $bottombar.length ) {
                _height = $bottombar.height();
            }

            _zindex = 1000;
        }

        $sidebar.css('padding-bottom', _height + 'px');
        $bottombar.css('padding-left', _left + 'px');
        $bottombar.css('z-index', _zindex);
    }, 460);
}

$(window).on('load', function(){
    dd('window.load');

    $.ajaxSetup({cache: false});

    initComponents();
    sidebarAutoHeight();
    bottomAutoWidth();

    $(window).keydown(function(event){
        if(event.keyCode === 13) {
            event.preventDefault();
            return false;
        }
    });

    $(document).on('click', '[data-dismiss="popup"]', function(){
        app.map.closePopup();
    });

    $(document)
        .on('shown.bs.collapse', '#objects_tab .group-collapse', function () {
            dd('#objects_tab .group-title shown');
            app.devices.toggleGroup( $(this).attr('data-id') );
        })
        .on('hidden.bs.collapse', '#objects_tab .group-collapse', function () {
            dd('#objects_tab .group-title hidden');
            app.devices.toggleGroup( $(this).attr('data-id') );
        });

    $(document)
        .on('shown.bs.collapse', '#geofencing_tab .group-collapse', function () {
            dd('#geofencing_tab .group-title shown');
            app.geofences.toggleGroup( $(this).attr('data-id') );
        })
        .on('hidden.bs.collapse', '#geofencing_tab .group-collapse', function () {
            dd('#geofencing_tab .group-title hidden');
            app.geofences.toggleGroup( $(this).attr('data-id') );
        });

    $(document)
        .on('hidden.bs.modal', '.modal', function () {
            $( this ).removeData('bs.modal');
        })
        .on('show.bs.modal', '.modal', function () {
            dd('show.bs.modal.begin');
        })
        .on('shown.bs.modal', '.modal', function () {
            dd('shown.bs.modal.begin');

            initComponents( this );

            dd('shown.bs.modal.end');
        });

    $(document)
        .on('show.bs.dropdown', '[data-position="fixed"]', function(e){
            dd('[data-position="fixed"].show.bs.dropdown', e);

            var $dropdown = $( e.target );

            ddp_set( $dropdown, 0 );
        })
        .on('hide.bs.dropdown', '[data-position="fixed"]', function(e){
            dd('[data-position="fixed"].on hide.bs.dropdown', e);

            var $dropdown = $( e.target );

            ddp_reset( $dropdown );

            $dropdown.removeClass('up down left right');
        });

    function ddp_set( dropdown, recursion_count ) {
        if ( recursion_count > 3)
            return;

        var $dropdown_menu = $( '.dropdown-menu', dropdown );

        ddp_reset( dropdown );

        $dropdown_menu.show();

        var
            _offset      = 10,
            _height      = $(window).height(),
            _width       = $(window).width(),
            _scrollTop   = $(window).scrollTop(),
            _menu_height = $dropdown_menu.height(),
            _menu_width  = $dropdown_menu.width(),
            _menu_top    = $dropdown_menu.offset().top,
            _menu_bottom = _menu_top + _menu_height,
            _menu_left   = $dropdown_menu.offset().left,
            _menu_right  = _menu_left + _menu_width;

        $dropdown_menu
            .css('top', _menu_top - _scrollTop)
            .css('left', _menu_left)
            .css('bottom', 'auto')
            .css('right', 'auto')
            .css('margin', '0')
            .css('position', 'fixed');

        _menu_height = $dropdown_menu.height();
        _menu_width  = $dropdown_menu.width();
        _menu_top    = $dropdown_menu.offset().top;
        _menu_left   = $dropdown_menu.offset().left;
        _menu_bottom = _menu_top + _menu_height;
        _menu_right  = _menu_left + _menu_width;

        if ( _menu_bottom > _height ) {
            dropdown.addClass('up');
            ddp_set( dropdown, ++recursion_count );
        }

        if ( _menu_right > _width ) {
            dropdown.addClass('right');
            ddp_set( dropdown, ++recursion_count );
        }

        $dropdown_menu.css('display', '');
    }

    function ddp_reset( $dropdown ) {
        var $dropdown_menu = $( '.dropdown-menu', $dropdown );

        $dropdown_menu
            .css('top', '')
            .css('left', '')
            .css('bottom', '')
            .css('right', '')
            .css('margin', '')
            .css('position', '');
    }

    $(document).on('focus', '.empty-input-add-new', function() {
        var el = $(this);
        var html = el[0].outerHTML;
        var parent = el.closest('.empty-input-items');

        el.removeClass('empty-input-add-new');

        parent.append(html);
        if (parent.find('.form-group').length >= 40) {
            parent.find('.empty-input-add-new').hide();
        }
    });

    $(document).on('click', '.empty-input-items .delete-item', function() {
        var parent = $(this).closest('.empty-input-items');
        $(this).closest('.form-group').remove();
        if (parent.find('.form-group').length < 40)
            parent.find('.empty-input-add-new').show();
    });

    $(document).on('change', 'input[name="cor_type"]', function () {
        $('[class^=cor_type_]').css('display', 'none');
        $('.cor_type_' + $(this).val()).css('display', 'block');
    });

    $(document).on('click', '#showPoint .btn-action', function () {
        var $container = $('#showPoint');

        var type = $('input[name="cor_type"]:checked', $container).val();
        var lat = $('.cor_type_'+type+' input[name="latitude"]', $container).val();
        var lng = $('.cor_type_'+type+' input[name="longitude"]', $container).val();
        var latlng;

        if (type == 2) {
            latlng = L.latLng(lat, lng);
        }
        else {
            latlng = L.latLng(degToLatLng(lat), degToLatLng(lng));
        }

        app.showPoint( latlng );
    });

    $(document).on('click', '.btn-minuse', function(){
        $(this).parent().siblings('input').val(parseInt($(this).parent().siblings('input').val()) - 1)
    });

    $(document).on('click', '.btn-pluss', function(){
        $(this).parent().siblings('input').val(parseInt($(this).parent().siblings('input').val()) + 1)
    });

    $(document).on('change', 'select[name="device_icons_type"]', function () {
        var val = $(this).val();
        $('.device-icons-group').hide();
        $('.device-icons-' + val).show();
    });

    $(document).on('change', 'select[name="engine_hours"]', function() {
        var val = $(this).val();
        if (val === 'engine_hours')
            $('.ignition_detection_engine').show();
        else
            $('.ignition_detection_engine').hide();
    });

    $(document).on('change', 'select[name="expiration_by"]', function() {
        var $input = $('input[name="last_service"]').val('').datepicker('remove');

        app.serviceCheck();
    });

    $(document).on('window_reload', function() {
        window.location.reload();
    });

    $(document).on('updateGeofenceGroupsSelect', function(el, data) {
        var $container = $('.geofence_groups_select_ajax'),
            $select = $('.geofence_groups_select');

        $.ajax({
            type: 'GET',
            dataType: "html",
            data: {
                group_id: data.id
            },
            url: data.url,
            beforeSend: function() {
                $select.attr('disabled', 'disabled');
                loader.add( $container );
            },
            success: function (res) {
                $container.html(res);

                initComponents( $container );
            },
            complete: function() {
                $select.removeAttr('disabled');
                loader.remove( $container );
            }
        });
    });
});
$(window).on('orientationchange resize shown.bs.tab', function(){
    sidebarAutoHeight();
    bottomAutoWidth();
});