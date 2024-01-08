@extends('Frontend.Layouts.modal')

@section('title')
    {!!trans('front.resend_code')!!}
@stop

@section('height', 100)

@section('body')
    {!!Form::open(['route' => 'email_confirmation.resend_code_submit', 'method' => 'POST', ''])!!}
        {!!Form::hidden('id')!!}
        <div class="form-group">
            {!!trans('front.do_really_resend')!!}
        </div>
    {!!Form::close()!!}
@stop

@section('buttons')
    <button type="button" class="btn btn-action update">{!!trans('global.yes')!!}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.cancel')!!}</button>
@stop