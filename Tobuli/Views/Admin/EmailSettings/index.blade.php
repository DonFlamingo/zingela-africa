@extends('Admin.Layouts.default')

@section('content')
    <div class="panel panel-default">

        <div class="panel-heading">
            <div class="panel-title"><i class="icon email"></i> {{ trans('validation.attributes.email') }}</div>
        </div>

        <div class="panel-body">
            @if (Session::has('errors'))
                <div class="alert alert-danger">
                    <ul>
                        @foreach (Session::get('errors')->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {!! Form::open(array('route' => 'admin.email_settings.save', 'method' => 'POST', 'class' => 'form form-horizontal', 'id' => 'email_settings_form')) !!}
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('from_name', trans('validation.attributes.from_name'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                        {!! Form::text('from_name', isset($settings['from_name']) ? $settings['from_name'] : null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('noreply_email', trans('validation.attributes.noreply_email'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                        {!! Form::text('noreply_email', isset($settings['noreply_email']) ? $settings['noreply_email'] : null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('signature', trans('validation.attributes.signature'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                        {!! Form::text('signature', isset($settings['signature']) ? $settings['signature'] : null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('provider', trans('validation.attributes.provider'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                        {!! Form::select('provider', $providers, isset($settings['provider']) ? $settings['provider'] : null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group provider-sendgrid provider-postmark provider-mailgun">
                        {!! Form::label('api_key', trans('validation.attributes.api_key'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                        {!! Form::text('api_key', isset($settings['api_key']) ? $settings['api_key'] : null, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group provider-mailgun">
                        {!! Form::label('domain', trans('validation.attributes.domain'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('domain', isset($settings['domain']) ? $settings['domain'] : null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group provider-smtp">
                        {!! Form::label('use_smtp_server', trans('validation.attributes.use_smtp_server'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                        {!! Form::select('use_smtp_server', ['0' => trans('global.no'), '1' => trans('global.yes')], isset($settings['use_smtp_server']) ? $settings['use_smtp_server'] : null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group  provider-smtp">
                        {!! Form::label('smtp_server_host', trans('validation.attributes.smtp_server_host'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                        {!! Form::text('smtp_server_host', isset($settings['smtp_server_host']) ? $settings['smtp_server_host'] : null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group provider-smtp">
                        {!! Form::label('smtp_server_port', trans('validation.attributes.smtp_server_port'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                        {!! Form::text('smtp_server_port', isset($settings['smtp_server_port']) ? $settings['smtp_server_port'] : null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group provider-smtp">
                        {!! Form::label('smtp_security', trans('validation.attributes.smtp_security'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                        {!! Form::select('smtp_security', ['0' => trans('global.no'), 'tls' => 'TLS', 'ssl' => 'SSL'], isset($settings['smtp_security']) ? $settings['smtp_security'] : null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group provider-smtp">
                        {!! Form::label('smtp_username', trans('validation.attributes.smtp_username'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                        {!! Form::text('smtp_username', isset($settings['smtp_username']) ? $settings['smtp_username'] : null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group provider-smtp">
                        {!! Form::label('smtp_password', trans('validation.attributes.smtp_password'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                        {!! Form::password('smtp_password', ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>

        <div class="panel-footer">
            <button type="submit" class="btn btn-action" form="email_settings_form">{{ trans('global.save') }}</button>
            <button type="button" class="btn btn-default" data-modal="test_email" data-url="{{ route('admin.email_settings.test_email') }}">{{ trans('front.test_email') }}</button>
        </div>
    </div>
@stop

@section('javascript')
    <script>
        $(document).ready(function() {
            $('select[name="use_smtp_server"]').on('change', function() {
                var val = $(this).val();
                if (val == 0)
                    $('input[name^="smtp_"], select[name^="smtp_"]').attr('disabled', 'disabled');
                else
                    $('input[name^="smtp_"], select[name^="smtp_"]').removeAttr('disabled', 'disabled');
            });
            $('select[name="use_smtp_server"]').trigger('change');


            $('select[name="provider"]').on('change', function() {
                $('div[class*="provider-"]').hide();
                $('.provider-' + $(this).val()).show();
            });
            $('select[name="provider"]').trigger('change');
        });
    </script>
@stop