@extends('Frontend.Layouts.modal')

@section('title')
    {!!trans('front.choose_language')!!}
@stop

@section('body')
<div class="lang-list">
    @foreach ($languages as $key => $lg)
    <div class="lang-item">
        <a href="{{ route('my_account_settings.change_lang', $key) }}" class="btn btn-default btn-block">
            <img src="{{ asset("assets/img/flag/$key.png") }}" /> {{ $lg }}
        </a>
    </div>
    @endforeach
</div>

<p>Coming soon</p>

<div class="lang-list">
    @foreach ($soon as $key => $lg)
    <div class="lang-item">
        <a href="javascript" class="btn btn-default btn-block disabled">
            <img src="{{ asset("assets/img/flag/$key.png") }}" /> {{ $lg }}
        </a>
    </div>
    @endforeach
</div>
@stop

@section('buttons')
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.close')!!}</button>
@stop