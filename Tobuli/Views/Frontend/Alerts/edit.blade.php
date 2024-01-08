@extends('Frontend.Layouts.modal')

@section('title', trans('global.edit'))

@section('body')
    <ul class="nav nav-tabs nav-default" role="tablist">
        <li class="active"><a href="#alerts-form-edit--user" role="tab" data-toggle="tab">{!!trans('front.user_info')!!} & {!!trans('front.devices')!!}</a></li>
        @if (count($drivers))
            <li><a href="#alerts-form-edit--drivers" role="tab" data-toggle="tab">{!!trans('front.drivers')!!}</a></li>
        @endif
        <li><a href="#alerts-form-edit--geofences" role="tab" data-toggle="tab">{!!trans('front.geofencing')!!}</a></li>
        <li><a href="#alerts-form-edit--overspeed" role="tab" data-toggle="tab">{!!trans('front.overspeed')!!}</a></li>
        {{--<li><a href="#alerts-form-edit--fuel" role="tab" data-toggle="tab">{!!trans('validation.attributes.fuel_consumption')!!}</a></li>--}}
        <li><a href="#alerts-form-edit--events" role="tab" data-toggle="tab">{!!trans('front.events')!!}</a></li>
    </ul>
    
    {!!Form::open(['route' => 'alerts.update', 'method' => 'PUT', 'class' => 'alert-form'])!!}
    {!!Form::hidden('id', $item->id)!!}
        <div class="tab-content">

            <div id="alerts-form-edit--user" class="tab-pane active">
                <div class="form-group">
                    {!!Form::label('name', trans('validation.attributes.name').'*:')!!}
                    {!!Form::text('name', $item->name, ['class' => 'form-control'])!!}
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!!Form::label('email', trans('validation.attributes.email').':')!!}
                            {!!Form::email('email', $item->email, ['class' => 'form-control'])!!}
                            <small>{!!trans('front.email_semicolon')!!}</small>
                        </div>
                    </div>
                    @if (Auth::User()->sms_gateway)
                        <div class="col-sm-6">
                            <div class="form-group">
                                {!!Form::label('mobile_phone', trans('validation.attributes.mobile_phone').':')!!}
                                {!!Form::text('mobile_phone', $item->mobile_phone, ['class' => 'form-control'])!!}
                                <small>{!!trans('front.sms_semicolon')!!}</small>
                            </div>
                        </div>
                    @endif
                </div>
            <!-- Ac_alarm Form Input -->
                {{--<div class="form-group">
                    <label>{!!Form::checkbox('ac_alarm', 1, $item->ac_alarm)!!} {!!trans('front.ac_alarm')!!}</label>
                </div>--}}

                <div class="form-group">
                    {!! Form::label('devices', trans('validation.attributes.devices').'*:') !!}
                    {!! Form::select('devices[]',$devices , $item->devices->lists('id', 'id')->all(), ['class' => 'form-control multiexpand', 'multiple' => 'multiple', 'data-live-search' => true, 'data-actions-box' => true]) !!}
                </div>
            </div>

            <div id="alerts-form-edit--drivers" class="tab-pane">
                <div class="form-group">
                    <div class="alert alert-info">{{ trans('front.alert_tab_driver_note') }}</div>
                    {!! Form::label('drivers', trans('front.drivers').':') !!}
                    {!! Form::select('drivers[]', $drivers, $item->drivers->lists('id', 'id')->all(), ['class' => 'form-control multiexpand', 'multiple' => 'multiple', 'data-live-search' => true, 'data-actions-box' => true]) !!}
                </div>
            </div>

            <div id="alerts-form-edit--geofences" class="tab-pane">
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

                    <table class="table table-bordered table-condensed alerts-geofences-list form-group">
                        @foreach ($geo_arr as  $key => $value)
                            @foreach ($value['zones'] as $zone)
                                <?php $item_id = $key.'-'.$zone.'-'.str_replace(':', '', $value['time_from']).'-'.str_replace(':', '', $value['time_to']); ?>
                                <tr class="{{ $item_id }}">
                                    <input type="hidden" name="geofences[{{ $item_id }}][id]" value="{{ $key }}">
                                    <input type="hidden" name="geofences[{{ $item_id }}][zone]" value="{{ $zone }}">
                                    <input type="hidden" name="geofences[{{ $item_id }}][time_from]" value="{{ $value['time_from'] }}">
                                    <input type="hidden" name="geofences[{{ $item_id }}][time_to]" value="{{ $value['time_to'] }}">
                                    <td class="text-center">{{ $value['name'] }}</td>
                                    <td class="text-center">{{ $alert_zones[$zone] }}</td>
                                    <td class="text-center">{{ $value['time_from'] }} - {{ $value['time_to'] }}</td>
                                    <td class="text-center"><a href="javascript:" class="alert-delete-item close center"><span aria-hidden="true">×</span></a></td>
                                </tr>
                            @endforeach
                        @endforeach
                    </table>
                @else
                    <div class="alert alert-warning" role="alert">{!!trans('front.no_geofences')!!}</div>
                @endif
            </div>

            <div id="alerts-form-edit--overspeed" class="tab-pane">
                <div class="form-group">
                    {!!Form::label('overspeed', trans('validation.attributes.overspeed').':')!!}<br>
                    <div class="row">
                        <div class="form-group col-md-5 col-sm-5 col-xs-12">
                            {!!Form::text('speed', null, ['class' => 'form-control numeric', 'placeholder' => trans('global.speed')])!!}
                        </div>
                        <div class="form-group col-md-5 col-sm-4 col-xs-12">
                            {!!Form::select('distance', $alert_distance, null, ['class' => 'form-control'])!!}
                        </div>
                        <div class="col-md-2 col-sm-3 col-xs-12">
                            <a href="javascript:" class="btn btn-action alert-add-overspeed"><i class="icon add" title="{{ trans('global.add') }}"></i></a>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered table-condensed alerts-overspeed-list form-group">
                    @if (!empty($item->overspeed_speed))
                        <tr class="alert-distance">
                            <input type="hidden" name="overspeed[speed]" value="{!!$item->overspeed_speed!!}">
                            <input type="hidden" name="overspeed[distance]" value="{!!$item->overspeed_distance!!}">
                            <td class="text-center">{!!$item->overspeed_speed!!} {!!$alert_distance[$item->overspeed_distance]!!}/h</td>
                            <td class="text-center"><a href="javascript:" class="alert-delete-item close center"><span aria-hidden="true">×</span></a></td>
                        </tr>
                    @endif
                </table>
            </div>

            <div id="alerts-form-edit--events" class="tab-pane">
                <div class="form-group">
                    {!!Form::label('event', trans('validation.attributes.event').':')!!}<br>
                    <div class="row">
                        <div class="form-group col-md-3 col-sm-3 col-xs-12">
                            {!!Form::select('event_type', $event_types, null, ['class' => 'form-control'])!!}
                        </div>
                        <div class="form-group col-md-3 col-sm-3 col-xs-12 event_protocol_ajax">
                            {!!Form::select('event_protocol', $event_protocols, null, ['class' => 'form-control'])!!}
                        </div>
                        <div class="form-group col-md-4 col-sm-4 col-xs-12 event_id_ajax">
                            {!!Form::select('event_id', [], null, ['class' => 'form-control', 'disabled' => 'disabled'])!!}
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-12">
                            <a href="javascript:" class="btn btn-action alert-add-event"><i class="icon add" title="{{ trans('global.add') }}"></i></a>
                        </div>
                    </div>
                    <div>
                        {!!trans('front.alert_events_tip')!!}
                    </div>
                </div>
                <table class="table table-bordered table-condensed alerts-events-list form-group">
                    @if (!empty($item->events_custom))
                        @foreach ($item->events_custom as $event_custom)
                            <tr class="alert-event event_{!!$event_custom->id!!}">
                                <input type="hidden" name="events_custom[]" value="{!!$event_custom->id!!}">
                                <td class="text-center">{!!$event_custom->protocol!!}</td>
                                <td class="text-center">{!!$event_custom->message!!}</td>
                                <td class="text-center"><a href="javascript:" class="alert-delete-item close center"><span aria-hidden="true">×</span></a></td>
                            </tr>
                        @endforeach
                    @endif
                </table>
            </div>
        </div>
    {!!Form::close()!!}
@stop

@section('buttons')
    <button type="button" class="btn btn-action update">{!!trans('global.save')!!}</button>
    <button class="btn btn-default" data-target="#deleteAlert" data-toggle="modal">{!!trans('global.delete')!!}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.cancel')!!}</button>
@stop