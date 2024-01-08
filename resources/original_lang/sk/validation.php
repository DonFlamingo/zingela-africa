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
	"email"                => "The Email must be a valid email address.",
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
	"required_if"          => "The :attribute field is required when :other is :value.",
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
		'password' => 'Heslo',
		'password_confirmation' => 'Potvrdenie hesla',
		'remember_me' => 'Zapamätať prihlásenie',
		'name' => 'Názov',
        'imei' => 'IMEI',
        'imei_device' => 'IMEI alebo ID zariadenia',
		'fuel_measurement_type' => 'Spotreba paliva',
		'fuel_cost' => 'Cena paliva',
		'icon_id' => 'Ikona zariadenia',
		'active' => 'Aktívne',
		'polygon_color' => 'Farba pozadia',
		'devices' => 'Zariadenia',
		'geofences' => 'GEOhranice',
		'overspeed' => 'Prekročenie rýchlosti',
		'fuel_consumption' => 'Spotreba paliva',
		'description' => 'Popis',
		'map_icon_id' => 'Ikona ID',
		'coordinates' => 'Súradnice',
		'date_from' => 'Dátum od',
		'date_to' => 'Dátum do',
		'code' => 'Kód',
		'title' => 'Titulok',
		'note' => 'Obsah',
		'path' => 'Súbor',
		'period_name' => 'Názov intervalu',
		'days' => 'Dni',
		'devices_limit' => 'Limit zariadení',
		'trial' => 'Trial',
		'price' => 'Cena',
		'message' => 'Správa',
		'tag' => 'Tag',
		'timezone_id' => 'Časová zóna',
		'unit_of_distance' => 'Jednotka vzdialenosti',
		'unit_of_capacity' => 'Jednotka objemu',
		'unit_of_altitude' => 'Jednotka nadmorskej výšky',
		'icons' => 'Ikony',
		'sms_gateway_url' => 'SMS brána URL',
		'mobile_phone' => 'Číslo GSM',
		'sim_number'     => 'SIM číslo',
		'device_model'     => 'Typ zariadenia',
		'group_id' => 'Skupina',
		'rfid'     => 'RFID',
		'phone' => 'Telefón',
		'device_id' => 'Zariadenie',
		'tag_value' => 'Tag value',
		'device_port' => 'port zariadenia',
		'event' => 'Udalosť',
		'port' => 'Port',
		'device_protocol' => 'Protokol zariadenia',
		'protocol' => 'Protokol',
		'sensor_name' => 'Názov senzoru',
		'sensor_type' => 'Typ senzoru',
        'sensor_template' => 'Šablóna senzoru',
        'tag_name' => 'Tag name',
		'min_value' => 'Min. hodnota',
		'max_value' => 'Max. hodnota',
		'on_value' => 'ON hodnota',
		'off_value' => 'OFF hodnota',
		'shown_value_by' => 'Zobraziť hodnotu podľa',
		'tag_value' => 'Tag value',
		'full_tank_value' => 'Tag value',
		'formula'     => 'Vzorec',
		'parameters' => 'Parameters',
		'full_tank' => 'Objem nádrže v litroch',
		'fuel_tank_name'     => 'Názov nádrže',
		'odometer_value' => 'Hodnota',
		'odometer_value_by' => 'Stav Km',
		'unit_of_measurement' => 'Jednotka',
		'plate_number' => 'EČV',
		'vin' => 'VIN',
		'registration_number' => 'Poznámka',
		'object_owner' => 'Poznámka 2',
        'additional_notes' => 'Doplňujúce Poznámky',
		'expiration_date' => 'Dátum expirácie',
		'days_to_remind' => 'Počet dní do expirácie',
		'type' => 'Typ',
		'format' => 'Formát',
		'show_addresses' => 'Zobraziť adresy',
		'stops' => 'Zastávky',
		'speed_limit' => 'Rýchlostný limit',
		'zones_instead' => 'Zobraziť krajiny v adresách',
		'daily' => 'Denne',
		'weekly' => 'Týždenne',
		'send_to_email' => 'Automanicky poslať na e-mail',
		'filter' => 'Filter',
		'status' => 'Stav',
		'date' => 'Dátum',
		'geofence_name' => 'Názov GEOhranice',
		'tail_color' => 'Farba stopy',
		'tail_length' => 'Dĺžka stopy',
		'engine_hours'     => 'Motohodiny',
		'detect_engine' => 'Rozpoznanie motohodiny podľa ON/OFF',
		'min_moving_speed' => 'Min. rýchlosť pohybu v km/h',
		'min_fuel_fillings'     => 'Minimálny rozdiel pre rozpoznanie tankovania',
		'min_fuel_thefts'     => 'Minimálny rozdiel pre rozpoznanie krádeže',
		'expiration_by' => 'Vyprší',
		'interval' => 'Interval',
		'last_service' => 'Posledný servis',
		'trigger_event_left' => 'Pripomenutie pred servisom',
		'current_odometer' => 'Aktuálny stav km',
		'current_engine_hours' => 'Aktuálny stav motohodín',
		'renew_after_expiration' => 'Obnoviť po expirácií',
		'sms_template_id' => 'šablóna SMS',
		'frequency' => 'Frekvencia',
		'unit' => 'Jednotka',
		'noreply_email' => 'Žiadna odpoveď e - mailová adresa',
		'signature' => 'Podpis',
		'use_smtp_server' => 'Použiť SMTP server',
		'smtp_server_host' => 'Hostiteľ SMTP server',
		'smtp_server_port' => 'Port SMTP server',
		'smtp_security' => 'Bezpečnosť SMTP',
		'smtp_username' => 'SMTP užívateľské meno',
		'smtp_password' => 'SMTP heslo',
		'from_name' => 'Z názvu',
		'icons' => 'Ikony',
		'server_name' => 'Názov servera',
		'available_maps' => 'Dostupné mapy',
		'default_language' => 'Predvolený jazyk',
		'default_timezone' => 'Predvolené časové pásmo',
		'default_unit_of_distance' => 'Predvolené jednotka vzdialenosti',
		'default_unit_of_capacity' => 'Predvolené jednotka kapacity',
		'default_unit_of_altitude' => 'Východzí jednotka nadmorskej výšky',
		'default_date_format' => 'Predvolený formát dátumu',
		'default_time_format' => 'Predvolený formát času',
		'default_map' => 'Východiskové mapu',
		'default_object_online_timeout' => 'Východzí objekt line timeout',
		'logo' => 'Logo',
		'login_page_logo' => 'Prihlasovacia stránka logo',
		'frontpage_logo' => 'Frontpage logo',
		'favicon' => 'Favicon',
		'allow_users_registration' => 'Povoliť užívateľom registráciu',
		'frontpage_logo_padding_top' => 'Frontpage logo polstrovanie top',
		'default_maps' => 'Predvolené mapy',
		'subscription_expiration_after_days' => 'Subscription platnosti po dňoch',
		'gprs_template_id' => 'GPRS šablóna',
		'calibrations' => 'Kalibrácia',
		'ftp_server' => 'FTP server',
		'ftp_port' => 'FTP Port',
		'ftp_username' => 'FTP užívateľské meno',
		'ftp_password' => 'FTP heslo',
		'ftp_path' => 'FTP cesta',
		'period' => 'Lehota',
		'hour' => 'Hodina',
		'color' => 'Farba',
		'polyline' => 'Trasa',
		'request_method' => 'Metóda Dopyt',
		'authentication' => 'Overovanie',
		'username' => 'Užívateľské meno',
		'encoding' => 'Kódovanie',
		'time_adjustment' => 'Nastavenie času',
		'parameter' => 'Parameter',
		'export_type' => 'Typ Export',
		'groups' => 'Skupiny',
		'file' => 'Súbor',
		'extra' => 'Navyše',
		'parameter_value' => 'Hodnota parametra',
		'enable_plans' => 'Aktivovať plány',
		'payment_type' => 'Typ platby',
		'paypal_client_id' => 'Paypal ID klienta',
		'paypal_secret' => 'Paypal tajomstvo',
		'paypal_currency' => 'Paypal mena',
		'paypal_payment_name' => 'Paypal názov platba',
		'objects' => 'Objekty',
		'duration_value' => 'Trvania',
		'permissions' => 'Oprávnenie',
		'plan' => 'Plán',
		'default_billing_plan' => 'Úvodný plán fakturácie',
		'sensor_group_id' => 'Skupina Sensor',
		'daylight_saving_time' => 'Letný čas',
		'phone_number' => 'Telefónne číslo',
		'action' => 'Akčná',
		'time' => 'čas',
		'order' => 'Objednávka',
		'geocoder_api' => 'Geocoder API',
        'geocoder_cache' => 'Geocoder cache',
        'geocoder_cache_days' => 'Geocoder cache days',
        'geocoder_cache_delete' => 'Delete geocoder cache',
        'api_key' => 'API key',
        'api_url' => 'API url',
		'map_center_latitude' => 'Mapa centrum šírky',
		'map_center_longitude' => 'Mapa centrum dĺžky',
		'map_zoom_level' => 'Úroveň mapa zoom',
		'dst_type' => 'Typ',
		'provider' => 'Poskytovateľ',
		'week_start_day' => 'Predvolené kalendárny týždeň počiatočného dňa',
		'ip' => 'IP',
		'gprs_templates_only' => 'Show GPRS Šablóny iba príkazy',
		'select_all_objects' => 'Vybrať všetky objekty',
		'icon_type' => 'Icon type',
        'on_setflag_1' => 'Starting character',
        'on_setflag_2' => 'Amount of characters',
        'on_setflag_3' => 'Value of parameter',
        'domain' => 'Domain',
        'auth_id' => 'Auth ID',
        'auth_token' => 'Auth token',
        'senders_phone' => 'Sender\'s phone number',
        'database_clear_status' => 'Automatická história vyčistenie',
        'database_clear_days' => 'Dni zachovať',
        'ignition_detection' => 'Detekcia zapaľovanie',
        'template_color' => 'Farba šablóny',
        'background' => 'Pozadie',
        'login_page_text_color' => 'Farba textu prihlasovacej stránky',
        'login_page_background_color' => 'Farba pozadia prihlasovacej stránky',
        'welcome_text' => 'Vitaj text',
        'bottom_text' => 'Spodný text',
        'apple_store_link' => 'Odkaz na AppleStore',
        'google_play_link' => 'Odkaz GooglePlay',
    ),

);
