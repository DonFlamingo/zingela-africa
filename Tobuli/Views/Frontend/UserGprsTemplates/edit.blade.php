@extends('Frontend.Layouts.modal')

@section('title', trans('global.edit'))

@section('body')
    {!!Form::open(['route' => 'user_gprs_templates.update', 'method' => 'PUT'])!!}
        {!!Form::hidden('id', $item->id)!!}
        <div class="form-group" style="margin-top: 0">
            {!!Form::label('title', trans('validation.attributes.title').':')!!}
            {!!Form::text('title', $item->title, ['class' => 'form-control'])!!}
        </div>

        <div class="form-group">
            {!!Form::label('message', trans('validation.attributes.message').':')!!}
            {!!Form::textarea('message', $item->message, ['class' => 'form-control', 'rows' => 3])!!}
        </div>

        <div class="alert alert-info small">
            {!! trans('front.raw_command_supports') !!}
            <br><br>
            {!! trans('front.gprs_template_variables') !!}
        </div>
    {!!Form::close()!!}
@stop