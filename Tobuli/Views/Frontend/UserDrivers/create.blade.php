@extends('Frontend.Layouts.modal')

@section('title')
    {!!trans('front.add_driver')!!}
@stop

@section('body')
    {!!Form::open(['route' => 'user_drivers.store', 'method' => 'POST'])!!}
        {!!Form::hidden('id')!!}
        <div class="form-group">
            {!!Form::label('name', trans('validation.attributes.name').'*:')!!}
            {!!Form::text('name', null, ['class' => 'form-control'])!!}
        </div>
        <div class="form-group">
            {!!Form::label('device_id', trans('validation.attributes.device_id').'*:')!!}
            {!!Form::select('device_id', $devices, null, ['class' => 'form-control', 'data-live-search' => true])!!}
        </div>
        <div class="form-group">
            {!!Form::label('rfid', trans('validation.attributes.rfid').':')!!}
            {!!Form::text('rfid', null, ['class' => 'form-control'])!!}
        </div>
        <div class="form-group">
            {!!Form::label('phone', trans('validation.attributes.phone').':')!!}
            {!!Form::text('phone', null, ['class' => 'form-control'])!!}
        </div>
        <div class="form-group">
            {!!Form::label('email', trans('validation.attributes.email').':')!!}
            {!!Form::text('email', null, ['class' => 'form-control'])!!}
        </div>
        <div class="form-group">
            {!!Form::label('description', trans('validation.attributes.description').':')!!}
            {!!Form::textarea('description', null, ['class' => 'form-control', 'rows' => 2])!!}
        </div>
    {!!Form::close()!!}
@stop