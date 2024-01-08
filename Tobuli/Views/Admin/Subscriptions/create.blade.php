@extends('Admin.Layouts.modal')

@section('title')
    {{ trans('global.add_new') }}
@stop

@section('height', 300)

@section('body')
    {!! Form::open(array('route' => 'admin.subscriptions.store', 'method' => 'POST')) !!}
    {!! Form::hidden('id') !!}

    <div class="form-group">
        {!! Form::label('name', trans('validation.attributes.name').':') !!}
        {!! Form::text('name', null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('period_name', trans('validation.attributes.period_name').':') !!}
        {!! Form::text('period_name', null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group row">
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            {!! Form::label('devices_limit', trans('validation.attributes.devices_limit').':') !!}
            {!! Form::text('devices_limit', null, ['class' => 'form-control']) !!}
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            {!! Form::label('days', trans('validation.attributes.days').':') !!}
            {!! Form::text('days', null, ['class' => 'form-control']) !!}
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            {!! Form::label('price', trans('validation.attributes.price').':') !!}
            {!! Form::text('price', null, ['class' => 'form-control']) !!}
        </div>
    </div>
    {!! Form::close() !!}
@stop