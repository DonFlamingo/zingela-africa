@extends('Frontend.Layouts.modal')

@section('title', trans('global.delete'))

@section('body')
    {!!trans('front.do_delete')!!}
@stop

@section('buttons')
    <button type="button" class="btn btn-action delete_history_messages" data-dismiss="modal">{!!trans('global.yes')!!}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.no')!!}</button>
@stop