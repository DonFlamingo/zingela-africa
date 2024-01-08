function Geofence(data) {
    var
        _this = this,
        defaults = {
            id: null,
            name: 'N/A',
            active: true,
            polygon_color: '#dddddd',
            coordinates: []
        },
        options = {},
        layer = null,
        popup = null;

    _this.id = function() {
        return options.id;
    };

    _this.options = function() {
        return options;
    };

    _this.active = function(value) {
        options.active = value;

        _this.update();
    };

    _this.isVisible = function() {
        return options.active == true;
    };

    _this.create = function(data) {
        $( document ).trigger('geofence.create', _this);

        data = data || {};

        options = $.extend({}, defaults, data);

        _this.searchValue = options.name.toLowerCase();

        $( document ).trigger('geofence.created', _this);
    };

    _this.update = function(data) {
        $( document ).trigger('geofence.update', _this);

        data = data || {};

        options = $.extend({}, options, data);

        _this.searchValue = options.name.toLowerCase();

        $( document ).trigger('geofence.updated', _this);
    };

    _this.getLatLngs = function() {
        if ( ! layer )
            return null;

        return layer.getLatLngs()[0];
    };

    _this.getBounds = function() {
        if ( ! layer )
            return [];

        return layer.getBounds();
    };

    _this.isLayerVisible = function () {
        if ( options.active != true ) {
            return false;
        }

        return app.settings.showGeofences == true;
    };

    _this.setLayer = function (_layer) {
        _this.removeLayer();

        layer = _layer;

        layer
            .on('remove', _this.onLayerRemove)
            .on('add', _this.onLayerAdd);

        return layer;
    };

    _this.getLayer = function () {
        if ( ! layer ) {
            layer = L.polygon( options.coordinates, {
                color: options.polygon_color,
                weight: 3,
                opacity: 1,
                fill: true,
                fillOpacity: 0.3,
                fillColor: options.polygon_color
            });

            layer
                .on('remove', _this.onLayerRemove)
                .on('add', _this.onLayerAdd);
        }

        return layer;
    };

    _this.updateLayer = function() {
        if ( ! _this.isLayerVisible() ) {
            _this.removeLayer();
        }
    };

    _this.removeLayer = function() {
        if ( ! layer )
            return;

        app.map.removeLayer( layer );

        layer = null;
    };

    _this.enableEdit = function() {
        _this.getLayer().editing.enable();
    };

    _this.disableEdit = function() {
        _this.getLayer().editing.disable();
    };

    _this.addPopup = function() {
        if ( ! layer )
            return;

        if ( ! app.settings.showGeofences )
            return;

        popup = new L.Marker(
            null,
            {
                icon: L.divIcon({
                    html: '<div class="name" style="background-color: ' + convertHex(options.polygon_color, 81) + '">' + options.name + '</div>',
                    className: 'leaflet-popup-geofence',
                    iconSize: 'auto'
                    //iconAnchor: [(width / 2), (height / 2)],
                    //popupAnchor: [0, 0 - height]
                })
            }
        );

        if ( ! layer.isEmpty() ) {
            popup.setLatLng( layer.getCenter() ).addTo( app.map );
        }
    };

    _this.removePopup = function() {
        if ( popup )
            popup.remove();
    };

    _this.onLayerAdd = function() {
        dd('goefence.onLayerAdd');
        _this.addPopup();
    };

    _this.onLayerRemove = function() {
        dd('goefence.onLayerRemove');
        _this.removePopup();
    };

    _this.create(data);
}