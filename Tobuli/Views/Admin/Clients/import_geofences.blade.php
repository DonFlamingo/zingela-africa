@extends('Admin.Layouts.modal')

@section('title')
    <i class="icon geofence-import"></i> {{ trans('front.import') }}
@stop

@section('body')
    {!! Form::open(['route' => 'admin.clients.import_geofences_set', 'method' => 'POST']) !!}
        {!! Form::hidden('id') !!}
        <div class="form-group">
            {!! Form::label('user_id', trans('validation.attributes.user').'*:') !!}
            {!! Form::select('user_id[]', $users->lists('email', 'id'), null, ['class' => 'form-control', 'multiple' => 'multiple', 'data-live-search' => true]) !!}
        </div>

        <div class="form-group">
            {!! Form::label('file', trans('validation.attributes.file').'*:') !!}
            {!! Form::file('file', ['class' => 'form-control']) !!}
        </div>
    {!! Form::close() !!}
@stop

@section('footer')
    <button type="button" class="btn btn-action update_with_files">{{ trans('global.save') }}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.cancel') }}</button>
@stop