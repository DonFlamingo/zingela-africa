@extends('Frontend.Layouts.default')

@section('header-menu-items')
@stop

@section('content')
    @if (Session::has('message'))
        <div class="alert alert-danger alert-dismissible">
            {!! Session::get('message') !!}
        </div>
    @endif
    @if (Session::has('success'))
        <div class="alert alert-success alert-dismissible">
            {!! Session::get('success') !!}
        </div>
    @endif

    <h1>{!! trans('front.renew_upgrade') !!}</h1>

    <div class="plans">
        @foreach($plans as $plan)
            <div class="plan-col">
                <div class="plan">
                    <div class="plan-heading">
                        <div class="plan-title">{{ $plan->title }}</div>
                    </div>
                    <div class="plan-body">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>{{ trans('validation.attributes.objects') }}</td>
                                    <td>{{ $plan->objects }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans('front.duration') }}</td>
                                    <td>{{ $plan->duration_value }} {{ trans('front.'.$plan->duration_type) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans('validation.attributes.price') }}</td>
                                    <td>{{ float($plan->price) }} {{ strtoupper(settings('main_settings.paypal_currency')) }}</td>
                                </tr>
                                @foreach ($permissions as $perm => $value)
                                    <tr>
                                        <td>{{ trans('front.'.$perm) }}</td>
                                        <td><i class="icon check {{ $plan->perm($perm, 'view') ? '' : 'disabled' }}"></i></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="plan-footer">
                        @if (!is_null(Auth::User()->billing_plan) && Auth::User()->billing_plan->objects <= $plan->objects)
                            <a href="{{ route('payments.checkout', $plan->id) }}" class="btn btn-action btn-plan">{{ $plan->id == Auth::User()->billing_plan_id ? trans('front.renew') : trans('front.upgrade') }}</a>
                        @else
                            <button class="btn btn-default">{{ trans('front.upgrade') }}</button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@stop