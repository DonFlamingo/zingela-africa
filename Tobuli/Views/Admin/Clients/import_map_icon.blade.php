@extends('Admin.Layouts.modal')

@section('title')
    {{ trans('front.import') }}
@stop

@section('body')
    {!! Form::open(['route' => 'admin.clients.import_map_icon_set', 'method' => 'POST']) !!}
    {!! Form::hidden('id') !!}
    <div class="form-group">
        {!! Form::label('user_id', trans('validation.attributes.user').'*:') !!}
        {!! Form::select('user_id[]', $users->lists('email', 'id'), null, ['class' => 'form-control', 'multiple' => 'multiple', 'data-live-search' => true]) !!}
    </div>

    <div class="form-group">
        {!!Form::label('map_icon_idd', trans('validation.attributes.map_icon_id').'*:')!!}
        {!!Form::hidden('map_icon_id')!!}
        <div class="scrollbox icon-list">
            @foreach($icons->toArray() as $key=>$value)
            <div class="checkbox-inline">
                {!! Form::radio('map_icon_id', $value['id'], null, ['data-width' => $value['width'], 'data-height' => $value['height']]) !!}
                <label> <img src="{!!asset($value['path'])!!}" alt="ICON"></label>
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

@section('footer')
    <button type="button" class="btn btn-action update_with_files">{{ trans('global.save') }}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.cancel') }}</button>
@stop