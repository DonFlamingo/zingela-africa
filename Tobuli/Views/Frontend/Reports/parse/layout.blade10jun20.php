<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" dir="{{ config('app.locale') == 'ar' ? 'RTL' : 'LTR' }}" >
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $data['format'] != 'xls' ? $_ENV['title'] : 'Report' }}</title>
    @if ($data['format'] != 'xls')
        @include('Frontend.Reports.styles')
    @endif
    @yield('scripts')
</head>
<body class="reports">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
            @if ($data['format'] != 'xls' && isset($data['logo']))
                @if ($data['format'] == 'html')
                        <header>
                            <div class="header-left">
                                <div class="report-wrap">
                                    <div class="report-logo" style="background: none;">
                                        <img src="{!! asset_logo('logo') !!}" class="logo" alt="Logo">
                                    </div>
                                </div>
                                <div class="report-curve">

                                </div>
                            </div>
                            <div class="header-right">

                            </div>
                        </header>
                @else
                        <img src="{!! asset_logo('logo') !!}" class="logo" alt="Logo">
                @endif
            @endif
            @yield('content')
            </div>
        </div>
    </div>
</body>
</html>