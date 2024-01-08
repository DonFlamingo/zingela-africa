@extends('Frontend.Layouts.modal')

@section('modal_class', 'modal-sm')

@section('title')
    {{ trans('front.login_as') }}
@stop

@section('body')
    {{ strtr(trans('front.do_login_as'), [':email' => $item->email]) }}
@stop

@section('buttons')
    <a href="{{ route('admin.clients.login_as_agree', $item->id) }}" class="btn btn-action">{{ trans('global.yes') }}</a>
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.no') }}</button>
@stop