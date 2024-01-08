@extends('Frontend.Layouts.modal')

@section('title')
    {!!trans('front.settings')!!}
@stop

@section('body')

    {!!Form::open(['route' => 'objects.listview_settings.update', 'method' => 'POST'])!!}

    @include('Frontend.ObjectsList.form')

    {!!Form::close()!!}
@stop

@section('buttons')
    <button type="button" class="btn btn-action update">{!!trans('global.save')!!}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.close')!!}</button>
@stop