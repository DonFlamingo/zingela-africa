@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon device"></i> {!!trans('global.add_new')!!}
@stop

@section('body')
    <ul class="nav nav-tabs nav-default" role="tablist">
        <li class="active"><a href="#device-add-form-main" role="tab" data-toggle="tab">{!!trans('front.main')!!}</a></li>
        <li><a href="#device-add-form-icons" role="tab" data-toggle="tab">{!!trans('front.icons')!!}</a></li>
        <li><a href="#device-add-form-advanced" role="tab" data-toggle="tab">{!!trans('front.advanced')!!}</a></li>
        @if (isAdmin())
            <li><a href="#device-add-form-sensors" role="tab" data-toggle="tab">{{ trans('front.sensors') }}</a></li>
        @endif
        <li><a href="#device-add-form-accuracy" role="tab" data-toggle="tab">{!!trans('front.accuracy')!!}</a></li>
        <li><a href="#device-add-form-tail" role="tab" data-toggle="tab">{!!trans('front.tail')!!}</a></li>
        <li><a href="javascript:" role="tab" class="disabled">{!!trans('front.services')!!}</a></li>
    </ul>

    {!!Form::open(['route' => 'devices.store', 'method' => 'POST'])!!}
    {!!Form::hidden('id')!!}
    <div class="tab-content">
        <div id="device-add-form-main" class="tab-pane active">
            @if (isAdmin())
            <div class="form-group">
                {!! Form::label('user_id', trans('validation.attributes.user').'*:') !!}
                {!! Form::select('user_id[]', $users->lists('email', 'id'), Auth::User()->id, ['class' => 'form-control', 'multiple' => 'multiple', 'data-live-search' => true]) !!}
            </div>
            @endif

            <div class="form-group">
                {!!Form::label('name', trans('validation.attributes.name').'*:')!!}
                {!!Form::text('name', null, ['class' => 'form-control'])!!}
            </div>

            <div class="form-group">
                {!!Form::label('imei', trans('validation.attributes.imei_device').'*:')!!}
                {!!Form::text('imei', null, ['class' => 'form-control'])!!}
            </div>

            @if (isAdmin())
                <div class="form-group">
                    {!! Form::label('expiration_date', trans('validation.attributes.expiration_date').':') !!}
                    <div class="input-group">
                        <div class="checkbox input-group-btn">
                            {!! Form::checkbox('enable_expiration_date', 1, false) !!}
                            {!! Form::label(null) !!}
                        </div>
                        {!! Form::text('expiration_date', NULL, ['class' => 'form-control datetimepicker']) !!}
                    </div>
                </div>
            @endif
        </div>
        <div id="device-add-form-icons" class="tab-pane">
            <div class="form-group">
                {!!Form::label('device_icons_type', trans('validation.attributes.icon_type').':')!!}
                {!!Form::select('device_icons_type', $icons_type, null, ['class' => 'form-control'])!!}
            </div>

            {!!Form::hidden('icon_id')!!}
            <?php $i = 1; ?>
            @foreach($device_icons_grouped as $group => $icons)
                <div class="device-icons-{{ $group }} device-icons-group" style="display: none">
                    <div class="form-group">
                        {!!Form::label('icon_idd', trans('validation.attributes.icon_id').':')!!}
                    </div>

                    <div class="icon-list">
                        @foreach($icons as $icon)
                            <div class="checkbox-inline">
                                {!! Form::radio('icon_id', $icon->id, ($i == 1)) !!}
                                <label>
                                    <img src="{!!asset($icon->path)!!}" alt="ICON" style="width: {!!$icon->width!!}px; height: {!!$icon->height!!}px;" />
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
            <div class="device-icons-arrow device-icons-group" style="display: none">
                <div class="form-group">
                    {!!Form::label('icon_moving', trans('front.moving').':')!!}
                    {!!Form::select('icon_moving', $device_icon_colors, 'green', ['class' => 'form-control'])!!}
                </div>
                <div class="form-group">
                    {!!Form::label('icon_stopped', trans('front.stopped').':')!!}
                    {!!Form::select('icon_stopped', $device_icon_colors, 'yellow', ['class' => 'form-control'])!!}
                </div>
                <div class="form-group">
                    {!!Form::label('icon_offline', trans('front.offline').':')!!}
                    {!!Form::select('icon_offline', $device_icon_colors, 'red', ['class' => 'form-control'])!!}
                </div>
                <div class="form-group">
                    {!!Form::label('icon_engine', trans('front.engine_idle').':')!!}
                    {!!Form::select('icon_engine', $device_icon_colors, 'yellow', ['class' => 'form-control'])!!}
                </div>
            </div>
        </div>
        <div id="device-add-form-advanced" class="tab-pane">
            <div class="form-group">
                {!!Form::label('group_id', trans('validation.attributes.group_id').':')!!}
                {!!Form::select('group_id', $device_groups, null, ['class' => 'form-control', 'data-live-search' => true])!!}
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        {!!Form::label('sim_number', trans('validation.attributes.sim_number').':')!!}
                        {!!Form::text('sim_number', null, ['class' => 'form-control'])!!}
                    </div>
                    <div class="form-group">
                        {!!Form::label('vin', trans('validation.attributes.vin').':')!!}
                        {!!Form::text('vin', null, ['class' => 'form-control'])!!}
                    </div>
                    <div class="form-group">
                        {!!Form::label('device_model', trans('validation.attributes.device_model').':')!!}
                        {!!Form::text('device_model', null, ['class' => 'form-control'])!!}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!!Form::label('plate_number', trans('validation.attributes.plate_number').':')!!}
                        {!!Form::text('plate_number', null, ['class' => 'form-control'])!!}
                    </div>
                    <div class="form-group">
                        {!!Form::label('registration_number', trans('validation.attributes.registration_number').':')!!}
                        {!!Form::text('registration_number', null, ['class' => 'form-control'])!!}
                    </div>
                    <div class="form-group">
                        {!!Form::label('object_owner', trans('validation.attributes.object_owner').':')!!}
                        {!!Form::text('object_owner', null, ['class' => 'form-control'])!!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                {!!Form::label('additional_notes', trans('validation.attributes.additional_notes').':')!!}
                {!!Form::text('additional_notes', null, ['class' => 'form-control'])!!}
            </div>
            <div class="form-group">
                <div class="checkbox">
                    {!! Form::checkbox('gprs_templates_only', 1, 0) !!}
                    {!! Form::label('gprs_templates_only', trans('validation.attributes.gprs_templates_only')) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!!Form::label('fuel_measurement_id', trans('validation.attributes.fuel_measurement_type').':')!!}
                        {!!Form::select('fuel_measurement_id', $device_fuel_measurements_select, null, ['class' => 'form-control'])!!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fuel_quantity">
                            <span class="distance_title"></span> {!!trans('front.per_one')!!} <span class="fuel_title"></span>:
                        </label>
                        {!!Form::text('fuel_quantity', null, ['class' => 'form-control', 'placeholder' => '0.00', 'id' => 'fuel_quantity'])!!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fuel_price">
                            {!!trans('front.cost_for')!!} <span class="fuel_title"></span>:
                        </label>
                        {!!Form::text('fuel_price', null, ['class' => 'form-control', 'placeholder' => '0.00', 'id' => 'fuel_price'])!!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!!Form::label('timezone_id', trans('validation.attributes.time_adjustment').':')!!}
                {!!Form::select('timezone_id', $timezones, 0, ['class' => 'form-control'])!!}
                <small>{!!trans('front.by_default_time')!!}</small>
            </div>
        </div>
        <div id="device-add-form-sensors" class="tab-pane">
            <div class="form-group">
                {!! Form::label('sensor_group_id', trans('validation.attributes.sensor_group_id').':') !!}
                {!! Form::select('sensor_group_id', $sensor_groups, null, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div id="device-add-form-accuracy" class="tab-pane">
            <div class="form-group">
                {!!Form::label('min_moving_speed', trans('validation.attributes.min_moving_speed').' ('.trans('front.affects_stops_track',['default'=>6]).'):')!!}
                {!!Form::text('min_moving_speed', '6', ['class' => 'form-control'])!!}
            </div>
            <div class="form-group">
                {!!Form::label('min_fuel_fillings', trans('validation.attributes.min_fuel_fillings').' ('.trans('front.default_value',['default'=>10]).'):')!!}
                {!!Form::text('min_fuel_fillings', '10', ['class' => 'form-control'])!!}
            </div>
            <div class="form-group">
                {!!Form::label('min_fuel_thefts', trans('validation.attributes.min_fuel_thefts').' ('.trans('front.default_value',['default'=>10]).'):')!!}
                {!!Form::text('min_fuel_thefts', '10', ['class' => 'form-control'])!!}
            </div>
        </div>
        <div id="device-add-form-tail" class="tab-pane">
            <div class="form-group">
                {!!Form::label('tail_color', trans('validation.attributes.tail_color').':')!!}
                {!!Form::text('tail_color', '#33CC33', ['class' => 'form-control colorpicker'])!!}
            </div>
            <div class="form-group">
                {!!Form::label('tail_length', trans('validation.attributes.tail_length').' (0-10 '.trans('front.last_points').'):')!!}
                {!!Form::text('tail_length', '5', ['class' => 'form-control'])!!}
            </div>
            {{--<div class="form-group">
                {!!Form::label('snap_to_road', trans('front.snap_to_road').':')!!}
                {!!Form::checkbox('snap_to_road', 1, null)!!}
            </div>--}}
        </div>
    </div>
    {!!Form::close()!!}

    <script>
        $(document).ready(function() {
            var measurements = {!!json_encode($device_fuel_measurements)!!};

            if ( typeof _static_objects_create === "undefined" ) {
                var _static_objects_create = true;

                $(document).on('change', '#devices_create select[name="fuel_measurement_id"]', function () {
                    var val = $(this).val();

                    $.each(measurements, function (index, value) {
                        if (value.id == val) {
                            $('.distance_title').html(value.distance_title);
                            $('.fuel_title').html(value.fuel_title);
                        }
                    });
                });

                $(document).on('change', '#devices_create input[name="enable_expiration_date"]', function () {
                    if ($(this).prop('checked'))
                        $('input[name="expiration_date"]').removeAttr('disabled');
                    else
                        $('input[name="expiration_date"]').attr('disabled', 'disabled');
                });
            }

            $('#devices_create select[name="fuel_measurement_id"]').trigger('change');

            $('#devices_create select[name="device_icons_type"]').trigger('change');

            $('#devices_create input[name="enable_expiration_date"]').trigger('change');
        });
    </script>
@stop