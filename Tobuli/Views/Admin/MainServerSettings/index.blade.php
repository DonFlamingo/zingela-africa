@extends('Admin.Layouts.default')

@section('content')
    @if (Session::has('errors'))
        <div class="alert alert-danger">
            <ul>
                @foreach (Session::get('errors')->all() as $error)
                    <li>{!! $error !!}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="row">
        @if (Auth::User()->group_id == 1)
        <div class="col-sm-6">
            <div class="panel panel-default">

                <div class="panel-heading">
                    <div class="panel-title"><i class="icon setup"></i> {{ trans('front.main_server_settings') }}</div>
                </div>

                <div class="panel-body">
                    {!! Form::open(array('route' => 'admin.main_server_settings.save', 'method' => 'POST', 'class' => 'form form-horizontal', 'id' => 'main-settings-form')) !!}

                    <div class="form-group">
                        {!! Form::label('allow_users_registration', trans('validation.attributes.server_name'), ['class' => 'col-xs-12 control-label"']) !!}
                        <div class="col-xs-12">
                            {!! Form::text('server_name', $settings['server_name'], ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12">
                            {!! Form::label(null, trans('validation.attributes.default_maps').':') !!}
                            <div class="checkboxes">
                                {!! Form::hidden('available_maps') !!}
                                @foreach ($maps as $id => $title)
                                    <div class="checkbox">
                                        {!! Form::checkbox('default_maps['.$id.']', $id, isset($settings['available_maps'][$id])) !!}
                                        {!! Form::label(null, $title) !!}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="form-group">
                        {!! Form::label('default_language', trans('validation.attributes.default_language'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::select('default_language', $langs, $settings['default_language'], ['class' => 'form-control', 'data-icon' => 'icon globe']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('default_date_format', trans('validation.attributes.default_date_format'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::select('default_date_format', $date_formats, $settings['default_date_format'], ['class' => 'form-control', 'data-icon' => 'icon calendar']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('default_time_format', trans('validation.attributes.default_time_format'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::select('default_time_format', $time_formats, $settings['default_time_format'], ['class' => 'form-control', 'data-icon' => 'icon calendar']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('default_unit_of_distance', trans('validation.attributes.default_unit_of_distance'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::select('default_unit_of_distance', $units_of_distance, $settings['default_unit_of_distance'], ['class' => 'form-control', 'data-icon' => 'icon unit-distance']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('default_unit_of_capacity', trans('validation.attributes.default_unit_of_capacity'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::select('default_unit_of_capacity', $units_of_capacity, $settings['default_unit_of_capacity'], ['class' => 'form-control', 'data-icon' => 'icon unit-capacity']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('default_unit_of_altitude', trans('validation.attributes.default_unit_of_altitude'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::select('default_unit_of_altitude', $units_of_altitude, $settings['default_unit_of_altitude'], ['class' => 'form-control', 'data-icon' => 'icon unit-altitude']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('default_object_online_timeout', trans('validation.attributes.default_object_online_timeout'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::select('default_object_online_timeout', $object_online_timeouts, $settings['default_object_online_timeout'], ['class' => 'form-control', 'data-icon' => 'icon time']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('default_map', trans('validation.attributes.default_map'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::select('default_map', $maps, $settings['default_map'], ['class' => 'form-control', 'data-icon' => 'icon map']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('map_zoom_level', trans('validation.attributes.map_zoom_level'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::select('map_zoom_level', $zoom_levels, settings('main_settings.map_zoom_level'), ['class' => 'form-control', 'data-icon' => 'icon search']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('map_center_latitude', trans('validation.attributes.map_center_latitude'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('map_center_latitude', settings('main_settings.map_center_latitude'), ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('map_center_longitude', trans('validation.attributes.map_center_longitude'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('map_center_longitude', settings('main_settings.map_center_longitude'), ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('geocoder_api', trans('validation.attributes.geocoder_api'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::select('geocoder_api', $geocoder_apis, settings('main_settings.geocoder_api'), ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group api_key">
                        {!! Form::label('api_key', trans('validation.attributes.api_key'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('api_key', settings('main_settings.api_key'), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group api_url">
                        {!! Form::label('api_url', trans('validation.attributes.api_url'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('api_url', settings('main_settings.api_url'), ['class' => 'form-control', 'placeholder' => 'http://yourdomain.com/nominatim/reverse.php']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('geocoder_cache_enabled', trans('validation.attributes.geocoder_cache'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::select('geocoder_cache_enabled', $geocoder_cache_status, settings('main_settings.geocoder_cache_enabled'), ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    @if (settings('main_settings.geocoder_cache_enabled'))
                        <div class="form-group">
                            {!! Form::label('geocoder_cache_days', trans('validation.attributes.geocoder_cache_days'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                            <div class="col-xs-12 col-sm-8">
                                {!! Form::select('geocoder_cache_days', $geocoder_cache_days, settings('main_settings.geocoder_cache_days'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        @if (isset($geocoder_cache_server_uptime) && (int)$geocoder_cache_server_uptime > 0)
                            <div class="form-group">
                                {!! Form::label(null, trans('validation.attributes.geocoder_cache_delete'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                                <div class="col-xs-12 col-sm-8">
                                    <button type="submit" class="btn btn-default btn-block" form="delete-geocoder-cache-form">
                                        <i class="icon trash"></i>
                                        Uptime: {{ gmdate("j", $geocoder_cache_server_uptime) }} days
                                    </button>
                                </div>
                            </div>
                        @endif
                    @endif
                    {!! Form::close() !!}
                    {!! Form::open(array('route' => 'admin.main_server_settings.delete_geocoder_cache', 'method' => 'POST', 'id' => 'delete-geocoder-cache-form')) !!}
                    {!! Form::close() !!}
                </div>

                <div class="panel-footer">
                    <button type="submit" class="btn btn-action" onClick="$('#main-settings-form').submit();">{{ trans('global.save') }}</button>
                </div>
            </div>
        </div>
        @endif

        <div class="col-sm-6">
            @if (Session::has('logo_errors'))
                <div class="alert alert-danger">
                    <ul>
                        @foreach (Session::get('logo_errors')->all() as $error)
                            <li>{!! $error !!}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title"><i class="icon stylize-1"></i> {{ trans('validation.attributes.logo') }}</div>
                </div>

                <div class="panel-body">
                    @if (Auth::User()->group_id == 3)
                        <div class="alert alert-info">
                            {{ trans('front.your_login_url') }}: {{ route('login', Auth::User()->id) }}
                        </div>
                    @endif

                    {!! Form::open(array('route' => 'admin.main_server_settings.logo_save', 'method' => 'POST', 'class' => 'form form-horizontal', 'enctype' => 'multipart/form-data', 'id' => 'logos-form')) !!}

                        <div class="row">
                            <div class="col-xs-6">
                                <div class="form-group">
                                    {!! Form::label(null, trans('validation.attributes.frontpage_logo'), ['class' => 'col-xs-12 control-label"']) !!}
                                    <div class="col-xs-12">
                                        <div class="form-image">
                                            <div class="form-image-controls">
                                                <label for="frontpage_logo" class="btn btn-default"><i class="icon upload"></i></label>
                                                {{--
                                                <button class="btn btn-default"><i class="icon delete"></i></button>
                                                --}}
                                            </div>
                                            @if (has_asset_logo('logo'))
                                                <img src="{{ asset_logo('logo') }}" alt="Logo" class="img-responsive">
                                            @endif
                                            <img src="{{ asset('assets/img/no-image.jpg') }}" class="no-image img-responsive">
                                            {!! Form::file('frontpage_logo', ['class' => 'hidden', 'id' => 'frontpage_logo']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-6">
                                <div class="form-group">
                                    {!! Form::label(null, trans('validation.attributes.favicon') . ' (16x16 .ICO)', ['class' => 'col-xs-12 control-label"']) !!}
                                    <div class="col-xs-12">
                                        <div class="form-image">
                                            <div class="form-image-controls">
                                                <label for="favicon" class="btn btn-default"><i class="icon upload"></i></label>
                                                {{--
                                                <button class="btn btn-default"><i class="icon delete"></i></button>
                                                --}}
                                            </div>
                                            @if (has_asset_logo('favicon'))
                                                <img src="{{ asset_logo('favicon') }}" alt="Logo" class="img-responsive">
                                            @endif
                                            <img src="{{ asset('assets/img/no-image.jpg') }}" class="no-image img-responsive">
                                            {!! Form::file('favicon', ['class' => 'hidden', 'id' => 'favicon']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if (Auth::User()->group_id == 1)
                            <div class="col-xs-6">
                                <div class="form-group">
                                    {!! Form::label('template_color', trans('validation.attributes.template_color'), ['class' => 'col-xs-12 control-label"']) !!}
                                    <div class="col-xs-12">
                                        {!! Form::select('template_color', config('tobuli.template_colors'), settings('main_settings.template_color'), ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-xs-6">
                                <div class="form-group">
                                    {!! Form::label(null, trans('validation.attributes.login_page_logo'), ['class' => 'col-xs-12 control-label"']) !!}
                                    <div class="col-xs-12">
                                        <div class="form-image">
                                            <div class="form-image-controls">
                                                <label for="login_page_logo" class="btn btn-default"><i class="icon upload"></i></label>
                                                {{--
                                                <button class="btn btn-default"><i class="icon delete"></i></button>
                                                --}}
                                            </div>
                                            @if (has_asset_logo('logo-main'))
                                            <img src="{{ asset_logo('logo-main') }}" alt="Logo" class="img-responsive">
                                            @endif
                                            <img src="{{ asset('assets/img/no-image.jpg') }}" class="no-image img-responsive">
                                            {!! Form::file('login_page_logo', ['class' => 'hidden', 'id' => 'login_page_logo']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-6">
                                <div class="form-group">
                                    {!! Form::label(null, trans('validation.attributes.background'), ['class' => 'col-xs-12 control-label"']) !!}
                                    <div class="col-xs-12">
                                        <div class="form-image">
                                            <div class="form-image-controls">
                                                <label for="background" class="btn btn-default"><i class="icon upload"></i></label>
                                                {{--
                                                <button class="btn btn-default"><i class="icon delete"></i></button>
                                                --}}
                                            </div>
                                            @if (has_asset_logo('background'))
                                            <img src="{{ asset_logo('background') }}" alt="Logo" class="img-responsive">
                                            @endif
                                            <img src="{{ asset('assets/img/no-image.jpg') }}" class="no-image img-responsive">
                                            {!! Form::file('background', ['class' => 'hidden', 'id' => 'background']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if (Auth::User()->group_id == 1)
                            <div class="col-xs-6">
                                <div class="form-group">
                                    {!! Form::label('login_page_text_color', trans('validation.attributes.login_page_text_color'), ['class' => 'col-xs-12 control-label"']) !!}
                                    <div class="col-xs-12">
                                        {!! Form::text('login_page_text_color', settings('main_settings.login_page_text_color'), ['class' => 'form-control colorpicker']) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-6">
                                <div class="form-group">
                                    {!! Form::label('login_page_background_color', trans('validation.attributes.login_page_background_color'), ['class' => 'col-xs-12 control-label"']) !!}
                                    <div class="col-xs-12">
                                        {!! Form::text('login_page_background_color', settings('main_settings.login_page_background_color'), ['class' => 'form-control colorpicker']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('welcome_text', trans('validation.attributes.welcome_text'), ['class' => 'col-xs-12 control-label"']) !!}
                            <div class="col-xs-12">
                                {!! Form::text('welcome_text', settings('main_settings.welcome_text'), ['class' => 'form-control']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('bottom_text', trans('validation.attributes.bottom_text'), ['class' => 'col-xs-12 control-label"']) !!}
                            <div class="col-xs-12">
                                {!! Form::text('bottom_text', settings('main_settings.bottom_text'), ['class' => 'form-control']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('apple_store_link', trans('validation.attributes.apple_store_link'), ['class' => 'col-xs-12 control-label"']) !!}
                            <div class="col-xs-12">
                                {!! Form::text('apple_store_link', settings('main_settings.apple_store_link'), ['class' => 'form-control', 'placeholder' => 'http://']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('google_play_link', trans('validation.attributes.google_play_link'), ['class' => 'col-xs-12 control-label"']) !!}
                            <div class="col-xs-12">
                                {!! Form::text('google_play_link', settings('main_settings.google_play_link'), ['class' => 'form-control', 'placeholder' => 'http://']) !!}
                            </div>
                        </div>
                        @endif
                    {!! Form::close() !!}
                </div>

                <div class="panel-footer">
                    <button type="submit" class="btn btn-action" onClick="$('#logos-form').submit();">{{ trans('global.save') }}</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <script>
        $(document).ready(function() {
            $(document).on('change', 'select[name="geocoder_api"]', function () {
                var val = $(this).val();

                $('.api_key').hide();
                $('.api_url').hide();

                if (val == 'google' || val == 'geocodio' || val == 'locationiq') {
                    $('.api_key').show();
                }
                if (val == 'nominatim') {
                    $('.api_url').show();
                }
            });
            $('select[name="geocoder_api"]').trigger('change');

            $(document).on('change', 'select[name="template_color"]', function () {
                _url = '{{ asset('assets/css') }}/' + $(this).val() + '.css';

                $("head").append('<link id="new-css" href="'+_url+'" type="text/css" rel="stylesheet" />');
            });
        });


    </script>
@stop