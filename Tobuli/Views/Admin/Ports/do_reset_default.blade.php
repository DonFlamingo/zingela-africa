@extends('Admin.Layouts.modal')

@section('title')
    {{ trans('admin.reset_default') }}
@stop

@section('body')
    {!! Form::open(array('url' => route('admin.ports.reset_default'), 'method' => 'POST')) !!}
        {{ trans('admin.do_reset_default') }}
    {!! Form::close() !!}
@stop

@section('footer')
    <button type="button" class="btn btn-action update">{{ trans('global.yes') }}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.cancel') }}</button>
@stop