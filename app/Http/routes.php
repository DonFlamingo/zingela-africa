<?php
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;

require_once 'global.php';
//checkLogin();

if ( env('forceSchema', false) ) {
    URL::forceSchema( env('forceSchema') );
}

# Authentication
Route::group(['middleware' => 'server_active'], function() {
    Route::get('/', ['as' => 'home', 'uses' => function () {
        if (Auth::check())
            return Redirect::route('objects.index');
        else
            return Redirect::route('authentication.create');
    }]);

    Route::get('login/{id?}', ['as' => 'login', 'uses' => 'Frontend\LoginController@create']);
    Route::any('authentication/store', ['as' => 'authentication.store', 'uses' => 'Frontend\LoginController@store']);
    Route::resource('authentication', 'Frontend\LoginController', ['only' => ['create', 'destroy']]);

    Route::resource('password_reminder', 'Frontend\PasswordReminderController', ['only' => ['create', 'store']]);
    Route::get('password/reset/{token}', array('uses' => 'Frontend\PasswordReminderController@reset', 'as' => 'password_reminder.reset'));
    Route::post('password/reset/{token}', array('uses' => 'Frontend\PasswordReminderController@update', 'as' => 'password_reminder.update'));
    if (settings('main_settings.allow_users_registration'))
        Route::resource('registration', 'Frontend\RegistrationController', ['only' => ['create', 'store']]);

    # GPS data
    Route::any('gpsdata_insert', ['as' => 'gpsdata_insert', 'uses' => 'Frontend\GpsDataController@insert']);

    Route::get('demo', ['as' => 'demo', 'uses' => 'Frontend\LoginController@demo']);
	
	Route::get('supported_devices', ['as' => 'supported_devices', 'uses' => 'Frontend\LoginController@supporteddevices']);
	
});


// Authenticated Frontend |active_subscription
Route::group(['middleware' => ['auth','active_subscription','server_active'], 'namespace' => 'Frontend'], function () {
    Route::resource('objects', 'ObjectsController', ['only' => ['index']]);
    Route::delete('objects/destroy/{objects}', ['as' => 'objects.destroy', 'uses' => 'DevicesController@destroy']);
    Route::get('objects/items', ['as' => 'objects.items', 'uses' => 'ObjectsController@items']);
    Route::get('objects/items_json', ['as' => 'objects.items_json', 'uses' => 'ObjectsController@itemsJson']);
    Route::get('objects/change_group_status', ['as' => 'objects.change_group_status', 'uses' => 'ObjectsController@changeGroupStatus']);
    Route::get('objects/change_alarm_status', ['as' => 'objects.change_alarm_status', 'uses' => 'ObjectsController@changeAlarmStatus']);
    Route::get('objects/alarm_position', ['as' => 'objects.alarm_position', 'uses' => 'ObjectsController@alarmPosition']);
    Route::get('objects/show_address', ['as' => 'objects.show_address', 'uses' => 'ObjectsController@showAddress']);
    Route::get('objects/stop_time/{id?}', ['as' => 'objects.stop_time', 'uses' => 'DevicesController@stopTime']);
    Route::get('objects/find_assets/vehicle', ['as' => 'objects.find_assets.vehicle', 'uses' => 'ObjectsController@findAssetsVehicle']);
    Route::get('objects/find_assets/poi', ['as' => 'objects.find_assets.poi', 'uses' => 'ObjectsController@findAssetsPoi']);

    Route::get('objects/list', ['as' => 'objects.listview', 'uses' => 'ObjectsListController@index']);
    Route::get('objects/list/items', ['as' => 'objects.listview.items', 'uses' => 'ObjectsListController@items']);
    Route::get('objects/list/settings', ['as' => 'objects.listview_settings.edit', 'uses' => 'ObjectsListController@edit']);
    Route::post('objects/list/settings', ['as' => 'objects.listview_settings.update', 'uses' => 'ObjectsListController@update']);

    # Geofences
    Route::get('geofences/export', ['as' => 'geofences.export', 'uses' => 'GeofencesController@export']);
    Route::get('geofences/export_type', ['as' => 'geofences.export_type', 'uses' => 'GeofencesController@exportType']);
    Route::resource('geofences', 'GeofencesController');
    Route::post('geofences/change_active', ['as' => 'geofences.change_active', 'uses' => 'GeofencesController@changeActive']);
    Route::post('geofences/export_create', ['as' => 'geofences.export_create', 'uses' => 'GeofencesController@exportCreate']);
    Route::post('geofences/import', ['as' => 'geofences.import', 'uses' => 'GeofencesController@import']);

    # Geofences groups
    Route::get('geofences_groups/update_select', ['as' => 'geofences_groups.update_select', 'uses' => 'GeofencesGroupsController@updateSelect']);
    Route::get('geofences_groups/change_status', ['as' => 'geofences_groups.change_status', 'uses' => 'GeofencesGroupsController@changeStatus']);
    Route::resource('geofences_groups', 'GeofencesGroupsController');

    # Routes
    Route::resource('routes', 'RoutesController');
    Route::post('routes/change_active', ['as' => 'routes.change_active', 'uses' => 'RoutesController@changeActive']);

    # Devices
    Route::get('devices/edit/{id}/{admin?}', ['as' => 'devices.edit', 'uses' => 'DevicesController@edit']);
    Route::resource('devices', 'DevicesController', ['except' => ['index', 'edit']]);
    Route::post('devices/change_active', ['as' => 'devices.change_active', 'uses' => 'DevicesController@changeActive']);
    Route::get('devices/follow_map/{id?}', ['as' => 'devices.follow_map', 'uses' => 'DevicesController@followMap']);

    # Alerts
    Route::resource('alerts', 'AlertsController');
    Route::put('alerts/update/{id?}', ['as' => 'alerts.update', 'uses' => 'AlertsController@update']);
    Route::get('alerts/do_destroy/{id}', ['as' => 'alerts.do_destroy', 'uses' => 'AlertsController@doDestroy']);
    Route::delete('alerts/destroy/{id?}', ['as' => 'alerts.destroy', 'uses' => 'AlertsController@destroy']);
    Route::post('alerts/change_active', ['as' => 'alerts.change_active', 'uses' => 'AlertsController@changeActive']);

    # History
    Route::get('history', ['as' => 'history.index', 'uses' => 'HistoryController@index']);
    Route::get('history/positions', ['as' => 'history.positions', 'uses' => 'HistoryController@positionsPaginated']);
    Route::get('history/history', ['as' => 'history.history', 'uses' => 'HistoryController@positionsPaginatedHistory']);
    Route::get('history/do_delete_positions', ['as' => 'history.do_delete_positions', 'uses' => 'HistoryController@doDeletePositions']);
    Route::any('history/delete_positions', ['as' => 'history.delete_positions', 'uses' => 'HistoryController@deletePositions']);

	Route::get('history/export', ['as' => 'history.export', 'uses' => 'HistoryExportController@generate']);
	Route::get('history/download/{file}/{name}', ['as' => 'history.download', 'uses' => 'HistoryExportController@download']);
	
	Route::get('history/geofence', ['as' => 'history.geofence', 'uses' => 'HistoryController@getGeofenceHistory']);
	
    # Events
    Route::get('events', ['as' => 'events.index', 'uses' => 'EventsController@index']);
    if (App::environment() == 'staging')
        Route::get('notifications', ['as' => 'events.index', 'uses' => 'EventsController@index']);
    Route::get('events/do_destroy', ['as' => 'events.do_destroy', 'uses' => 'EventsController@doDestroy']);
    Route::delete('events/destroy', ['as' => 'events.destroy', 'uses' => 'EventsController@destroy']);

    # Map Icons
    Route::get('map_icons/import', ['as' => 'map_icons.import', 'uses' => 'MapIconsController@import_form']);
    Route::post('map_icons/import', ['as' => 'map_icons.import', 'uses' => 'MapIconsController@import']);
    Route::get('map_icons/list', ['as' => 'map_icons.list', 'uses' => 'MapIconsController@iconsList']);
    Route::resource('map_icons', 'MapIconsController');
    Route::post('map_icons/change_active', ['as' => 'map_icons.change_active', 'uses' => 'MapIconsController@changeActive']);

    # Reports
    Route::any('reports/update', ['as' => 'reports.update', 'uses' => 'ReportsController@update']);
    Route::get('reports/do_destroy/{id}', ['as' => 'reports.do_destroy', 'uses' => 'ReportsController@doDestroy']);
    Route::resource('reports', 'ReportsController', ['except' => ['edit', 'update']]);

	# Report Logs
    Route::any('reports/logs', ['as' => 'reports.logs', 'uses' => 'ReportsController@logs']);
    Route::any('reports/log/download/{id}', ['as' => 'reports.log_download', 'uses' => 'ReportsController@logDownload']);
	Route::any('reports/log/destroy', ['as' => 'reports.log_destroy', 'uses' => 'ReportsController@logDestroy']);

    # My account
    Route::post('my_account/change_map', ['as' => 'my_account.change_map', 'uses' => 'MyAccountController@changeMap']);
    Route::resource('my_account', 'MyAccountController', ['only' => ['edit', 'update']]);
    Route::resource('email_confirmation', 'EmailConfirmationController', ['only' => ['edit', 'update']]);
    Route::get('email_confirmation/resend', ['as' => 'email_confirmation.resend_code', 'uses' => 'EmailConfirmationController@resendActivationCode']);
    Route::post('email_confirmation/resend', ['as' => 'email_confirmation.resend_code_submit', 'uses' => 'EmailConfirmationController@resendActivationCodeSubmit']);
    Route::get('my_account_settings/change_language/{lang}', ['as' => 'my_account_settings.change_lang', 'uses' => 'MyAccountSettingsController@changeLang']);

    # User drivers
    Route::resource('user_drivers', 'UserDriversController');
    Route::get('user_drivers/do_destroy/{id}', ['as' => 'user_drivers.do_destroy', 'uses' => 'UserDriversController@doDestroy']);

    # Sensors
    Route::resource('sensors', 'SensorsController', ['only' => ['store', 'edit', 'update', 'destroy']]);
    Route::get('sensors/do_destroy/{id}', ['as' => 'sensors.do_destroy', 'uses' => 'SensorsController@doDestroy']);
    Route::get('sensors/create/{device_id?}', ['as' => 'sensors.create', 'uses' => 'SensorsController@create']);
    Route::get('sensors/index/{device_id}', ['as' => 'sensors.index', 'uses' => 'SensorsController@index']);

    # Services
    Route::resource('services', 'ServicesController', ['only' => ['store', 'edit', 'update', 'destroy']]);
    Route::get('services/do_destroy/{id}', ['as' => 'services.do_destroy', 'uses' => 'ServicesController@doDestroy']);
    Route::get('services/create/{device_id?}', ['as' => 'services.create', 'uses' => 'ServicesController@create']);
    Route::get('services/index/{device_id}', ['as' => 'services.index', 'uses' => 'ServicesController@index']);

    # Expenses
    Route::get('expenses', ['as' => 'expenses.index', 'uses' => 'ExpensesController@index']);
    Route::post('expenses/store', ['as' => 'expenses.store', 'uses' => 'ExpensesController@store']);
    Route::get('expenses/create', ['as' => 'expenses.create', 'uses' => 'ExpensesController@create']);
    Route::get('expenses/do_destroy/{id}', ['as' => 'expenses.do_destroy', 'uses' => 'ExpensesController@doDestroy']);
    Route::delete('expenses/destroy', ['as' => 'expenses.destroy', 'uses' => 'ExpensesController@destroy']);

    # Custom events
    Route::resource('custom_events', 'CustomEventsController');
    Route::get('custom_events/do_destroy/{id}', ['as' => 'custom_events.do_destroy', 'uses' => 'CustomEventsController@doDestroy']);
    Route::post('custom_events/get_events', ['as' => 'custom_events.get_events', 'uses' => 'CustomEventsController@getEvents']);
    Route::post('custom_events/get_protocols', ['as' => 'custom_events.get_protocols', 'uses' => 'CustomEventsController@getProtocols']);

    # User sms templates
    Route::resource('user_sms_templates', 'UserSmsTemplatesController');
    Route::get('user_sms_templates/do_destroy/{id}', ['as' => 'user_sms_templates.do_destroy', 'uses' => 'UserSmsTemplatesController@doDestroy']);
    Route::post('user_sms_templates/get_message', ['as' => 'user_sms_templates.get_message', 'uses' => 'UserSmsTemplatesController@getMessage']);

    # User gprs templates
    Route::resource('user_gprs_templates', 'UserGprsTemplatesController');
    Route::get('user_gprs_templates/do_destroy/{id}', ['as' => 'user_gprs_templates.do_destroy', 'uses' => 'UserGprsTemplatesController@doDestroy']);
    Route::post('user_gprs_templates/get_message', ['as' => 'user_gprs_templates.get_message', 'uses' => 'UserGprsTemplatesController@getMessage']);

    Route::get('membership/languages', ['as' => 'subscriptions.languages', 'uses' => 'SubscriptionsController@languages']);

    #My account settings
    Route::resource('my_account_settings', 'MyAccountSettingsController', ['only' => ['edit', 'update']]);
    Route::get('my_account_settings/change_top_toolbar', ['as' => 'my_account_settings.change_top_toolbar', 'uses' => 'MyAccountSettingsController@changeTopToolbar']);
    Route::get('my_account_settings/change_map_settings', ['as' => 'my_account_settings.change_map_settings', 'uses' => 'MyAccountSettingsController@changeMapSettings']);


    # Send command
    Route::resource('send_command', 'SendCommandController', ['only' => ['create', 'store']]);
    Route::post('send_command/gprs', ['as' => 'send_command.gprs', 'uses' => 'SendCommandController@gprsStore']);
    Route::get('send_command/get_device_sim_number', ['as' => 'send_command.get_device_sim_number', 'uses' => 'SendCommandController@getDeviceSimNumber']);

    # SMS gateway
    Route::get('sms_gateway/test_sms', ['as' => 'sms_gateway.test_sms', 'uses' => 'SmsGatewayController@testSms']);
    Route::post('sms_gateway/send_test_sms', ['as' => 'sms_gateway.send_test_sms', 'uses' => 'SmsGatewayController@sendTestSms']);
    Route::get('sms_gateway/clear_queue', ['as' => 'sms_gateway.clear_queue', 'uses' => 'SmsGatewayController@clearQueue']);

    Route::get('membership', ['as' => 'subscriptions.index', 'uses' => 'SubscriptionsController@index']);
    Route::get('membership/pricing', ['as' => 'subscriptions.pricing', 'uses' => 'SubscriptionsController@pricing']);

    Route::get('logout', ['as' => 'logout', 'uses' => 'LoginController@destroy']);
});

// Authenticated Admin
Route::group(['prefix' => 'admin', 'middleware' => ['auth','auth.manager','server_active'], 'namespace' => 'Admin'], function () {
    Route::get('/', ['as' => 'admin', 'uses' => function () {
        return Redirect::route('admin.clients.index');
    }]);

    # Clients
    Route::get('users/clients/import_geofences', ['as' => 'admin.clients.import_geofences', 'uses' => 'ClientsController@importGeofences']);
    Route::post('users/clients/import_geofences', ['as' => 'admin.clients.import_geofences_set', 'uses' => 'ClientsController@importGeofencesSet']);
    Route::get('users/clients/import_map_icon', ['as' => 'admin.clients.import_map_icon', 'uses' => 'ClientsController@importMapIcon']);
    Route::post('users/clients/import_map_icon', ['as' => 'admin.clients.import_map_icon_set', 'uses' => 'ClientsController@importMapIconSet']);
    Route::any('users/clients', ['as' => 'admin.clients.index', 'uses' => 'ClientsController@index']);
    Route::any('users/clients/get_devices/{id}', ['as' => 'admin.clients.get_devices', 'uses' => 'ClientsController@getDevices']);
    Route::any('users/clients/get_permissions_table', ['as' => 'admin.clients.get_permissions_table', 'uses' => 'ClientsController@getPermissionsTable']);
    Route::resource('clients', 'ClientsController', ['except' => ['index']]);

    # Login as
    Route::get('login_as/{id}', ['as' => 'admin.clients.login_as', 'uses' => 'ClientsController@loginAs']);
    Route::get('login_as_agree/{id}', ['as' => 'admin.clients.login_as_agree', 'uses' => 'ClientsController@loginAsAgree']);

    # Objects
    Route::any('users/objects', ['as' => 'admin.objects.index', 'uses' => 'ObjectsController@index']);
    Route::resource('objects', 'ObjectsController', ['except' => ['index']]);

    # Main server settings
    Route::get('main_server_settings/index', ['as' => 'admin.main_server_settings.index', 'uses' => 'MainServerSettingsController@index']);
    Route::post('main_server_settings/logo_save', ['as' => 'admin.main_server_settings.logo_save', 'uses' => 'MainServerSettingsController@logoSave']);
});

# Payments
Route::get('payments/checkout/{id}', ['as' => 'payments.checkout', 'uses' => 'Frontend\PaymentsController@getCheckout', 'middleware' => ['auth']]);
Route::get('payments/get_done/{id}/{plan_id}', ['as' => 'payments.get_done', 'uses' => 'Frontend\PaymentsController@getDone']);
Route::get('payments/get_cancel', ['as' => 'payments.get_cancel', 'uses' => 'Frontend\PaymentsController@getCancel']);
Route::get('subscriptions/renew', ['as' => 'subscriptions.renew', 'uses' => 'Frontend\SubscriptionsController@renew', 'middleware' => ['auth']]);

Route::group(['prefix' => 'admin', 'middleware' => ['auth','auth.admin','server_active'], 'namespace' => 'Admin'], function () {
    # Billing
    Route::any('billing/index', ['as' => 'admin.billing.index', 'uses' => 'BillingController@index']);
    Route::any('billing/plans', ['as' => 'admin.billing.plans', 'uses' => 'BillingController@plans']);
    Route::post('billing/plan_store', ['as' => 'admin.billing.plan_store', 'uses' => 'BillingController@planStore']);
    Route::get('billing/billing_plans_form', ['as' => 'admin.billing.billing_plans_form', 'uses' => 'BillingController@billingPlansForm']);
    Route::resource('billing', 'BillingController', ['except' => ['index']]);
	Route::get('billing/{id}/destroy', ['as' => 'admin.billing.billing_plans_getDestroyOne', 'uses' => 'BillingController@showDestroyOne']);
	Route::post('billing/{id}/destroy', ['as' => 'admin.billing.billing_plans_destroyOne', 'uses' => 'BillingController@destroyOne']);

    # Events
    Route::any('events/index', ['as' => 'admin.events.index', 'uses' => 'EventsController@index']);
    Route::resource('events', 'EventsController', ['except' => ['index']]);

    # Email templates
    Route::any('email_templates/index', ['as' => 'admin.email_templates.index', 'uses' => 'EmailTemplatesController@index']);
    Route::resource('email_templates', 'EmailTemplatesController', ['except' => ['index', 'create', 'store']]);

    # Sms templates
    Route::any('sms_templates/index', ['as' => 'admin.sms_templates.index', 'uses' => 'SmsTemplatesController@index']);
    Route::resource('sms_templates', 'SmsTemplatesController', ['except' => ['index', 'create', 'store']]);

    # Map icons
    Route::any('map_icons/index', ['as' => 'admin.map_icons.index', 'uses' => 'MapIconsController@index']);
    Route::resource('map_icons', 'MapIconsController', ['only' => ['store', 'destroy']]);

    # Device icons
    Route::any('device_icons/index', ['as' => 'admin.device_icons.index', 'uses' => 'DeviceIconsController@index']);
    Route::resource('device_icons', 'DeviceIconsController', ['only' => ['store', 'destroy']]);

    # Logs
    Route::any('logs/index', ['as' => 'admin.logs.index', 'uses' => 'LogsController@index']);
    Route::resource('logs', 'LogsController', ['only' => ['edit', 'destroy']]);

    # Unregistered devices log
    Route::any('unregistered_devices_log/index', ['as' => 'admin.unregistered_devices_log.index', 'uses' => 'UnregisteredDevicesLogController@index']);
    Route::resource('unregistered_devices_log', 'UnregisteredDevicesLogController', ['only' => ['destroy']]);

    # Restart traccar
    Route::any('restart_traccar', ['as' => 'admin.restart_traccar', 'uses' => 'ObjectsController@restartTraccar']);

    # Email settings
    Route::get('email_settings/index', ['as' => 'admin.email_settings.index', 'uses' => 'EmailSettingsController@index']);
    Route::post('email_settings/save', ['as' => 'admin.email_settings.save', 'uses' => 'EmailSettingsController@save']);
    Route::get('email_settings/test_email', ['as' => 'admin.email_settings.test_email', 'uses' => 'EmailSettingsController@testEmail']);
    Route::post('email_settings/test_email_send', ['as' => 'admin.email_settings.test_email_send', 'uses' => 'EmailSettingsController@testEmailSend']);

    # Main server settings
    Route::post('main_server_settings/save', ['as' => 'admin.main_server_settings.save', 'uses' => 'MainServerSettingsController@save']);
    Route::post('main_server_settings/new_user_defaults_save', ['as' => 'admin.main_server_settings.new_user_defaults_save', 'uses' => 'MainServerSettingsController@newUserDefaultsSave']);
    Route::post('main_server_settings/delete_geocoder_cache', ['as' => 'admin.main_server_settings.delete_geocoder_cache', 'uses' => 'MainServerSettingsController@deleteGeocoderCache']);

    # Backups
    Route::get('backups/index', ['as' => 'admin.backups.index', 'uses' => 'BackupsController@index']);
    Route::get('backups/panel', ['as' => 'admin.backups.panel', 'uses' => 'BackupsController@panel']);
    Route::post('backups/save', ['as' => 'admin.backups.save', 'uses' => 'BackupsController@save']);
    Route::get('backups/test', ['as' => 'admin.backups.test', 'uses' => 'BackupsController@test']);
    Route::get('backups/logs', ['as' => 'admin.backups.logs', 'uses' => 'BackupsController@logs']);

    # Ports
    Route::any('ports/index', ['as' => 'admin.ports.index', 'uses' => 'PortsController@index']);
    Route::get('ports/do_update_config', ['as' => 'admin.ports.do_update_config', 'uses' => 'PortsController@doUpdateConfig']);
    Route::post('ports/update_config', ['as' => 'admin.ports.update_config', 'uses' => 'PortsController@updateConfig']);
    Route::get('ports/do_reset_default', ['as' => 'admin.ports.do_reset_default', 'uses' => 'PortsController@doResetDefault']);
    Route::post('ports/reset_default', ['as' => 'admin.ports.reset_default', 'uses' => 'PortsController@resetDefault']);
    Route::resource('ports', 'PortsController', ['only' => ['edit', 'update']]);

    # Translations
    Route::get('translations/check_trans', ['as' => 'admin.translations.check_trans', 'uses' => 'TranslationsController@checkTrans']);
    Route::get('translations/file_trans', ['as' => 'admin.translations.file_trans', 'uses' => 'TranslationsController@fileTrans']);
    Route::post('translations/save', ['as' => 'admin.translations.save', 'uses' => 'TranslationsController@save']);
    Route::resource('translations', 'TranslationsController', ['only' => ['index', 'show', 'edit', 'update']]);

	# Report Logs
    Route::any('report_logs/index', ['as' => 'admin.report_logs.index', 'uses' => 'ReportLogsController@index']);
    Route::resource('report_logs', 'ReportLogsController', ['only' => ['edit','destroy']]);

    # Sensor groups
    Route::any('sensor_groups/index', ['as' => 'admin.sensor_groups.index', 'uses' => 'SensorGroupsController@index']);
    Route::resource('sensor_groups', 'SensorGroupsController', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);

    Route::any('sensor_group_sensors/index/{id}/{ajax?}', ['as' => 'admin.sensor_group_sensors.index', 'uses' => 'SensorGroupSensorsController@index']);
    Route::get('sensor_group_sensors/create/{id}', ['as' => 'admin.sensor_group_sensors.create', 'uses' => 'SensorGroupSensorsController@create']);
    Route::resource('sensor_group_sensors', 'SensorGroupSensorsController', ['only' => ['store', 'edit', 'update', 'destroy']]);

    # Blocked ips
    Route::any('blocked_ips/index', ['as' => 'admin.blocked_ips.index', 'uses' => 'BlockedIpsController@index']);
    Route::get('ports/do_destroy/{id}', ['as' => 'admin.blocked_ips.do_destroy', 'uses' => 'BlockedIpsController@doDestroy']);
    Route::resource('blocked_ips', 'BlockedIpsController', ['only' => ['create', 'store', 'destroy']]);

    # Tools
    Route::any('tools/index', ['as' => 'admin.tools.index', 'uses' => 'ToolsController@index']);

    # DB clear
    Route::any('db_clear/panel', ['as' => 'admin.db_clear.panel', 'uses' => 'DatabaseClearController@panel']);
    Route::post('db_clear/save', ['as' => 'admin.db_clear.save', 'uses' => 'DatabaseClearController@save']);

    # Plugins
    Route::any('plugins/index', ['as' => 'admin.plugins.index', 'uses' => 'PluginsController@index']);
    Route::post('plugins/save', ['as' => 'admin.plugins.save', 'uses' => 'PluginsController@save']);
});

// API
Route::get('api/registration_status', function() {
    // return ['status' => settings('main_settings.allow_users_registration') ? 1 : 0];
    return ['status' => 1];
});

//Device Addition
Route::any('api/device/create', ['as' => "api.device_create" ,'uses' => '\App\Http\Controllers\Frontend\DevicesController@store'])->middleware(['api_auth', 'server_active', 'api_active', 'throttle:60,1']);

// register api
Route::post('user/api/register', ['as' => 'api.register', 'uses' => '\App\Http\Controllers\Frontend\RegistrationController@apiRegister','middleware' => ['server_active', 'api_active', 'throttle:60,1']]);
Route::any('api/login', ['as' => 'api.login', 'uses' => 'Frontend\ApiController@login', 'middleware' => ['server_active', 'api_active', 'throttle:60,1']]);
Route::any('api/geo_address', ['as' => 'api.geo_address', 'uses' => 'Frontend\ApiController@geoAddress', 'middleware' => ['server_active']]);
Route::group(['prefix' => 'api', 'middleware' => ['api_auth', 'server_active', 'api_active'], 'namespace' => 'Frontend'], function () {

    Route::get("logout",['as' => 'api.get_devices', 'uses' => 'ApiController@logout']);

    Route::any('get_devices', ['as' => 'api.get_devices', 'uses' => 'ApiController@getDevices']);
    Route::any('get_devices_latest', ['as' => 'api.get_devices_json', 'uses' => 'ApiController@getDevicesJson']);

    Route::any('add_device_data', ['as' => 'api.add_device_data', 'uses' => 'ApiController@DevicesController#create']);
    Route::any('add_device', ['as' => 'api.add_device', 'uses' => 'ApiController@DevicesController#store']);
    Route::any('get_device', ['as' => 'api.get_device', 'uses' => 'ApiController@getSingleDevice']);
    Route::any('edit_device_data', ['as' => 'api.edit_device_data', 'uses' => 'ApiController@DevicesController#edit']);
    Route::any('edit_device', ['as' => 'api.edit_device', 'uses' => 'ApiController@DevicesController#update']);
    Route::any('change_active_device', ['as' => 'api.change_active_device', 'uses' => 'ApiController@DevicesController#changeActive']);
    Route::any('destroy_device', ['as' => 'api.destroy_device', 'uses' => 'ApiController@DevicesController#destroy']);
    Route::get('change_alarm_status', ['as' => 'api.change_alarm_status', 'uses' => 'ApiController@ObjectsController#changeAlarmStatus']);
    Route::get('device_stop_time', ['as' => 'api.device_stop_time', 'uses' => 'ApiController@DevicesController#stopTime']);
    Route::get('alarm_position', ['as' => 'api.alarm_position', 'uses' => 'ApiController@ObjectsController#alarmPosition']);
    Route::any('set_device_expiration', ['as' => 'api.set_device_expiration', 'uses' => 'ApiController@setDeviceExpiration']);

    Route::any('get_sensors', ['as' => 'api.get_sensors', 'uses' => 'ApiController@SensorsController#index']);
    Route::any('add_sensor_data', ['as' => 'api.add_sensor_data', 'uses' => 'ApiController@SensorsController#create']);
    Route::any('add_sensor', ['as' => 'api.add_sensor', 'uses' => 'ApiController@SensorsController#store']);
    Route::any('edit_sensor_data', ['as' => 'api.edit_sensor_data', 'uses' => 'ApiController@SensorsController#edit']);
    Route::any('edit_sensor', ['as' => 'api.edit_sensor', 'uses' => 'ApiController@SensorsController#update']);
    Route::any('destroy_sensor', ['as' => 'api.destroy_sensor', 'uses' => 'ApiController@SensorsController#destroy']);
    Route::any('get_protocols', ['as' => 'api.get_protocols', 'uses' => 'ApiController@SensorsController#getProtocols']);
	Route::any('get_events_by_protocol', ['as' => 'api.get_events_by_protocol', 'uses' => 'ApiController@SensorsController#getEvents2']);

    Route::any('get_services', ['as' => 'api.get_services', 'uses' => 'ApiController@ServicesController#index']);
    Route::any('add_service_data', ['as' => 'api.add_service_data', 'uses' => 'ApiController@ServicesController#create']);
    Route::any('add_service', ['as' => 'api.add_service', 'uses' => 'ApiController@ServicesController#store']);
    Route::any('edit_service_data', ['as' => 'api.edit_service_data', 'uses' => 'ApiController@ServicesController#edit']);
    Route::any('edit_service', ['as' => 'api.edit_service', 'uses' => 'ApiController@ServicesController#update']);
    Route::any('destroy_service', ['as' => 'api.destroy_service', 'uses' => 'ApiController@ServicesController#destroy']);

    Route::any('get_events', ['as' => 'api.get_events', 'uses' => 'ApiController@EventsController#index']);
    Route::any('destroy_events', ['as' => 'api.destroy_events', 'uses' => 'ApiController@EventsController#destroy']);

    #get summary data
    Route::any('get_summery', ['as' => 'api.get_summery', 'uses' => 'ApiController@ReportsController#getSummary']);


    Route::any('get_history', ['as' => 'api.get_history', 'uses' => 'ApiController@HistoryController#index']);
    Route::any('get_history_messages', ['as' => 'api.get_history_messages', 'uses' => 'ApiController@HistoryController#positionsPaginated']);
    Route::any('delete_history_positions', ['as' => 'api.delete_history_positions', 'uses' => 'ApiController@HistoryController#deletePositions']);

    Route::any('get_alerts', ['as' => 'api.get_alerts', 'uses' => 'ApiController@AlertsController#index']);
    Route::any('add_alert_data', ['as' => 'api.add_alert_data', 'uses' => 'ApiController@AlertsController#create']);
    Route::any('add_alert', ['as' => 'api.add_alert', 'uses' => 'ApiController@AlertsController#store']);
    Route::any('edit_alert_data', ['as' => 'api.edit_alert_data', 'uses' => 'ApiController@AlertsController#edit']);
    Route::any('edit_alert', ['as' => 'api.edit_alert', 'uses' => 'ApiController@AlertsController#update']);
    Route::any('change_active_alert', ['as' => 'api.change_active_alert', 'uses' => 'ApiController@AlertsController#changeActive']);
    Route::any('destroy_alert', ['as' => 'api.destroy_alert', 'uses' => 'ApiController@AlertsController#destroy']);

    Route::any('get_geofences', ['as' => 'api.get_geofences', 'uses' => 'ApiController@GeofencesController#index']);
    Route::any('get_geofence', ['as' => 'api.get_geofence', 'uses' => 'ApiController@GeofencesController#getGeofence']);
    Route::any('add_geofence_data', ['as' => 'api.add_geofence_data', 'uses' => 'ApiController@GeofencesController#create']);
    Route::any('add_geofence', ['as' => 'api.add_geofence', 'uses' => 'ApiController@GeofencesController#store']);
    Route::any('edit_geofence', ['as' => 'api.edit_geofence', 'uses' => 'ApiController@GeofencesController#update']);
    Route::any('change_active_geofence', ['as' => 'api.change_active_geofence', 'uses' => 'ApiController@GeofencesController#changeActive']);
    Route::any('destroy_geofence', ['as' => 'api.destroy_geofence', 'uses' => 'ApiController@GeofencesController#destroy']);

    Route::any('get_routes', ['as' => 'api.get_routes', 'uses' => 'ApiController@RoutesController#index']);
    Route::any('add_route', ['as' => 'api.add_route', 'uses' => 'ApiController@RoutesController#store']);
    Route::any('edit_route', ['as' => 'api.edit_route', 'uses' => 'ApiController@RoutesController#update']);
    Route::any('change_active_route', ['as' => 'api.change_active_route', 'uses' => 'ApiController@RoutesController#changeActive']);
    Route::any('destroy_route', ['as' => 'api.destroy_route', 'uses' => 'ApiController@RoutesController#destroy']);

    Route::any('get_reports', ['as' => 'api.get_reports', 'uses' => 'ApiController@ReportsController#index']);
    Route::any('add_report_data', ['as' => 'api.add_report_data', 'uses' => 'ApiController@ReportsController#create']);
    Route::any('add_report', ['as' => 'api.add_report', 'uses' => 'ApiController@ReportsController#store']);
    Route::any('edit_report', ['as' => 'api.edit_report', 'uses' => 'ApiController@ReportsController#store']);
    Route::any('generate_report', ['as' => 'api.generate_report', 'uses' => 'ApiController@ReportsController#update']);
    Route::any('destroy_report', ['as' => 'api.destroy_report', 'uses' => 'ApiController@ReportsController#destroy']);
    //test megha
    Route::any('reports/update', ['as' => 'api.generate_report', 'uses' => 'ApiController@ReportsController#update']);

    Route::any('get_user_map_icons', ['as' => 'api.get_user_map_icons', 'uses' => 'ApiController@MapIconsController#index']);
    Route::any('get_map_icons', ['as' => 'api.get_map_icons', 'uses' => 'ApiController@MapIconsController#getIcons']);
    Route::any('add_map_icon', ['as' => 'api.add_map_icon', 'uses' => 'ApiController@MapIconsController#store']);
    Route::any('edit_map_icon', ['as' => 'api.edit_map_icon', 'uses' => 'ApiController@MapIconsController#update']);
    Route::any('change_active_map_icon', ['as' => 'api.change_active_map_icon', 'uses' => 'ApiController@MapIconsController#changeActive']);
    Route::any('destroy_map_icon', ['as' => 'api.destroy_map_icon', 'uses' => 'ApiController@MapIconsController#destroy']);

    Route::any('send_command_data', ['as' => 'api.send_command_data', 'uses' => 'ApiController@SendCommandController#create']);
    Route::any('send_sms_command', ['as' => 'api.send_sms_command', 'uses' => 'ApiController@SendCommandController#store']);
    Route::any('send_gprs_command', ['as' => 'api.send_gprs_command', 'uses' => 'ApiController@SendCommandController#gprsStore']);

    /*Route::any('add_my_icon_data', ['as' => 'api.add_my_icons_data', 'uses' => 'ApiController@addMyIconsData']);
    Route::any('add_my_icon', ['as' => 'api.add_my_icons', 'uses' => 'ApiController@addMyIcons']);
    Route::any('destroy_my_icon', ['as' => 'api.destroy_my_icons', 'uses' => 'ApiController@destroyMyIcons']);*/

    Route::any('edit_setup_data', ['as' => 'api.edit_setup_data', 'uses' => 'ApiController@MyAccountSettingsController#edit']);
    Route::any('edit_setup', ['as' => 'api.edit_setup', 'uses' => 'ApiController@MyAccountSettingsController#update']);

    Route::any('get_user_drivers', ['as' => 'api.get_user_drivers', 'uses' => 'ApiController@UserDriversController#index']);
    Route::any('add_user_driver_data', ['as' => 'api.add_user_driver_data', 'uses' => 'ApiController@UserDriversController#create']);
    Route::any('add_user_driver', ['as' => 'api.add_user_driver', 'uses' => 'ApiController@UserDriversController#store']);
    Route::any('edit_user_driver_data', ['as' => 'api.edit_user_driver_data', 'uses' => 'ApiController@UserDriversController#edit']);
    Route::any('edit_user_driver', ['as' => 'api.edit_user_driver', 'uses' => 'ApiController@UserDriversController#update']);
    Route::any('destroy_user_driver', ['as' => 'api.destroy_user_driver', 'uses' => 'ApiController@UserDriversController#destroy']);

    Route::any('get_custom_events', ['as' => 'api.get_custom_events', 'uses' => 'ApiController@CustomEventsController#index']);
    Route::any('add_custom_event_data', ['as' => 'api.add_custom_event_data', 'uses' => 'ApiController@CustomEventsController#create']);
    Route::any('add_custom_event', ['as' => 'api.add_custom_event', 'uses' => 'ApiController@CustomEventsController#store']);
    Route::any('edit_custom_event_data', ['as' => 'api.edit_custom_event_data', 'uses' => 'ApiController@CustomEventsController#edit']);
    Route::any('edit_custom_event', ['as' => 'api.edit_custom_event', 'uses' => 'ApiController@CustomEventsController#update']);
    Route::any('destroy_custom_event', ['as' => 'api.destroy_custom_event', 'uses' => 'ApiController@CustomEventsController#destroy']);

    Route::any('send_test_sms', ['as' => 'api.send_test_sms', 'uses' => 'ApiController@SmsGatewayController#sendTestSms']);

    Route::any('get_user_gprs_templates', ['as' => 'api.get_user_gprs_templates', 'uses' => 'ApiController@UserGprsTemplatesController#index']);
    Route::any('add_user_gprs_template_data', ['as' => 'api.add_user_gprs_template', 'uses' => 'ApiController@UserGprsTemplatesController#create']);
    Route::any('add_user_gprs_template', ['as' => 'api.add_user_gprs_template', 'uses' => 'ApiController@UserGprsTemplatesController#store']);
    Route::any('edit_user_gprs_template_data', ['as' => 'api.edit_user_gprs_template_data', 'uses' => 'ApiController@UserGprsTemplatesController#edit']);
    Route::any('edit_user_gprs_template', ['as' => 'api.edit_user_gprs_template', 'uses' => 'ApiController@UserGprsTemplatesController#update']);
    Route::any('get_user_gprs_message', ['as' => 'api.get_user_gprs_message', 'uses' => 'ApiController@UserGprsTemplatesController#getMessage']);
    Route::any('destroy_user_gprs_template', ['as' => 'api.destroy_user_gprs_template', 'uses' => 'ApiController@UserGprsTemplatesController#destroy']);

    Route::any('get_user_sms_templates', ['as' => 'api.get_user_sms_templates', 'uses' => 'ApiController@UserSmsTemplatesController#index']);
    Route::any('add_user_sms_template_data', ['as' => 'api.add_user_sms_template', 'uses' => 'ApiController@UserSmsTemplatesController#create']);
    Route::any('add_user_sms_template', ['as' => 'api.add_user_sms_template', 'uses' => 'ApiController@UserSmsTemplatesController#store']);
    Route::any('edit_user_sms_template_data', ['as' => 'api.edit_user_sms_template_data', 'uses' => 'ApiController@UserSmsTemplatesController#edit']);
    Route::any('edit_user_sms_template', ['as' => 'api.edit_user_sms_template', 'uses' => 'ApiController@UserSmsTemplatesController#update']);
    Route::any('get_user_sms_message', ['as' => 'api.get_user_sms_message', 'uses' => 'ApiController@UserSmsTemplatesController#getMessage']);
    Route::any('destroy_user_sms_template', ['as' => 'api.destroy_user_sms_template', 'uses' => 'ApiController@UserSmsTemplatesController#destroy']);

    Route::any('get_user_data', ['as' => 'api.get_user_data', 'uses' => 'ApiController@getUserData']);

    Route::any('register', ['as' => 'api.register', 'uses' => 'ApiController@RegistrationController#store']);
    Route::any('change_password', ['as' => 'api.change_password', 'uses' => 'ApiController@RegistrationController#changePassword']);

    Route::any('change/password', ['as' => 'api.change_password', 'uses' => 'ApiController@changePassword']);
    Route::any('get_sms_events', ['as' => 'api.get_sms_events', 'uses' => 'ApiController@getSmsEvents']);
    Route::any('getSingleDevice', ['as' => 'api.getSingleDevice', 'uses' => 'ApiController@getSingleDevice']);
	
	Route::any('fcm_token', ['as' => 'api.fcm_token', 'uses' => 'ApiController@setFcmToken']);
    Route::any('services_keys', ['as' => 'api.services_keys', 'uses' => 'ApiController@getServicesKeys']);
});

Route::any('api/insert_position', ['uses' => 'Frontend\PositionsController@insert']);

Route::group(['middleware' => ['server_active']], function () {
    Route::get('streetview', function(\Illuminate\Http\Request $request) {
        $keys = config('services.streetView.keys');
        $key  = $keys[ rand(0, count($keys) - 1) ];

        try {
            $location = $request->get('location');
            $size = $request->get('size');
            $heading = $request->get('heading');
            $response = Response::make(file_get_contents("https://maps.googleapis.com/maps/api/streetview?size=$size&location=$location&heading=$heading&pitch=-0.76&key=".$key));
            $response->header('Content-Type', 'image/jpeg');

            return $response;
        }
        catch (Exception $e) {
            $image = public_path('assets/img/no-streetview.jpg');

            if ( file_exists(public_path('assets/img/no-streetview-'.$size.'.jpg')) )
                $image = public_path('assets/img/no-streetview-'.$size.'.jpg');

            $response = Response::make(file_get_contents($image));
            $response->header('Content-Type', 'image/jpeg');

            return $response;
        }
    });
});


Route::get('/', ['as' => 'home', 'uses' => function () {
    if (Auth::check())
        return Redirect::route('objects.index');
    else
        return Redirect::route('authentication.create');
}]);
