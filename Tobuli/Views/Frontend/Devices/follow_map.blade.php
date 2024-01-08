<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ settings('main_settings.server_name') }}</title>

    <base href="{{ url('/') }}">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@if ($_ENV['server'] == 'gpsbait') GPSBait, Law Enforcement Grade GPS Tracking Hardware and Software @else GPS Tracking System for Personal Use or Business @endif">
    <link rel="shortcut icon" href="{{ asset_logo('favicon') }}" type="image/x-icon">

    <link rel="stylesheet" href="{{ asset('assets/css/'.settings('main_settings.template_color').'.css?v='.config('tobuli.version')) }}">

    <script src="{{ asset('assets/js/core.js?v='.config('tobuli.version')) }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/app.js?v='.config('tobuli.version')) }}" type="text/javascript"></script>

    @yield('styles')

</head>
<body>
@include('Frontend.Layouts.partials.loading')

<div id="map"></div>

@include('Frontend.Layouts.partials.trans')
@include('Frontend.Layouts.partials.app')

<script>
    $(window).on("load", function() {
        //$('a[href="#gps-device-street-view-large"]').addClass('disabled');

        app.follow({!! json_encode($item) !!});
    });
</script>
</body>