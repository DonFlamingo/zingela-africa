@extends('Frontend.Layouts.modal')

@section('title')
    {!!trans('front.confirm_email')!!}
@stop

@section('height', 150)

@section('body')
    {!!Form::open(['route' => 'email_confirmation.update', 'method' => 'PUT', ''])!!}
        {!!Form::hidden('id', $item->id)!!}
        <!-- Name Form Input -->
        <div class="form-group">
            {!!Form::label('code', trans('validation.attributes.code').'*:')!!}
            {!!Form::text('code', null, ['class' => 'form-control'])!!}
        </div>
        <div class="form-group">
            <a href="javascript:" class="btn btn-action" data-url="{!!route('email_confirmation.resend_code')!!}" data-modal="email_resend_code">{!!trans('front.resend_code')!!}</a>
        </div>
    {!!Form::close()!!}
@stop