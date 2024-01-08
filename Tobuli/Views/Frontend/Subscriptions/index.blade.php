@extends('Frontend.Layouts.modal')

@section('title')
{{ trans('front.subscriptions') }}
@stop

@section('body')
    <div class="form-group">
        First Name: {{ Auth::User()->first_name }}
    </div>
    <div class="form-group">
        Last Name: {{ Auth::User()->last_name }}
    </div>
    <div class="form-group">
        {{ trans('validation.attributes.email') }}: {{ Auth::User()->email }}
    </div>

    @if (!is_null(Auth::User()->billing_plan_id))
        <div class="form-group">
            {{ trans('front.plan') }}: {{ Auth::User()->billing_plan->title  }}
            <a href="{{ route('subscriptions.renew') }}" class="btn btn-action btn-xs " style="margin-bottom: 10px;">{{ trans('front.renew_upgrade') }}</a>
        </div>
    @endif

    <div class="form-group">
        {{ trans('validation.attributes.devices_limit') }}: {{ is_null(Auth::User()->devices_limit) ? trans('front.unlimited') : Auth::User()->devices_limit  }}
    </div>

    @if (Auth::User()->subscription_expiration != '0000-00-00 00:00:00')
    <div class="form-group">
        {{ trans('front.expiration_date') }}: {{ datetime(Auth::User()->subscription_expiration) }} ({{ $days_left }} {{ trans('front.days_left') }})
    </div>
    @endif
@stop

@section('buttons')
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.close') }}</button>
@stop