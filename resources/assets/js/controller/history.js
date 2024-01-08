function History() {
    var
        _this = this,
        layer = null;

    _this.init = function() {
        _this.events();

        _this.graph = new HistoryGraph();
        _this.player = new HistoryPlayer();
    };

    _this.events = function() {
        $(document)
            .on('hidden.bs.tab', '[data-toggle="tab"][href="#history_tab"]', function (e) {
                dd('History.hidden.bs.tab');

                _this.clear();

                _this.hideControls();
                app.showControls();
                app.devices.show();
            })
            .on('shown.bs.tab', '[data-toggle="tab"][href="#datalog"]', function (e) {
                $('#graph_sensors').hide();

                _this.getMessages();
            })
            .on('shown.bs.tab', '[data-toggle="tab"][href="#history-list"]', function (e) {
                dd('History.shown.bs.tab');
                _this.getHistories();
            })

            .on('shown.bs.tab', '[data-toggle="tab"][href="#graph"]', function (e) {
                $('#graph_sensors').show();
            });

        $(document).on('click', '#history-table-content-table tbody tr', function() {
            $('#history-table-content-table tbody tr').removeClass('selected');
            $(this).addClass('selected');
            var item = $(this).data();
            item.other_arr = jQuery.parseJSON($(this).find('.message_other').html());
            item.sensors_arr = jQuery.parseJSON($(this).find('.message_sensors').html());
            _this.historyPointPopup(item);
        });
    };

    _this.showControls = function() {
        $('#history-control-layers').show();
    };
    _this.hideControls = function() {
        $('#history-control-layers').hide();
    };

    _this.select = function(history_id) {
        $('#ajax-history [data-history-id]').removeClass('active');
        $('#ajax-history [data-history-id="'+history_id+'"]').addClass('active');

        var item = window.history_items[history_id];

        if ( ! item )
            return;

        app.map.closePopup();

        if (_this.polyline_sector != null)
            app.map.removeLayer( _this.polyline_sector );

        var coordinates = [];

        $.each(item.items, function(sindex) {
            coordinates.push( window.history_cords[sindex] );
        });

        _this.polyline_sector = L.featureGroup();
        _this.polyline_sector.addLayer(L.polyline( coordinates, {color: '#66FF33'}));
        _this.polyline_sector.addTo(app.map);

        // If selected marker open popup
        if (item.status > 1 && typeof _this.markers[history_id] != 'undefined') {
            var marker = _this.markers[history_id];
            marker.fire('click');
            app.map.setView( marker.getLatLng() );
        }
        else {
            setTimeout(function () {
                app.map.fitBounds( _this.polyline_sector.getBounds(), app.getMapPadding() );
            }, 100);
        }
    };

    _this.filterData = function(tab) {
        var $container = $('#history_tab');

        return {
            device_id: $('select[name="devices"]', $container).val(),
            from_date: $('input[name="from_date"]', $container).val(),
            from_time: $('select[name="from_time"]', $container).val(),
            to_date: $('input[name="to_date"]', $container).val(),
            to_time: $('select[name="to_time"]', $container).val(),
            snap_to_road: $('input[name="snap_to_road"]', $container).prop('checked'),
            tab: tab
        };
    };

    _this.get = function() {
        var $container = $('#history_tab');

        $.ajax({
            type: 'GET',
            dataType: "html",
            url: app.urls.history,
            data: _this.filterData(),
            beforeSend: function () {
                loader.add( $container );
                _this.graph.clear();
            },
            success: function (res) {
                $('#ajax-history').html(res);

                _this.parse();
            },
            complete: function() {
                loader.remove( $container );
            },
            error: handlerFail
        });
    };

    _this.export = function( format ) {
        var $container = $('#history_tab');
        var data = _this.filterData();

        data.format = format;

        $.ajax({
            type: 'GET',
            dataType: "json",
            url: app.urls.historyExport,
            data: data,
            beforeSend: function () {
                loader.add( $container );
            },
            success: function (res) {
                if ( res.download != null ) {
                    window.location.href = res.download;
                }
                if ( res.error != null ) {
                    app.notice.error(res.error);
                }
            },
            complete: function() {
                loader.remove( $container );
            },
            error: handlerFail
        });
    };

    _this.device = function(device_id, period) {
        var id = $(this).data('id');

        if (period == 'last_hour') {
            var date = moment().subtract(1, "hours");
            var hour = date.hours();
            var min =  date.minutes();

            if (hour < 10) {
                hour = "0" + hour;
            }

            if (min != 0) {
                if (min >= 45) {
                    min = 45;
                }
                if (min < 45 && min > 15) {
                    min = 30;
                }

                if (min <= 15) {
                    min = 15;
                }
            } else {
                min = '00';
            }

            var from_time = hour + ':' + min;
            var to_time = '23:45';

            var from_date = date;
            var to_date = moment();
        }

        if (period == 'today') {
            var from_date = moment();
            var to_date = moment().add(1, "days");
            var from_time = '00:00';
            var to_time = '00:00';
        }

        if (period == 'yesterday') {
            var from_date = moment().subtract(1, "days");
            var to_date = moment();
            var from_time = '00:00';
            var to_time = '00:00';
        }

        from_date = from_date.format('YYYY-MM-DD');
        to_date = to_date.format('YYYY-MM-DD');

        var $history_form = $('#history-form');

        $('input[name="from_date"]', $history_form).val( from_date ).datepicker( "setDate", from_date);
        $('input[name="to_date"]', $history_form).val( to_date ).datepicker( "setDate", to_date);
        $('select[name="from_time"]', $history_form).val(from_time).trigger('change');
        $('select[name="to_time"]', $history_form).val(to_time).trigger('change');
        $('select[name="devices"]', $history_form).val(device_id).trigger('change');

        app.openTab('history_tab');

        _this.get();
    };

    _this.clear = function (clear) {
        if ( _this.player ) {
            _this.player.clear();
        }

        if ( _this.polylines != null ) {
            app.map.removeLayer( _this.polylines );
        }
        if ( _this.markers != null ) {
            app.map.removeLayer( _this.markers );
        }
        if ( _this.polyline_sector != null ) {
            app.map.removeLayer( _this.polyline_sector );
        }

        _this.polylines = null;
        _this.markers = null;
        _this.polyline_sector = null;

        if (typeof clear == 'undefined')
            $('#ajax-history').html('');

        _this.graph.clear();
    };

    _this.parse = function() {
        _this.clear('yes');

        app.devices.hide();
        app.hideControls();
        _this.showControls();

        if (window.history_items != null) {

            app.map.invalidateSize();

            var polylines = L.featureGroup();
            var bounds = [];
            var poly = null;
            var marker = {};
            var markers = {};
            var poly_ids = [];
            var first_index = null;
            var polyArray = [];

            var myIcons = {
                2: L.icon({
                    iconUrl: app.urls.asset + 'assets/images/route_stop.png',
                    iconSize: [32, 32],
                    iconAnchor: [16, 32]
                }),
                3: L.icon({
                    iconUrl: app.urls.asset + 'assets/images/route_start.png',
                    iconSize: [32, 32],
                    iconAnchor: [16, 32]
                }),
                4: L.icon({
                    iconUrl: app.urls.asset + 'assets/images/route_end.png',
                    iconSize: [32, 32],
                    iconAnchor: [16, 32]
                }),
                5: L.icon({
                    iconUrl: app.urls.asset + 'assets/images/route_event.png',
                    iconSize: [32, 32],
                    iconAnchor: [16, 32]
                })
            };

            var icon = L.icon({
                iconUrl: app.urls.asset + 'assets/images/history2.png',
                iconSize: [12, 12],
                iconAnchor: [6, 6]
            });

            var last_point, point;
            var color, lastColor = 'blue';
            $.each(window.history_items, function(index, value) {

                first_index = null;

                if (typeof value.items != 'undefined') {
                    $.each(value.items, function(sindex, svalue) {
                        first_index = sindex;
                        if (value.status != 5) {
                            var latlngs = {
                                lat: parseFloat(window.history_cords[sindex].lat),
                                lng: parseFloat(window.history_cords[sindex].lng)
                            };
                            var id = latlngs.lat + '_' + latlngs.lng;
                            point = app.map.project(latlngs);

                            color = 'blue';
                            if (typeof window.history_cords[sindex].color !== 'undefined') {
                                color = window.history_cords[sindex].color;
                            }

                            if (typeof last_point == 'undefined' || poly_ids[id] === undefined) {

                                if (poly != null && lastColor !== color) {
                                    poly.addLatLng(latlngs);
                                    polyArray.push(poly);
                                }

                                if (poly == null || lastColor !== color) {
                                    poly = L.polyline(latlngs, {
                                        color: color,
                                        weight: 3
                                    });
                                }

                                poly.addLatLng(latlngs);
                                poly_ids[id] = 1;
                            }
                            last_point = point;
                            lastColor = color;
                        }
                    });

                    if (value.status > 1) {
                        if ( ! app.settings.showHistoryEvent && value.status == 5 )
                            return;
                        if ( ! app.settings.showHistoryStop && value.status == 2 )
                            return;

                        var item = window.history_cords[first_index];
                        marker = L.marker([item.lat, item.lng], {icon: myIcons[value.status]}).on('click', _this.openPopup);
                        marker.id = index;
                        markers[index] = marker;
                        polylines.addLayer(marker);
                    }
                }
            });

            if (typeof poly !== 'undefined' && poly.getLatLngs().length > 1) {
                polyArray.push(poly);
            }

            if ( app.settings.showHistoryRoute ) {
                $.each(polyArray, function (index, poly) {
                    polylines.addLayer(poly);
                });
            }

            if ( app.settings.showHistoryArrow ) {
                polylines.addLayer( L.polylineDecorator(polyArray, {
                    patterns: [
                        {
                            offset: 25, repeat: 250, symbol: L.Symbol.marker({
                            rotate: true, markerOptions: {
                                icon: icon
                            }
                        })
                        }
                    ]
                }));
            }

            _this.markers = markers;
            _this.polylines = polylines;
            _this.polylines.addTo( app.map );


            setTimeout(function () { app.map.fitBounds( polylines.getBounds(), app.getMapPadding()); }, 200);

            _this.onDataReceived();

            if( $('#messages_tab:visible') ) {
                _this.getMessages();
            }

            if( $('#messages_tab2:visible') ) {
                _this.getHistories();
            }
        }
    };

    _this.openPopup = function(e) {
        var id = e.target.id;
        var value = window.history_items[id];
        var marker = _this.markers[id];
        var first_index;

        $.each(value.items, function(sindex, svalue) {
            first_index = sindex;
        });
        var item = window.history_cords[first_index];

        marker.unbindPopup();

        var nav = '';
        nav += '<ul class="nav nav-tabs nav-default" role="tablist">';
        nav += '<li data-toggle="tooltip" data-placement="top" title="Close"><a href="javascript:" data-dismiss="popup"><i class="fa fa-times fa-1"></i></a></li>';
        nav += '</ul>';

        var parametersHTML = '';
        parametersHTML += '<table class="table table-condensed"><tbody>';
        if ( value.status == 5 ) {
            parametersHTML += '<tr><th>' + window.lang.event + ':</th><td>' + (item.message != null ? item.message : '-') + '</td></tr>';
        }

        parametersHTML += '<tr><th>' + window.lang.address + ':</th><td><span data-device="address" data-lat="'+item.lat+'" data-lng="'+item.lng+'"></span></td></tr>';
        parametersHTML += '<tr><th>' + window.lang.street_view + ':</th><td><a href="http://maps.google.com/?q=&cbll=' + item.lat + ',' + item.lng + '&cbp=12,20.09,,0,5&layer=c&hl=' + window.lang.lang + '" target="_blank">' + window.lang.preview + ' &gt;&gt;</a></td></tr>';
        parametersHTML += '<tr><th>' + window.lang.driver + ':</th><td>' + (value.driver != null ? value.driver : '-') + '</td></tr>';
        parametersHTML += '<tr><th>' + window.lang.lat + ':</th><td>' + item.lat + '&deg;</td></tr>';
        parametersHTML += '<tr><th>' + window.lang.lng + ':</th><td>' + item.lng + '&deg;</td></tr>';
        parametersHTML += '<tr><th>' + window.lang.altitude + ':</th><td>' + item.altitude + ' ' + window.lang.m + '</td></tr>';
        if ( value.status == 2 ) {
            parametersHTML += '<tr><th>' + window.lang.speed + ':</th><td>' + item.speed + ' ' + window.distance_unit_hour + '</td></tr>';
            parametersHTML += '<tr><th>' + window.lang.time + ':</th><td>' + value.show + '</td></tr>';
            parametersHTML += '<tr><th>' + window.lang.came + ':</th><td>' + value.show + '</td></tr>';
            parametersHTML += '<tr><th>' + window.lang.left + ':</th><td>' + (typeof value.left != 'undefined' ? value.left : '-') + '</td></tr>';
            parametersHTML += '<tr><th>' + window.lang.duration + ':</th><td>' + value.time + '</td></tr>';

        }
        if ( value.status == 3 || value.status == 4 ) {
            parametersHTML += '<tr><th>' + window.lang.route_length + ':</th><td>' + $('#history_distance_sum').val() + '</td></tr>';
            parametersHTML += '<tr><th>' + window.lang.move_duration + ':</th><td>' + $('#history_move_duration').val() + '</td></tr>';
            parametersHTML += '<tr><th>' + window.lang.stop_duration + ':</th><td>' + $('#history_stop_duration').val() + '</td></tr>';
            parametersHTML += '<tr><th>' + window.lang.top_speed + ':</th><td>' + $('#history_top_speed').val() + '</td></tr>';
            parametersHTML += '<tr><th>' + window.lang.fuel_cons + ' (' + window.lang.gps + ')' + ':</th><td>' + $('#history_fuel_consumption').val() + '</td></tr>';
        }

        $.each(window.history_fuel_consumption_arr, function(sindex, svalue) {
            parametersHTML += '<tr><th>' + window.lang.fuel_cons + ' (' +  svalue.name + '):</th><td>' + svalue.value + '</td></tr>';
        });

        parametersHTML += '</tbody></table>';

        var html  = '';
        html += '<div class="popup-content" data-history-id="'+id+'">';
        html += '   <div class="popup-header">'+nav+'<div class="popup-title"></div></div>';
        html += '   <div class="popup-body">'+parametersHTML+'</div>';
        html += '</div>';

        marker.bindPopup( html,
            {
                className: 'leaflet-popup-history',
                closeButton: false,
                maxWidth: "auto"
            }).openPopup();

        initComponents( marker._popup.getElement() );
    };

    _this.onDataReceived = function() {
        var
            $graph_sensors = $('#graph_sensors');

        $graph_sensors.html('');
        $graph_sensors.append('<li role="presentation"><a href="#speed" role="tab" data-toggle="tab" data-id="speed">' + window.lang.speed + '</a></li>');
        $graph_sensors.append('<li role="presentation"><a href="#altitude" role="tab" data-toggle="tab" data-id="altitude">' + window.lang.altitude + '</a></li>');

        $.each(window.history_sensors, function(index, value) {
            $graph_sensors.append('<li role="presentation"><a href="#' + index + '" role="tab" data-toggle="tab" data-id="' + index + '">' + value.name + '</a></li>');
        });


        $('li:first a', $graph_sensors).trigger('click');
        $('#bottom-history').show();

        _this.graph.graph_data = {};
        _this.graph.parseData();
    };

    _this.getMessages = function() {
        var $container = $('#messages_tab');

        $( '[data-filter]', $container ).remove();

        var filters = _this.filterData();

        $.each( filters, function( key, value ) {
            $('<input type="hidden" name="'+key+'" value="'+value+'" data-filter />').appendTo( $container );
        });
        $('<input type="hidden" name="limit" value="0" data-filter />').appendTo( $container );

        tables.get('messages_tab');
    };

    _this.getHistories = function() {
        var $container = $('#messages_tab2');

        $( '[data-filter]', $container ).remove();

        var filters = _this.filterData();

        $.each( filters, function( key, value ) {
            $('<input type="hidden" name="'+key+'" value="'+value+'" data-filter />').appendTo( $container );
        });
        $('<input type="hidden" name="limit" value="0" data-filter />').appendTo( $container );

        var historiesConfig = {
          url : app.urls.historyHistory,
          delete_url: app.urls.historyPositionsDelete
        };

        tables.get('messages_tab2', historiesConfig);
    };

    _this.historyPointPopup = function (item) {
        var nav = '';
        nav += '<ul class="nav nav-tabs nav-default" role="tablist">';
        nav += '<li data-toggle="tooltip" data-placement="top" title="Close"><a href="javascript:" data-dismiss="popup"><i class="fa fa-times fa-1"></i></a></li>';
        nav += '</ul>';

        var parametersHTML = '';
        parametersHTML += '<table class="table table-condensed"><tbody>';
        parametersHTML += '<tr><th>' + window.lang.address + ':</th><td><span data-device="address" data-lat="'+item.lat+'" data-lng="'+item.lng+'"></span></td></tr>';
        parametersHTML += '<tr><th>' + window.lang.street_view + ':</th><td><a href="http://maps.google.com/?q=&cbll=' + item.lat + ',' + item.lng + '&cbp=12,20.09,,0,5&layer=c&hl=' + window.lang.lang + '" target="_blank">' + window.lang.preview + ' &gt;&gt;</a></td></tr>';
        parametersHTML += '<tr><th>' + window.lang.lat + ':</th><td>' + item.lat + '&deg;</td></tr>';
        parametersHTML += '<tr><th>' + window.lang.lng + ':</th><td>' + item.lng + '&deg;</td></tr>';
        parametersHTML += '<tr><th>' + window.lang.altitude + ':</th><td>' + item.altitude + ' ' + window.lang.m + '</td></tr>';
        parametersHTML += '<tr><th>' + window.lang.speed + ':</th><td>' + item.speed + ' ' + window.distance_unit_hour + '</td></tr>';
        parametersHTML += '<tr><th>' + window.lang.time + ':</th><td>' + item.time + '</td></tr>';

        if (typeof item.sensors_arr != 'undefined') {
            $.each(item.sensors_arr, function(index, value) {
                parametersHTML += '<tr><th>' + window.lang.sensors + ' ' + value.name + '</th><td>' + value.value + '</td></tr>';
            });
        }

        if (typeof item.other_arr != 'undefined') {
            var _other = '';
            $.each(item.other_arr, function (index, value) {
                _other += value + '<br>';
            });
            parametersHTML += '<tr><th></th><td>' + _other + '</td></tr>';
        }

        parametersHTML += '</tbody></table>';

        var html  = '';
        html += '<div class="popup-content" data-history-id="'+item.position_id+'">';
        html += '   <div class="popup-header">'+nav+'<div class="popup-title"></div></div>';
        html += '   <div class="popup-body">'+parametersHTML+'</div>';
        html += '</div>';

        var popup = L.popup({
            className: 'leaflet-popup-history',
            closeButton: false,
            maxWidth: "auto"
        })
            .setLatLng([item.lat, item.lng])
            .setContent( html )
            .openOn( app.map );

        app.map.setView(popup.getLatLng());

        initComponents( popup.getElement() );
    }
}