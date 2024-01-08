@extends('Frontend.Layouts.modal')

@section('title')
    {{ trans('global.edit') }}
@stop

@section('body')
    {!! Form::open(['route' => 'my_account.update', 'method' => 'PUT', 'class' => 'form']) !!}
        {!! Form::hidden('id', $item->id) !!}
        <input style="display:none" type="text" name="fakeusernameremembered"/>
        <input style="display:none" type="password" name="fakepasswordremembered"/>

        <div class="form-group">
            {!! Form::label('email', trans('validation.attributes.email').'*:') !!}
            {!! Form::text('email', $item->email, ['class' => 'form-control']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('password', trans('validation.attributes.password').'*:') !!}
            {!! Form::password('password', ['class' => 'form-control']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('password_confirmation', trans('validation.attributes.password_confirmation').'*:') !!}
            {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
        </div>
    {!! Form::close() !!}
@stop