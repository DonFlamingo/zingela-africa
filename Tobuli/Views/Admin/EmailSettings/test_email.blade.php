@extends('Frontend.Layouts.modal')

@section('title')
    {{ trans('front.test_email') }}
@stop

@section('body')
    {!! Form::open(array('route' => 'admin.email_settings.test_email_send', 'method' => 'POST')) !!}
        {!! Form::hidden('id') !!}

        <div class="form-group">
            {!! Form::label('email', trans('validation.attributes.email').':') !!}
            {!! Form::text('email', Auth::User()->email, ['class' => 'form-control']) !!}
        </div>

    {!! Form::close() !!}
@stop

@section('buttons')
    <button type="button" class="btn btn-action update">{{ trans('front.send') }}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.cancel') }}</button>
@stop