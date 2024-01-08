function MapTiles() {
    var
        _this = this,
        map = null,
        layers = null,
        _maps = {
        1: {
            require:    'google',
            name:       'Google Normal',
            maxZoom:    18,
            traffic:    true,
            tile:       function(){
                var _tileLayer = L.gridLayer.googleMutant({type: 'roadmap'});

                _tileLayer.map_id = 1;

                return _tileLayer;
            }
        },
        2: {
            name: 'OpenStreetMap',
            maxZoom: 18,
            tile: function(){
                var _tileLayer =  new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 18});

                _tileLayer.map_id = 2;

                return _tileLayer;
            }
        },
        3: {
            require:    'google',
            name:       'Google Hybrid',
            maxZoom:    18,
            traffic:    true,
            tile:       function(){
                var _tileLayer = L.gridLayer.googleMutant({type: 'hybrid'});

                _tileLayer.map_id = 3;

                return _tileLayer;
            }
        },
        4: {
            require:    'google',
            name:       'Google Satellite',
            maxZoom:    18,
            traffic:    true,
            tile:       function(){
                var _tileLayer = L.gridLayer.googleMutant({type: 'satellite'});

                _tileLayer.map_id = 4;

                return _tileLayer;
            }
        },
        5: {
            require:    'google',
            name:       'Google Terrain',
            maxZoom:    18,
            traffic:    true,
            tile:       function(){
                var _tileLayer = L.gridLayer.googleMutant({type: 'terrain'});

                _tileLayer.map_id = 5;

                return _tileLayer;
            }
        },
        6: {
            require:    'yandex',
            name:       'Yandex',
            maxZoom:    18,
            tile:       function(){
                var _tileLayer = new L.Yandex(null, {maxZoom: 18});

                _tileLayer.map_id = 6;

                return _tileLayer;
            }
        },
        10: {
            name:       'Here',
            maxZoom:    18,
            tile:       function(){
                if (!app.settings.keys.here_map_id)
                    return false;

                if (!app.settings.keys.here_map_code)
                    return false;

                var _tileLayer =  L.tileLayer('https://{s}.{base}.maps.cit.api.here.com/maptile/2.1/{type}/{mapID}/{scheme}/{z}/{x}/{y}/{size}/{format}?app_id={app_id}&app_code={app_code}&lg={language}', {
                    attribution: 'Map &copy; 2016 <a href="http://developer.here.com">HERE</a>',
                    subdomains: '1234',
                    base: 'base',
                    type: 'maptile',
                    scheme: 'pedestrian.day',
                    app_id: app.settings.keys.here_map_id,
                    app_code: app.settings.keys.here_map_code,
                    mapID: 'newest',
                    maxZoom: 18,
                    language: 'eng',
                    format: 'png8',
                    size: '256'
                });

                _tileLayer.map_id = 10;

                return _tileLayer;
            }
        },
        99: {
            name: 'Tourist map Slovakia',
            maxZoom: 16,
            tile: function(){
                var _tileLayer = new L.TileLayer('https://{s}.freemap.sk/T/{z}/{x}/{y}.png', {
                    minZoom: 1,
                    maxZoom: 16,
                    subdomains: ['a', 'b', 'c'],
                    attribution: "Copyright <a href='http://www.freemap.sk'>©2016 Freemap Slovakia</a>. <a href='http://www.openstreetmap.org'>OpenStreetMap</a>, Licensed as Creative Commons <a href='http://creativecommons.org/licenses/by-sa/2.0'>CC-BY-SA 2.0</a>. Some rights reserved."
                });

                _tileLayer._tileOnError = function(done, tile, e) {

                    var errorUrl = tile.src.replace('freemap.sk/T', 'tile.openstreetmap.org');

                    if (errorUrl && tile.src !== errorUrl) {
                        tile.src = errorUrl;
                    }

                    done(e, tile);
                };

                _tileLayer.map_id = 99;

                return _tileLayer;
            }
        }
    };

    _this.mapList = function() {
        var requires = {};

        _this.layers = {};

        $.each(app.settings.availableMaps, function(index, map_id){
            if ( typeof _maps[map_id] !== "undefined" ) {
                var _tile = _maps[map_id];

                if (!_tile.tile())
                    return;

                _this.layers[ _tile.name ] = _tile.tile();

                if ( typeof _tile.require !== "undefined" && typeof requires[_tile.require] === "undefined" ) {
                    requires[_tile.require] = _tile.require;
                }
            }
        });

        $.each(requires, function(index, require){
            switch (require) {
                case 'google':
                    var scriptTag = document.createElement('script');
                    scriptTag.type = 'text/javascript';
                    scriptTag.async = true;
                    scriptTag.src = 'https://maps.google.com/maps/api/js?v=3.34&key=' + app.settings.keys.google;
                    document.head.appendChild(scriptTag);
                    break;
                case 'yandex':
                    break;
            }
        });

        var layersControl = L.control.layers(_this.layers, {},{
            collapsed: true
        });

        layersControl.addTo(_this.map);

        $(layersControl.getContainer()).remove();

        $(layersControl.onAdd(_this.map)).insertAfter($('#map-controls > div').first());
    };

    _this.current = function(){
        if ( typeof _maps[app.settings.map_id] === 'undefined' )
            app.settings.map_id = null;

        if ( app.settings.map_id ) {
            var _existMapID = false;

            $.each(app.settings.availableMaps, function (index, map_id) {
                if ( app.settings.map_id == map_id ) {
                    _existMapID = true;
                    return false;
                }
            });

            if ( ! _existMapID ) {
                app.settings.map_id = null;
            }
        }

        if ( ! app.settings.map_id ) {
            $.each(app.settings.availableMaps, function (index, map_id) {
                app.settings.map_id = map_id;
                return false;
            });
        }

        return _this.layers[ _maps[app.settings.map_id].name ];
    };

    _this.disableTraffic = function() {
        dd( 'disabling traffic' );

        $('#showTraffic')
            .attr('disabled', 'disabled')
            .parent().addClass('disabled');
    };
    _this.enableTraffic = function() {
        dd( 'enabling traffic' );

        $('#showTraffic')
            .removeAttr('disabled')
            .parent().removeClass('disabled');
    };

    _this.init = function( map ) {
        dd( 'mapTiles.init' );

        _this.map = map;

        _this.mapList();

        var tileLayer = _this.current();

        _this.map.addLayer( tileLayer );

        if ( tileLayer.options.maxZoom && _this.map.getZoom() > tileLayer.options.maxZoom ) {
            _this.map.options.maxZoom = tileLayer.options.maxZoom;
            _this.map.zoomOut();
        }

        _this.map.on('baselayerchange', function(e) {
            dd( 'baselayerchange', e );

            var
                map_id = e.layer.map_id,
                _map = _maps[ map_id ];

            _this.map.options.maxZoom = _map.maxZoom;

            if ( _this.map.getZoom() > _this.map.options.maxZoom )
                _this.map.zoomOut();


            if ( _map.traffic ) {
                _this.enableTraffic();
            } else {
                _this.disableTraffic();
            }

            app.saveSetting('map', _map.name);
        });

    };
}