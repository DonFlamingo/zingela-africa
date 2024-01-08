@extends('Frontend.Layouts.modal')

@section('modal_class', 'modal-lg')

@section('title', trans('global.edit'))

@section('body')
    {!! Form::open(['route' => $route, 'method' => 'PUT']) !!}
        {!! Form::hidden('id', $item->id) !!}
        {!! Form::hidden('device_id', $item->device_id) !!}
    <div class="row">
        <div class="col-md-6 sen-data-fields">
            <div class="form-group">
                {!! Form::label('sensor_name', trans('validation.attributes.sensor_name').':') !!}
                {!! Form::text('sensor_name', $item->name, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('sensor_type', trans('validation.attributes.sensor_template').':') !!}
                {!! Form::select('sensor_type', $sensors, $item->type, ['class' => 'form-control', 'id' => 'sensor_type']) !!}
            </div>

            <div class="form-group tag_name">
                @if (is_array($parameters))
                {!! Form::label('tag_name', trans('validation.attributes.tag_name').':') !!}
                {!! Form::select('tag_name', $parameters, $item->tag_name, ['class' => 'form-control']) !!}
                @else
                    {!! Form::label('tag_name', trans('validation.attributes.tag_name').':') !!}
                    {!! Form::text('tag_name', $item->tag_name, ['class' => 'form-control']) !!}
                @endif
            </div>

            <div class="form-group unit_of_measurement">
                {!! Form::label('unit_of_measurement', trans('validation.attributes.unit_of_measurement').':') !!}
                {!! Form::text('unit_of_measurement', $item->unit_of_measurement, ['class' => 'form-control']) !!}
            </div>

            <div class="sensors_form_inputs acc harsh_acceleration harsh_breaking ignition door engine">
                <div class="form-group">
                    <div class="checkbox">
                        {!! Form::checkbox('setflag', 1, $item->setflag) !!}
                        {!! Form::label('setflag', trans('front.setflag')) !!}
                    </div>
                </div>
            </div>

            <div class="sensors_form_inputs fuel_tank fuel_tank_calibration">
                <div class="form-group">
                    {!! Form::label('fuel_tank_name', trans('validation.attributes.fuel_tank_name').':') !!}
                    {!! Form::text('fuel_tank_name', $item->fuel_tank_name, ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="sensors_form_inputs fuel_tank">
                <div class="form-group">
                    {!! Form::label('parameters', trans('validation.attributes.parameters').':') !!}
                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            {!! Form::text('full_tank', $item->full_tank, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.full_tank')]) !!}
                        </div>
                        <div class="col-md-6 col-sm-6">
                            {!! Form::text('full_tank_value', $item->full_tank_value, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.tag_value')]) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="sensors_form_inputs odometer">
                <div class="form-group">
                    {!! Form::label('odometer_value_by', trans('validation.attributes.odometer_value_by').':') !!}
                    {!! Form::select('odometer_value_by', ['connected_odometer' => trans('front.connected_odometer'), 'virtual_odometer' => trans('front.virtual_odometer')], $item->odometer_value_by, ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="sensors_form_inputs harsh_acceleration harsh_breaking notsetflag">
                <div class="form-group">
                    {!! Form::label('parameter_value', trans('validation.attributes.parameter_value').':') !!}
                    {!! Form::text('parameter_value', $item->on_value, ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="sensors_form_inputs harsh_acceleration harsh_breaking setflag">
                <div class="form-group">
                    {!! Form::label('parameter_value', trans('validation.attributes.parameter_value').':') !!}
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('value_setflag_1', $item->value_setflag_1, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_1')]) !!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('value_setflag_2', $item->value_setflag_2, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_2')]) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="sensors_form_inputs odometer_value_by virtual_odometer">
                <div class="form-group">
                    {!! Form::label('odometer_value', trans('validation.attributes.odometer_value').':') !!}
                    <div class="row">
                        <div class="col-xs-6">
                            {!! Form::text('odometer_value', $item->odometer_value, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-xs-6">
                            {!! Form::select('odometer_value_unit', ['km' => trans('front.km'), 'mi' => trans('front.mi')], $item->odometer_value_unit, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="sensors_form_inputs battery">
                <div class="form-group">
                    {!! Form::label('shown_value_by', trans('validation.attributes.shown_value_by').':') !!}
                    {!! Form::select('shown_value_by', ['tag_value' => trans('validation.attributes.tag_value'), 'min_max_values' => trans('front.min_max_values'), 'formula' => trans('validation.attributes.formula')], $item->shown_value_by, ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="sensors_form_inputs battery_value_by formula temperature odometer_value_by connected_odometer tachometer">
                <div class="form-group">
                    {!! Form::label('formula', trans('validation.attributes.formula').':') !!}
                    {!! Form::text('formula', (empty($item->formula) ? '[value]' : $item->formula), ['class' => 'form-control']) !!}
                    <span class="help-block">{{ trans('front.formula_example') }}</span>
                </div>
                <div class="alert alert-info" style="font-size: 12px;">
                    {{ trans('front.setflag_formula_info') }}
                </div>
            </div>

            <div class="sensors_form_inputs gsm battery_value_by min_max_values">
                <div class="form-group">
                    {!! Form::label('min_value', trans('validation.attributes.min_value').':') !!}
                    {!! Form::text('min_value', $item->min_value, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('max_value', trans('validation.attributes.max_value').':') !!}
                    {!! Form::text('max_value', $item->max_value, ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="sensors_form_inputs acc notsetflag">
                <div class="form-group">
                    {!! Form::label('on_value', trans('validation.attributes.on_value').':') !!}
                    {!! Form::text('on_value', $item->on_value, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('off_value', trans('validation.attributes.off_value').':') !!}
                    {!! Form::text('off_value', $item->off_value, ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="sensors_form_inputs acc setflag">
                <div class="form-group">
                    {!! Form::label('on_value', trans('validation.attributes.on_value').':') !!}
                    <div class="row">
                        <div class="col-md-4">
                            {!! Form::text('on_setflag_1', $item->on_setflag_1, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_1')]) !!}
                        </div>
                        <div class="col-md-4">
                            {!! Form::text('on_setflag_2', $item->on_setflag_2, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_2')]) !!}
                        </div>
                        <div class="col-md-4">
                            {!! Form::text('on_setflag_3', $item->on_setflag_3, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_3')]) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('off_value', trans('validation.attributes.off_value').':') !!}
                    <div class="row">
                        <div class="col-md-4">
                            {!! Form::text('off_setflag_1', $item->off_setflag_1, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_1')]) !!}
                        </div>
                        <div class="col-md-4">
                            {!! Form::text('off_setflag_2', $item->off_setflag_2, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_2')]) !!}
                        </div>
                        <div class="col-md-4">
                            {!! Form::text('off_setflag_3', $item->off_setflag_3, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_3')]) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="sensors_form_inputs ignition door engine notsetflag drive_business drive_private">
                <div class="form-group">
                    {!! Form::label('on_type', trans('validation.attributes.on_value').':') !!}
                    <div class="row">
                        <div class="col-md-4 col-xs-4">
                            {!! Form::select('on_type', ['1' => trans('front.event_type_1'), '2' => trans('front.event_type_2'), '3' => trans('front.event_type_3')], $item->on_type, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-8 col-xs-4">
                            {!! Form::text('on_tag_value', $item->on_tag_value, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.tag_value')]) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('off_type', trans('validation.attributes.off_value').':') !!}
                    <div class="row">
                        <div class="col-md-4 col-xs-4">
                            {!! Form::select('off_type', ['1' => trans('front.event_type_1'), '2' => trans('front.event_type_2'), '3' => trans('front.event_type_3')], $item->off_type, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-8 col-xs-4">
                            {!! Form::text('off_tag_value', $item->off_tag_value, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.tag_value')]) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="sensors_form_inputs ignition door engine setflag">
                <div class="form-group">
                    {!! Form::label('on_value', trans('validation.attributes.on_value').':') !!}
                    <div class="row">
                        <div class="col-md-3">
                            {!! Form::select('on_type_setflag', ['1' => trans('front.event_type_1'), '2' => trans('front.event_type_2'), '3' => trans('front.event_type_3')], null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::text('on_tag_setflag_1', $item->on_tag_setflag_1, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_1')]) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::text('on_tag_setflag_2', $item->on_tag_setflag_2, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_2')]) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::text('on_tag_setflag_3', $item->on_tag_setflag_3, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_3')]) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('off_value', trans('validation.attributes.off_value').':') !!}
                    <div class="row">
                        <div class="col-md-3">
                            {!! Form::select('off_type_setflag', ['1' => trans('front.event_type_1'), '2' => trans('front.event_type_2'), '3' => trans('front.event_type_3')], null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::text('off_tag_setflag_1', $item->off_tag_setflag_1, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_1')]) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::text('off_tag_setflag_2', $item->off_tag_setflag_2, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_2')]) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::text('off_tag_setflag_3', $item->off_tag_setflag_3, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_3')]) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="sensors_form_input">
                <div class="form-group">
                    <div class="checkbox">
                        {!! Form::checkbox('add_to_history', 1, $item->add_to_history) !!}
                        {!! Form::label('add_to_history', trans('front.add_to_history')) !!}
                    </div>
                </div>
            </div>
            {{--<div class="alert alert-info" style="font-size: 12px;">
                {{ trans('front.setflag_info') }}
            </div>--}}
        </div>
        <div class="col-md-6 sen-cal-fields">
            <label class="report_label">{{ trans('front.calibration') }}</label>
            {!! Form::hidden('calibrations_fake') !!}
            <div style="display: block; height: 400px;overflow-y: scroll; border: 1px solid #dddddd; margin-bottom: 20px;">
                <table class="table">
                    <thead>
                        <th style="font-weight: normal">{{ trans('validation.attributes.tag_value') }}</th>
                        <th style="font-weight: normal">{{ trans('front.liters_gallons') }}</th>
                        <th></th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-xs-5">
                        {!! Form::label('x',trans('validation.attributes.tag_value')) !!}
                        {!! Form::text('x', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                    </div>
                    <div class="col-xs-5">
                        {!! Form::label('y',trans('front.liters_gallons')) !!}
                        {!! Form::text('y', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                    </div>
                    <div class="col-xs-2">
                        {!! Form::label(null,'.') !!}
                        <a href="javascript:" class="btn btn-action btn-block add_calibration" type="button"><i class="icon add" title="{{ trans('global.add') }}"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span class="calibrations" style="display: none;">{{ json_encode($item->calibrations) }}</span>
    {!! Form::close() !!}
    <script>
        $(document).ready(function() {
            app.sensors.inputs($('#sensors_edit'));
        });
    </script>
@stop
