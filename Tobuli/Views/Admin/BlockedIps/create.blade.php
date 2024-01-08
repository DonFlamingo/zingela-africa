@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon edit"></i> {{ trans('global.add_new') }}
@stop

@section('body')
    {!! Form::open(['route' => 'admin.blocked_ips.store', 'method' => 'POST']) !!}
    <div class="form-group">
        {!! Form::label('ip', trans('validation.attributes.ip').':') !!}
        {!! Form::text('ip', null, ['class' => 'form-control']) !!}
    </div>
    {!! Form::close() !!}
@stop