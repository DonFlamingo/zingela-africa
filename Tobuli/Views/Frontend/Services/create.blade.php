@extends('Frontend.Layouts.modal')

@section('title', trans('front.add_service'))

@section('body')
    {!!Form::open(['route' => 'services.store', 'method' => 'POST'])!!}
        {!!Form::hidden('id')!!}
        {!!Form::hidden('device_id', $device_id)!!}
        <div class="form-group">
            {!!Form::label('name', trans('validation.attributes.name').':')!!}
            {!!Form::text('name', null, ['class' => 'form-control'])!!}
        </div>
        <div class="form-group">
            {!!Form::label('expiration_by', trans('validation.attributes.expiration_by').':')!!}
            {!!Form::select('expiration_by', $expiration_by, 1, ['class' => 'form-control'])!!}
        </div>
        <div class="form-group">
            {!!Form::label('interval', trans('validation.attributes.interval').':')!!}
            {!!Form::text('interval', null, ['class' => 'form-control'])!!}
        </div>
        <div class="form-group">
            {!!Form::label('last_service', trans('validation.attributes.last_service').':')!!}
            {!!Form::text('last_service', null, ['class' => 'form-control service-datepicker'])!!}
        </div>
        <div class="form-group">
            {!!Form::label('trigger_event_left', trans('validation.attributes.trigger_event_left').':')!!}
            {!!Form::text('trigger_event_left', null, ['class' => 'form-control'])!!}
        </div>
        <div class="form-group">
            <div class="checkbox">
                {!!Form::checkbox('renew_after_expiration', 1, null)!!}
                {!!Form::label('renew_after_expiration', trans('validation.attributes.renew_after_expiration'))!!}
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6">
                <div class="form-group">
                    {!!Form::label('current_odometer', trans('validation.attributes.current_odometer').':')!!}
                    {!!Form::text('current_odometer', $odometer_value, ['class' => 'form-control', 'disabled' => 'disabled'])!!}
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6">
                <div class="form-group">
                    {!!Form::label('current_engine_hours', trans('validation.attributes.current_engine_hours').':')!!}
                    {!!Form::text('current_engine_hours', $engine_hours_value, ['class' => 'form-control', 'disabled' => 'disabled'])!!}
                </div>
            </div>
        </div>
        <div class="form-group">
            {!!Form::label('email', trans('validation.attributes.email').':')!!}
            {!!Form::text('email', null, ['class' => 'form-control'])!!}
        </div>
        @if (Auth::User()->sms_gateway)
            <div class="form-group">
                {!!Form::label('mobile_phone', trans('validation.attributes.mobile_phone').':')!!}
                {!!Form::text('mobile_phone', null, ['class' => 'form-control'])!!}
            </div>
        @endif
    {!!Form::close()!!}
@stop