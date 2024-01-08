@extends('Admin.Layouts.default')

@section('content')

    <div class="row">
        <div class="col-sm-6">

            @if (Session::has('user_defaults_errors'))
                <div class="alert alert-danger">
                    <ul>
                        @foreach (Session::get('user_defaults_errors')->all() as $error)
                            <li>{!! $error !!}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="panel panel-default">

                <div class="panel-heading">
                    <div class="panel-title">{{ trans('front.registration') }}</div>
                </div>

                <div class="panel-body">
                    {!! Form::open(array('route' => 'admin.main_server_settings.new_user_defaults_save', 'method' => 'POST', 'class' => 'form form-horizontal', 'id' => 'new-user-defaults-form')) !!}

                    <div class="form-group">
                        {!! Form::label('allow_users_registration', trans('validation.attributes.allow_users_registration'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::select('allow_users_registration', ['0' => trans('global.no'), '1' => trans('global.yes')], $settings['allow_users_registration'], ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12 col-sm-4"></div>
                        <div class="col-xs-12 col-sm-8">
                            <div class="checkbox">
                                {!! Form::checkbox('enable_plans', 1, settings('main_settings.enable_plans')) !!}
                                {!! Form::label('enable_plans', trans('validation.attributes.enable_plans') ) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="default_billing_plan">
                        {!! Form::label('default_billing_plan', trans('validation.attributes.default_billing_plan'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::select('default_billing_plan', $items->lists('title','id')->all(), settings('main_settings.default_billing_plan'), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('default_timezone', trans('validation.attributes.default_timezone'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::select('default_timezone', $timezones, $settings['default_timezone'], ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label(null, trans('validation.attributes.daylight_saving_time'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                            <div class="col-xs-6">
                                <div class="form-group input-group">
                                    <div class="checkbox input-group-btn">
                                        {!! Form::checkbox('dst', 1, !is_null(settings('main_settings.dst'))) !!}
                                        {!! Form::label(null) !!}
                                    </div>
                                    {!! Form::text('dst_date_from', settings('main_settings.dst_date_from'), ['class' => 'form-control', 'placeholder' => trans('validation.attributes.date_from')]) !!}
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group input-group">
                                    <div class="input-group-btn">
                                        {!! Form::label(null, '-') !!}
                                    </div>
                                    {!! Form::text('dst_date_to', settings('main_settings.dst_date_to'), ['class' => 'form-control', 'placeholder' => trans('validation.attributes.date_to')]) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="default_user_fields">
                        <div class="form-group">
                            {!! Form::label(null, trans('validation.attributes.devices_limit'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                            <div class="col-xs-12 col-sm-8">
                                <div class="input-group">
                                    <div class="checkbox input-group-btn">
                                        {!! Form::checkbox('enable_devices_limit', 1, !is_null(settings('main_settings.devices_limit'))) !!}
                                        {!! Form::label(null) !!}
                                    </div>
                                    {!! Form::text('devices_limit', settings('main_settings.devices_limit'), ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label(null, trans('validation.attributes.subscription_expiration_after_days'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                            <div class="col-xs-12 col-sm-8">
                                <div class="input-group">
                                    <div class="checkbox input-group-btn">
                                        {!! Form::checkbox('enable_subscription_expiration_after_days', 1, !is_null(settings('main_settings.subscription_expiration_after_days'))) !!}
                                        {!! Form::label(null) !!}
                                    </div>
                                    {!! Form::text('subscription_expiration_after_days', settings('main_settings.subscription_expiration_after_days'), ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3>{{ trans('validation.attributes.permissions') }}</h3>
                            <table class="table">
                                <thead>
                                <th style="text-align: left">{{ trans('front.permission') }}</th>
                                <th style="text-align: center">{{ trans('front.view') }}</th>
                                <th style="text-align: center">{{ trans('global.edit') }}</th>
                                <th style="text-align: center">{{ trans('global.delete') }}</th>
                                </thead>
                                <tbody>
                                @foreach($perms as $perm => $modes)
                                    <tr>
                                        <td>{{ trans('front.'.$perm) }}</td>
                                        <td style="text-align: center">
                                            <div class="checkbox">
                                                @if ($modes['view'])
                                                    {!! Form::checkbox("perms[$perm][view]", 1, getMainPermission($perm, 'view'), ['class' => 'perm_checkbox perm_view']) !!}
                                                @else
                                                    {!! Form::checkbox('', 0, 0, ['disabled' => 'disabled']) !!}
                                                @endif
                                                {!! Form::label(null, null) !!}
                                            </div>
                                        </td>
                                        <td style="text-align: center">
                                            <div class="checkbox">
                                                @if ($modes['edit'])
                                                    {!! Form::checkbox("perms[$perm][edit]", 1, getMainPermission($perm, 'edit'), ['class' => 'perm_checkbox perm_edit']) !!}
                                                @else
                                                    {!! Form::checkbox('', 0, 0, ['disabled' => 'disabled']) !!}
                                                @endif
                                                {!! Form::label(null, null) !!}
                                            </div>
                                        </td>
                                        <td style="text-align: center">
                                            <div class="checkbox">
                                                @if ($modes['remove'])
                                                    {!! Form::checkbox("perms[$perm][remove]", 1, getMainPermission($perm, 'remove'), ['class' => 'perm_checkbox perm_remove']) !!}
                                                @else
                                                    {!! Form::checkbox('', 0, 0, ['disabled' => 'disabled']) !!}
                                                @endif
                                                {!! Form::label(null, null) !!}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>

                <div class="panel-footer">
                    <button type="submit" class="btn btn-action" onClick="$('#new-user-defaults-form').submit();">{{ trans('global.save') }}</button>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            @if (Session::has('billing_success'))
                <div class="alert alert-success">
                    {!! Session::get('billing_success') !!}
                </div>
            @endif
            @if (Session::has('billing_errors'))
                <div class="alert alert-danger">
                    <ul>
                        @foreach (Session::get('billing_errors')->all() as $error)
                            <li>{!! $error !!}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="panel panel-default">

                <div class="panel-heading">
                    <div class="panel-title">{{ trans('admin.billing_gateway') }}</div>
                </div>

                <div class="panel-body">
                    {!! Form::open(array('route' => 'admin.billing.store', 'method' => 'POST', 'class' => 'form form-horizontal', 'id' => 'billing-gateway-form')) !!}

                    <div class="form-group">
                        {!! Form::label('payment_type', trans('validation.attributes.payment_type'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::select('payment_type', $payment_types, settings('main_settings.payment_type'), ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('paypal_client_id', trans('validation.attributes.paypal_client_id'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('paypal_client_id', settings('main_settings.paypal_client_id'), ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('paypal_secret', trans('validation.attributes.paypal_secret'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('paypal_secret', settings('main_settings.paypal_secret'), ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('paypal_currency', trans('validation.attributes.paypal_currency'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('paypal_currency', settings('main_settings.paypal_currency'), ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('paypal_payment_name', trans('validation.attributes.paypal_payment_name'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('paypal_payment_name', settings('main_settings.paypal_payment_name'), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>

                <div class="panel-footer">
                    <button type="submit" class="btn btn-action" onClick="$('#billing-gateway-form').submit();">{{ trans('global.save') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default" id="table_billing_plans">
                <div class="panel-heading">
                    <ul class="nav nav-tabs nav-icons pull-right">
                        <li role="presentation" class="">
                            <a href="javascript:" type="button" data-modal="billing_plans_create" data-url="{{ route("admin.billing.create") }}">
                                <i class="icon add" title="{{ trans('admin.add_new_user') }}"></i>
                            </a>
                        </li>
                    </ul>

                    <div class="panel-title">{!! trans('front.plans') !!}</div>
                </div>

                <div class="panel-body" data-table>
                    @include('Admin.Billing.table')
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
<script>
    tables.set_config('table_billing_plans', {
        url:'{{ route("admin.billing.plans") }}',
        delete_url:'{{ route("admin.billing.destroy") }}'
    });

    function billing_plans_edit_modal_callback() {
        tables.get('table_billing_plans');
        updateBillingPlans();
    }

    function billing_plans_create_modal_callback() {
        tables.get('table_billing_plans');
        updateBillingPlans();
    }

    function updateBillingPlans() {
        $.ajax({
            type: 'GET',
            dataType: "html",
            url: '{{ route('admin.billing.billing_plans_form') }}',
            success: function(res){
                $('#default_billing_plan div').html(res);
            }
        });
    }

    $(document).ready(function() {
        $(document).on('change', 'input[name="enable_plans"]', function() {
            if ($(this).prop('checked')) {
                $('#default_billing_plan').show();
                $('#default_user_fields').hide();
            }
            else {
                $('#default_user_fields').show();
                $('#default_billing_plan').hide();
            }
        });

        $(document).on('click', '.multi_delete', function() {
            setTimeout(function() {
                updateBillingPlans();
            }, 2000);
        });

        $('input[name="enable_plans"]').trigger('change');

        checkPerms();

        $(document).ready(function () {
            $('input[name="dst_date_from"]').datetimepicker({
                changeYear: false,
                format: 'mm-dd hh:ii',
                closeOnDateSelect: true
            });
            $('input[name="dst_date_to"]').datetimepicker({
                changeYear: false,
                format: 'mm-dd hh:ii',
                closeOnDateSelect: true
            });
        });
    });
</script>
@stop