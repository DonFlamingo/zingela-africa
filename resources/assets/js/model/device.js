function Device(data) {
    var
        _this = this,
        defaults = {
            name : 'N/A',
            active: false,
            timestamp: 0,
            acktimestamp: 0,
            lat: null,
            lng: null,
            speed: 0,
            altitude: 0,
            course: 0,
            power: null,
            time: null,

            address: null,
            alarm: 0,
            protocol: null,
            driver: null,
            online: null,
            engine_hours: null,
            icon: {
                type: 'arrow',
                width: null,
                height: null,
                path: null
            },
            icon_color: null,
            icon_colors: {
                engine: "blue",
                moving: "green",
                offline: "black",
                stopped: "red",
                idle: "yellow"
            },

            tail: null
        },
        options = {},
        popup = null,
        layer = null,
        tail,
        lat,lng;

    _this.id = function() {
        return options.id;
    };

    _this.options = function() {
        return options;
    };

    _this.name = function() {
        return options.name;
    };

    _this.countSensors = function () {
        return options.sensors ? options.sensors.length : 0;
    };

    _this.active = function(value) {
        options.active = value;
    };

    _this.isVisible = function() {
        return options.active == true;
    };

    _this.checkOffline = function(timestamp, timeout) {
        var
            _online = options.online,
            diff = timestamp - options.timestamp,
            ack_diff = timestamp - options.acktimestamp;

        dd('timestamp', timestamp, diff, ack_diff);

        if (diff >= timeout && ack_diff < timeout) {
            _online = 'ack';
        }

        if (diff >= timeout && ack_diff >= timeout) {
            _online = 'offline';
        }

        if ( _online !== options.online ) {
            _this.update({
                online: _online,
                speed: 0
            });
        }
    };

    _this.create = function(data) {
        $( document ).trigger('device.create', _this);

        data = data || {};

        if ( typeof data.tail === "string" )
            data.tail = JSON.parse(data.tail);

        if ( typeof data.formatSensors !== "undefined" )
            data.sensors = data.formatSensors;

        if ( typeof data.sensors === "string" )
            data.sensors = JSON.parse(data.sensors);

        if ( typeof data.formatServices !== "undefined" )
            data.services = data.formatServices;

        if ( typeof data.services === "string" )
            data.services = JSON.parse(data.services);

        options = $.extend({}, defaults, data);

        _this.lat = options.lat;
        _this.lng = options.lng;

        _this.searchValue = options.name.toLowerCase();

        _this.dataDOMUpdate();
        _this.updatePopup();

        $( document ).trigger('device.created', _this);
    };

    _this.update = function(data) {
        $( document ).trigger('device.update', _this);

        data = data || {};

        if ( typeof data.tail === "string" )
            data.tail = JSON.parse(data.tail);

        if ( typeof data.formatSensors !== "undefined" )
            data.sensors = data.formatSensors;

        if ( typeof data.sensors === "string" )
            data.sensors = JSON.parse(data.sensors);

        if ( typeof data.formatServices !== "undefined" )
            data.services = data.formatServices;

        if ( typeof data.services === "string" )
            data.services = JSON.parse(data.services);

        options = $.extend({}, options, data);

        _this.searchValue = options.name.toLowerCase();


        _this.dataDOMUpdate();
        _this.updatePopup();

        $( document ).trigger('device.updated', _this);
    };

    _this.getSensorByType = function(type) {
        var _sensor = null;

        if ( ! options.sensors )
            return null;

        $.each( options.sensors, function(index, sensor){
            if ( sensor.type == type ) {
                _sensor = sensor;
                return;
            }
        });

        return _sensor;
    };

    _this.sensorData = function( sensor ) {
        if ( ! sensor )
            return null;

        if ( typeof sensor.text === "undefined" )
            sensor.text = sensor.value;

        switch (sensor.type) {
            case 'acc':
            case 'door':
            case 'engine':
            case 'ignition':
                sensor.value = sensor.text == window.lang.on ? true : false;

                break;

            case 'odometer':
            case 'battery':
            case 'tachometer':
            case 'temperature':
            case 'gsm':
            case 'fuel_tank':
            case 'satellites':
            case 'engine_hours':
                sensor.value = parseFloat( sensor.text );

                break;

            default:
                break;
        }

        if (sensor.type === 'gsm' || sensor.type === 'battery') {

            var _value = sensor.value ? sensor.value : 0;

            sensor.num = Math.ceil(_value / 20);
        }

        return sensor;
    };

    _this.data = function(parameter, data) {
        var
            _text        = '-',
            _value       = null,
            _measurement = '',
            _options     = '',
            _sensor      = null;

        dd( 'device.data', data );

        switch (parameter) {
            case 'status':
                _value = options.online;
                _text = _this.getStatusText();
                _options = 'style="background-color: '+_this.getStatusColor()+'" alt="'+_text+'"';
                break;
            case 'status-text':
                _value = options.online;
                _text = _this.getStatusText();
                break;
            case 'name':
                _text = options.name;
                break;
            case 'streetview':

                var _lat = options.lat ? parseFloat(options.lat).toFixed(5) : 0,//Math.round(options.lat, 5),
                    _lng = options.lng ? parseFloat(options.lng).toFixed(5) : 0,
                    _course = Math.round(options.course);
                /*
                var _lat = options.lat,
                    _lng = options.lng,
                    _course = Math.round(options.course);
                */

                _text = '<img alt="Street view" src="'+app.urls.streetView+'?size='+data.size+'&amp;location='+_lat+','+_lng+'&amp;heading='+_course+'">';
                _options = 'data-size="'+data.size+'"';
                break;
            case 'address':
                _text = '';
                _options = 'data-lat="'+options.lat+'" data-lng="'+options.lng+'"';
                break;
            case 'stoptime':
                _text = '';
                _options = 'data-id="'+options.id+'"';
                break;
            case 'time':
                _text = options.time;
                break;
            case 'speed':
                _value = options.speed ? options.speed : 0;
                _text = _value + ' ' + window.distance_unit_hour;
                break;
            case 'position':
                _text = options.lat+'&deg;, '+options.lng+'&deg;';
                break;
            case 'angle':
                _value = options.course;
                _text = options.course+'&deg;';
                break;
            case 'altitude':
                _value = options.altitude;
                _text = options.altitude + ' m';
                break;

            case 'driver':
                _text = '-';
                _value = '-';
                if  ( options.driver ) {
                    if ( typeof options.driver === 'object' && options.driver.length ) {
                        _text = options.driver[0].name;
                        _value = options.driver[0].name;
                    } else {
                        _text = options.driver;
                        _value = options.driver;
                    }
                }
                break;
            case 'detect_engine':
                var _engine = options.engine_hours;

                if ( _engine ) {
                    _sensor = _this.getSensorByType(_engine);

                    if ( _sensor ) {
                        _sensor = _this.sensorData( _sensor );

                        if ( _sensor.value === true ) {
                            _options = 'class="on"';
                        }
                        else if ( _sensor.value === false ) {
                            _options = 'class="off"';
                        }
                    }
                }

                _text = '<i class="icon detect_engine"></i>';
                break;
            case 'acc':
            case 'door':
            case 'engine':
            case 'ignition':
                _sensor = _this.getSensorByType(parameter);

                if ( _sensor ) {
                    _sensor = _this.sensorData( _sensor );

                    _text = _sensor.text;

                    if ( _sensor.value === true ) {
                        _options = 'class="on"';
                    }
                    else if ( _sensor.value === false ) {
                        _options = 'class="off"';
                    }
                } else {
                    _text = '';
                }

                break;

            case 'odometer':
            case 'battery':
            case 'tachometer':
            case 'temperature':
            case 'gsm':
            case 'fuel_tank':
            case 'satellites':
            case 'engine_hours':
                _sensor = _this.getSensorByType(parameter);

                if ( _sensor ) {
                    _sensor = _this.sensorData( _sensor );

                    _text = _sensor.text;
                    _value = _sensor.value;
                } else {
                    _text = '-';
                }

                break;

            default:
                _text = fetchFromObject(options, parameter) || '-';
                break;
        }

        return {
            value: _value,
            text: _text,
            options: _options,
            measurement: _measurement
        };
    };

    _this.dataDOM = function(parameter, data) {
        if ( parameter === 'sensors' ) {
            return _this.dataSensorsDOM( options.sensors );
        }
        if ( parameter === 'services' ) {
            return _this.dataServicesDOM( options.services );
        }

        var _data = _this.data(parameter, data),
            _text = _data.text + (_data.measurement ? ' ' + _data.measurement : '');

        return '<span data-device="' + parameter + '"' + _data.options + '>' + _text + '</span>';
    };

    _this.dataSensorsDOM = function( sensors ) {
        sensors = sensors || [];

        var _html = '<div data-device="sensors">';
        var _row = '';
        var _rows = [];

        $.each( sensors, function(index, item){
            var _data = _this.sensorData(item);

            _row  = '';
            _row += '<tr>';
            if (item.type == 'gsm' || item.type == 'battery') {

                var _type = item.type + ' ' + item.type + '-' + item.num;

                _row += '<td><i class="icon ' + _type + '"></i>' + item.name + '</td>';
            } else {
                _row += '<td><i class="icon ' + item.type + '"></i>' + item.name + '</td>';
            }
            _row += '<td>' + _data.text + '</td>';
            _row += '</tr>';

            _rows.push( _row );
        });

        _row  = '';
        _row += '<tr>';
        _row += '<td><i class="icon speed"></i>'+window.lang.speed+'</td>';
        _row += '<td>' + options.speed + ' ' + window.distance_unit_hour + '</td>';
        _row += '</tr>';

        _rows.push( _row );

        var i,j,chunk = 4;
        for (i=0,j=_rows.length; i<j; i+=chunk) {
            _html += '<table class="table">' + _rows.slice(i,i+chunk).join('') + '</table>';
        }

        _html += '</div>';

        return _html;
    };

    _this.dataServicesDOM = function( services ) {
        services = services || [];

        var _html = '<table class="table" data-device="services">';

        $.each( services, function(index, item){
            _html += '<tr>';
            _html += '<td>' + item.name + '</td>';
            _html += '<td>' + item.value + '</td>';
            _html += '</tr>';
        });

        _html += '</table>';

        return _html;
    };

    _this.dataDOMUpdate = function() {
        $( '[data-device-id="'+_this.id()+'"]' ).each( function(){
            var $_container = $( this );

            dd( 'dataDOMUpdate', $_container );

            $( '[data-device]', $_container ).each( function(){
                var _newDOM = _this.dataDOM( $(this).attr('data-device'), $(this).data() ),
                    _oldDOM = $( this ).outerHTML();

                if ( $(this).is("[data-no-text]") ) {
                    $( _newDOM ).attr('data-no-text', true).html('');
                }

                if ( _newDOM != _oldDOM ) {
                    $( this ).replaceWith( _newDOM );
                }
            });

            initComponents($_container);
        });
    };

    _this.getLatLng = function () {
        layer = _this.getLayer();

        if ( ! layer )
            return null;

        return layer.getLatLng();
    };

    _this.getStatusColor = function() {
        var _color;

        switch (options.online) {
            case 'online':
                _color = options.icon_colors.moving;
                break;
            case 'offline':
                _color = options.icon_colors.offline;
                break;
            case 'ack':
                _color = options.icon_colors.stopped;
                break;
            case 'idle':
                _color = options.icon_colors.idle;
                break;
            case 'engine':
                _color = options.icon_colors.engine;
                break;

            default:
                _color = 'grey';
        }

        switch (_color) {
            case 'green':
                _color = '#2eaf61';
                break;
            case 'red':
                _color = '#f45e5e';
                break;
            case 'yellow':
                _color = '#eee200';
                break;
            case 'blue':
                _color = '#3d8fe3';
                break;
            case 'orange':
                _color = '#f2ab00';
                break;
            case 'black':
                _color = '#222222';
                break;
        }

        return _color;
    };

    _this.getStatusText = function(){
        if ( typeof window.lang['status_' + options.online] !== "undefined" ) {
            return window.lang['status_' + options.online];
        } else {
            return options.online;
        }
    };

    _this.isLayerVisible = function () {
        if ( options.active != true ) {
            return false;
        }

        if ( options.lat == 0 && options.lng == 0 )
            return false;

        return app.settings.showDevice == true;
    };

    _this.getLayer = function () {
        var
            icon        = null,
            course      = 0,
            width       = options.icon.width,
            height      = options.icon.height,
            color       = _this.getStatusColor(),
            position    = new L.LatLng(options.lat, options.lng);

        if (options.icon.type == 'arrow') {
            icon   = '<i class="ico ico-object-arrow" style="color:'+color+'"></i>';
            width  = 25;
            height = 33;
            course = options.course;
        } else {
            icon = '<img src="'+options.icon.path+'" />';
        }

        if (options.icon.type == 'rotating') {
            course = options.course;
        }

        var html = '';
        html += '<span class="name"><i>' + options.name + ' (' + _this.data('speed').text + ')' + '</i></span>';
        html += icon;

        var divIcon = L.divIcon({
            html: html,
            className: 'leaf-device-marker',
            iconSize: [width, height],
            iconAnchor: [(width / 2), (height / 2)],
            popupAnchor: [0, 0 - height]
        });

        if ( ! layer ) {
            layer = new L.Marker(
                position,
                {
                    icon: divIcon,
                    iconAngle: course
                }
            );

            layer
                .on('click', _this.onLayerClick)
                .on('remove', _this.onlayerRemove)
                .on('add', _this.onlayerAdd)
            ;
        } else {
            layer
                .setIcon( divIcon )
                .setLatLng( position )
                .setIconAngle( course );
        }

        return layer;
    };

    _this.openPopup = function() {

        var nav = '';
        nav += '<ul class="nav nav-tabs nav-default" role="tablist">';
        nav += '<li data-toggle="tooltip" data-placement="top" title="" role="presentation" data-original-title="Google Street View"><a href="#gps-device-street-view" aria-controls="gps-device-street-view" role="tab" data-toggle="tab"><i class="fa fa-road fa-1"></i></a></li>';
        nav += '<li data-toggle="tooltip" data-placement="top" title="" role="presentation" class="active" data-original-title="Parameters"><a href="#gps-device-parameters" aria-controls="gps-device-parameters" role="tab" data-toggle="tab"><i class="fa fa-bars fa-1"></i></a></li>';
        nav += '<li data-toggle="tooltip" data-placement="top" title="Close"><a href="javascript:" data-dismiss="popup"><i class="fa fa-times fa-1"></i></a></li>';
        nav += '</ul>';

        var navLargeView = '';
        navLargeView += '<ul class="nav nav-tabs nav-default" role="tablist">';
        navLargeView += '<li><a href="#gps-device-parameters-view" aria-controls="gps-device-parameters-view" role="tab" data-toggle="tab"><i class="fa fa-compress"></i></a></li>';
        navLargeView += '<li data-toggle="tooltip" data-placement="top" title="Close"><a href="javascript:" data-dismiss="popup"><i class="fa fa-times fa-1"></i></a></li>';
        navLargeView += '</ul>';

        var streetViewHTML = '';
        streetViewHTML += '<div role="tabpanel" class="tab-pane" id="gps-device-street-view">';
        streetViewHTML += _this.dataDOM("streetview", {size: '290x125'});
        streetViewHTML += '<div class="buttons buttons-right"> <a href="#gps-device-street-view-large" class="btn" type="button" data-toggle="tab" aria-controls="gps-device-street-view-large">Enlarge <i class="fa fa-expand"></i></a> </div>';
        streetViewHTML += '</div>';

        var streetViewLargeHTML = '';
        streetViewLargeHTML += _this.dataDOM("streetview", {size: '598x313'});
        streetViewLargeHTML += '<div class="buttons buttons-right"></div>';

        var parametersHTML = '';
        parametersHTML += '<div role="tabpanel" class="tab-pane active" id="gps-device-parameters">';
        parametersHTML += '<table class="table table-condensed"><tbody>';
        parametersHTML += '<tr><th>'+window.lang.address+':</th><td>' + _this.dataDOM("address") + '</td></tr>';
        parametersHTML += '<tr><th>'+window.lang.time+':</th><td>' + _this.dataDOM("time") + '</td></tr>';
        parametersHTML += '<tr><th>'+window.lang.stop_duration+':</th><td>' + _this.dataDOM("stoptime") + '</td></tr>';

        if (options.sensors) {
            $.each(options.sensors, function (index, sensor) {
                //if (!item.show_in_popup) return;
                sensor = _this.sensorData(sensor);
                parametersHTML += '<tr><th>' + sensor.name + ':</th><td>' + sensor.text + '</td></tr>';
            });
        }
        parametersHTML += '</tbody></table>';
        parametersHTML += '<div id="device-side-params" class="collapse"><table class="table table-condensed"><tbody>';
        parametersHTML += '<tr><th>'+window.lang.position+':</th><td>'+_this.dataDOM("position")+'</td></tr>';
        parametersHTML += '<tr><th>'+window.lang.speed+':</th><td>'+_this.dataDOM("speed")+'</td></tr>';
        parametersHTML += '<tr><th>'+window.lang.altitude+':</th><td>'+_this.dataDOM("altitude")+'</td></tr>';
        parametersHTML += '<tr><th>'+window.lang.angle+':</th><td>'+_this.dataDOM("angle")+'</td></tr>';
        parametersHTML += '<tr><th>'+window.lang.driver+':</th><td>'+_this.dataDOM("driver")+'</td></tr>';
        parametersHTML += '<tr><th>'+window.lang.model+':</th><td>'+_this.dataDOM("device_model")+'</td></tr>';
        parametersHTML += '<tr><th>'+window.lang.plate+':</th><td>'+_this.dataDOM("plate_number")+'</td></tr>';
        parametersHTML += '<tr><th>'+window.lang.protocol+':</th><td>'+_this.dataDOM("protocol")+'</td></tr>';
        if (options.services) {
            $.each(options.services, function (index, item) {
                parametersHTML += '<tr><th>' + item.name + ':</th><td>' + item.value + '</td></tr>';
            });
        }
        parametersHTML += '</tbody></table></div>';
        parametersHTML += '<div class="text-center"><i class="btn icon ico-options-h" data-toggle="collapse" data-target="#device-side-params"></i></div>';
        parametersHTML += '</div>';

        var html  = '';
        html += '<div class="popup-content" data-device-id="'+options.id+'"><div class="tab-content">';
        html += '<div role="tabpanel" class="tab-pane active" id="gps-device-parameters-view">';
        html += '   <div class="popup-header">'+nav+'<div class="popup-title">'+_this.dataDOM("name")+'</div></div>';
        html += '   <div class="popup-body"><div class="tab-content">'+parametersHTML+streetViewHTML+'</div></div>';
        html += '</div>';
        html += '<div role="tabpanel" class="tab-pane" id="gps-device-street-view-large">';
        html += '   <div class="popup-header">'+navLargeView+'<div class="popup-title">'+window.lang.streetview+'</div></div>';
        html +=     streetViewLargeHTML;
        html += '</div>';
        html += '</div></div>';

        popup = L.popup({
            className: 'leaflet-popup-device',
            closeButton: false,
            maxWidth: "auto"
        })
            .setLatLng( _this.getLatLng() )
            .setContent( html )
            .openOn( app.map );

        initComponents( popup.getElement() );
    };

    _this.updatePopup = function() {
        if ( ! popup )
            return false;

        if ( ! popup.isOpen() )
            return false;

        /* get popup content with all changes */
        var _content =  $( popup.getElement() ).find('.leaflet-popup-content').html();

        popup
            .setLatLng( _this.getLatLng() )
            .setContent( _content )
            .update();

        initComponents( popup.getElement() );
    };

    _this.updateWitgets = function () {
        dd('device.updateWitgets');

        var $widgets = $('#widgets');

        $widgets.attr('data-device-id', options.id);

        _this.dataDOMUpdate();

        $widgets.find('[data-modal="services_create"]').attr('data-url', app.urls.devicesServiceCreate + options.id);
        $widgets.find('[data-modal="sensors_create"]').attr('data-url', app.urls.devicesSonsorCreate + options.id);

        setTimeout(function(){
            initComponents('#widgets');
        }, 50)

    };

    _this.addTail = function() {
        if ( ! options.tail )
            return;

        if ( ! app.settings.showDevice )
            return;

        if ( ! app.settings.showTail )
            return;

        tail = L.polyline(options.tail, {color: options.tail_color, className: 'leaf-device-tail'});
        tail.addTo( app.map );
    };

    _this.removeTail = function() {
        if ( tail )
            tail.remove();
    };

    _this.onLayerClick = function() {
        _this.openPopup();
        _this.updateWitgets();
    };

    _this.onlayerAdd = function(){
        _this.addTail();
    };

    _this.onlayerRemove = function(){
        _this.removeTail();
    };

    _this.create(data);
}
