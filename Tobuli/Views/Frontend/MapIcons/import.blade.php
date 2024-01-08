@extends('Frontend.Layouts.modal')

@section('title')
    {{ trans('front.import') }}
@stop

@section('body')
    {!! Form::open(['route' => 'map_icons.import', 'method' => 'POST']) !!}
    {!! Form::hidden('id') !!}
    <div class="form-group">
        {!!Form::label(null, trans('validation.attributes.map_icon_id').'*:')!!}
        {!!Form::hidden('map_icon_id')!!}
        <div class="scrollbox icon-list">
            @foreach($icons->toArray() as $key=>$value)
                <div class="checkbox-inline">
                    {!!Form::radio('map_icon_id', $value['id'], null, ['data-width' => $value['width'], 'data-height' => $value['height']])!!}
                    <label><img src="{!!asset($value['path'])!!}" alt="ICON"></label>
                </div>
            @endforeach
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('file', trans('validation.attributes.file').'*:') !!}
        {!! Form::file('file', ['class' => 'form-control']) !!}
    </div>

    {!! Form::close() !!}
@stop

@section('buttons')
    <button type="button" class="btn btn-action" onclick="app.mapIcons.import( this );">{{ trans('front.import') }}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.cancel') }}</button>
@stop