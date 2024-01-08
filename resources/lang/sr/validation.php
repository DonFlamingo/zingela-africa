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

	"accepted"             => ":attribute mora biti prihvaćeno.",
	"active_url"           => ":attribute nije validna URL.",
	"after"                => ":attribute mora biti datum posle :date.",
	"alpha"                => ":attribute može sadržati samo slova.",
	"alpha_dash"           => ":attribute može sadržati samo slova, brojeve, i crtice.",
	"alpha_num"            => ":attribute može sadržati samo slova i brojeve.",
	"array"                => ":attribute mora biti niz.",
	"before"               => ":attribute mora biti datum pre :date.",
	"between"              => array(
		"numeric" => ":attribute mora biti između :min i :max.",
		"file"    => ":attribute mora biti između :min i :max kilobajta.",
		"string"  => ":attribute mora biti između :min i :max karaktera.",
		"array"   => ":attribute mora imati imeđu :min i :max činilaca.",
	),
	"confirmed"            => ":attribute potvrda se ne slaže.",
	"date"                 => ":attribute nije ispravan datum.",
	"date_format"          => ":attribute ne odgovara formatu :format.",
	"different"            => ":attribute i :other mora biti različit.",
	"digits"               => ":attribute mora biti :digits broj/a/eva.",
	"digits_between"       => ":attribute mora biti :min i :max brojeva.",
	"email"                => "Email mora biti validna email adresa.",
	"exists"               => "odabran :attribute je neispravan.",
	"image"                => ":attribute mora biti slika.",
	"in"                   => "izabrani :attribute je neispravan.",
	"integer"              => ":attribute mora biti integer.",
	"ip"                   => ":attribute mora biti ispravna IP adresa.",
	"max"                  => array(
		"numeric" => ":attribute ne može biti veći od :max.",
		"file"    => ":attribute ne može biti veći od :max kilobajta.",
		"string"  => ":attribute ne može biti veći od :max karaktera.",
		"array"   => ":attribute ne može da ima više od :max stavki.",
	),
	"mimes"                => "The :attribute must be a file of type: :values.",
	"min"                  => array(
		"numeric" => ":attribute mora biti najmanje :min.",
		"file"    => ":attribute mora biti najmanje :min kilobajta.",
		"string"  => ":attribute mora biti najmanje :min karaktera.",
		"array"   => ":attribute mora imati najmanje :min stavki.",
	),
	"not_in"               => "izabran :attribute je neispravan.",
	"numeric"              => ":attribute mora biti broj.",
	"regex"                => ":attribute format je neispravan.",
	"required"             => ":attribute polje je obavezno.",
	"required_if"          => ":attribute polje je obavezno kada :other je :value.",
	"required_with"        => ":attribute polje je obavezno kada :values je prisutan.",
	"required_with_all"    => ":attribute polje je obavezno kada :values je prisutan.",
	"required_without"     => ":attribute polje je obavezno kada :values nije prisutan.",
	"required_without_all" => ":attribute polje je obavezno kada nijedan :values nije prisutan.",
	"same"                 => ":attribute i :other mora se podudarati.",
	"size"                 => array(
		"numeric" => ":attribute mora biti :size.",
		"file"    => ":attribute mora biti :size kilobytes.",
		"string"  => ":attribute mora biti :size characters.",
		"array"   => ":attribute mora sadržati :size stavki.",
	),
	"unique"               => ":attribute već je zauzet.",
	"url"                  => ":attribute format je neispravan.",


    "array_max" => ":attribute max stavki :max.",

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
        'password' => 'Lozinka',
        'password_confirmation' => 'Potvrda lozinke',
        'remember_me' => 'Zapamti me',
        'name' => 'Ime',
        'imei' => 'IMEI',
        'imei_device' => 'IMEI ili Identifikator uređaja',
        'fuel_measurement_type' => 'Merenj goriva',
        'fuel_cost' => 'Cena goriva',
        'icon_id' => 'Ikonica uređaja',
        'active' => 'Aktivan',
        'polygon_color' => 'Boja pozadine',
        'devices' => 'Uređaji',
        'geofences' => 'Geoograde',
        'overspeed' => 'Prekoračenje brzine',
        'fuel_consumption' => 'Potrošnja goriva',
        'description' => 'Opis',
        'map_icon_id' => 'Ikonica markera',
        'coordinates' => 'Tačka na mapi',
        'date_from' => 'Datum od',
        'date_to' => 'Datum do',
        'code' => 'Šifra',
        'title' => 'Naziv',
        'note' => 'Sadržaj',
        'path' => 'Fajl',
        'period_name' => 'Ime perioda',
        'days' => 'Dana',
        'devices_limit' => 'Broj uređaja limit',
        'trial' => 'Probni period',
        'price' => 'Cena',
        'message' => 'Poruka',
        'tag' => 'Oznaka',
        'timezone_id' => 'Vremenska zona',
        'unit_of_distance' => 'Jedinica razdaljine',
        'unit_of_capacity' => 'Jedinica kapacitetaUnit of capacity',
        'unit_of_altitude' => 'Mera za visinu',
        'user' => 'KOrisnik',
        'group_id'     => 'Grupa',
        'permission_to_add_devices' => 'Dodaj uređaje',
        'sms_gateway_url' => 'SMS gateway URL',
        'mobile_phone' => 'Mobilni telefon',
        'permission_to_use_sms_gateway' => 'SMS gateway',
        'loged_at'     => 'Zadnje logovanje',
        'manager_id' => 'Menadžer',
        'sim_number'     => 'SIM broj',
        'device_model'     => 'Model uređaja',
        'group_id' => 'Grupa',
        'rfid'     => 'RFID',
        'phone' => 'Telefon',
        'device_id' => 'Uređaj',
        'tag_value' => 'Vrednost parametra',
        'device_port' => 'Port uređaja',
        'event' => 'Događaj',
        'port' => 'Port',
        'device_protocol' => 'Protokol uređaja',
        'protocol' => 'Protokol',
        'sensor_name' => 'Ime senzora',
        'sensor_type' => 'Tip senzora',
        'sensor_template' => 'Senzor šablon',
        'tag_name' => 'Ime prametra',
        'min_value' => 'Min. vrednost',
        'max_value' => 'Max. vrednost',
        'on_value' => 'UKLJ. vrednost',
        'off_value' => 'ISKLJ. vrednost',
        'shown_value_by' => 'Pokaži vrednost po',
        'full_tank_value' => 'Vrednost parametra',
        'formula'     => 'Formula',
        'parameters' => 'Parametri',
        'full_tank' => 'Rezervoar goriva litara/galona',
        'fuel_tank_name'     => 'Ime rezervoara goriva',
		'odometer_value' => 'вредност',
        'odometer_value_by' => 'Odometar',
        'unit_of_measurement' => 'Jedinica merenja',
        'plate_number' => 'Br.Tablica',
        'vin' => 'Br.Šasije',
        'registration_number' => 'Registracioni/Vlasništvo broj',
        'object_owner' => 'Vlasnik objekta/Menadžer',
        'additional_notes' => 'Dodatne napomene',
        'expiration_date' => 'Datum isticanja',
        'days_to_remind' => 'Dana za podsećanje pre isticanja',
        'type' => 'Tip',
        'format' => 'Format',
        'show_addresses' => 'Pokaži adrese',
        'stops' => 'Zaustavljanja',
        'speed_limit' => 'Ograničenje brzine',
        'zones_instead' => 'Zone umesto adresa',
        'daily' => 'Dnevno',
        'weekly' => 'Nedeljno',
        'send_to_email' => 'Pošalji na email',
        'filter' => 'Filter',
        'status' => 'Status',
        'date' => 'Datum',
        'geofence_name' => 'Ime geoograde',
        'tail_color' => 'Boja traga',
        'tail_length' => 'Dužina traga',
        'engine_hours'     => 'Moto časovi',
        'detect_engine' => 'Detektuj motor UKLJ./ISKL. sa',
        'min_moving_speed' => 'Min. brzina kretanja u km/h',
        'min_fuel_fillings'     => 'Min. razlika u nivou goriva za detektovanje tankovanja',
        'min_fuel_thefts'     => 'Min. razlika u nivou goriva za detektovanje krađe',
        'expiration_by' => 'Expiration by',
        'interval' => 'Interval',
        'last_service' => 'Poslednji servis',
        'trigger_event_left' => 'Okidač događaja kada ostane',
        'current_odometer' => 'Sadašnje stanje odometra',
        'current_engine_hours' => 'Sadašnji motočasovi',
        'renew_after_expiration' => 'Zanavljanje posle isticanja',
        'sms_template_id' => 'SMS šablon',
        'frequency' => 'Učestalost',
        'unit' => 'Jedinica',
        'noreply_email' => 'No reply email adresa',
        'signature' => 'Potpis',
        'use_smtp_server' => 'Koristi SMTP server',
        'smtp_server_host' => 'SMTP server host',
        'smtp_server_port' => 'SMTP server port',
        'smtp_security' => 'SMTP security',
        'smtp_username' => 'SMTP Korisničko ime',
        'smtp_password' => 'SMTP Lozinka',
        'from_name' => 'Sa imena',
        'icons' => 'Ikonice',
        'server_name' => 'Ime Servera',
        'available_maps' => 'Dostupne mape',
        'default_language' => 'Podrazumevani jezik',
        'default_timezone' => 'Podrazumevana vremenska zona',
        'default_unit_of_distance' => 'Podrazumevana jedinica udaljenosti',
        'default_unit_of_capacity' => 'Podrazumevana jedinica kapaciteta',
        'default_unit_of_altitude' => 'Podrazumevana jedinica za visinu',
        'default_date_format' => 'Podrazumevani format datuma',
        'default_time_format' => 'Podrazumevani vremenski format ',
        'default_map' => 'Podrazumevana mapa',
        'default_object_online_timeout' => 'Podrazumevano isticanje vremena online objekta',
        'logo' => 'Logo',
        'login_page_logo' => 'Login stranica logo',
        'frontpage_logo' => 'Prednja strana logo',
        'favicon' => 'Favicon',
        'allow_users_registration' => 'Dozvoli registraciju korisnika',
        'frontpage_logo_padding_top' => 'Prednja strana logo padding top',
        'default_maps' => 'Podrazumevane mape',
        'subscription_expiration_after_days' => 'Nalog ističe posle dana',
        'gprs_template_id' => 'GPRS šablon',
        'calibrations' => 'Kalibracije',
        'ftp_server'     => 'FTP server',
        'ftp_port'     => 'FTP port',
        'ftp_username'     => 'FTP korisničko ime',
        'ftp_password'     => 'FTP lozinka',
        'ftp_path' => 'FTP put',
        'period' => 'Period',
        'hour' => 'Sat',
        'color' => 'Boja',
        'polyline' => 'Ruta',
        'request_method' => 'Zahtevani metod',
        'authentication' => 'autentifikacija',
        'username' => 'Korisničko ime',
        'encoding' => 'Šifrovanje',
		'time_adjustment' => 'Podešavanje vremena',
        'parameter' => 'Parametar',
        'export_type' => 'Tip izvoza',
        'groups' => 'Grupe',
        'file' => 'Fajl',
        'extra' => 'Extra',
		'parameter_value' => 'Vrednost parametra',
		'enable_plans' => 'Омогући планове',
		'payment_type' => 'Врста плаћања',
		'paypal_client_id' => 'Паипал ИД клијента',
		'paypal_secret' => 'паипал тајна',
		'paypal_currency' => 'паипал валута',
		'paypal_payment_name' => 'Паипал име плаћања',
		'objects' => 'Objekti',
		'duration_value' => 'Трајање',
		'permissions' => 'Дозволе',
		'plan' => 'План',
		'default_billing_plan' => 'План дефаулт наплате',
		'sensor_group_id' => 'сензор група',
		'daylight_saving_time' => 'Летњег рачунања времена',
		'phone_number' => 'број телефона',
		'action' => 'акција',
		'time' => 'време',
		'order' => 'ред',
		'geocoder_api' => 'Geocoder API',
        'geocoder_cache' => 'Geocoder cache',
        'geocoder_cache_days' => 'Geocoder cache days',
        'geocoder_cache_delete' => 'Delete geocoder cache',
        'api_key' => 'API key',
        'api_url' => 'API url',
		'map_center_latitude' => 'Карта центар ширина',
		'map_center_longitude' => 'Карта центар дужине',
		'map_zoom_level' => 'Карта зум ниво',
		'dst_type' => 'Тип',
		'provider' => 'снабдевач',
		'week_start_day' => 'Деафулт календар седмица почетак дана',
		'ip' => 'IP',
		'gprs_templates_only' => 'Схов ЕДГЕ Шаблон команде само',
		'select_all_objects' => 'Изабери све објекте',
		'icon_type' => 'Icon type',
        'on_setflag_1' => 'Starting character',
        'on_setflag_2' => 'Amount of characters',
        'on_setflag_3' => 'Value of parameter',
        'domain' => 'Domain',
        'auth_id' => 'Auth ID',
        'auth_token' => 'Auth token',
        'senders_phone' => 'Sender\'s phone number',
        'database_clear_status' => 'Аутоматски историја чишћење',
        'database_clear_days' => 'Дана да би',
        'ignition_detection' => 'Детекција паљење од',
        'template_color' => 'Боја боја',
        'background' => 'Позадина',
        'login_page_text_color' => 'Боја странице за пријављивање',
        'login_page_background_color' => 'Боја странице за пријављивање на страницу',
        'welcome_text' => 'Добродошли текст',
        'bottom_text' => 'Доњи текст',
        'apple_store_link' => 'АпплеСторе линк',
        'google_play_link' => 'Линк ГооглеПлаи',
    ),

);