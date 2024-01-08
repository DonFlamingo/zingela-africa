@extends('Frontend.Layouts.modal')

@section('title')
    {!!trans('global.edit')!!}
@stop

@section('body')
    {!!Form::open(['route' => 'user_drivers.update', 'method' => 'PUT'])!!}
    {!!Form::hidden('id', $item->id)!!}
    <div class="form-group">
        {!!Form::label('name', trans('validation.attributes.name').'*:')!!}
        {!!Form::text('name', $item->name, ['class' => 'form-control'])!!}
    </div>
    <div class="form-group">
        {!!Form::label('device_id', trans('validation.attributes.device_id').'*:')!!}
        {!!Form::select('device_id', $devices, $item->device_id, ['class' => 'form-control', 'data-live-search' => true])!!}
    </div>
    <div class="form-group">
        {!!Form::label('rfid', trans('validation.attributes.rfid').':')!!}
        {!!Form::text('rfid', $item->rfid, ['class' => 'form-control'])!!}
    </div>
    <div class="form-group">
        {!!Form::label('phone', trans('validation.attributes.phone').':')!!}
        {!!Form::text('phone', $item->phone, ['class' => 'form-control'])!!}
    </div>
    <div class="form-group">
        {!!Form::label('email', trans('validation.attributes.email').':')!!}
        {!!Form::text('email', $item->email, ['class' => 'form-control'])!!}
    </div>
    <div class="form-group">
        {!!Form::label('description', trans('validation.attributes.description').':')!!}
        {!!Form::textarea('description', $item->description, ['class' => 'form-control', 'rows' => 2])!!}
    </div>
    {!!Form::close()!!}
@stop