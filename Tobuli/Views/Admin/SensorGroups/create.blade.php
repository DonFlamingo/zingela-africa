@extends('Admin.Layouts.modal')

@section('title')
    {{ trans('global.add_new') }}
@stop

@section('body')
    {!! Form::open(array('route' => 'admin.sensor_groups.store', 'method' => 'POST')) !!}
    {!! Form::hidden('id') !!}
        <div class="form-group">
            {!! Form::label('title', trans('validation.attributes.title').':') !!}
            {!! Form::text('title', null, ['class' => 'form-control']) !!}
        </div>
    {!! Form::close() !!}
@stop