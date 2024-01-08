<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used by
	| the validator class. Some of these rules have multiple versions such
	| as the size rules. Feel free to tweak each of these messages here.
	|
	*/

	"accepted"             => "The :attribute must be accepted.",
	"active_url"           => "The :attribute is not a valid URL.",
	"after"                => "The :attribute must be a date after :date.",
	"alpha"                => "The :attribute may only contain letters.",
	"alpha_dash"           => "The :attribute may only contain letters, numbers, and dashes.",
	"alpha_num"            => "The :attribute may only contain letters and numbers.",
	"array"                => "The :attribute must be an array.",
	"before"               => "The :attribute must be a date before :date.",
	"between"              => array(
		"numeric" => "The :attribute must be between :min and :max.",
		"file"    => "The :attribute must be between :min and :max kilobytes.",
		"string"  => "The :attribute must be between :min and :max characters.",
		"array"   => "The :attribute must have between :min and :max items.",
	),
	"confirmed"            => "The :attribute confirmation does not match.",
	"date"                 => "The :attribute is not a valid date.",
	"date_format"          => "The :attribute does not match the format :format.",
	"different"            => "The :attribute and :other must be different.",
	"digits"               => "The :attribute must be :digits digits.",
	"digits_between"       => "The :attribute must be between :min and :max digits.",
	"email"                => "The :attribute must be a valid email address.",
	"exists"               => "The selected :attribute is invalid.",
	"image"                => "The :attribute must be an image.",
	"in"                   => "The selected :attribute is invalid.",
	"integer"              => "The :attribute must be an integer.",
	"ip"                   => "The :attribute must be a valid IP address.",
	"max"                  => array(
		"numeric" => "The :attribute may not be greater than :max.",
		"file"    => "The :attribute may not be greater than :max kilobytes.",
		"string"  => "The :attribute may not be greater than :max characters.",
		"array"   => "The :attribute may not have more than :max items.",
	),
	"mimes"                => "The :attribute must be a file of type: :values.",
	"min"                  => array(
		"numeric" => "The :attribute must be at least :min.",
		"file"    => "The :attribute must be at least :min kilobytes.",
		"string"  => "The :attribute must be at least :min characters.",
		"array"   => "The :attribute must have at least :min items.",
	),
	"not_in"               => "The selected :attribute is invalid.",
	"numeric"              => "The :attribute must be a number.",
	"regex"                => "The :attribute format is invalid.",
	"required"             => "The :attribute field is required.",
	"required_if"          => "The :attribute field is required.",
	"required_with"        => "The :attribute field is required when :values is present.",
	"required_with_all"    => "The :attribute field is required when :values is present.",
	"required_without"     => "The :attribute field is required when :values is not present.",
	"required_without_all" => "The :attribute field is required when none of :values are present.",
	"same"                 => "The :attribute and :other must match.",
	"size"                 => array(
		"numeric" => "The :attribute must be :size.",
		"file"    => "The :attribute must be :size kilobytes.",
		"string"  => "The :attribute must be :size characters.",
		"array"   => "The :attribute must contain :size items.",
	),
	"unique"               => "The :attribute has already been taken.",
	"url"                  => "The :attribute format is invalid.",

    "array_max" => "The :attribute max items :max.",
    "lesser_than" => "The :attribute must be lesser than :other",

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute.rule" to name the lines. This makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/

	'custom' => array(
		'attribute-name' => array(
			'rule-name' => 'custom-message',
		),
	),

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly such as E-Mail Address instead
	| of "email". This simply helps us make messages a little cleaner.
	|
	*/


	'attributes' => array(
        'email' => 'Email',
        'password' => 'Password',
        'password_confirmation' => 'Password confirmation',
        'remember_me' => 'Remember me',
        'name' => 'Name',
        'imei' => 'IMEI',
        'imei_device' => 'IMEI or Device identifier',
        'fuel_measurement_type' => 'Fuel measurement',
        'fuel_cost' => 'Fuel cost',
        'icon_id' => 'Device icon',
        'active' => 'Active',
        'polygon_color' => 'Background color',
        'devices' => 'Devices',
        'geofences' => 'Geofences',
        'overspeed' => 'Overspeed',
        'fuel_consumption' => 'Fuel consumption',
        'description' => 'Description',
        'map_icon_id' => 'Marker icon',
        'coordinates' => 'Map point',
        'date_from' => 'Date from',
        'date_to' => 'Date to',
        'code' => 'Code',
        'title' => 'Title',
        'note' => 'Content',
        'path' => 'File',
        'period_name' => 'Period name',
        'days' => 'Days',
        'devices_limit' => 'Devices limit',
        'trial' => 'Trial',
        'price' => 'Price',
        'message' => 'Message',
        'tag' => 'Parameter',
        'timezone_id' => 'Timezone',
        'unit_of_distance' => 'Unit of distance',
        'unit_of_capacity' => 'Unit of capacity',
		'unit_of_altitude' => 'Unit of altitude',
        'user' => 'User',
        'group_id'     => 'Group',
        'permission_to_add_devices' => 'Add devices',
        'sms_gateway_url' => 'SMS gateway URL',
        'mobile_phone' => 'Mobile phone',
        'permission_to_use_sms_gateway' => 'SMS gateway',
        'loged_at'     => 'Last login',
        'manager_id' => 'Manager',
        'sim_number'     => 'SIM number',
        'device_model'     => 'Device model',
        'group_id' => 'Group',
        'rfid'     => 'RFID',
        'phone' => 'Phone',
        'device_id' => 'Device',
        'tag_value' => 'Parameter value',
        'device_port' => 'Device port',
        'event' => 'Event',
        'port' => 'Port',
        'device_protocol' => 'Device protocol',
        'protocol' => 'Protocol',
        'sensor_name' => 'Sensor name',
        'sensor_type' => 'Sensor type',
        'sensor_template' => 'Sensor template',
        'tag_name' => 'Parameter name',
        'min_value' => 'Min. value',
        'max_value' => 'Max. value',
        'on_value' => 'ON value',
        'off_value' => 'OFF value',
        'shown_value_by' => 'Show value by',
        'full_tank_value' => 'Parameter value',
        'formula'     => 'Formula',
        'parameters' => 'Parameters',
        'full_tank' => 'Full tank in liters/gallons',
        'fuel_tank_name'     => 'Fuel tank name',
        'odometer_value' => 'Value',
        'odometer_value_by' => 'Odometer',
        'unit_of_measurement' => 'Unit of measurement',
        'plate_number' => 'Plate number',
        'vin' => 'VIN',
        'registration_number' => 'Registration/Asset number',
        'object_owner' => 'Object owner/Manager',
        'additional_notes' => 'Additional notes',
        'expiration_date' => 'Expiration date',
        'days_to_remind' => 'Days to remind before expiration',
        'type' => 'Type',
        'format' => 'Format',
        'show_addresses' => 'Show addresses',
        'stops' => 'Stops',
        'speed_limit' => 'Speed limit',
        'zones_instead' => 'Zones instead of addresses',
        'daily' => 'Daily',
        'weekly' => 'Weekly',
        'send_to_email' => 'Send to email',
        'filter' => 'Filter',
        'status' => 'Status',
        'date' => 'Date',
        'geofence_name' => 'Geofence name',
        'tail_color' => 'Tail color',
        'tail_length' => 'Tail length',
        'engine_hours'     => 'Engine hours',
        'detect_engine' => 'Detect engine ON/OFF by',
        'min_moving_speed' => 'Min. moving speed in km/h',
        'min_fuel_fillings'     => 'Min. fuel difference to detect fuel fillings',
        'min_fuel_thefts'     => 'Min. fuel difference to detect fuel thefts',
        'expiration_by' => 'Expiration by',
        'interval' => 'Interval',
        'last_service' => 'Last service',
        'trigger_event_left' => 'Trigger event when left',
        'current_odometer' => 'Current odometer',
        'current_engine_hours' => 'Current engine hours',
        'renew_after_expiration' => 'Renew after expiration',
        'sms_template_id' => 'SMS template',
        'frequency' => 'Frequency',
        'unit' => 'Unit',
        'noreply_email' => 'No reply email address',
        'signature' => 'Signature',
        'use_smtp_server' => 'Use SMTP server',
        'smtp_server_host' => 'SMTP server host',
        'smtp_server_port' => 'SMTP server port',
        'smtp_security' => 'SMTP security',
        'smtp_username' => 'SMTP username',
        'smtp_password' => 'SMTP password',
        'from_name' => 'From name',
        'icons' => 'Icons',
        'server_name' => 'Server name',
        'available_maps' => 'Available maps',
        'default_language' => 'Default language',
        'default_timezone' => 'Default timezone',
        'default_unit_of_distance' => 'Default unit of distance',
        'default_unit_of_capacity' => 'Default unit of capacity',
        'default_unit_of_altitude' => 'Default unit of altitude',
        'default_date_format' => 'Default date format',
        'default_time_format' => 'Default time format',
        'default_map' => 'Default map',
        'default_object_online_timeout' => 'Default object online timeout',
        'logo' => 'Logo',
        'login_page_logo' => 'Login page logo',
        'frontpage_logo' => 'Frontpage logo',
        'favicon' => 'Favicon',
        'allow_users_registration' => 'Allow users registration',
        'frontpage_logo_padding_top' => 'Frontpage logo padding top',
        'default_maps' => 'Default maps',
        'subscription_expiration_after_days' => 'Subscription expiration after days',
        'gprs_template_id' => 'GPRS template',
        'calibrations' => 'Calibrations',
        'ftp_server'     => 'FTP server',
        'ftp_port'     => 'FTP port',
        'ftp_username'     => 'FTP username',
        'ftp_password'     => 'FTP password',
        'ftp_path' => 'FTP path',
        'period' => 'Period',
        'hour' => 'Hour',
        'color' => 'Color',
        'polyline' => 'Route',
        'request_method' => 'Request method',
        'authentication' => 'Authentication',
        'username' => 'Username',
        'encoding' => 'Encoding',
		'time_adjustment' => 'Time adjustment',
        'parameter' => 'Parameter',
        'export_type' => 'Export type',
        'groups' => 'Groups',
        'file' => 'File',
        'extra' => 'Extra',
		'parameter_value' => 'Parameter value',
        'enable_plans' => 'Enable plans',
        'payment_type' => 'Payment type',
        'paypal_client_id' => 'Paypal client ID',
        'paypal_secret' => 'Paypal secret',
        'paypal_currency' => 'Paypal currency',
        'paypal_payment_name' => 'Paypal payment name',
        'objects' => 'Objects',
        'duration_value' => 'Duration',
        'permissions' => 'Permissions',
        'plan' => 'Plan',
        'default_billing_plan' => 'Default billing plan',
        'sensor_group_id' => 'Sensor group',
        'daylight_saving_time' => 'Daylight saving time',
        'phone_number' => 'Phone number',
        'action' => 'Action',
        'time' => 'Time',
        'order' => 'Order',
		'geocoder_api' => 'Geocoder API',
        'geocoder_cache' => 'Geocoder cache',
        'geocoder_cache_days' => 'Geocoder cache days',
        'geocoder_cache_delete' => 'Delete geocoder cache',
        'api_key' => 'API key',
        'api_url' => 'API url',
        'map_center_latitude' => 'Map center latitude',
        'map_center_longitude' => 'Map center longitude',
        'map_zoom_level' => 'Map zoom level',
        'dst_type' => 'Type',
        'provider' => 'Provider',
		'week_start_day'     => 'Default calendar week start day',
        'ip' => 'IP',
        'gprs_templates_only' => 'Show GPRS Templates commands only',
        'icon_type' => 'Icon type',
        'on_setflag_1' => 'Starting character',
        'on_setflag_2' => 'Amount of characters',
        'on_setflag_3' => 'Value of parameter',
        'domain' => 'Domain',
        'auth_id' => 'Auth ID',
        'auth_token' => 'Auth token',
        'senders_phone' => 'Sender\'s phone number',
        'database_clear_status' => 'Automatic history cleanup',
        'database_clear_days' => 'Days to keep',
        'ignition_detection' => 'Ignition detection by',
        'template_color' => 'Template color',
        'background' => 'Background',
        'login_page_text_color' => 'Login page text color',
        'login_page_background_color' => 'Login page background color',
        'welcome_text' => 'Welcome text',
        'bottom_text' => 'Bottom text',
        'apple_store_link' => 'AppleStore link',
        'google_play_link' => 'GooglePlay link',
    ),

);
