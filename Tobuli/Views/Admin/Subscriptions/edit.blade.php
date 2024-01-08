@extends('Admin.Layouts.modal')

@section('title')
    {{ trans('global.edit') }}
@stop

@section('body')
    {!! Form::open(array('route' => 'admin.subscriptions.update', 'method' => 'PUT')) !!}

    {!! Form::hidden('id', $item->id) !!}
    <div class="form-group">
        {!! Form::label('name', trans('validation.attributes.name').':') !!}
        {!! Form::text('name', $item->name, ['class' => 'form-control']) !!}
        {!! error_for('name', $errors) !!}
    </div>

    <div class="form-group">
        {!! Form::label('period_name', trans('validation.attributes.period_name').':') !!}
        {!! Form::text('period_name', $item->period_name, ['class' => 'form-control']) !!}
        {!! error_for('period_name', $errors) !!}
    </div>

    <div class="form-group row">
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            {!! Form::label('devices_limit', trans('validation.attributes.devices_limit').':') !!}
            {!! Form::text('devices_limit', $item->devices_limit, ['class' => 'form-control']) !!}
            {!! error_for('devices_limit', $errors) !!}
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            {!! Form::label('days', trans('validation.attributes.days').':') !!}
            {!! Form::text('days', $item->days, ['class' => 'form-control']) !!}
            {!! error_for('days', $errors) !!}
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            {!! Form::label('price', trans('validation.attributes.price').':') !!}
            {!! Form::text('price', $item->price, ['class' => 'form-control']) !!}
            {!! error_for('price', $errors) !!}
        </div>
    </div>
    {!! Form::close() !!}
@stop