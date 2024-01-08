@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon device"></i> {!!trans('global.edit')!!}
@stop

@section('body')
    <ul class="nav nav-tabs nav-default" role="tablist">
        <li class="active"><a href="#device-form-main" role="tab" data-toggle="tab">{!!trans('front.main')!!}</a></li>
        <li><a href="#device-form-icons" role="tab" data-toggle="tab">{!!trans('front.icons')!!}</a></li>
        <li><a href="#device-form-advanced" role="tab" data-toggle="tab">{!!trans('front.advanced')!!}</a></li>
        <li><a href="#device-form-sensors" role="tab" data-toggle="tab">{!!trans('front.sensors')!!}</a></li>
        <li><a href="#device-form-services" role="tab" data-toggle="tab">{!!trans('front.services')!!}</a></li>
        <li><a href="#device-form-accuracy" role="tab" data-toggle="tab">{!!trans('front.accuracy')!!}</a></li>
        <li><a href="#device-form-tail" role="tab" data-toggle="tab">{!!trans('front.tail')!!}</a></li>
    </ul>

    {!!Form::open(['route' => 'devices.update', 'method' => 'PUT'])!!}
    {!!Form::hidden('id', $item->id)!!}
    <div class="tab-content">
        <div id="device-form-main" class="tab-pane active">
            @if (isAdmin())
            <div class="form-group">
                {!! Form::label('user_id', trans('validation.attributes.user').'*:') !!}
                {!! Form::select('user_id[]', $users->lists('email', 'id'), $sel_users, ['class' => 'form-control', 'multiple' => 'multiple', 'data-live-search' => true]) !!}
            </div>
            @endif

            <div class="form-group">
                {!!Form::label('name', trans('validation.attributes.name').'*:')!!}
                {!!Form::text('name', $item->name, ['class' => 'form-control'])!!}
            </div>

            <div class="form-group">
                {!!Form::label('imei', trans('validation.attributes.imei_device').'*:')!!}
                {!!Form::text('imei', $item->imei, ['class' => 'form-control'])!!}
            </div>

            @if (isAdmin())
                <div class="form-group">
                    {!! Form::label('expiration_date', trans('validation.attributes.expiration_date').':') !!}
                    <div class="input-group">
                        <div class="checkbox input-group-btn">
                            {!! Form::checkbox('enable_expiration_date', 1, ($item->expiration_date != '0000-00-00')) !!}
                            {!! Form::label(null) !!}
                        </div>
                        {!! Form::text('expiration_date', $item->expiration_date == '0000-00-00' ? NULL : $item->expiration_date, ['class' => 'form-control datetimepicker']) !!}
                    </div>
                </div>
            @endif
        </div>
        <div id="device-form-icons" class="tab-pane">
            <div class="form-group">
                {!!Form::label('device_icons_type', trans('validation.attributes.icon_type').':')!!}
                {!!Form::select('device_icons_type', $icons_type, $item->icon->type, ['class' => 'form-control'])!!}
            </div>

            {!!Form::hidden('icon_id')!!}
            @foreach($device_icons_grouped as $group => $icons)
                <div class="device-icons-{{ $group }} device-icons-group" style="display: none">
                    <div class="form-group">
                        {!!Form::label('icon_idd', trans('validation.attributes.icon_id').':')!!}
                    </div>
                    <div class="icon-list">
                        @foreach($icons as $icon)
                            <div class="checkbox-inline">
                                {!! Form::radio('icon_id', $icon->id, ($item['icon_id'] == $icon['id'])) !!}
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
                    {!!Form::select('icon_moving', $device_icon_colors, $item->icon_colors['moving'], ['class' => 'form-control'])!!}
                </div>
                <div class="form-group">
                    {!!Form::label('icon_stopped', trans('front.stopped').':')!!}
                    {!!Form::select('icon_stopped', $device_icon_colors, $item->icon_colors['stopped'], ['class' => 'form-control'])!!}
                </div>
                <div class="form-group">
                    {!!Form::label('icon_offline', trans('front.offline').':')!!}
                    {!!Form::select('icon_offline', $device_icon_colors, $item->icon_colors['offline'], ['class' => 'form-control'])!!}
                </div>
                <div class="form-group">
                    {!!Form::label('icon_engine', trans('front.engine_idle').':')!!}
                    {!!Form::select('icon_engine', $device_icon_colors, $item->icon_colors['engine'], ['class' => 'form-control'])!!}
                </div>
            </div>
        </div>
        <div id="device-form-advanced" class="tab-pane">
            <div class="form-group">
                {!!Form::label('group_id', trans('validation.attributes.group_id').':')!!}
                {!!Form::select('group_id', $device_groups, $group_id, ['class' => 'form-control'])!!}
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        {!!Form::label('sim_number', trans('validation.attributes.sim_number').':')!!}
                        {!!Form::text('sim_number', $item->sim_number, ['class' => 'form-control'])!!}
                    </div>
                    <div class="form-group">
                        {!!Form::label('vin', trans('validation.attributes.vin').':')!!}
                        {!!Form::text('vin', $item->vin, ['class' => 'form-control'])!!}
                    </div>
                    <div class="form-group">
                        {!!Form::label('device_model', trans('validation.attributes.device_model').':')!!}
                        {!!Form::text('device_model', $item->device_model, ['class' => 'form-control'])!!}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!!Form::label('plate_number', trans('validation.attributes.plate_number').':')!!}
                        {!!Form::text('plate_number', $item->plate_number, ['class' => 'form-control'])!!}
                    </div>
                    <div class="form-group">
                        {!!Form::label('registration_number', trans('validation.attributes.registration_number').':')!!}
                        {!!Form::text('registration_number', $item->registration_number, ['class' => 'form-control'])!!}
                    </div>
                    <div class="form-group">
                        {!!Form::label('object_owner', trans('validation.attributes.object_owner').':')!!}
                        {!!Form::text('object_owner', $item->object_owner, ['class' => 'form-control'])!!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!!Form::label('additional_notes', trans('validation.attributes.additional_notes').':')!!}
                {!!Form::text('additional_notes', $item->additional_notes, ['class' => 'form-control'])!!}
            </div>
            <div class="form-group">
                <div class="checkbox">
                    {!! Form::checkbox('gprs_templates_only', 1, $item->gprs_templates_only) !!}
                    {!! Form::label('gprs_templates_only', trans('validation.attributes.gprs_templates_only') ) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!!Form::label('fuel_measurement_id', trans('validation.attributes.fuel_measurement_type').':')!!}
                        {!!Form::select('fuel_measurement_id', $device_fuel_measurements_select, $item->fuel_measurement_id, ['class' => 'form-control'])!!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fuel_quantity"><span class="distance_title"></span> {!!trans('front.per_one')!!} <span class="fuel_title"></span>:</label>
                        {!!Form::text('fuel_quantity', $item->fuel_quantity, ['class' => 'form-control', 'placeholder' => '0.00', 'id' => 'fuel_quantity'])!!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fuel_price">{!!trans('front.cost_for')!!} <span class="fuel_title"></span>:</label>
                        {!!Form::text('fuel_price', $item->fuel_price, ['class' => 'form-control', 'placeholder' => '0.00', 'id' => 'fuel_price'])!!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!!Form::label('timezone_id', trans('validation.attributes.time_adjustment').':')!!}
                {!!Form::select('timezone_id', $timezones, !is_null($timezone_id) ? $timezone_id : 0, ['class' => 'form-control'])!!}
                <small>{!!trans('front.by_default_time')!!}</small>
            </div>
        </div>
        <div id="device-form-sensors" class="tab-pane">
            <div class="action-block">
                <a href="javascript:" class="btn btn-action" data-url="{!!route('sensors.create', $item->id)!!}" data-modal="sensors_create" type="button">
                    <i class="icon add"></i> {{ trans('front.add_sensor') }}
                </a>
            </div>
            <div data-table>
                @include('Frontend.Sensors.index')
            </div>
            @if (isAdmin())
                <div class="form-group">
                    {!! Form::label('sensor_group_id', trans('validation.attributes.sensor_group_id').':') !!}
                    {!! Form::select('sensor_group_id', $sensor_groups, null, ['class' => 'form-control']) !!}
                </div>
            @endif
        </div>
        <div id="device-form-services" class="tab-pane">
            <div class="action-block">
                <a href="javascript:" class="btn btn-action" data-url="{!!route('services.create', $item->id)!!}" data-modal="services_create" type="button">
                    <i class="icon add"></i> {{ trans('front.add_service') }}
                </a>
            </div>
            <div data-table>
                @include('Frontend.Services.index')
            </div>
        </div>
        <div id="device-form-accuracy" class="tab-pane">
            <div class="form-group">
                {!!Form::label('engine_hours', trans('validation.attributes.ignition_detection').':')!!}
                {!!Form::select('engine_hours', $engine_hours, $item->engine_hours, ['class' => 'form-control'])!!}
            </div>
            <div class="form-group ignition_detection_engine">
                {!!Form::label('detect_engine', trans('validation.attributes.detect_engine').':')!!}
                {!!Form::select('detect_engine', $detect_engine, $item->detect_engine, ['class' => 'form-control'])!!}
            </div>
            <div class="form-group">
                {!!Form::label('min_moving_speed', trans('validation.attributes.min_moving_speed').' ('.trans('front.affects_stops_track',['default'=>6]).'):')!!}
                {!!Form::text('min_moving_speed', $item->min_moving_speed, ['class' => 'form-control'])!!}
            </div>
            <div class="form-group">
                {!!Form::label('min_fuel_fillings', trans('validation.attributes.min_fuel_fillings').' ('.trans('front.default_value',['default'=>10]).'):')!!}
                {!!Form::text('min_fuel_fillings', $item->min_fuel_fillings, ['class' => 'form-control'])!!}
            </div>
            <div class="form-group">
                {!!Form::label('min_fuel_thefts', trans('validation.attributes.min_fuel_thefts').' ('.trans('front.default_value',['default'=>10]).'):')!!}
                {!!Form::text('min_fuel_thefts', $item->min_fuel_thefts, ['class' => 'form-control'])!!}
            </div>
        </div>
        <div id="device-form-tail" class="tab-pane">
            <div class="form-group">
                {!!Form::label('tail_color', trans('validation.attributes.tail_color').':')!!}
                {!!Form::text('tail_color', $item->tail_color, ['class' => 'form-control colorpicker'])!!}
            </div>
            <div class="form-group">
                {!!Form::label('tail_length', trans('validation.attributes.tail_length').' (0-10 '.trans('front.last_points').'):')!!}
                {!!Form::text('tail_length', $item->tail_length, ['class' => 'form-control'])!!}
            </div>
            {{--<div class="form-group">
                {!!Form::label('snap_to_road', trans('front.snap_to_road').':')!!}
                {!!Form::checkbox('snap_to_road', 1, $item->snap_to_road)!!}
            </div>--}}
        </div>
    </div>
    {!!Form::close()!!}
    <script>
        $(document).ready(function() {

            var measurements = {!!json_encode($device_fuel_measurements)!!};

            if ( typeof _static_objects_edit === "undefined" ) {
                var _static_objects_edit = true;

                $(document).on('change', '#devices_edit select[name="fuel_measurement_id"]', function () {
                    var val = $(this).val();

                    $.each(measurements, function (index, value) {
                        if (value.id == val) {
                            $('.distance_title').html(value.distance_title);
                            $('.fuel_title').html(value.fuel_title);
                        }
                    });
                });

                $(document).on('change', '#devices_edit input[name="enable_expiration_date"]', function() {
                    if ($(this).prop('checked'))
                        $('input[name="expiration_date"]').removeAttr('disabled');
                    else
                        $('input[name="expiration_date"]').attr('disabled', 'disabled');
                });
            }

            $('#devices_edit select[name="engine_hours"]').trigger('change');

            $('#devices_edit input[name="enable_expiration_date"]').trigger('change');

            $('#devices_edit select[name="device_icons_type"]').trigger('change');

            $('#devices_edit select[name="fuel_measurement_id"]').trigger('change');
        });

        tables.set_config('device-form-services', {
            url: '{!!route('services.index', $item->id)!!}'
        });
        function services_create_modal_callback() {
            tables.get('device-form-services');
        }
        function services_edit_modal_callback() {
            tables.get('device-form-services');
        }
        function services_destroy_modal_callback() {
            tables.get('device-form-services');
        }

        tables.set_config('device-form-sensors', {
            url: '{!!route('sensors.index', $item->id)!!}'
        });
        function sensors_create_modal_callback() {
            tables.get('device-form-sensors');
        }
        function sensors_edit_modal_callback() {
            tables.get('device-form-sensors');
        }
        function sensors_destroy_modal_callback() {
            tables.get('device-form-sensors');
        }
    </script>
@stop

@section('buttons')
    <button type="button" class="btn btn-action update">{!!trans('global.save')!!}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.cancel')!!}</button>
    @if (Auth::User()->perm('devices', 'remove'))
    <button class="btn btn-danger" data-target="#deleteObject" data-toggle="modal" onclick="app.devices.delete({{ $item->id }});">{!!trans('global.delete')!!}</button>
    @endif
@stop