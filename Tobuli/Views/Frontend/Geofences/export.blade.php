@extends('Frontend.Layouts.modal')

@section('title')
    {{ trans('front.export') }}
@stop

@section('body')
    {!! Form::open(['route' => 'geofences.export_create', 'method' => 'POST']) !!}
    {!! Form::hidden('id') !!}
    <div class="form-group">
        {!! Form::label('export_type', trans('validation.attributes.export_type').':') !!}
        {!! Form::select('export_type', $export_types, null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group geofences-export-input">
        {!! Form::label('geofences', trans('validation.attributes.geofences').':') !!}
        {!! Form::select('geofences[]', $geofences, null, ['class' => 'form-control', 'multiple' => 'multiple']) !!}
    </div>

    {!! Form::close() !!}
@stop

@section('buttons')
    <button type="button" class="btn btn-action" onclick="$('#geofences_export form').submit();" data-dismiss="modal">{{ trans('front.export') }}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.cancel') }}</button>
@stop