@extends('Frontend.Layouts.modal')

@section('title', trans('global.delete'))

@section('body')
    {!!Form::open(['route' => 'events.destroy', 'method' => 'DELETE'])!!}
        {!!trans('front.do_delete_events')!!}
    {!!Form::close()!!}
@stop

@section('buttons')
    <button type="button" class="btn btn-action update">{!!trans('global.yes')!!}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.no')!!}</button>
@stop