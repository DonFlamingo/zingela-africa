@extends('Frontend.Layouts.modal')

@section('modal_class', 'modal-md')

@section('title')
    <i class="icon reports"></i> {!!trans('front.reports')!!}
@stop

@section('body')
    <ul class="nav nav-tabs nav-default" role="tablist">
        <li class="active"><a href="#reports-form-reports" class="reports-form-reports-link" role="tab" data-toggle="tab">{!!trans('front.main')!!}</a></li>
        <li><a href="#reports-form-generated-reports" class="reports-form-generated-reports-tab-link" role="tab" data-toggle="tab" style="width: auto; padding-left: 10px; padding-right: 10px;">{!!trans('front.generated_reports')!!}</a></li>
        <li><a href="#reports-form-report-logs" class="reports-form-report-logs-tab-link" role="tab" data-toggle="tab">{{ trans('front.report_logs') }}</a></li>
    </ul>

    <div id="reports-modal">
    {!!Form::open(['route' => 'reports.store', 'method' => 'POST', 'id' => 'report_form'])!!}
    {!!Form::hidden('id')!!}
    {!!Form::hidden('_method', 'POST')!!}

        <div class="tab-content" id="reports_form_inputs">

            <div id="reports-form-reports" class="tab-pane active">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            {!!Form::label('title', trans('validation.attributes.title'))!!}
                            {!!Form::text('title', null, ['class' => 'form-control'])!!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!!Form::label('type', trans('validation.attributes.type'))!!}
                            {!!Form::select('type', $types_list, null, ['class' => 'form-control', 'id' => 'reports_type'])!!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!!Form::label('format', trans('validation.attributes.format'))!!}
                            {!!Form::select('format', $formats, null, ['class' => 'form-control'])!!}
                        </div>
                    </div>
                </div>

                <hr class="section-line">

                <div class="row form-horizontal">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="type" class="col-sm-3 control-label">{{ trans('validation.attributes.period') }}</label>
                            <div class="col-sm-9">
                                {!! Form::select('filter', $filters, 1, ['class' => 'form-control', 'id' => 'reports_period', 'data-icon' => 'icon time']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="from" class="col-sm-3 control-label">{{ trans('validation.attributes.date_from') }}</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="has-feedback">
                                        <i class="icon calendar form-control-feedback"></i>
                                        <input name="date_from" type="text" class="datepicker form-control" value="{{ date('Y-m-d') }}">
                                    </div>
                                    <span class="input-group-btn">
                                        {!!Form::select('from_time', Config::get('tobuli.history_time'), '00:00', ['class' => 'form-control timeselect'])!!}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="to" class="col-sm-3 control-label">{{ trans('validation.attributes.date_to') }}</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="has-feedback">
                                        <i class="icon calendar form-control-feedback"></i>
                                        <input class="datepicker form-control" name="date_to" type="text" value="{{ date('Y-m-d', strtotime(date('Y-m-d').' +1 day')) }}">
                                    </div>
                                    <span class="input-group-btn">
                                        {!!Form::select('to_time', Config::get('tobuli.history_time'), '00:00', ['class' => 'form-control timeselect'])!!}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="to" class="col-sm-3 control-label">{!!trans('front.devices')!!}</label>
                            {!! Form::hidden('devices_fake') !!}
                            <div class="col-sm-9">
                                {!!Form::select('devices[]', $devices->lists('name', 'id')->all(), null, ['class' => 'form-control', 'multiple' => 'multiple', 'data-icon' => 'icon devices', 'data-live-search' => true, 'data-actions-box' => true])!!}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="to" class="col-sm-3 control-label">{!!trans('front.geofences')!!}</label>
                            {!! Form::hidden('geofences_fake') !!}
                            <div class="col-sm-9">
                                {!!Form::select('geofences[]', $geofences->lists('name', 'id')->all(), null, ['class' => 'form-control', 'multiple' => 'multiple', 'data-icon' => 'icon geofences', 'data-live-search' => true, 'data-actions-box' => true])!!}
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="section-line">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ trans('validation.attributes.send_to_email') }}</label>
                            <div class="has-feedback">
                                <i class="icon email form-control-feedback"></i>
                                <input name="send_to_email" class="form-control" type="email" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ trans('validation.attributes.speed_limit') }}</label>
                            <input name="speed_limit" class="form-control" type="text" placeholder="60" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ trans('validation.attributes.stops') }}</label>
                            {!! Form::select('stops', $stops, null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" name="daily" id="daily" value="1">
                                <label for="daily">
                                    {{ trans('validation.attributes.daily') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" name="weekly" id="weekly" value="1">
                                <label for="weekly">
                                    {{ trans('validation.attributes.weekly') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" name="show_addresses" id="show_addresses" value="1">
                                <label for="show_addresses">
                                    {{ trans('validation.attributes.show_addresses') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" name="zones_instead" id="zones_instead" value="1">
                                <label for="zones_instead">
                                    {{ trans('validation.attributes.zones_instead') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::text('daily_time', '00:00', ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::text('weekly_time', '00:00', ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div id="reports-form-generated-reports" class="tab-pane">
                <div data-table>
                    @include('Frontend.Reports.index')
                </div>
            </div>
            <div id="reports-form-report-logs" class="tab-pane">
                <div data-table>
                    @include('Frontend.Reports.logs')
                </div>
            </div>
        </div>
        {!!Form::close()!!}
    </div>
    <script>
        if ( typeof _static_reports_create === "undefined") {
            var _static_reports_create = true;

            $(document).on('change', 'select[name="filter"]', function() {
                momentCalendar($(this).val(), '#reports_create')
            });

            $(document).on('click', '#reports_create button.save:visible', function() {
                $('#reports_create form').attr('action', $(this).data('action'));
                $('#reports_create input[name="_method"]').val('POST');
                $('#reports_create button.update_hidden').trigger('click');
            });

            $(document).on('click', '#reports_create button.generate:visible', function() {
                $('#reports_create form').attr('action', $(this).data('action'));
                $('#reports_create input[name="_method"]').val('POST');
                $('#reports_create button.update_hidden').trigger('click');
            });

            $(document).on('click', '#reports_create button.new:visible', function() {
                var parent = $('#reports_create');

                parent.find('input[name="id"]').val('')
                parent.find('input[name="title"]').val('');
                parent.find('select[name="type"]').val(1).trigger('change');
                parent.find('select[name="format"]').val('html').trigger('change');
                parent.find('input[name="show_addresses"]').prop('checked', 0);
                parent.find('input[name="zones_instead"]').prop('checked', 0);
                parent.find('select[name="stops"]').val(1).trigger('change');
                parent.find('input[name="speed_limit"]').val('');
                parent.find('input[name="daily"]').prop('checked', 0).trigger('change');
                parent.find('input[name="weekly"]').prop('checked', 0).trigger('change');
                parent.find('input[name="send_to_email"]').val('');
                parent.find('select.reports_geofences').val([]);
                parent.find('select.reports_devices').val([]);
                parent.find('input[name="daily_time"]').val('00:00');
                parent.find('input[name="weekly_time"]').val('00:00');

                $('a.reports-form-reports-link').trigger('click');
            });

            $(document).on('click', '#reports_create .report_item_edit', function() {
                var parent = $('#reports_create');
                var item = jQuery.parseJSON($(this).closest('td').find('.report_item_json').html());

                parent.find('input[name="id"]').val(item.id)
                parent.find('input[name="title"]').val(item.title);
                parent.find('select[name="type"]').val(item.type).trigger('change');
                parent.find('select[name="format"]').val(item.format).trigger('change');
                parent.find('input[name="show_addresses"]').prop('checked', item.show_addresses);
                parent.find('input[name="zones_instead"]').prop('checked', item.zones_instead);
                parent.find('select[name="stops"]').val(item.stops).trigger('change');
                parent.find('input[name="speed_limit"]').val(item.speed_limit);
                parent.find('input[name="daily"]').prop('checked', item.daily).trigger('change');
                parent.find('input[name="weekly"]').prop('checked', item.weekly).trigger('change');
                parent.find('input[name="send_to_email"]').val(item.email);
                parent.find('input[name="daily_time"]').val(item.daily_time);
                parent.find('input[name="weekly_time"]').val(item.weekly_time);

                var from_time = moment(item.from_formated).format('HH:mm');
                parent.find('select[name="from_time"]').val(from_time).trigger('change');

                var to_time = moment(item.to_formated).format('HH:mm');
                parent.find('select[name="to_time"]').val(to_time).trigger('change');

                var geofences = [];
                $.each(item.geofences, function( index, value ) {
                    geofences.push( value.id );
                });
                $('#report_form select[name="geofences[]"]').val( geofences ).trigger('change').selectpicker('refresh');
                dd( 'geofences', geofences );

                var devices = [];
                $.each(item.devices, function( index, value ) {
                    devices.push( value.id );
                });
                $('#report_form select[name="devices[]"]').val( devices ).trigger('change').selectpicker('refresh');
                dd( 'devices', devices );

                $('a.reports-form-reports-link').trigger('click');
            });

            $(document).on('change', '#reports_type', function() {
                var val = $(this).val();
                var parent = $('#reports_create');
                $('#reports_form_inputs input, #reports_form_inputs select').not('input[name="daily_time"], input[name="weekly_time"]').removeAttr('disabled');
                if ($("#format option[value='xls']").length == 0) {
                    $('#format')
                            .append($("<option></option>")
                                    .attr("value", 'xls')
                                    .text('XLS'));
                    $('#format')
                            .append($("<option></option>")
                                    .attr("value", 'pdf')
                                    .text('PDF'));
                    $('#format')
                            .append($("<option></option>")
                                    .attr("value", 'pdf_land')
                                    .text('PDF (Landscape)'));
                }

                switch (val) {
                    case "1":
                        parent.find('select.reports_geofences, input[name="show_addresses"], input[name="zones_instead"]').attr('disabled', 'disabled');
                        break;
                    case "2":
                        parent.find('select.reports_geofences, input[name="show_addresses"], input[name="zones_instead"]').attr('disabled', 'disabled');
                        break;
                    case "16":
                        parent.find('select.reports_geofences, input[name="show_addresses"], input[name="zones_instead"]').attr('disabled', 'disabled');
                        break;
                    case "3":
                        parent.find('select.reports_geofences, input[name="speed_limit"]').attr('disabled', 'disabled');
                        break;
                    case "4":
                        parent.find('select.reports_geofences, input[name="speed_limit"]').attr('disabled', 'disabled');
                        break;
                    case "5":
                        parent.find('select.reports_geofences, select[name="stops"]').attr('disabled', 'disabled');
                        break;
                    case "6":
                        parent.find('select.reports_geofences, select[name="stops"]').attr('disabled', 'disabled');
                        break;
                    case "7":
                        parent.find('input[name="speed_limit"], select[name="stops"]').attr('disabled', 'disabled');
                        break;
                    case "8":
                        parent.find('select.reports_geofences, input[name="speed_limit"], select[name="stops"]').attr('disabled', 'disabled');
                        break;
                    case "9":
                        parent.find('select.reports_geofences, input[name="speed_limit"], select[name="stops"], input[name="show_addresses"], input[name="zones_instead"]').attr('disabled', 'disabled');
                        break;
                    case "10":
                        parent.find('select.reports_geofences, input[name="speed_limit"], select[name="stops"]').attr('disabled', 'disabled');
                        parent.find("#format option[value='pdf']").remove();
                        parent.find("#format option[value='pdf_land']").remove();
                        parent.find("#format option[value='xls']").remove();
                        break;
                    case "11":
                        parent.find('select.reports_geofences, input[name="speed_limit"], select[name="stops"]').attr('disabled', 'disabled');
                        break;
                    case "12":
                        parent.find('select.reports_geofences, input[name="speed_limit"], select[name="stops"]').attr('disabled', 'disabled');
                        break;
                    case "13":
                        parent.find('select.reports_geofences, input[name="speed_limit"], select[name="stops"]').attr('disabled', 'disabled');
                        parent.find("#format option[value='pdf']").remove();
                        parent.find("#format option[value='pdf_land']").remove();
                        parent.find("#format option[value='xls']").remove();
                        break;
                    case "18":
                        parent.find('select.reports_geofences, input[name="speed_limit"]').attr('disabled', 'disabled');
                        break;
                }

                parent.find('select[name="format"]').selectpicker('refresh');
            });
        }

        $(document).ready(function() {
            $('select[name="filter"]:first, #reports_type').trigger('change');
        });

        tables.set_config('reports-form-report-logs', {
            url:'{{ route("reports.logs") }}',
            delete_url:'{{ route("reports.log_destroy") }}'
        });

        tables.set_config('reports-form-generated-reports', {
            url:'{{ route('reports.index') }}'
        });

        function reports_create_modal_callback(res) {
            if (res.status == 3) {
                if (typeof res.url != 'undefined') {
                    var form = $('<form method="POST" action="' + res.url + '">');
                    $('body').append(form);
                    form.submit();
                }
            }
            if (res.status == 2) {
                tables.get('reports-form-generated-reports');
                $('a.reports-form-generated-reports-tab-link').trigger('click');
            }
        }

        function reports_destroy_modal_callback() {
            tables.get('reports-form-generated-reports');
        }
/*
        Assets.load(['timepicker'], function () {
            $('input[name="daily_time"]').timepicker({
                minuteStep: 5,
                showInputs: false,
                showSeconds: false,
                showMeridian: false,
                format: 'HH:mm'
            });

            $('input[name="weekly_time"]').timepicker({
                minuteStep: 5,
                showInputs: false,
                showSeconds: false,
                showMeridian: false,
                format: 'HH:mm'
            });
        });
*/
        $('input[name="daily"]').on('change', timeCon);
        $('input[name="weekly"]').on('change', timeCon);

        function timeCon() {
            var daily = $('input[name="daily"]').prop("checked");
            if (daily) {
                $('input[name="daily_time"]').removeAttr('disabled');
            }
            else {
                $('input[name="daily_time"]').attr('disabled', 'disabled');
            }

            var weekly = $('input[name="weekly"]').prop("checked");
            if (weekly) {
                $('input[name="weekly_time"]').removeAttr('disabled');
            }
            else {
                $('input[name="weekly_time"]').attr('disabled', 'disabled');
            }
        }

        timeCon();
    </script>
@stop

@section('buttons')
    <button type="button" class="update_hidden" style="display: none;"></button>
    <button type="button" class="btn btn-action generate" data-action="{!!route('reports.update')!!}">{!!trans('front.generate')!!}</button>
    <button type="button" class="btn btn-default save" data-action="{!!route('reports.store')!!}">{!!trans('global.save')!!}</button>
    <button type="button" class="btn btn-default new">{!!trans('front.new')!!}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.cancel')!!}</button>
@stop