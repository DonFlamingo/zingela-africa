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
	'lesser_than' => ':attribute muss kleiner als sein :other',

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
        'password' => 'Passwort',
        'password_confirmation' => 'Passwort Bestätigung',
        'remember_me' => 'Erinnere dich an mich',
        'name' => 'Name',
        'imei' => 'IMEI',
        'imei_device' => 'IMEI oder Gerätekennung',
        'fuel_measurement_type' => 'Kraftstoffmessung',
        'fuel_cost' => 'Kraftstoffkosten',
        'icon_id' => 'Gerätesymbol',
        'active' => 'Aktiv',
        'polygon_color' => 'Hintergrundfarbe',
        'devices' => 'Geräte',
        'geofences' => 'Geofences',
        'overspeed' => 'Überdrehzahl',
        'fuel_consumption' => 'Kraftstoffverbrauch',
        'description' => 'Beschreibung',
        'map_icon_id' => 'Marker Symbol',
        'coordinates' => 'Kartenpunkt',
        'date_from' => 'Stammen aus',
        'date_to' => 'Datum bis',
        'code' => 'Code',
        'title' => 'Titel',
        'note' => 'Inhalt',
        'path' => 'Datei',
        'period_name' => 'Periodenname',
        'days' => 'Tage',
        'devices_limit' => 'Gerätebegrenzung',
        'trial' => 'Versuch',
        'price' => 'Preis',
        'message' => 'Nachricht',
        'tag' => 'Parameter',
        'timezone_id' => 'Zeitzone',
        'unit_of_distance' => 'Einheit der Entfernung',
        'unit_of_capacity' => 'Einheit der Kapazität',
        'unit_of_altitude' => 'Höheneinheit',
        'user' => 'Benutzer',
        'group_id' => 'Gruppe',
        'permission_to_add_devices' => 'Geräte hinzufügen',
        'sms_gateway_url' => 'SMS Gateway URL',
        'mobile_phone' => 'Mobiltelefon',
        'permission_to_use_sms_gateway' => 'SMS Gateway',
        'loged_at' => 'Letzte Anmeldung',
        'manager_id' => 'Manager',
        'sim_number' => 'SIM-Nummer',
        'device_model' => 'Gerätemodell',
        'rfid' => 'RFID',
        'phone' => 'Telefon',
        'device_id' => 'Gerät',
        'tag_value' => 'Parameterwert',
        'device_port' => 'Geräteanschluss',
        'event' => 'Event',
        'port' => 'Hafen',
        'device_protocol' => 'Geräteprotokoll',
        'protocol' => 'Protokoll',
		'sensor_name' => 'Sensornamen',
		'sensor_type' => 'Sensortyp',
        'sensor_template' => 'Sensorvorlage',
        'tag_name' => 'Parametername',
		'min_value' => 'Minute Wert',
		'max_value' => 'Max. Wert',
		'on_value' => 'EIN Wert',
		'off_value' => 'AUS Wert',
		'shown_value_by' => 'Zeige Wert',
		'full_tank_value' => 'Parameterwert',
		'formula' => 'Formel',
		'parameters' => 'Parameter',
		'full_tank' => 'Voller Tank in Liter/Gallonen',
		'fuel_tank_name' => 'Kraftstofftank Namen',
		'odometer_value' => 'Wert',
		'odometer_value_by' => 'Entfernungsmesser',
		'unit_of_measurement' => 'Maßeinheit',
		'plate_number' => 'Kennzeichen',
		'vin' => 'VIN',
		'registration_number' => 'Registrierung/Inventarnummer',
		'object_owner' => 'Object Owner/Manager',
        'additional_notes' => 'Zusätzliche Bemerkungen',
		'expiration_date' => 'Ablaufdatum',
		'days_to_remind' => 'Tage vor Ablauf erinnern',
		'type' => 'Type',
		'format' => 'Formatieren',
		'show_addresses' => 'Adressen anzeigen',
		'stops' => 'Anschläge',
		'speed_limit' => 'Geschwindigkeitsbegrenzung',
		'zones_instead' => 'Zonen anstelle von Adressen',
		'daily' => 'Täglich',
		'weekly' => 'Wöchentlich',
		'send_to_email' => 'An Email schicken',
		'filter' => 'Filter',
		'status' => 'Status',
		'date' => 'Datum',
		'geofence_name' => 'Geofence Namen',
		'tail_color' => 'Schwanz Farbe',
		'tail_length' => 'Schwanzlänge',
		'engine_hours' => 'Motorstunden',
		'detect_engine' => 'Detect Motor EIN/AUS durch',
		'min_moving_speed' => 'Minute Bewegungsgeschwindigkeit in km/h',
		'min_fuel_fillings' => 'Minute Kraftstoff Unterschied zum Kraftstofffüllungenerkennen',
		'min_fuel_thefts' => 'Minute Kraftstoff Unterschied zum Kraftstoffdiebstählezu erkennen',
		'expiration_by' => 'Ablauf von',
		'interval' => 'Intervall',
		'last_service' => 'Letzter Service',
		'trigger_event_left' => 'Trigger-Ereignis, wenn links',
		'current_odometer' => 'Aktuelle Kilometer',
		'current_engine_hours' => 'Aktuelle Betriebsstunden',
		'renew_after_expiration' => 'Erneuern nach Ablauf',
		'sms_template_id' => 'SMS template',
		'frequency' => 'Frequenz',
		'unit' => 'Aggregat',
		'noreply_email' => 'Keine Antwort E-Mail Adresse',
		'signature' => 'Unterschrift',
		'use_smtp_server' => 'Verwenden Sie SMTP-Server',
		'smtp_server_host' => 'SMTP -Server-Host',
		'smtp_server_port' => 'SMTP-Server -Port',
		'smtp_security' => 'SMTP- Sicherheit',
		'smtp_username' => 'SMTP- Benutzernamen',
		'smtp_password' => 'SMTP-Passwort',
		'from_name' => 'Vom Namen',
		'server_name' => 'Servername',
		'available_maps' => 'Verfügbare Karten',
		'default_language' => 'Standard-Sprache',
		'default_timezone' => 'Standard-Zeitzone',
		'default_unit_of_distance' => 'StandardeinheitDistanz',
		'default_unit_of_capacity' => 'Standardkapazitätseinheit',
		'default_unit_of_altitude' => 'Standardeinheit der Höhen',
		'default_date_format' => 'Standard-Datumsformat',
		'default_time_format' => 'Standard Zeitformat',
		'default_map' => 'Standard- Karte',
		'default_object_online_timeout' => 'Standard Objekt Online- Timeout',
		'logo' => 'Logo',
		'login_page_logo' => 'Login-Seite logo',
		'frontpage_logo' => 'Startseite logo',
		'favicon' => 'Favicon',
		'allow_users_registration' => 'Benutzern erlauben Registrierung',
		'frontpage_logo_padding_top' => 'Startseite logo padding oben',
		'default_maps' => 'Standard- Karten',
		'subscription_expiration_after_days' => 'Subskriptionsablaufdatum nach Tagen',
		'gprs_template_id' => 'GPRS -Vorlage',
		'calibrations' => 'Kalibrierungen',
		'ftp_server' => 'FTP-Server',
		'ftp_port' => 'FTP-Port',
		'ftp_username' => 'FTP-Benutzernamen',
		'ftp_password' => 'FTP-Passwort',
		'ftp_path' => 'FTP-Pfad',
		'period' => 'Periode',
		'hour' => 'Stunde',
		'color' => 'Farbe',
		'polyline' => 'Route',
		'request_method' => 'Anforderungsmethode',
		'authentication' => 'Authentication',
		'username' => 'Benutzername',
		'encoding' => 'Codierung',
		'time_adjustment' => 'Zeiteinstellung',
		'parameter' => 'Parameter',
		'export_type' => 'Export -Typ',
		'groups' => 'Gruppen',
		'file' => 'Datei',
		'extra' => 'Extra',
		'parameter_value' => 'Parameterwert ',
		'enable_plans' => 'Aktivieren Pläne',
		'payment_type' => 'Zahlungsart',
		'paypal_client_id' => 'Paypal-Client-ID',
		'paypal_secret' => 'Paypal Geheimnis',
		'paypal_currency' => 'Paypal Währung',
		'paypal_payment_name' => 'Paypal Zahlung Name',
		'objects' => 'Objekte',
		'duration_value' => 'Dauer',
		'permissions' => 'Berechtigungen',
		'plan' => 'Planen',
		'default_billing_plan' => 'Standardfakturierungsplan',
		'sensor_group_id' => 'Sensor Gruppe',
		'daylight_saving_time' => 'Sommerzeit',
		'phone_number' => 'Telefonnummer',
		'action' => 'Aktion',
		'time' => 'Zeit',
		'order' => 'Auftrag',
		'geocoder_api' => 'Geocoder API',
        'geocoder_cache' => 'Geocoder cache',
        'geocoder_cache_days' => 'Geocoder cache days',
        'geocoder_cache_delete' => 'Delete geocoder cache',
        'api_key' => 'API key',
        'api_url' => 'API url',
		'map_center_latitude' => 'Karte Zentrum Breite',
		'map_center_longitude' => 'Karte Zentrum Länge',
		'map_zoom_level' => 'Karte Zoom-Stufe',
		'dst_type' => 'Art',
		'provider' => 'Versorger',
		'week_start_day' => 'Standard-Kalenderwochenstarttag',
		'ip' => 'IP',
		'gprs_templates_only' => 'Anzeigen GPRS Vorlagen nur Befehle',
		'select_all_objects' => 'Wählen Sie alle Objekte',
		'icon_type' => 'Icon type',
        'on_setflag_1' => 'Starting character',
        'on_setflag_2' => 'Amount of characters',
        'on_setflag_3' => 'Value of parameter',
        'domain' => 'Domain',
        'auth_id' => 'Auth ID',
        'auth_token' => 'Auth token',
        'senders_phone' => 'Sender\'s phone number',
        'database_clear_status' => 'Automatische Verlaufsbereinigung',
        'database_clear_days' => 'Tage zu halten',
        'ignition_detection' => 'Zündungserkennung durch',
        'template_color' => 'Vorlagenfarbe',
        'background' => 'Hintergrund',
        'login_page_text_color' => 'Anmeldeseite Textfarbe',
        'login_page_background_color' => 'Login-Seiten-Hintergrundfarbe',
        'welcome_text' => 'Begrüßungstext',
        'bottom_text' => 'Fußzeile',
        'apple_store_link' => 'AppleStore Link',
        'google_play_link' => 'GooglePlay Link',
    ),

);
