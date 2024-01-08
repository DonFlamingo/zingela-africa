@extends('Frontend.Layouts.modal')

@section('title', trans('global.edit'))

@section('body')
    {!!Form::open(['route' => 'user_sms_templates.update', 'method' => 'PUT'])!!}
        {!!Form::hidden('id', $item->id)!!}
        <div class="form-group">
            {!!Form::label('title', trans('validation.attributes.title').':')!!}
            {!!Form::text('title', $item->title, ['class' => 'form-control'])!!}
        </div>

        <div class="form-group">
            {!!Form::label('message', trans('validation.attributes.message').':')!!}
            {!!Form::textarea('message', $item->message, ['class' => 'form-control', 'rows' => 3])!!}
        </div>
    {!!Form::close()!!}
@stop