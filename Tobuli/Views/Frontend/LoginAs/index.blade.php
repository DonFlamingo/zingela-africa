@extends('Frontend.Layouts.frontend')

@section('content')
    <h1 class="sign-in-text text-center">
        {{ trans('front.sign_in') }} {{ strtoupper($sub) }}
    </h1>

    <div class="panel">
        <div class="panel-body">

            @if ( has_asset_logo('logo-main') )
                <a href="{{ route('home') }}">
                    <img class="img-responsive center-block" src="{{ asset_logo('logo-main') }}" alt="Logo">
                </a>
            @endif

            <hr>

            @if (Session::has('success'))
                <div class="alert alert-success alert-dismissible">
                    {!! Session::get('success') !!}
                </div>
            @endif

            @if (Session::has('message'))
                <div class="alert alert-danger alert-dismissible">
                    {!! Session::get('message') !!}
                </div>
            @endif

            {!! Form::open(array('route' => 'loginaspost', 'class' => 'form')) !!}
            <div class="form-group">
                {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.email'), 'id' => 'sign-in-form-email']) !!}
            </div>
            <div class="form-group">
                {!! Form::password('password', ['class' => 'form-control', 'placeholder' => trans('validation.attributes.password'), 'id' => 'sign-in-form-password']) !!}
            </div>

            <button class="btn btn-lg btn-primary btn-block"  name="Submit" value="Login" type="Submit">{!! trans('front.sign_in') !!}</button>

            {!! Form::close() !!}
        </div>
    </div>
@stop