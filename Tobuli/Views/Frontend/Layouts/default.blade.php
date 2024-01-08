<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ settings('main_settings.server_name') }}</title>

    <base href="{{ url('/') }}">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="GPS Tracking System for Personal Use or Business">
    <link rel="shortcut icon" href="{{ asset_logo('favicon') }}" type="image/x-icon">

    <link rel="stylesheet" href="{{ asset('assets/css/'.settings('main_settings.template_color').'.css?v='.config('tobuli.version')) }}">

    @yield('styles')
</head>
<body>

<div id="header" class="folded">
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                @if ( has_asset_logo('logo') )
                    <a class="navbar-brand" href="/" title="{{ settings('main_settings.server_name') }}"><img src="{{ asset_logo('logo') }}"></a>
                @endif
            </div>

            <ul class="nav navbar-nav navbar-right">

                @yield('header-menu-items')

                <li class="language-selection">
                    <a href="javascript:" data-url="{{ route('subscriptions.languages') }}" data-modal="language-selection">
                        <img src="{!!asset('assets/img/flag/'.(Session::has('language') ? Session::get('language') : Auth::user()->lang).'.png')!!}" alt="Language" class="img-thumbnail">
                    </a>
                </li>
            </ul>


        </div>
    </nav>
</div>

<div class="content">
    <div class="container-fluid">
        @yield('content')
    </div>
</div>


@include('Frontend.Layouts.partials.trans')
@include('Frontend.Objects.partials.urls')

<script src="{{ asset('assets/js/core.js?v='.config('tobuli.version')) }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/app.js?v='.config('tobuli.version')) }}" type="text/javascript"></script>

@yield('scripts')

</body>
</html>