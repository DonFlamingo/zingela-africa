@extends('Frontend.Layouts.modal')

@section('title', trans('front.send_test_sms'))

@section('body')
    {!! Form::open(['route' => 'sms_gateway.send_test_sms', 'method' => 'POST']) !!}
    {!! Form::hidden('request_method') !!}
    {!! Form::hidden('authentication') !!}
    {!! Form::hidden('username') !!}
    {!! Form::hidden('password') !!}
    {!! Form::hidden('sms_gateway_url') !!}
    {!! Form::hidden('encoding') !!}
    {!! Form::hidden('auth_id') !!}
    {!! Form::hidden('auth_token') !!}
    {!! Form::hidden('senders_phone') !!}
    <div class="form-group">
        {!! Form::label('mobile_phone', trans('validation.attributes.mobile_phone').':') !!}
        {!! Form::text('mobile_phone', null, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('message', trans('validation.attributes.message').':') !!}
        {!! Form::textarea('message', null, ['class' => 'form-control', 'rows' => 2]) !!}
    </div>
    {!! Form::close() !!}
    <script>
        $(document).ready(function() {
            $('#send_test_sms input[name="request_method"]').val($('#setup-form-sms-gateway select[name="request_method"]').val());
            $('#send_test_sms input[name="authentication"]').val($('#setup-form-sms-gateway select[name="authentication"]').val());
            $('#send_test_sms input[name="username"]').val($('#setup-form-sms-gateway input[name="username"]').val());
            $('#send_test_sms input[name="password"]').val($('#setup-form-sms-gateway input[name="password"]').val());
            $('#send_test_sms input[name="sms_gateway_url"]').val($('#setup-form-sms-gateway textarea[name="sms_gateway_url"]').val());
            $('#send_test_sms input[name="encoding"]').val($('#setup-form-sms-gateway select[name="encoding"]').val());

            $('#send_test_sms input[name="auth_id"]').val($('#setup-form-sms-gateway input[name="auth_id"]').val());
            $('#send_test_sms input[name="auth_token"]').val($('#setup-form-sms-gateway input[name="auth_token"]').val());
            $('#send_test_sms input[name="senders_phone"]').val($('#setup-form-sms-gateway input[name="senders_phone"]').val());
        });
    </script>
@stop

@section('buttons')
    <button type="button" class="btn btn-action update">{{ trans('front.send') }}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.cancel') }}</button>
@stop