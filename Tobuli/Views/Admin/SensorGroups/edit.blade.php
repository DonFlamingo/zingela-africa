@extends('Admin.Layouts.modal')

@section('title')
    {{ trans('global.edit') }}
@stop

@section('body')
    {!! Form::open(array('route' => 'admin.sensor_groups.update', 'method' => 'PUT')) !!}
    {!! Form::hidden('id', $item->id) !!}
        <div class="form-group">
            {!! Form::label('title', trans('validation.attributes.title').':') !!}
            {!! Form::text('title', $item->title, ['class' => 'form-control']) !!}
        </div>
    {!! Form::close() !!}
@stop