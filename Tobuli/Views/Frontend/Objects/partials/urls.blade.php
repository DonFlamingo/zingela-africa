<script>

    window.objects_url = '{{ route('objects.items') }}';
    window.objects_stop_time = '{!!route('objects.stop_time')!!}';
    window.check_objects_url = '{{ route('objects.items_json') }}';
    window.delete_objects_url = '{{ route('objects.destroy') }}';
    window.device_change_active = '{{ route('devices.change_active') }}';
    window.device_group_status_change_url = '{{ route('objects.change_group_status') }}';

    window.listview_url = '{{ route('objects.listview') }}';
    window.listview_items_url = '{{ route('objects.listview.items') }}';

    window.geofences_url = '{{ route('geofences.index') }}';
    window.geofence_change_active = '{{ route('geofences.change_active') }}';
    window.geofence_delete_url = '{{ route('geofences.destroy') }}';

    window.routes_url = '{{ route('routes.index') }}';
    window.route_change_active = '{{ route('routes.change_active') }}';
    window.route_delete_url = '{{ route('routes.destroy') }}';

    window.alerts_url = '{{ route('alerts.index') }}';
    window.alert_change_active = '{{ route('alerts.change_active') }}';
    window.alert_delete_url = '{{ route('alerts.destroy') }}';
    window.alert_get_events = '{{ route('custom_events.get_events') }}';
    window.alert_get_protocols = '{{ route('custom_events.get_protocols') }}';

    window.history_url = '{{ route('history.index') }}';
    window.history_positions_url = '{{ route('history.positions') }}';
    window.history_positions_delete_url = '{{ route('history.delete_positions') }}';
    window.history_export_url = '{{ route('history.export') }}';

    window.events_url = '{{ route('events.index') }}';

    window.assets_url = '{{ asset('') }}'

    window.map_icons_url = '{{ route('map_icons.index') }}';
    window.map_icons_change_active = '{{ route('map_icons.change_active') }}';
    window.map_icons_delete_url = '{{ route('map_icons.destroy') }}';
    window.map_icons_list = '{{ route('map_icons.list') }}';

    window.change_map_url = '{{ route('my_account.change_map') }}';
    window.change_map_settings_url = '{!! route('my_account_settings.change_map_settings') !!}';

    window.geo_address_url = '{{ route('api.geo_address') }}';

    window.geofence_group_status_change_url = '{{ route('geofences_groups.change_status') }}';
    window.geofences_export_type_url = '{{ route('geofences.export_type') }}';
    window.geofences_import_url = '{{ route('geofences.import') }}';
    window.change_toolbar_top_status_url = '{!! route('my_account_settings.change_top_toolbar') !!}';
    window.change_map_settings_url = '{!! route('my_account_settings.change_map_settings') !!}';
</script>