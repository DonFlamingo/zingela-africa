<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ settings('main_settings.server_name') }}</title>

    <base href="{{ url('/') }}">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="GPS Fleet Tracking Application">
    <link rel="shortcut icon" href="{{ asset_logo('favicon') }}" type="image/x-icon">

    <link rel="stylesheet" href="{{ asset('assets/css/'.settings('main_settings.template_color').'.css?v='.config('tobuli.version')) }}">

    @yield('styles')

    <style>
        body {
            overflow: hidden;
        }
    </style>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-19127801-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-19127801-1');

  gtag('set', {'user_id': '{{Auth::user()->first_name}} {{Auth::user()->last_name}}'}); // Set the user ID using signed-in user_id.
</script>

</head>

<body>


@include('Frontend.Layouts.partials.loading')
@include('Frontend.Layouts.partials.header')


<div id="sidebar">

    <a class="btn-collapse" onclick="app.changeSetting('toggleSidebar');"><i></i></a>

    <div class="sidebar-content">
        <ul class="nav nav-tabs nav-default">
            <li role="presentation" class="active">
                <a href="#objects_tab" type="button" data-toggle="tab">{!!trans('front.objects')!!}</a>
            </li>
            <li role="presentation">
                <a href="#events_tab" type="button" data-toggle="tab">{!!trans('front.events')!!}</a>
            </li>
            <li role="presentation">
                <a href="#history_tab" type="button" data-toggle="tab">{!!trans('front.history')!!}</a>
            </li>
            {{-- hidden, import for correct tab work (shown, hidden evenets) --}}
            <li role="presentation" class="hidden"><a href="#alerts_tab" data-toggle="tab"></a></li>
            <li role="presentation" class="hidden"><a href="#geofencing_tab" data-toggle="tab"></a></li>
            <li role="presentation" class="hidden"><a href="#geofencing_create" data-toggle="tab"></a></li>
            <li role="presentation" class="hidden"><a href="#geofencing_edit" data-toggle="tab"></a></li>
            <li role="presentation" class="hidden"><a href="#routes_tab" data-toggle="tab"></a></li>
            <li role="presentation" class="hidden"><a href="#routes_create" data-toggle="tab"></a></li>
            <li role="presentation" class="hidden"><a href="#routes_edit" data-toggle="tab"></a></li>
            <li role="presentation" class="hidden"><a href="#map_icons_tab" data-toggle="tab"></a></li>
            <li role="presentation" class="hidden"><a href="#map_icons_create" data-toggle="tab"></a></li>
            <li role="presentation" class="hidden"><a href="#map_icons_edit" data-toggle="tab"></a></li>
        </ul>

        @yield('items')
    </div>
</div>

<div id="mapWrap">
    <div id="map"></div>
    <div id="map-controls">
        <div>
            <div class="btn-group-vertical" role="group">
                <button type="button" class="btn" onclick="app.mapFull();">
                    <span class="icon map-expand"></span>
                </button>
            </div>
        </div>

        <div>
            <div class="btn-group-vertical" data-position="fixed" role="group">
                <button type="button" class="btn" onClick="$('.leaflet-control-layers').toggleClass('leaflet-control-layers-expanded');">
                    <span class="icon map-change"></span>
                </button>
            </div>
        </div>

        <div>
            <div class="btn-group-vertical" role="group">
                <button type="button" class="btn" onclick="app.zoomIn();"><span class="icon zoomIn"></span></button>
                <button type="button" class="btn" onclick="app.zoomOut();"><span class="icon zoomOut"></span></button>
            </div>
        </div>

        <div id="map-controls-layers">
            <div class="btn-group-vertical" role="group" data-toggle="buttons">
                <label class="btn" data-toggle="tooltip" data-placement="left" title="Toggle Cluster">
                    <input id="clusterDevice" type="checkbox" autocomplete="off" onchange="app.changeSetting('clusterDevice', this.checked);">
                    <span class="icon group-devices"></span>
                </label>
                <label class="btn" data-toggle="tooltip" data-placement="left" title="{!!trans('front.fit_objects')!!}">
                    <input id="fitBounds" type="checkbox" autocomplete="off" onchange="app.devices.toggleFitBounds(this.checked);">
                    <span class="icon fitBounds"></span>
                </label>
                <label class="btn" data-toggle="tooltip" data-placement="left" title="{!!trans('front.objects')!!}">
                    <input id="showDevice" type="checkbox" autocomplete="off" onchange="app.changeSetting('showDevice', this.checked);">
                    <span class="icon devices"></span>
                </label>
                <label class="btn" data-toggle="tooltip" data-placement="left" title="{!!trans('front.geofences')!!}">
                    <input id="showGeofences" type="checkbox" autocomplete="off" onchange="app.changeSetting('showGeofences', this.checked);">
                    <span class="icon geofences"></span>
                </label>
                <label class="btn" data-toggle="tooltip" data-placement="left" title="{!!trans('front.routes')!!}">
                    <input id="showRoutes" type="checkbox" autocomplete="off" onchange="app.changeSetting('showRoutes', this.checked);">
                    <span class="icon routes"></span>
                </label>
                <label class="btn" data-toggle="tooltip" data-placement="left" title="{!!trans('front.poi')!!}">
                    <input id="showPoi" type="checkbox" autocomplete="off" onchange="app.changeSetting('showPoi', this.checked);">
                    <span class="icon poi"></span>
                </label>
                <label class="btn" data-toggle="tooltip" data-placement="left" title="{!!trans('front.show_names')!!}">
                    <input id="showNames" type="checkbox" autocomplete="off" onchange="app.changeSetting('showNames', this.checked);">
                    <span class="icon show-name"></span>
                </label>
                <label class="btn" data-toggle="tooltip" data-placement="left" title="{!!trans('front.show_tails')!!}">
                    <input id="showTail" type="checkbox" autocomplete="off" onchange="app.changeSetting('showTail', this.checked);">
                    <span class="icon show-tail"></span>
                </label>
                <label class="btn" data-toggle="tooltip" data-placement="left" title="{!!trans('front.live_traffic')!!}">
                    <input id="showTraffic" type="checkbox" autocomplete="off" onchange="app.changeSetting('showTraffic', this.checked);">
                    <span class="icon traffic"></span>
                </label>
            </div>
        </div>

        <div id="history-control-layers" style="display: none;">
            <div class="btn-group-vertical" role="group" data-toggle="buttons">
                <label class="btn" data-toggle="tooltip" data-placement="left" title="{!!trans('front.route')!!}">
                    <input id="showHistoryRoute" type="checkbox" autocomplete="off" onchange="app.changeSetting('showHistoryRoute', this.checked);">
                    <span class="icon routes"></span>
                </label>
                <label class="btn" data-toggle="tooltip" data-placement="left" title="{!!trans('front.arrows')!!}">
                    <input id="showHistoryArrow" type="checkbox" autocomplete="off" onchange="app.changeSetting('showHistoryArrow', this.checked);">
                    <span class="icon device"></span>
                </label>
                <label class="btn" data-toggle="tooltip" data-placement="left" title="{!!trans('front.stops')!!}">
                    <input id="showHistoryStop" type="checkbox" autocomplete="off" onchange="app.changeSetting('showHistoryStop', this.checked);">
                    <span class="icon stop"></span>
                </label>
                <label class="btn" data-toggle="tooltip" data-placement="left" title="{!!trans('front.events')!!}">
                    <input id="showHistoryEvent" type="checkbox" autocomplete="off" onchange="app.changeSetting('showHistoryEvent', this.checked);">
                    <span class="icon event"></span>
                </label>
            </div>
        </div>
    </div>
</div>

@include('Frontend.Objects.partials.devicePopup')
<a class="ajax-popup-link hidden"></a>
<input id="upload_file" type="file" style="display: none;" onchange=""/>

@include('Frontend.Layouts.partials.trans')
<script src="{{ asset('assets/js/core.js?v='.config('tobuli.version')) }}" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fixed-header-table/1.3.0/jquery.fixedheadertable.min.js" type="text/javascript"></script>
<script src="{{ asset('assets/js/app.js?v='.config('tobuli.version')) }}" type="text/javascript"></script>

<div id="bottombar">
    @include('Frontend.History.bottom')
    @include('Frontend.Widgets.index')
</div>

{{--
<script type="text/javascript">
    var handlers = L.drawLocal.draw.handlers;
    handlers.polygon.tooltip.start = '{{ trans('front.click_to_start_drawing_shape') }}';
    handlers.polygon.tooltip.cont = '{{ trans('front.click_to_continue_drawing_shape') }}';
    handlers.polygon.tooltip.end = '{{ trans('front.click_first_point_to_close_this_shape') }}';
    handlers.polyline.error = '{{ trans('front.shape_edges_cannot_cross') }}';
    handlers.polyline.tooltip.start = '{{ trans('front.click_to_start_drawing_line') }}';
    handlers.polyline.tooltip.cont = '{{ trans('front.click_to_continue_drawing_line') }}';
    handlers.polyline.tooltip.end = '{{ trans('front.click_last_point_to_finish_line') }}';
</script>
--}}

@yield('scripts')

@include('Frontend.Layouts.partials.app')
{{--
<script src="http://jscrollpane.kelvinluck.com/script/jquery.jscrollpane.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="http://jscrollpane.kelvinluck.com/style/jquery.jscrollpane.css">
--}}

<script type="text/javascript">
    $(window).on("load", function() {
        app.init();
    });
</script>
</body>
</html>