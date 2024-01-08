function EventSys(data) {
    var
        _this = this,
        defaults = {
            id: null,
            name: null,
            altitude: null,
            speed: null,
            course: null,
            latitude: null,
            longitude: null,
            time: null,
            device: null,
            geofence: null,
            message: null,
            detail: null,
            device_name: null
        },
        options;

    _this.create = function(data) {
        $( document ).trigger('event.create', _this);

        data = data || {};

        options = $.extend({}, defaults, data);

        _this.lat = options.latitude;
        _this.lng = options.longitude;

        $( document ).trigger('event.created', _this);
    };

    _this.notice = function() {
        var message = options.message + '<br>';
        var notificationTitle = options.message;
        var notificationMessage = '';
        // Geofence
        if (options.geofence != null) {
            if (options.geofence.name != null) {
                message += window.lang.geofence + ': ' + options.geofence.name + '<br>';
                notificationMessage += window.lang.geofence + ': ' + options.geofence.name + ',';
            }
        }

        //Device
        if (options.device != null) {
            message += window.lang.device + ': ' + options.device_name + '<br>';
            notificationMessage += window.lang.device + ': ' + options.device_name + ', ';
        }
        if (options.speed != null) {
            message += window.lang.speed + ': ' + options.speed + ' ' + window.distance_unit_hour + '<br>';
            notificationMessage += window.lang.speed + ': ' + options.speed + ' ' + window.distance_unit_hour + ', ';
        }
        if (options.address != null) {
            message += window.lang.address + ': ' + options.address + '<br>';
            notificationMessage += window.lang.speed + ': ' + options.speed + ' ' + window.distance_unit_hour + ', ';
        }

        notificationMessage = notificationMessage.slice(0, notificationMessage.length - 2);

        //app.notice.info(message, window.lang.event, {timeOut: 60000});
        app.notice.info(message);
        new Notifications().notify(notificationTitle, notificationMessage, '/assets/images/marker-icon-2x.png', '/objects');
    };

    _this.html = function () {
        var _html = '';
        var _message = options.message + (options.geofence != null ? (' ('+options.geofence.name+')') : '');

        _html += '<tr data-event-id="' + options.id + '" onClick="app.events.select(' + options.id + ');">';
        _html += '<td class="datetime">';
        _html += '<span class="time">'+ moment.utc(options.time).format('HH:mm:ss') +'</span>';
        _html += '<span class="date">'+ moment.utc(options.time).format('YYYY-MM-DD') +'</span>';
        _html += '</td>';
        _html += '<td>'+ (options.device != null ? options.device_name : '-') +'</td>';
        _html += '<td>'+ _message +'</td>';

        return _html;
    };

    _this.popup = function() {
        var nav = '';
        nav += '<ul class="nav nav-tabs nav-default" role="tablist">';
        nav += '<li data-toggle="tooltip" data-placement="top" title="Close"><a href="javascript:" data-dismiss="popup"><i class="fa fa-times fa-1"></i></a></li>';
        nav += '</ul>';

        var parametersHTML = '';
        parametersHTML += '<table class="table table-condensed"><tbody>';
        parametersHTML += '<tr><th>' + window.lang.address + ':</th><td><span data-device="address" data-lat="'+options.latitude+'" data-lng="'+options.longitude+'"></span></td></tr>';
        parametersHTML += '<tr><th>' + window.lang.street_view + ':</th><td><a href="http://maps.google.com/?q=&cbll=' + options.latitude + ',' + options.longitude + '&cbp=12,20.09,,0,5&layer=c&hl=' + window.lang.lang + '" target="_blank">' + window.lang.preview + ' &gt;&gt;</a></td></tr>';
        parametersHTML += '<tr><th>' + window.lang.lat + ':</th><td>' + options.latitude + '&deg;</td></tr>';
        parametersHTML += '<tr><th>' + window.lang.lng + ':</th><td>' + options.longitude + '&deg;</td></tr>';
        parametersHTML += '<tr><th>' + window.lang.altitude + ':</th><td>' + options.altitude + ' ' + window.lang.m + '</td></tr>';
        parametersHTML += '<tr><th>' + window.lang.speed + ':</th><td>' + options.speed + ' ' + window.distance_unit_hour + '</td></tr>';
        parametersHTML += '<tr><th>' + window.lang.time + ':</th><td>' + options.time + '</td></tr>';

        parametersHTML += '</tbody></table>';

        var html  = '';
        html += '<div class="popup-content" data-history-id="'+options.id+'">';
        html += '   <div class="popup-header">'+nav+'<div class="popup-title">'+options.device_name+'</div></div>';
        html += '   <div class="popup-body">'+parametersHTML+'</div>';
        html += '</div>';

        var popup = L.popup({
            className: 'leaflet-popup-event',
            closeButton: false,
            maxWidth: "auto"
        })
            .setLatLng([options.latitude, options.longitude])
            .setContent( html )
            .openOn( app.map );

        app.setView(popup.getLatLng());

        initComponents( popup.getElement() );
    };

    _this.create(data);
}