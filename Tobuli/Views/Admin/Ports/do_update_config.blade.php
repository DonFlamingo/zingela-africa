@extends('Admin.Layouts.modal')

@section('title')
    {{ trans('admin.update_config_and') }}
@stop

@section('body')
    {!! Form::open(array('url' => route('admin.ports.update_config'), 'method' => 'POST')) !!}
        {{ trans('admin.do_update_config') }}
    {!! Form::close() !!}
@stop

@section('footer')
    <button type="button" class="btn btn-action update">{{ trans('global.yes') }}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.cancel') }}</button>
@stop