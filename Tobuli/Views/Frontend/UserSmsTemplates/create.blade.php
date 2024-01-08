@extends('Frontend.Layouts.modal')

@section('title', trans('front.add_template'))

@section('body')
    {!!Form::open(['route' => 'user_sms_templates.store', 'method' => 'POST'])!!}
        {!!Form::hidden('id')!!}
        <div class="form-group">
            {!!Form::label('title', trans('validation.attributes.title').':')!!}
            {!!Form::text('title', null, ['class' => 'form-control'])!!}
        </div>

        <div class="form-group">
            {!!Form::label('message', trans('validation.attributes.message').':')!!}
            {!!Form::textarea('message', null, ['class' => 'form-control', 'rows' => 3])!!}
        </div>
    {!!Form::close()!!}
@stop