@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon send-command"></i> {!!trans('front.send_command')!!}
@stop

@section('body')
    <ul class="nav nav-tabs nav-default" role="tablist">
        <li class="active"><a href="#command-form-gprs" role="tab" data-toggle="tab">{!!trans('front.gprs')!!}</a></li>
        <li><a href="#command-form-sms" role="tab" data-toggle="tab">{!!trans('front.sms')!!}</a></li>
    </ul>

    {!!Form::open(['route' => 'send_command.store', 'method' => 'POST'])!!}
    {!!Form::hidden('id')!!}
    <div class="alert alert-success" role="alert" style="display: none;">{!!trans('front.command_sent')!!}</div>
    <div class="alert alert-danger main-alert" role="alert" style="display: none;"></div>

    <div class="tab-content">

        <div id="command-form-gprs" class="tab-pane active" data-url="{!!route('send_command.gprs')!!}">
            @if (!Auth::User()->perm('send_command', 'view'))
                <div class="alert alert-danger" role="alert">{{ trans('front.dont_have_permission') }}</div>
            @else
            <div class="form-group">
                {!!Form::label('device_id', trans('validation.attributes.device_id').':')!!}
                {!!Form::select('device_id', $devices_gprs, $device_id, ['class' => 'form-control', 'data-live-search' => true])!!}
            </div>
            <div class="form-group send-command-type">
                {!!Form::label('type', trans('validation.attributes.type').':')!!}
                {!!Form::select('type', $commands, null, ['class' => 'form-control'])!!}
            </div>
            <div class="send-command-periodic row">
                <div class="col-xs-6">
                    <div class="form-group">
                        {!!Form::label('type', trans('validation.attributes.frequency').':')!!}
                        <div class="input-group">
                            <span class="input-group-btn">
                                <button class="btn btn-side btn-minuse" type="button"><i class="icon minus"></i></button>
                            </span>

                            <input type="text" name="frequency" class="form-control text-center" maxlength="3" value="1">

                            <span class="input-group-btn">
                                <button class="btn btn-main btn-pluss" type="button"><i class="icon plus"></i></button>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-xs-6">
                    <div class="form-group">
                        {!!Form::label('unit', trans('validation.attributes.unit').':')!!}
                        {!!Form::select('unit', $units, 'minute', ['class' => 'form-control'])!!}
                    </div>
                </div>
            </div>

            <div class="form-group send-command-parameter">
                {!! Form::label('parameter', trans('validation.attributes.parameter').':') !!}
                {!! Form::text('parameter', null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group send-command-custom">
                {!! Form::label('gprs_template_id', trans('validation.attributes.gprs_template_id').':') !!}
                {!! Form::select('gprs_template_id', $gprs_templates, null, ['class' => 'form-control gprs_template_id', 'data-url' => route('user_gprs_templates.get_message')]) !!}
                <small>{{ trans('front.add_gprs_template_info') }}</small>
            </div>

            <div class="form-group send-command-templates-only">
                {!! Form::label('gprs_template_only_id', trans('validation.attributes.gprs_template_id').':') !!}
                {!! Form::select('gprs_template_only_id', $gprs_templates_only, null, ['class' => 'form-control gprs_template_id', 'data-url' => route('user_gprs_templates.get_message')]) !!}
            </div>

            <div class="form-group send-command-sms">
                {!! Form::label('sim_number', trans('validation.attributes.sim_number').'*:') !!}
                {!! Form::text('sim_number', null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group send-command-password">
                {!! Form::label('password', trans('validation.attributes.password').'*:') !!}
                {!! Form::text('password', '000000', ['class' => 'form-control']) !!}
            </div>

            <div class="form-group send-command-custom send-command-sms">
                {!! Form::label('message', trans('validation.attributes.message').'*:') !!}
                {!! Form::textarea('message', null, ['class' => 'form-control', 'rows' => 3]) !!}
                <small>
                    {!! trans('front.raw_command_supports') !!}
                    <br><br>
                    {!! trans('front.gprs_template_variables') !!}
                </small>
            </div>
            @endif
        </div>
        <div id="command-form-sms" class="tab-pane" data-url="{!!route('send_command.store')!!}">
            @if (!Auth::User()->sms_gateway)
                <div class="alert alert-danger" role="alert">{!!trans('front.sms_gateway_disabled')!!}</div>
            @else
                @if (empty($devices_sms))
                    <div class="alert alert-danger" role="alert">{!!trans('front.no_devices_with_sim_number')!!}</div>
                @endif

                <div class="form-group">
                    {!!Form::label('devices', trans('validation.attributes.devices').'*:')!!}
                    @if (empty($devices_sms))
                        {!!Form::text('devices[]', null, ['class' => 'form-control', 'disabled' => 'disabled'])!!}
                    @else
                        {!!Form::select('devices[]', $devices_sms, null, ['class' => 'form-control', 'multiple' => 'multiple', 'data-live-search' => true])!!}
                    @endif
                    {!!Form::hidden('devices_fake')!!}
                    <small>{!!trans('front.add_sim_number_info')!!}</small>
                </div>

                <div class="form-group">
                    {!!Form::label('sms_template_id', trans('validation.attributes.sms_template_id').':')!!}
                    {!!Form::select('sms_template_id', $sms_templates, null, ['class' => 'form-control', 'data-url' => route('user_sms_templates.get_message')])!!}
                    <small>{!!trans('front.add_sms_template_info')!!}</small>
                </div>

                <div class="form-group">
                    {!!Form::label('message', trans('validation.attributes.message').'*:')!!}
                    {!!Form::textarea('message_sms', null, ['class' => 'form-control', 'rows' => 3])!!}
                    {!!Form::hidden('message_fake')!!}
                </div>

                <div class="send_command_result" style="display: none;">
                    <div>
                        <p>{!!trans('front.get_request')!!}:</p>
                        <p class="get_request result_parse"></p>
                    </div>
                    <div>
                        <p>{!!trans('front.response')!!}:</p>
                        <p class="get_result result_parse"></p>
                    </div>
                </div>
            @endif
        </div>
    </div>
    {!!Form::close()!!}
    <script>
        var send_commands_devices_protocols = {!! json_encode($devices_protocols) !!};
        var send_commands_all = {!! json_encode($commands_all) !!};
        var gprs_templates_devices = {!! json_encode($gprs_templates_devices) !!};

        $(document).ready(function() {
            $('#send_command select[name="type"]').trigger('change');
            $('#send_command select[name="device_id"]').trigger('change');
        });

        if ( typeof _static_send_command === "undefined" ) {
            var _static_send_command = true;

            $(document).on('change', '#send_command select[name="type"]', function() {
                $('.send-command-custom, .send-command-periodic, .send-command-parameter, .send-command-sms, .send-command-sos-number, .send-command-action, .send-command-silence-time, .send-command-order, .send-command-password').css('display', 'none');
                if ($(this).val() == 'positionPeriodic')
                    $('.send-command-periodic').css('display', 'block');

                if ($(this).val() == 'setTimezone' || $(this).val() == 'movementAlarm')
                    $('.send-command-parameter').css('display', 'block');

                // Custom
                if ($(this).val() == 'custom' || $(this).val() == 'pt502_custom' || $(this).val() == 'watch_custom')
                    $('.send-command-custom').css('display', 'block');

                if ($(this).val() == 'sendSms')
                    $('.send-command-sms').css('display', 'block');

                // Watch
                if ($(this).val() == 'watch_sosNumber')
                    $('.send-command-sos-number').css('display', 'block');

                if ($(this).val() == 'watch_alarmSos' || $(this).val() == 'watch_alarmBattery' || $(this).val() == 'watch_alarmRemove')
                    $('.send-command-action').css('display', 'block');

                if ($(this).val() == 'watch_silenceTime')
                    $('.send-command-silence-time').css('display', 'block');

                if ($(this).val() == 'watch_alarmClock' || $(this).val() == 'watch_setPhonebook')
                    $('.send-command-order').css('display', 'block');

                // PT502
                if ($(this).val() == 'pt502_engineStop' || $(this).val() == 'pt502_engineResume' || $(this).val() == 'pt502_doorOpen' || $(this).val() == 'pt502_doorClose' || $(this).val() == 'pt502_requestPhoto')
                    $('.send-command-password').css('display', 'block');
            });

            $(document).on('change', '.gprs_template_id', function() {
                var url = $(this).data('url');
                var val = $(this).val();
                if (val == 0) {
                    $('#command-form-gprs input[name="message"]').val('');
                    return;
                }
                $.ajax({
                    type: 'POST',
                    dataType: "html",
                    data: {
                        id: val
                    },
                    url: url,
                    beforeSend: function() {
                        $('.gprs_template_id, #command-form-gprs input[name="message"]').attr('disabled', 'disabled');
                    },
                    success: function (res) {
                        $('#command-form-gprs #message').val(res);
                    },
                    complete: function() {
                        $('.gprs_template_id, #command-form-gprs input[name="message"]').removeAttr('disabled').selectpicker('refresh');
                    }
                });
            });

            $(document).on('change', '#sms_template_id', function() {
                var url = $(this).data('url');
                var val = $(this).val();
                if (val == 0) {
                    $('#command-form-sms input[name="message_sms"]').val('');
                    return;
                }
                $.ajax({
                    type: 'POST',
                    dataType: "html",
                    data: {
                        id: val
                    },
                    url: url,
                    beforeSend: function() {
                        $('#sms_template_id, #command-form-sms textarea[name="message_sms"]').attr('disabled', 'disabled');
                    },
                    success: function (res) {
                        $('#command-form-sms textarea[name="message_sms"]').val(res);
                    },
                    complete: function() {
                        $('#sms_template_id, #command-form-sms textarea[name="message_sms"]').removeAttr('disabled').selectpicker('refresh');
                    }
                });
            });

            $(document).on('change', '#send_command select[name="device_id"]', function() {
                var id = $(this).val();
                if (id > 0) {
                    if (typeof gprs_templates_devices[id] != 'undefined') {
                        var type_el = $('#send_command select[name="type"]');
                        type_el.closest('.form-group').hide();
                        if (type_el.find("option[value='custom']").length !== 0) {
                            type_el.val('custom');
                        }
                        if (type_el.find("option[value='pt502_custom']").length !== 0) {
                            type_el.val('pt502_custom');
                        }
                        if (type_el.find("option[value='watch_custom']").length !== 0) {
                            type_el.val('watch_custom');
                        }
                        type_el.trigger('change');
                        $('.send-command-custom textarea[name="message"]').closest('.send-command-custom').hide();
                        $('.send-command-custom select[name="gprs_template_id"]').closest('.send-command-custom').hide();
                        $('.send-command-templates-only').show();
                        $('.send-command-templates-only select[name="gprs_template_only_id"]').trigger('change');
                    }
                    else {
                        var type_el = $('#send_command select[name="type"]');
                        type_el.closest('.form-group').show();
                        type_el.trigger('change');
                        $('.send-command-custom select[name="gprs_template_id"]').closest('.send-command-custom').show();
                        $('.send-command-templates-only').hide();
                        var protocol = send_commands_devices_protocols[id];
                        var commands_protocol = 'default';
                        if (typeof send_commands_all[protocol] != 'undefined') {
                            commands_protocol = protocol;
                        }
                        updateSendCommands(commands_protocol);

                        $.ajax({
                            type: 'GET',
                            data: {
                                id: id
                            },
                            url: $(this).data('url'),
                            beforeSend: function() {
                                $('#send_command input[name="sim_number"]').attr('disabled', 'disabled');
                            },
                            success: function (res) {
                                $('#send_command input[name="sim_number"]').val(res.sim_number);
                            },
                            complete: function() {
                                $('#send_command input[name="sim_number"]').removeAttr('disabled');
                            }
                        });
                    }
                }
            });

            $(document).on('click', '#send_command button.btn.command-save', function() {
                var url = $('#send_command .tab-pane.active').data('url');
                $('#send_command form').attr('action', url);
                $('#send_command button.update_hidden').trigger('click');
                $('#send_command .alert-success').css('display', 'none');
            });

            $(document).on('send_command', function(e, res) {
                if (res.error) {
                    $('#send_command .alert-success').css('display', 'none');
                    $('#send_command .alert-danger.main-alert').css('display', 'block').html(res.error);
                }
                else {
                    $('#send_command .alert-danger.main-alert').css('display', 'none');
                    $('#send_command .alert-success').css('display', 'block');
                }
            });

            function updateSendCommands(protocol) {
                var commands = send_commands_all[protocol];
                var el = $('.send-command-type select');
                var val;
                el.find('option').remove();
                $.each(commands, function( index, value ) {
                    index = (protocol == 'default' ? '' : protocol + '_') + index;
                    if (typeof val == 'undefined') {
                        val = index;
                    }
                    el.append('<option value="' + index + '">' + value + '</option>');
                });
                el.val(val);
                el.trigger('change');
                el.selectpicker('refresh');
            }
        }
    </script>
@stop

@section('buttons')
    <button type="button" class="update_hidden" style="display: none;"></button>
    <button type="button" class="btn btn-action command-save">{!!trans('front.send')!!}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.cancel')!!}</button>
@stop