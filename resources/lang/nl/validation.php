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

	"accepted"             => "De :attribute moet worden aanvaard.",
	"active_url"           => "De :attribute is geen geldig URL.",
	"after"                => "De :attribute moet een datum zijn na :date.",
	"alpha"                => "De :attribute mag alleen letter bevatten.",
	"alpha_dash"           => "De :attribute mag alleen letters, cijfers en streepjes bevatten.",
	"alpha_num"            => "De :attribute mag alleen letters en cijfers bevatten.",
	"array"                => "De :attribute moet een reeks zijn.",
	"before"               => "De :attribute moet een datum zijn voor :date.",
	"between"              => array(
		"numeric" => "De :attribute moet tussen :min en :max zijn.",
		"file"    => "De :attribute moet tussen :min en :max kilobytes zijn.",
		"string"  => "De :attribute moet tussen :min and :max karakters zijn.",
		"array"   => "De :attribute moet tussen de :min en :max items zijn.",
	),
	"confirmed"            => "De :attribute bevestiging komt niet overeen.",
	"date"                 => "De :attribute is geen geldige datum.",
	"date_format"          => "De :attribute opmaak komt niet overeen met :format.",
	"different"            => "De :attribute en :other moet verschillend zijn.",
	"digits"               => "De :attribute moet :digits cijfers zijn.",
	"digits_between"       => "De :attribute moet tussen de :min en :max cijfers zijn.",
	"email"                => "De Email moet een geldig e-mailadres zijn.",
	"exists"               => "De geselecteerde :attribute is ongeldig.",
	"image"                => "De :attribute moet een afbeelding zijn.",
	"in"                   => "De geselecteerde :attribute is ongeldig.",
	"integer"              => "De :attribute moet een geheel (getal) zijn.",
	"ip"                   => "De :attribute moet een geldig IP adres zijn",
	"max"                  => array(
		"numeric" => "De :attribute mag niet groter dan :max zijn.",
		"file"    => "De :attribute mag niet groter dan :max kilobytes zijn.",
		"string"  => "De :attribute mag niet groter dan :max karakters zijn.",
		"array"   => "De :attribute mag niet meer dan :max items hebben.",
	),
	"mimes"                => "De :attribute moet een bestand of type: :values zijn.",
	"min"                  => array(
		"numeric" => "De :attribute moet minimaal :min zijn.",
		"file"    => "De :attribute moet minimaal :min kilobytes zijn.",
		"string"  => "De :attribute moet minimaal :min karakters hebben.",
		"array"   => "De :attribute moet minimaal :min items hebben.",
	),
	"not_in"               => "De geselcteerde :attribute is ongeldig.",
	"numeric"              => "De :attribute moet een getal zijn.",
	"regex"                => "De :attribute formaat is ongeldig.",
	"required"             => "Het :attribute veld is noodzakelijk.",
	"required_if"          => "Het :attribute veld is noodzakelijk.",
	"required_with"        => "Het :attribute veld is noodzakelijk wanneer :values is aanwezig.",
	"required_with_all"    => "Het :attribute veld is noodzakelijk wanneer :values is aanwezig.",
	"required_without"     => "Het :attribute veld is noodzakelijk wanneer :values niet aanwezig is.",
	"required_without_all" => "Het :attribute veld is noodzakelijk wanneer geen van :values aanwezig is.",
	"same"                 => "De :attribute en :other moet overeenkomen.",
	"size"                 => array(
		"numeric" => "De :attribute moet :size zijn.",
		"file"    => "De :attribute moet :size kilobytes zijn.",
		"string"  => "De :attribute moet :size karakters zijn.",
		"array"   => "De :attribute moet :size items bevatten.",
	),
	"unique"               => "De :attribute is reeds bezet.",
	"url"                  => "De :attribute formaat is ongeldig.",


    "array_max" => "De :attribute maximale aantal items :max.",
	'lesser_than' => 'De :attribute moet kleiner zijn dan :other',

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
			'rule-name' => 'aangepast bericht',
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
        'email' => 'E-mail',
        'password' => 'Wachtwoord',
        'password_confirmation' => 'Wachtwoord bevestiging',
        'remember_me' => 'onthoud mij',
        'name' => 'Naam',
        'imei' => 'IMEI',
        'imei_device' => 'IMEI of apparaat identificatie',
        'fuel_measurement_type' => 'Brandstof meting',
        'fuel_cost' => 'Brandstof kosten',
        'icon_id' => 'Apparaat pictogram',
        'active' => 'Actief',
        'polygon_color' => 'Achtergrond kleur',
        'devices' => 'Apparaten',
        'geofences' => 'Virtueel hek',
        'overspeed' => 'Snelheidsoverschrijding',
        'fuel_consumption' => 'Brandstofverbruik',
        'description' => 'Omschrijving',
        'map_icon_id' => 'Kaart pictogram',
        'coordinates' => 'Map punt',
        'date_from' => 'Datum vanaf',
        'date_to' => 'Datum tot',
        'code' => 'Code',
        'title' => 'Titel',
        'note' => 'Inhoud',
        'path' => 'Bestand',
        'period_name' => 'Periode naam',
        'days' => 'Dagen',
        'devices_limit' => 'Maximum aantal apparaten',
        'trial' => 'Proef',
        'price' => 'Prijs',
        'message' => 'Bericht',
		'tag' => 'Parameter',
        'timezone_id' => 'Tijdzone',
        'unit_of_distance' => 'Afstandseenheid',
        'unit_of_capacity' => 'Capaciteit eenheid',
        'unit_of_altitude' => 'Hoogte eenheid',
        'icons' => 'Pictogrammen',
        'sms_gateway_url' => 'SMS gateway URL',
        'mobile_phone' => 'Mobiele telefoon',
        'sim_number'     => 'SIM nummer',
        'device_model'     => 'Apparaat model',
        'group_id' => 'Groep',
        'rfid'     => 'RFID',
        'phone' => 'Telefoon',
        'device_id' => 'Apparaat',
		'tag_value' => 'Parameter waarde',
        'device_port' => 'Apparaat poort',
        'event' => 'Gebeurtenis',
        'port' => 'Poort',
        'device_protocol' => 'Apparaat protocol',
        'protocol' => 'Protocol',
		'sensor_name' => 'Naam van de sensor',
		'sensor_type' => 'Sensor type',
        'sensor_template' => 'Sensor sjabloon',
        'tag_name' => 'Parameter naam',
		'min_value' => 'Min. waarde',
		'max_value' => 'Max. waarde',
		'on_value' => 'ON waarde',
		'off_value' => 'OFF waarde',
		'shown_value_by' => 'Show waarde',
		'full_tank_value' => 'Parameter waarde',
		'formula' => 'Formule',
		'parameters' => 'Parameters',
		'full_tank' => 'Volle tank in liters/gallons',
		'fuel_tank_name' => 'Brandstoftank naam',
		'odometer_value' => 'Waarde',
		'odometer_value_by' => 'Kilometerteller',
		'unit_of_measurement' => 'Meet eenheid',
		'plate_number' => 'Kentekenplaat',
		'vin' => 'VIN',
		'registration_number' => 'Registratie/Asset nummer',
		'object_owner' => 'Voorwerp eigenaar/Manager',
        'additional_notes' => 'Extra notities',
		'expiration_date' => 'Houdbaarheidsdatum',
		'days_to_remind' => 'Dagen te herinneren vóór het verstrijken',
		'type' => 'Type',
		'format' => 'Formaat',
		'show_addresses' => 'Toon adressen',
		'stops' => 'Stopt',
		'speed_limit' => 'Snelheidslimiet',
		'zones_instead' => 'Zones plaats van adressen',
		'daily' => 'Dagelijks',
		'weekly' => 'Wekelijks',
		'send_to_email' => 'Stuur naar e-mail',
		'filter' => 'Filter',
		'status' => 'Toestand',
		'date' => 'Datum',
		'geofence_name' => 'Geofence naam',
		'tail_color' => 'Staart kleur',
		'tail_length' => 'Staartlengte',
		'engine_hours' => 'Motoruren',
		'detect_engine' => 'Detecteren motor ON/OFF door',
		'min_moving_speed' => 'Min . bewegingssnelheid in km/h',
		'min_fuel_fillings' => 'Min . brandstof verschil om brandstof vullingen detecteren',
		'min_fuel_thefts' => 'Min . brandstof verschil om brandstof diefstallen detecteren',
		'expiration_by' => 'Expiratie door',
		'interval' => 'Interval',
		'last_service' => 'Laatste dienst',
		'trigger_event_left' => 'Trigger event wanneer de linkerzijde',
		'current_odometer' => 'Huidige kilometerstand',
		'current_engine_hours' => 'Huidige engine uur',
		'renew_after_expiration' => 'Vernieuwen na afloop',
		'sms_template_id' => 'SMS sjabloon',
		'frequency' => 'Frequentie',
		'unit' => 'Eenheid',
		'noreply_email' => 'Geen antwoord e-mail adres',
		'signature' => 'Handtekening',
		'use_smtp_server' => 'Gebruik SMTP-server',
		'smtp_server_host' => 'SMTP- server host',
		'smtp_server_port' => 'SMTP-server poort',
		'smtp_security' => 'SMTP- beveiliging',
		'smtp_username' => 'SMTP- gebruikersnaam',
		'smtp_password' => 'SMTP-wachtwoord',
		'from_name' => 'Van naam',
		'server_name' => 'Server naam',
		'available_maps' => 'Beschikbare kaarten',
		'default_language' => 'Standaardtaal',
		'default_timezone' => 'Standaard tijdzone',
		'default_unit_of_distance' => 'Standaardeenheid van afstand',
		'default_unit_of_capacity' => 'Standaardeenheid van de capaciteit',
		'default_unit_of_altitude' => 'Standaardeenheid van hoogte',
		'default_date_format' => 'Standaarddatumnotatie',
		'default_time_format' => 'Standaardtijd formaat',
		'default_map' => 'Standaard kaart',
		'default_object_online_timeout' => 'Standaard object online timeout',
		'logo' => 'Logo',
		'login_page_logo' => 'Login pagina logo',
		'frontpage_logo' => 'Frontpage logo',
		'favicon' => 'Favicon',
		'allow_users_registration' => 'Kunnen gebruikers registratie',
		'frontpage_logo_padding_top' => 'Frontpage logo padding top',
		'default_maps' => 'Standaard kaarten',
		'subscription_expiration_after_days' => 'Abonnement verlopen na dagen',
		'gprs_template_id' => 'GPRS template',
		'calibrations' => 'Kalibraties',
		'ftp_server' => 'FTP-server',
		'ftp_port' => 'FTP-poort',
		'ftp_username' => 'FTP- gebruikersnaam',
		'ftp_password' => 'FTP wachtwoord',
		'ftp_path' => 'FTP-pad',
		'period' => 'Periode',
		'hour' => 'Uur',
		'color' => 'Kleur',
		'polyline' => 'Route',
		'request_method' => 'Methode verzoek',
		'authentication' => 'Authenticatie',
		'username' => 'Gebruikersnaam',
		'encoding' => 'Codering',
		'time_adjustment' => 'Tijd aanpassing',
		'parameter' => 'Parameter',
		'export_type' => 'Export type',
		'groups' => 'Groepen',
		'file' => 'Bestand',
		'extra' => 'Extra',
		'parameter_value' => 'Parameter waarde',
		'enable_plans' => 'Plannen mogelijk te maken',
		'payment_type' => 'Betalingswijze',
		'paypal_client_id' => 'Paypal client-ID',
		'paypal_secret' => 'Paypal geheim',
		'paypal_currency' => 'Paypal munt',
		'paypal_payment_name' => 'Paypal betalingen naam',
		'objects' => 'Voorwerpen',
		'duration_value' => 'Duur',
		'permissions' => 'Machtigingen',
		'plan' => 'Plan',
		'default_billing_plan' => 'Standaard factureringsmodel',
		'sensor_group_id' => 'Sensor groep',
		'daylight_saving_time' => 'Zomertijd',
		'phone_number' => 'Telefoonnummer',
		'action' => 'Actie',
		'time' => 'Tijd',
		'order' => 'Bestellen',
		'geocoder_api' => 'Geocoder API',
        'geocoder_cache' => 'Geocoder cache',
        'geocoder_cache_days' => 'Geocoder cache days',
        'geocoder_cache_delete' => 'Delete geocoder cache',
        'api_key' => 'API key',
        'api_url' => 'API url',
		'map_center_latitude' => 'Kaart midden breedtegraad',
		'map_center_longitude' => 'Kaart midden lengtegraad',
		'map_zoom_level' => 'Kaart zoomniveau',
		'dst_type' => 'Type',
		'provider' => 'Leverancier',
		'week_start_day' => 'Verzuim kalenderweek startdag',
		'ip' => 'IP',
		'gprs_templates_only' => 'Toon GPRS Templates commando&#39;s alleen',
		'select_all_objects' => 'Selecteer alle objecten',
		'icon_type' => 'Icon type',
        'on_setflag_1' => 'Starting character',
        'on_setflag_2' => 'Amount of characters',
        'on_setflag_3' => 'Value of parameter',
        'domain' => 'Domain',
        'auth_id' => 'Auth ID',
        'auth_token' => 'Auth token',
        'senders_phone' => 'Sender\'s phone number',
        'database_clear_status' => 'Automatische geschiedenis opruimen',
        'database_clear_days' => 'Dagen te houden',
        'ignition_detection' => 'Ignition detectie door',
        'template_color' => 'Sjabloon kleur',
        'background' => 'Achtergrond',
        'login_page_text_color' => 'Inloggen pagina tekst kleur',
        'login_page_background_color' => 'Inloggen pagina achtergrondkleur',
        'welcome_text' => 'Welkom tekst',
        'bottom_text' => 'Onderste tekst',
        'apple_store_link' => 'AppleStore-link',
        'google_play_link' => 'GooglePlay-link',
    ),

);
