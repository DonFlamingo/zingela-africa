@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon user"></i> {{ trans('global.edit') }}
@stop

@section('body')
    <ul class="nav nav-tabs nav-default" role="tablist">
        <li class="active"><a href="#client-edit-form-main" role="tab" data-toggle="tab">{{ trans('front.main') }}</a></li>
        <li><a href="#client-edit-form-permissions" role="tab" data-toggle="tab">{{ trans('validation.attributes.permissions') }}</a></li>
        <li><a href="#client-edit-form-objects" role="tab" data-toggle="tab">{{ trans('front.objects') }}</a></li>
        @if (settings('plugins.object_listview.status'))
            <li><a href="#client-edit-form-listview" role="tab" data-toggle="tab">{{ trans('front.object_listview') }}</a></li>
        @endif
    </ul>

    {!! Form::open(array('route' => 'admin.clients.update', 'method' => 'PUT')) !!}
    <input style="display:none" type="text" name="fakeusernameremembered"/>
    <input style="display:none" type="password" name="fakepasswordremembered"/>
    {!! Form::hidden('id', $item->id) !!}

    <div class="tab-content">
        <div id="client-edit-form-main" class="tab-pane active">
            <div class="form-group">
                <div class="checkbox">
                    {!! Form::checkbox('active', 1, $item->active) !!}
                    {!! Form::label(null, trans('validation.attributes.active')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('first_name', trans('validation.attributes.first_name').':') !!}
                {!! Form::text('first_name', $item->first_name, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('last_name', trans('validation.attributes.last_name').':') !!}
                {!! Form::text('last_name', $item->last_name, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('email', trans('validation.attributes.email').':') !!}
                {!! Form::text('email', $item->email, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('phone', trans('validation.attributes.phone').':') !!}
                {!! Form::text('phone',$item->phone, ['class' => 'form-control']) !!}
            </div>
            <div class="row">
                <div class="col-sm-6">
                    @if (Auth::User()->group_id != 3 || Auth::User()->id != $item->id)
                        @if (Auth::User()->group_id == 1)
                            <div class="form-group">
                                {!! Form::label('group_id', trans('validation.attributes.group_id').'*:') !!}
                                {!! Form::select('group_id', ['1' => trans('admin.group_1'), '3' => trans('admin.group_3'), '2' => trans('admin.group_2')], $item->group_id, ['class' => 'form-control']) !!}
                            </div>
                        @else
                            {!! Form::hidden('group_id', 2) !!}
                        @endif
                    @endif
                </div>

                <div class="col-sm-6">
                    @if (Auth::User()->group_id == 1)
                        <div class="form-group field_manager_id">
                            {!! Form::label('manager_id', trans('validation.attributes.manager_id').'*:') !!}
                            {!! Form::select('manager_id', $managers, $item->manager_id, ['class' => 'form-control']) !!}
                        </div>
                    @else
                        {!! Form::hidden('manager_id', Auth::User()->id) !!}
                    @endif
                </div>
            </div>

            <div class="form-group">
                {!! Form::label(null, trans('validation.attributes.available_maps').':') !!}
                <div class="checkboxes">
                    {!! Form::hidden('available_maps') !!}
                    @foreach ($maps as $id => $title)
                        <div class="checkbox">
                            {!! Form::checkbox('available_maps['.$id.']', $id, isset($item->available_maps[$id])) !!}
                            {!! Form::label(null, $title) !!}
                        </div>
                    @endforeach
                </div>
            </div>

            @if (Auth::User()->group_id != 3 || Auth::User()->id != $item->id)
                <div class="row">
                    <div class="col-sm-6 no_billing_plan">
                        <div class="form-group">
                            {!! Form::label('devices_limit', trans('validation.attributes.devices_limit').':') !!}

                            <div class="input-group">
                                <div class="checkbox input-group-btn">
                                    {!! Form::checkbox('enable_devices_limit', 1, (!is_null($objects_limit) || !is_null($item->devices_limit)), !is_null($objects_limit) ? ['disabled' => 'disabled'] : []) !!}
                                    {!! Form::label(null, null) !!}
                                </div>
                                {!! Form::text('devices_limit', $item->devices_limit, ['class' => 'form-control']) !!}
                            </div>
                            @if (!is_null($objects_limit))
                                <div class="help-block"> {{ trans('front.maximum_of_objects').': '.$objects_limit }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('expiration_date', trans('validation.attributes.expiration_date').':') !!}

                            <div class="input-group">
                                <div class="checkbox input-group-btn">
                                    {!! Form::checkbox('enable_expiration_date', 1, ($item->subscription_expiration != '0000-00-00 00:00:00')) !!}
                                    {!! Form::label(null, null) !!}
                                </div>
                                {!! Form::text('expiration_date', $item->subscription_expiration == '0000-00-00 00:00:00' ? NULL : $item->subscription_expiration, ['class' => 'form-control datetimepicker']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="form-group">
                <h4>{{ trans('admin.password_change') }}</h4>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('password', trans('validation.attributes.password').':') !!}
                        {!! Form::password('password', ['class' => 'form-control']) !!}
                        {!! error_for('password', $errors) !!}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('password_confirmation', trans('validation.attributes.password_confirmation').':') !!}
                        {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
                        {!! error_for('password_confirmation', $errors) !!}
                    </div>
                </div>
            </div>
        </div>
        <div id="client-edit-form-permissions" class="tab-pane">
            @if (Auth::User()->group_id != 3 || Auth::User()->id != $item->id)
                @if (!empty($plans))
                    <div class="form-group">
                        {!! Form::label('billing_plan_id', trans('front.plan').':') !!}
                        {!! Form::select('billing_plan_id', $plans, $item->billing_plan_id, ['class' => 'form-control', 'data-url' => route('admin.clients.get_permissions_table')]) !!}
                    </div>
                @endif
            @endif
            <div class="user_permissions_ajax">
                @include('Admin.Clients._perms')
            </div>
        </div>
        <div id="client-edit-form-objects" class="tab-pane">
            <div class="form-group">
                {!! Form::label('objects', trans('validation.attributes.objects').'*:') !!}
                {!! Form::select('objects[]', $devices, $item->devices->lists('id', 'id')->all(), ['class' => 'form-control multiexpand', 'multiple' => 'multiple', 'data-live-search' => true, 'data-actions-box' => true]) !!}
            </div>

            <div class="form-group">
                <div class="checkbox">
                    {!! Form::checkbox('select_all_objects', 1) !!}
                    {!! Form::label(null, trans('validation.attributes.select_all_objects')) !!}
                </div>
            </div>
        </div>
        <div id="client-edit-form-listview" class="tab-pane">
            @include('Frontend.ObjectsList.form')
        </div>
    </div>

    {!! Form::close() !!}
    <script>
        $(document).ready(function() {
            $('#clients_edit').find('input[name="enable_devices_limit"]').trigger('change');
            $('#clients_edit').find('input[name="enable_expiration_date"]').trigger('change');
            $('#clients_edit').find('select[name="billing_plan_id"]').trigger('change');
            checkPerms();
        });
    </script>
@stop
