@extends('Admin.Layouts.modal')

@section('title')
    {{ trans('global.add_new') }}
@stop

@section('body')
    {!! Form::open(array('route' => 'admin.admins.store', 'method' => 'POST')) !!}
    <input style="display:none" type="text" name="fakeusernameremembered"/>
    <input style="display:none" type="password" name="fakepasswordremembered"/>
    {!! Form::hidden('id') !!}
    <!-- Active field -->
    <div class="form-group">
        {!! Form::label('active', trans('validation.attributes.active').':') !!}
        {!! Form::checkbox('active', 1, 1) !!}
    </div>
    <!-- Email Form Input -->
    <div class="form-group">
        {!! Form::label('email', trans('validation.attributes.email').':') !!}
        {!! Form::text('email', null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        <h3>{{ trans('admin.password_change') }}</h3>
    </div>

    <!-- Password field -->
    <div class="form-group">
        {!! Form::label('password', trans('validation.attributes.password').':') !!}
        {!! Form::password('password', ['class' => 'form-control']) !!}
        {!! error_for('password', $errors) !!}
    </div>

    <!-- Password_confirmation field -->
    <div class="form-group">
        {!! Form::label('password_confirmation', trans('validation.attributes.password_confirmation').':') !!}
        {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
        {!! error_for('password_confirmation', $errors) !!}
    </div>

    {!! Form::close() !!}
@stop