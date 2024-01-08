@extends('Frontend.Layouts.modal')

@section('title', trans('global.add_new'))

@section('body')
    <ul class="nav nav-tabs nav-default" role="tablist">
        <li class="active"><a href="#alerts-form-add-user" role="tab" data-toggle="tab">{!!trans('front.user_info')!!} & {!!trans('front.devices')!!}</a></li>
        @if (count($drivers))
            <li><a href="#alerts-form-add-drivers" role="tab" data-toggle="tab">{!!trans('front.drivers')!!}</a></li>
        @endif
        <li><a href="#alerts-form-add-geofences" role="tab" data-toggle="tab">{!!trans('front.geofencing')!!}</a></li>
        <li><a href="#alerts-form-add-overspeed" role="tab" data-toggle="tab">{!!trans('front.overspeed')!!}</a></li>
        {{--<li><a href="#alerts-form-add-fuel" role="tab" data-toggle="tab">{!!trans('validation.attributes.fuel_consumption')!!}</a></li>--}}
        <li><a href="#alerts-form-add-events" role="tab" data-toggle="tab">{!!trans('front.events')!!}</a></li>
    </ul>
    
    {!!Form::open(['route' => 'alerts.store', 'method' => 'POST', 'class' => 'alert-form'])!!}
    {!!Form::hidden('id')!!}
        <div class="tab-content">
            <div id="alerts-form-add-user" class="tab-pane active">

                <div class="form-group">
                    {!!Form::label('name', trans('validation.attributes.name').'*:')!!}
                    {!!Form::text('name', null, ['class' => 'form-control'])!!}
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!!Form::label('email', trans('validation.attributes.email').':')!!}
                            {!!Form::email('email', null, ['class' => 'form-control'])!!}
                            <small>{!!trans('front.email_semicolon')!!}</small>
                        </div>
                    </div>
                    @if (Auth::User()->sms_gateway)
                        <div class="col-sm-6">
                            <div class="form-group">
                                {!!Form::label('mobile_phone', trans('validation.attributes.mobile_phone').':')!!}
                                {!!Form::text('mobile_phone', null, ['class' => 'form-control'])!!}
                                <small>{!!trans('front.sms_semicolon')!!}</small>
                            </div>
                        </div>
                    @endif
                </div>
                {{--<div class="form-group">
                    <label>{!!Form::checkbox('ac_alarm', 1, null)!!} {!!trans('front.ac_alarm')!!}</label>
                </div>--}}

                <div class="form-group">
                    {!! Form::label('devices', trans('validation.attributes.devices').'*:') !!}
                    {!! Form::select('devices[]',$devices , null, ['class' => 'form-control multiexpand', 'multiple' => 'multiple', 'data-live-search' => true, 'data-actions-box' => true]) !!}
                </div>
            </div>

            <div id="alerts-form-add-drivers" class="tab-pane">
                <div class="form-group">
                    <div class="alert alert-info">{{ trans('front.alert_tab_driver_note') }}</div>
                    {!! Form::label('drivers', trans('front.drivers').':') !!}
                    {!! Form::select('drivers[]', $drivers, null, ['class' => 'form-control multiexpand', 'multiple' => 'multiple', 'data-live-search' => true, 'data-actions-box' => true]) !!}
                </div>
            </div>

            <div id="alerts-form-add-geofences" class="tab-pane">
                @if (!empty($geofences))
                <div class="row">
                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                        {!! Form::label('geofences', trans('validation.attributes.geofences').':') !!}
                        {!! Form::hidden('geofences') !!}
                        <div class="row">
                            <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                {!!Form::select('geofence', $geofences, null, ['class' => 'form-control'])!!}
                            </div>
                            <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                {!! Form::select('zone_type', $alert_zones, null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-4 col-sm-4 col-xs-10">
                        <div class="row">
                            <div class="form-group col-md-6 col-sm-6 col-xs-6">
                                {!! Form::label('geofences', trans('front.from').':') !!}
                                {!! Form::text('time_from', '00:00', ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-md-6 col-sm-6 col-xs-6">
                                {!! Form::label('geofences', trans('front.to').':') !!}
                                {!! Form::text('time_to', '00:00', ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-1 col-sm-1 col-xs-2">
                        <div class="row">
                            {!! Form::label(null, '&nbsp;') !!}
                            <a href="javascript:" class="btn btn-action btn-block alert-add-geofence"><i class="icon add" title="{{ trans('global.add') }}"></i></a>
                        </div>
                    </div>
                </div>
                <small>{{ trans('front.geofence_help_text') }}</small>
                @else
                    <div class="alert alert-warning" role="alert">{!!trans('front.no_geofences')!!}</div>
                @endif
                <table class="table table-bordered alerts-geofences-list"></table>
            </div>

            <div id="alerts-form-add-overspeed" class="tab-pane">
                <div class="form-group">
                    {!!Form::label('overspeed', trans('validation.attributes.overspeed').':')!!}<br>
                    <div class="row">
                        <div class="form-group col-md-5 col-sm-5 col-xs-12">
                            {!!Form::text('speed', null, ['class' => 'form-control numeric', 'placeholder' => trans('global.speed')])!!}
                        </div>
                        <div class="form-group col-md-5 col-sm-4 col-xs-12">
                            {!!Form::select('distance', $alert_distance, null, ['class' => 'form-control'])!!}
                        </div>
                        <div class="col-md-1 col-sm-1 col-xs-12">
                            <a href="javascript:" class="btn btn-action alert-add-overspeed"><i class="icon add" title="{{ trans('global.add') }}"></i></a>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered alerts-overspeed-list"></table>
            </div>

            <div id="alerts-form-add-events" class="tab-pane">
                <div class="form-group">
                    {!!Form::label('event', trans('validation.attributes.event').':')!!}<br>
                    <div class="row">
                        <div class="form-group col-md-3 col-sm-3 col-xs-12">
                            {!!Form::select('event_type', $event_types, null, ['class' => 'form-control'])!!}
                        </div>
                        <div class="form-group col-md-3 col-sm-3 col-xs-12 event_protocol_ajax">
                            {!!Form::select('event_protocols', $event_protocols, null, ['class' => 'form-control'])!!}
                        </div>
                        <div class="form-group col-md-4 col-sm-4 col-xs-12 event_id_ajax">
                            {!!Form::select('event_id', [], null, ['class' => 'form-control', 'disabled' => 'disabled'])!!}
                        </div>
                        <div class="col-md-1 col-sm-1 col-xs-12">
                            <a href="javascript:" class="btn btn-action alert-add-event"><i class="icon add" title="{{ trans('global.add') }}"></i></a>
                        </div>
                    </div>
                    <div>
                        {!!trans('front.alert_events_tip')!!}
                    </div>
                </div>
                <table class="table table-bordered alerts-events-list">
                </table>
            </div>
        </div>

    {!!Form::close()!!}
@stop