function Sensors() {
    var _this = this;

    _this.events = function () {
        $(document).on('change', '.modal-dialog:visible select[name="sensor_type"]', function() {
            var parent = $(this).closest('.modal-content');
            $('.help-block.error').remove();
            _this.inputs(parent);
        });

        $(document).on('change', '.modal-dialog:visible select[name="shown_value_by"]', function() {
            var parent = $(this).closest('.modal-content');
            _this.batteryInputs(parent);
        });

        $(document).on('change', '.modal-dialog:visible select[name="odometer_value_by"]', function() {
            var parent = $(this).closest('.modal-content');
            _this.odometerInputs(parent);
        });

        $(document).on('click', '.add_calibration', function() {
            var parent = $(this).closest('.modal-content');
            if (parent.find('select[name="sensor_type"]').val() !== 'fuel_tank_calibration')
                return;

            var x = parent.find('input[name="x"]');
            var y = parent.find('input[name="y"]');
            var x_val = x.val();
            var y_val = y.val();
            var error = false;

            x.css('border-color', '#ccc');
            y.css('border-color', '#ccc');
            if (!isNumeric(x_val) || parent.find('input[name="calibrations[' + x_val + ']"]').length) {
                x.css('border-color', 'red');
                error = true;
            }
            if (!isNumeric(y_val) || parent.find('input[name="ys[' + y_val + ']"]').length) {
                y.css('border-color', 'red');
                error = true;
            }

            if (error)
                return;

            parent.find('table tbody').append(_this.calibrationRow(x_val, y_val));
        });

        $(document).on('click', '.remove_calibration', function() {
            $(this).closest('tr').remove();
        });

        $(document).on('change', 'input[name="setflag"]', function() {
            var parent = $(this).closest('.modal-content');
            _this.inputs(parent);
        });
    };

    _this.events();

    _this.inputs = function (parent) {
        var type = parent.find('select[name="sensor_type"]').val();
        parent.find('.sensors_form_inputs').hide();
        parent.find('.tag_name').show();
        parent.find('.unit_of_measurement').show();
        if (parent.find('input[name="setflag"]').prop('checked')) {
            parent.find('.sensors_form_inputs.setflag.' + type).show();
            parent.find('.sensors_form_inputs.' + type).not('.notsetflag').show();
        }
        else {
            parent.find('.sensors_form_inputs.' + type).not('.setflag').show();
        }

        if (type === 'battery')
            _this.batteryInputs(parent);

        if (type === 'odometer')
            _this.odometerInputs(parent);

        if (type === 'ignition' || type === 'drive_business' || type === 'drive_private')
            parent.find('.unit_of_measurement').hide();

        if (type === 'fuel_tank_calibration') {
            parent.find('input[name="y"], input[name="x"]').removeAttr('disabled');
            var calibrations = parent.find('.calibrations').html();
            if (typeof calibrations !== 'undefined') {
                calibrations = jQuery.parseJSON(calibrations);
                if (calibrations !== null) {
                    $.each(calibrations, function(index, value) {
                        parent.find('table tbody').append(_this.calibrationRow(index, value));
                    });
                }
            }

            $('#sensors_create, #sensors_edit').find('.modal-dialog').addClass('modal-lg');
            $('.sen-cal-fields').show();
            $('.sen-data-fields').removeClass('col-md-12').addClass('col-md-6');
        }
        else {
            $('#sensors_create, #sensors_edit').find('.modal-dialog').removeClass('modal-lg');
            $('.sen-cal-fields').hide();
            $('.sen-data-fields').removeClass('col-md-6').addClass('col-md-12');

            parent.find('table tbody').html('');
            parent.find('input[name="y"], input[name="x"]').attr('disabled', 'disabled');
        }
    };

    _this.batteryInputs = function (parent) {
        parent.find('.sensors_form_inputs.battery_value_by').hide();
        var value_by = parent.find('select[name="shown_value_by"]').val();

        parent.find('.sensors_form_inputs.battery_value_by.' + value_by).show();
    };

    _this.odometerInputs = function (parent) {
        parent.find('.sensors_form_inputs.odometer_value_by').hide();
        var value_by = parent.find('select[name="odometer_value_by"]').val();

        if (value_by === 'connected_odometer')
            parent.find('.tag_name').show();
        else
            parent.find('.tag_name').hide();

        parent.find('.sensors_form_inputs.odometer_value_by.' + value_by).show();
    };

    _this.calibrationRow = function (x, y) {
        return '<tr><td>' + x + '<input type="hidden" name="calibrations[' + x + ']" value="' + y + '"></td><td>' + y + '<input type="hidden" name="ys[' + y + ']" value="1"></td><td><button type="button" class="remove_calibration close"><span aria-hidden="true">×</span></button></td></tr>';
    }
}