function Alerts() {
    var
        _this = this,
        items = [],
        loadFails = 0;

    _this.events = function() {
        $(document).on('click', '.alert-form:visible .alert-add-geofence', function() {
            var modal = $('.alert-form:visible');
            var geofence_id = modal.find('select[name="geofence"]').val();
            var geofence_name = modal.find('select[name="geofence"]>option:selected').text();
            var zone_type_id = modal.find('select[name="zone_type"]').val();
            var zone_type_name = modal.find('select[name="zone_type"]>option:selected').text();
            var zone_time_from = modal.find('input[name="time_from"]').val();
            var zone_time_to = modal.find('input[name="time_to"]').val();
            var time_id = zone_time_from.replace(':', '') + '-' + zone_time_to.replace(':', '');
            var item_id = geofence_id + '-' + zone_type_id + '-' + time_id;
            if (modal.find('.' + item_id).length) {
                return;
            }

            modal.find('.alerts-geofences-list').append('<tr class="' + item_id + '">' +
                '<input type="hidden" name="geofences[' + item_id + '][id]" value="' + geofence_id + '">' +
                '<input type="hidden" name="geofences[' + item_id + '][zone]" value="' + zone_type_id + '">' +
                '<input type="hidden" name="geofences[' + item_id + '][time_from]" value="' + zone_time_from + '">' +
                '<input type="hidden" name="geofences[' + item_id + '][time_to]" value="' + zone_time_to + '">' +
                '<td class="text-center">' + geofence_name + '</td>' +
                '<td class="text-center">' + zone_type_name + '</td>' +
                '<td class="text-center">' + zone_time_from + ' - ' + zone_time_to + '</td>' +
                '<td class="text-center"><a href="javascript:;" class="alert-delete-item close center"><span aria-hidden="true">×</span></a>' + '</td>' +
                '</tr>');

            initComponents('.alert-form');
        });

        $(document).on('click', '.alert-form:visible .alert-add-overspeed', function() {
            var modal = $('.alert-form:visible');
            var speed = modal.find('input[name="speed"]');
            var speed_val = modal.find('input[name="speed"]').val();
            var distance_id = modal.find('select[name="distance"]').val();
            var distance_name = modal.find('select[name="distance"]>option:selected').text();
            var item_id = 'alert-distance';
            if (modal.find('.' + item_id).length || speed_val < 1) {
                if (speed_val < 1) {
                    speed.addClass('error');
                }
                else {
                    speed.removeClass('error');
                }
                return;
            }

            speed.removeClass('error');
            modal.find('.alerts-overspeed-list').append('<tr class="' + item_id + '">' +
                '<input type="hidden" name="overspeed[speed]" value="' + speed_val + '">' +
                '<input type="hidden" name="overspeed[distance]" value="' + distance_id + '">' +
                '<td class="text-center">' + speed_val + ' ' + distance_name + '/' + window.lang.h + '</td>' +
                '<td class="text-center"><a href="javascript:;" class="alert-delete-item close center"><span aria-hidden="true">×</span></a>' + '</td>' +
                '</tr>');

            _this.displayDistanceAdd();

            initComponents('.alert-form');
        });

        $(document).on('click', '.alert-form:visible .alert-add-fuel-consumption', function() {
            var modal = $('.alert-form:visible');
            var quantity = modal.find('input[name="quantity"]');
            var quantity_val = modal.find('input[name="quantity"]').val();
            var from = modal.find('input[name="from"]');
            var from_val = modal.find('input[name="from"]').val();
            var to = modal.find('input[name="to"]');
            var to_val = modal.find('input[name="to"]').val();
            var fuel_type = modal.find('select[name="fuel_type"]').val();
            var fuel_type_name = modal.find('select[name="fuel_type"]>option:selected').text();
            var item_id = quantity_val + '-' + from_val + '-' + to_val + '-' + fuel_type;

            quantity.removeClass('error');
            from.removeClass('error');
            to.removeClass('error');

            if (quantity_val < 1)
                quantity.addClass('error');

            var date_from = new Date(from_val);
            var date_to = new Date(to_val);

            if (date_from == 'Invalid Date')
                from.addClass('error');

            if (date_to == 'Invalid Date')
                to.addClass('error');

            // If from > to
            if (date_from > date_to)
                from.addClass('error');

            // If from = to
            if (from_val == to_val) {
                from.addClass('error');
                to.addClass('error');
            }

            if (DateDiff.inDays(date_from, date_to) > 90) {
                var modal = $('#warning_modal');
                modal.find('.content').html(window.lang.alerts_maximum_date_range)
                modal.modal('show');
                from.addClass('error');
                to.addClass('error');
            }

            if ($('#alerts-form-edit--fuel .error').length == 0) {
                modal.find('.alerts-fuel-consumption-list').append('<tr class="' + item_id + '">' +
                    '<input type="hidden" name="fuel_consumption[' + item_id + '][quantity]" value="' + quantity_val + '">' +
                    '<input type="hidden" name="fuel_consumption[' + item_id + '][from]" value="' + from_val + '">' +
                    '<input type="hidden" name="fuel_consumption[' + item_id + '][to]" value="' + to_val + '">' +
                    '<input type="hidden" name="fuel_consumption[' + item_id + '][fuel_type]" value="' + fuel_type + '">' +
                    '<td class="text-center">' + quantity_val + ' ' + fuel_type_name + ', ' + from_val + ' - ' + to_val + '</td>' +
                    '<td class="text-center"><a href="javascript:;" class="alert-delete-item close center"><span aria-hidden="true">×</span></a>' + '</td>' +
                    '</tr>');

                initComponents('.alert-form');
            }
        });

        $(document).on('click', '.alert-form .alert-delete-item', function() {
            var el = $(this).closest('tr');
            el.remove();
            if (el.hasClass('alert-distance'))
                _this.displayDistanceAdd();

            initComponents('.alert-form');
        });

        $(document).on('change', '.alert-form:visible select[name="event_protocol"]', function() {
            var protocol = $('.alert-form:visible select[name="event_protocol"]').val();
            var type = $('.alert-form:visible select[name="event_type"]').val();
            $.ajax({
                type: 'POST',
                url: app.urls.alertGetEvents,
                data: {
                    type: type,
                    protocol: protocol
                },
                beforeSend: function() {
                    $('.alert-form:visible select[name="event_id"]').attr('disabled', 'disabled');
                },
                success: function(res) {
                    $('.alert-form:visible .event_id_ajax').html(res);

                    initComponents( $('.alert-form:visible .event_id_ajax') );
                }
            });
        });

        $(document).on('change', '.alert-form:visible select[name="devices[]"]', function() {
            $('.alert-form:visible select[name="event_type"]').trigger('change');
        });

        $(document).on('change', '.alert-form select[name="devices[]"]', function() {
            var $warning = $('#warning-device-empty');

            if ( $(this).val().length ) {
                $warning.hide();
            } else {
                $warning.show();
            }
        });

        $(document).on('change', '.alert-form:visible select[name="event_type"]', function() {
            var type = $(this).val();
            var _items = $('.alert-form:visible select[name="devices[]"]').val();

            $.ajax({
                type: 'POST',
                url: app.urls.alertGetProtocols,
                data: {
                    type: type,
                    devices: _items
                },
                beforeSend: function() {
                    $('.alert-form:visible select[name="event_id"]').attr('disabled', 'disabled')
                        .find('option')
                        .remove()
                        .end();
                    $('.alert-form:visible select[name="event_protocol"]').attr('disabled', 'disabled');
                },
                success: function(res) {
                    $('.alert-form:visible .event_protocol_ajax').html(res);

                    initComponents( $('.alert-form:visible .event_protocol_ajax') );
                }
            });
        });

        $(document).on('click', '.alert-form:visible .alert-add-event', function() {
            var modal = $('.alert-form:visible');
            var event_protocol = modal.find('select[name="event_protocol"]').val();
            var event_id = modal.find('select[name="event_id"]').val();
            var event_message = modal.find('select[name="event_id"] option:selected').text();

            if (event_id == null || modal.find('.event_' + event_id).length)
                return;

            modal.find('.alerts-events-list').append('<tr class="event_' + event_id + '">' +
                '<input type="hidden" name="events_custom[]" value="' + event_id + '">' +
                '<td class="text-center">' + event_protocol + '</td>' +
                '<td class="text-center">' + event_message + '</td>' +
                '<td class="text-center"><a href="javascript:;" class="alert-delete-item close center"><span aria-hidden="true">×</span></a>' + '</td>' +
                '</tr>');

            initComponents( modal );
        });
    };

    _this.events();

    _this.list = function() {
        var dataType = 'html';

        dd('alerts.list');

        var $container = $('#ajax-alerts');

        $.ajax({
            type: 'GET',
            dataType: dataType,
            url: app.urls.alerts,
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

    _this.active = function(alert_id, value) {
        _this.changeActive( alert_id, value );
    };

    _this.changeActive = function( id, status ) {
        dd( 'alerts.changeActive', id, status );

        $.ajax({
            type: 'POST',
            url: app.urls.alertChangeActive,
            data: {
                id: id,
                active: status
            },
            error: handlerFail
        });
    };

    _this.displayDistanceAdd = function () {
        var el = $('.alerts-overspeed-list tr');
        var add_el = $('.alert-add-overspeed');
        if (el.length) {
            add_el.css('display', 'none');
        }
        else {
            add_el.css('display', 'inline-block');
        }
    }
}

function alerts_create_modal_callback(res) {
    if (res.status == 1)
        app.notice.success(window.lang.successfully_created_alert);

    app.alerts.list();
}

function alerts_destroy_modal_callback(res) {
    if (res.status == 1)
        app.notice.success(window.lang.successfully_updated_alert);

    app.alerts.list();
}

function alerts_edit_modal_callback(res) {
    if (res.status == 1)
        app.notice.success(window.lang.successfully_updated_alert);

    app.alerts.list();
}