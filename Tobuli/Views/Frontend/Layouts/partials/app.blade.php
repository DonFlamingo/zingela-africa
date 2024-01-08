<script type="text/javascript">
    app.debug = {{ env('APP_DEBUG', false) ? 'true' : 'false' }};
    app.version = '{{ config('tobuli.version') }}';
    app.offlineTimeout = {{ settings('main_settings.default_object_online_timeout') * 60 }};

    app.show_object_info_after = {{ settings('plugins.show_object_info_after.status') }};
    app.object_listview = {{ settings('plugins.object_listview.status') }};

    app.urls = {
        asset:                  '{{ asset('') }}',
        check:                  '{{ route('objects.items_json') }}',
        streetView:             '{{ asset('streetview') }}',
        geoAddress:             '{{ route('api.geo_address') }}',

        events:                 '{{ route('events.index') }}',

        history:                '{{ route('history.index') }}',
        historyGeofence:          '{{ route('history.geofence') }}',
		historyExport:          '{{ route('history.export') }}',
        historyPositions:       '{{ route('history.positions') }}',
        historyHistory:       '{{ route('history.history') }}',
        historyPositionsDelete: '{{ route('history.delete_positions') }}',

        devices:                '{{ route('objects.items') }}',
        deviceDelete:           '{{ route('objects.destroy') }}',
        deviceChangeActive:     '{{ route('devices.change_active') }}',
        deviceToggleGroup:      '{{ route('objects.change_group_status') }}',
        deviceStopTime:         '{{ route('objects.stop_time') }}/',
        deviceFollow:           '{{ route('devices.follow_map') }}/',
        devicesSensorCreate:    '{{ route('sensors.create') }}/',
        devicesServiceCreate:   '{{ route('services.create') }}/',

        geofences:              '{{ route('geofences.index') }}',
        geofenceChangeActive:   '{{ route('geofences.change_active') }}',
        geofenceDelete:         '{{ route('geofences.destroy') }}',
        geofencesExportType:    '{{ route('geofences.export_type') }}',
        geofencesImport:        '{{ route('geofences.import') }}',
        geofenceToggleGroup:    '{{ route('geofences_groups.change_status') }}',

        routes:                 '{{ route('routes.index') }}',
        routeChangeActive:      '{{ route('routes.change_active') }}',
        routeDelete:            '{{ route('routes.destroy') }}',

        alerts:                 '{{ route('alerts.index') }}',
        alertChangeActive:      '{{ route('alerts.change_active') }}',
        alertDelete:            '{{ route('alerts.destroy') }}',
        alertGetEvents:         '{{ route('custom_events.get_events') }}',
        alertGetProtocols:      '{{ route('custom_events.get_protocols') }}',

        mapIcons:               '{{ route('map_icons.index') }}',
        mapIconsDelete:         '{{ route('map_icons.destroy') }}',
        mapIconsChangeActive:   '{{ route('map_icons.change_active') }}',
        mapIconsList:           '{{ route('map_icons.list') }}',

        changeMap:              '{{ route('my_account.change_map') }}',
        changeMapSettings:      '{{ route('my_account_settings.change_map_settings') }}',

        clearQueue:             '{{ route('sms_gateway.clear_queue') }}',

        listView:               '{{ route('objects.listview') }}',
        listViewItems:          '{{ route('objects.listview.items') }}'
    };

    window.distance_unit_hour = '{{ Auth::User()->distance_unit_hour }}';

    app.settings.weekStart = '{{ Auth::User()->week_start_day }}';

    app.settings.mapCenter = [parseFloat('{{ settings('main_settings.map_center_latitude') }}'), parseFloat('{{ settings('main_settings.map_center_longitude') }}')];
    app.settings.mapZoom = {{ settings('main_settings.map_zoom_level') }};
    app.settings.user_id = '{{ Auth::User()->id }}';
    app.settings.map_id = '{{ Auth::User()->map_id }}';
    app.settings.availableMaps = {!! json_encode(Auth::User()->available_maps) !!};

    app.settings.toggleSidebar  = false;
    app.settings.showDevice     = {{ Auth::User()->map_controls->m_objects ? 'true' : 'false' }};
    app.settings.showGeofences  = {{ Auth::User()->map_controls->m_geofences ? 'true' : 'false' }};
    app.settings.showRoutes     = {{ Auth::User()->map_controls->m_routes ? 'true' : 'false' }};
    app.settings.showPoi        = {{ Auth::User()->map_controls->m_poi ? 'true' : 'false' }};
    app.settings.showTail       = {{ Auth::User()->map_controls->m_show_tails ? 'true' : 'false' }};
    app.settings.showNames      = {{ Auth::User()->map_controls->m_show_names ? 'true' : 'false' }};
    app.settings.showTraffic    = false;

    app.settings.showHistoryRoute = {{ Auth::User()->map_controls->history_control_route ? 'true' : 'false' }};
    app.settings.showHistoryArrow = {{ Auth::User()->map_controls->history_control_arrows ? 'true' : 'false' }};
    app.settings.showHistoryStop  = {{ Auth::User()->map_controls->history_control_stops ? 'true' : 'false' }};
    app.settings.showHistoryEvent = {{ Auth::User()->map_controls->history_control_events ? 'true' : 'false' }};

    app.settings.keys.google = '{{ env('google_api_key', 'AIzaSyDG5ZheVmnPJbn5t0hsEF8e8ZRG-k_X0Xc') }}';

	app.notification = {
    	logo: '{{ asset_logo('favicon') }}'
    };
</script>
