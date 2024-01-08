@extends('Frontend.Layouts.frontend')

@section('content')
    <h1 class="sign-in-text text-center">
        @if ( settings('main_settings.welcome_text') )
            {!! settings('main_settings.welcome_text') !!}
        @else
            {!! trans('front.sign_in') !!}
        @endif
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

            {!! Form::open(array('route' => 'authentication.store', 'class' => 'form')) !!}
                <div class="form-group">
                    {!! Form::text('user_name', null, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.email'), 'id' => 'sign-in-form-email']) !!}
                </div>
                <div class="form-group">
                    {!! Form::password('password', ['class' => 'form-control', 'placeholder' => trans('validation.attributes.password'), 'id' => 'sign-in-form-password']) !!}
                </div>

                <div class="form-group">
                    <div class="checkbox">
                        {!! Form::checkbox('remember_me', 1, ['id' => 'sign-in-form-remember']) !!}
                        <label>{!! trans('validation.attributes.remember_me') !!}</label>
                    </div>
                </div>

                <button class="btn btn-lg btn-primary btn-block"  name="Submit" value="Login" type="Submit">{!! trans('front.sign_in') !!}</button>

                <hr>

                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-12 col-sm-6">
                            <a href="{!! route('password_reminder.create') !!}" class="btn btn-block btn-lg btn-default">{!! trans('front.cant_sign_in') !!}</a>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            @if (settings('main_settings.allow_users_registration'))
                                <a href="{!! route('registration.create') !!}" class="btn btn-block btn-lg btn-default">{!! trans('front.not_a_member') !!}</a>
                            @endif
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
        </div>
    </div>

    @if ( settings('main_settings.google_play_link') || settings('main_settings.apple_store_link') )
        <div class="app-links">
            @if ( settings('main_settings.google_play_link') )
                <div class="col-xs-6">
                    <a href="{{ settings('main_settings.google_play_link') }}" target="_blank"><img src="{{ asset('assets/img/google-play.png') }}" class="img-responsive" /></a>
                </div>
            @endif

            @if ( settings('main_settings.apple_store_link') )
                <div class="col-xs-6">
                    <a href="{{ settings('main_settings.apple_store_link') }}" target="_blank"><img src="{{ asset('assets/img/apple-store.png') }}" class="img-responsive" /></a>
                </div>
            @endif
            <div class="clearfix"></div>
        </div>

    @endif

    @if ( settings('main_settings.bottom_text') )
        <p class="sign-in-text">{!! settings('main_settings.bottom_text') !!}</p>
    @endif
@stop