@extends('Admin.Layouts.modal')

@section('title')
    {{ trans('global.edit') }}
@stop

@section('body')
    {!! Form::open(array('route' => 'admin.admins.update', 'method' => 'PUT')) !!}
    <input style="display:none" type="text" name="fakeusernameremembered"/>
    <input style="display:none" type="password" name="fakepasswordremembered"/>
    {!! Form::hidden('id', $item->id) !!}
    <!-- Active field -->
    <div class="form-group">
        {!! Form::label('active', trans('validation.attributes.active').':') !!}
        {!! Form::checkbox('active', 1, $item->active) !!}
    </div>
    <!-- Email Form Input -->
    <div class="form-group">
        {!! Form::label('email', trans('validation.attributes.email').':') !!}
        {!! Form::text('email', $item->email, ['class' => 'form-control']) !!}
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