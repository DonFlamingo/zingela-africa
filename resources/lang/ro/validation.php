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

	"accepted"             => ":attribute trebuie să fie acceptat.",
	"active_url"           => ":attribute nu este o adresă URL validă.",
	"after"                => ":attribute trebuie să fie o dată după :date.",
	"alpha"                => ":attribute poate conține doar litere.",
	"alpha_dash"           => ":attribute poate conține doar litere, numere și liniuțe.",
	"alpha_num"            => ":attribute poate conține doar litere și numere.",
	"array"                => ":attribute trebuie sa fie un vector.",
	"before"               => ":attribute trebuie să fie o dată înainte de :date.",
	"between"              => array(
		"numeric" => ":attribute trebuie să fie între :min și :max.",
		"file"    => ":attribute trebuie să fie între :min și :max kilobytes.",
		"string"  => ":attribute trebuie să fie între :min și :max caractere.",
		"array"   => ":attribute trebuie să fie între :min și :max elemente.",
	),
	"confirmed"            => "Confirmarea pentru :attribute nu se potrivește.",
	"date"                 => ":attribute nu este o dată validă.",
	"date_format"          => ":attribute nu se potrivește cu formatul :format.",
	"different"            => ":attribute și :other trebuie să fie diferite.",
	"digits"               => ":attribute trebuie să fie format din :digits cifre.",
	"digits_between"       => ":attribute trebuie să fie între :min și :max cifre.",
	"email"                => ":attribute trebuie să fie o adresă de e-mail validă.",
	"exists"               => ":attribute selectat este invalid.",
	"image"                => ":attribute trebuie să fie o imagine.",
	"in"                   => ":attribute selectat este invalid.",
	"integer"              => ":attribute trebuie să fie un număr întreg.",
	"ip"                   => ":attribute trebuie să fie o adresă IP validă.",
	"max"                  => array(
		"numeric" => ":attribute nu poate fi mai mare decât :max.",
		"file"    => ":attribute nu poate fi mai mare decât :max kilobytes.",
		"string"  => ":attribute nu poate fi mai mare decât :max caractere.",
		"array"   => ":attribute nu poate avea mai mult de :max elemente.",
	),
	"mimes"                => ":attribute trebuie să fie un fișier de tip: :values.",
	"min"                  => array(
		"numeric" => ":attribute trebuie să fie cel puțin :min.",
		"file"    => ":attribute trebuie să fie cel puțin :min kilobytes.",
		"string"  => ":attribute trebuie să fie cel puțin :min caractere.",
		"array"   => ":attribute trebuie să aibă cel puțin :min elemente.",
	),
	"not_in"               => ":attribute selectat este invalid.",
	"numeric"              => ":attribute trebuie să fie un număr.",
	"regex"                => ":attribute are formatul invalid.",
	"required"             => "Câmpul :attribute este obligatoriu.",
	"required_if"          => "Câmpul :attribute este obligatoriu.",
	"required_with"        => "Câmpul :attribute este obligatoriu când :values este prezent.",
	"required_with_all"    => "Câmpul :attribute este obligatoriu când :values este prezent.",
	"required_without"     => "Câmpul :attribute este obligatoriu când :values nu este prezent.",
	"required_without_all" => "Câmpul :attribute este obligatoriu când nici unul dintre :values este prezent.",
	"same"                 => ":attribute și :other trebuie să se potrivească",
	"size"                 => array(
		"numeric" => ":attribute trebuie să fie :size.",
		"file"    => ":attribute trebuie să fie :size kilobytes.",
		"string"  => ":attribute trebuie să fie :size caractere.",
		"array"   => ":attribute trebuie să conțină :size elemente.",
	),
	"unique"               => ":attribute a fost deja luat.",
	"url"                  => ":attribute are formatul invalid.",

    "array_max" => "Numărul maxim de alemente al :attribute este :max.",
    "lesser_than" => ":attribute trebuie să fie mai mic decât :other",

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
        'email' => 'E-mail',
        'password' => 'Parolă',
        'password_confirmation' => 'Confirmarea parolei',
        'remember_me' => 'Tine-mă minte',
        'name' => 'Nume',
        'imei' => 'IMEI sau Identificator de Dispozitiv',
        'fuel_measurement_type' => 'Măsurarea combustibilului',
        'fuel_cost' => 'Costul combustibilului',
        'icon_id' => 'Pictograma dispozitivului',
        'active' => 'Activ',
        'polygon_color' => 'Culoare de fundal',
        'devices' => 'Dispozitive',
        'geofences' => 'Geo-zone',
        'overspeed' => 'Depășirea vitezei',
        'fuel_consumption' => 'Consum de combustibil',
        'description' => 'Descriere',
        'map_icon_id' => 'Pictograma marcatorului',
        'coordinates' => 'Punct de hartă',
        'date_from' => 'Data de la',
        'date_to' => 'Data până la',
        'code' => 'Cod',
        'title' => 'Titlu',
        'note' => 'Conținut',
        'path' => 'Fișier',
        'period_name' => 'Numele perioadei',
        'days' => 'Zile',
        'devices_limit' => 'Limita maximă de dispozitive',
        'trial' => 'De probă',
        'price' => 'Preț',
        'message' => 'Mesaj',
        'tag' => 'Parametru',
        'timezone_id' => 'Fus orar',
        'unit_of_distance' => 'Unitate de distanță',
        'unit_of_capacity' => 'Unitate de capacitate',
		'unit_of_altitude' => 'Unitate de altitudine',
        'user' => 'Utilizator',
        'group_id'     => 'Grup',
        'permission_to_add_devices' => 'Adaugați dispozitive',
        'sms_gateway_url' => 'Adresa URL a gateway-ului SMS',
        'mobile_phone' => 'Telefon mobil',
        'permission_to_use_sms_gateway' => 'Gateway SMS',
        'loged_at'     => 'Ultima conexiune',
        'manager_id' => 'Manager',
        'sim_number'     => 'Număr SIM',
        'device_model'     => 'Modelul dispozitivului',
        'group_id' => 'Grup',
        'rfid'     => 'RFID',
        'phone' => 'Telefon',
        'device_id' => 'Dispozitiv',
        'tag_value' => 'Valoarea parametrului',
        'device_port' => 'Portul dispozitiv',
        'event' => 'Eveniment',
        'port' => 'Port',
        'device_protocol' => 'Protocolul dispozitivului',
        'protocol' => 'Protocol',
        'sensor_name' => 'Numele senzorului',
        'sensor_type' => 'Tipul senzorului',
        'tag_name' => 'Numele parametrului',
        'min_value' => 'Val. minimă',
        'max_value' => 'Val. maximă',
        'on_value' => 'Valoare pornit',
        'off_value' => 'Valoare oprit',
        'shown_value_by' => 'Afișați valoarea prin',
        'full_tank_value' => 'Valoarea parametrului',
        'formula'     => 'Formulă',
        'parameters' => 'Parametri',
        'full_tank' => 'Rezervor plin în litri/galoane',
        'fuel_tank_name'     => 'Nume rezervor',
        'odometer_value' => 'Valoare',
        'odometer_value_by' => 'Odometru',
        'unit_of_measurement' => 'Unitate de măsură',
        'plate_number' => 'Număr inmatriculare',
        'vin' => 'VIN',
        'registration_number' => 'Număr de înregistrare/număr de activ',
        'object_owner' => 'Proprietar obiect/Manager',
        'expiration_date' => 'Data expirării',
        'days_to_remind' => 'Zile de reamintire înainte de expirare',
        'type' => 'Tip',
        'format' => 'Format',
        'show_addresses' => 'Arată adresă',
        'stops' => 'Opriri',
        'speed_limit' => 'Limită de viteză',
        'zones_instead' => 'Geo-zone în loc de adrese',
        'daily' => 'Zilnic',
        'weekly' => 'Săptamânal',
        'send_to_email' => 'Trimite pe email',
        'filter' => 'Filtru',
        'status' => 'Stare',
        'date' => 'Dată',
        'geofence_name' => 'Nume geo-zonă',
        'tail_color' => 'Culoare coadă',
        'tail_length' => 'Lungime coadă',
        'engine_hours'     => 'Ore motor',
        'detect_engine' => 'Detectați motor pornit/oprit prin',
        'min_moving_speed' => 'Viteza minimă de deplasare în km/h',
        'min_fuel_fillings'     => 'Diferența minimă de combustibil pt. a detecta umplerile de combustibil',
        'min_fuel_thefts'     => 'Diferența minimă de combustibil pt. a detecta furturile de combustibil',
        'expiration_by' => 'Expirarea la',
        'interval' => 'Interval',
        'last_service' => 'Ultimul serviciu',
        'trigger_event_left' => 'Declanșare eveniment la ieșire',
        'current_odometer' => 'Odometru curent',
        'current_engine_hours' => 'Ore motor curente',
        'renew_after_expiration' => 'Reînnoire după expirare',
        'sms_template_id' => 'Șablon SMS',
        'frequency' => 'Frecvență',
        'unit' => 'Unitate',
        'noreply_email' => 'Fară adresă de e-mail pt. răspuns',
        'signature' => 'Semnătură',
        'use_smtp_server' => 'Folositți server SMTP',
        'smtp_server_host' => 'Server SMTP',
        'smtp_server_port' => 'Port server SMTP',
        'smtp_security' => 'Securitate SMTP',
        'smtp_username' => 'Utilizator SMTP',
        'smtp_password' => 'Parolă SMTP',
        'from_name' => 'Nume câmp De La',
        'icons' => 'Pictograme',
        'server_name' => 'Nume server',
        'available_maps' => 'Hărți disponibile',
        'default_language' => 'Limba implicita',
        'default_timezone' => 'Fus orar implicit',
        'default_unit_of_distance' => 'Unitatea implicită de distanță',
        'default_unit_of_capacity' => 'Unitatea implicită de capacitate',
        'default_unit_of_altitude' => 'Unitatea implicită de altitudine',
        'default_date_format' => 'Format implicit de dată',
        'default_time_format' => 'Format implicit de timp',
        'default_map' => 'Hartă implicită',
        'default_object_online_timeout' => 'Timp implicit pt. detectarea deconectarii obiectului',
        'logo' => 'Logo',
        'login_page_logo' => 'Logo pagină de conectare',
        'frontpage_logo' => 'Logo pagină principală',
        'favicon' => 'Favicon',
        'allow_users_registration' => 'Permiteți înregistrarea utilizatorilor',
        'frontpage_logo_padding_top' => 'Căptușeala de sus pt. logo-ul paginii principale',
        'default_maps' => 'Harți implicite',
        'subscription_expiration_after_days' => 'Termenul de expirare al abonamentului după zile',
        'gprs_template_id' => 'Șablon GPRS',
        'calibrations' => 'Calibrări',
        'ftp_server'     => 'Server FTP',
        'ftp_port'     => 'Port FTP',
        'ftp_username'     => 'Utilizator FTP',
        'ftp_password'     => 'Parolă FTP',
        'ftp_path' => 'Cale FTP',
        'period' => 'Perioadă',
        'hour' => 'Oră',
        'color' => 'Culoare',
        'polyline' => 'Rută',
        'request_method' => 'Metodă cerere',
        'authentication' => 'Autentificare',
        'username' => 'Utilizator',
        'encoding' => 'Codare',
		'time_adjustment' => 'Ajustare timp',
        'parameter' => 'Parametru',
        'export_type' => 'Tipul de export',
        'groups' => 'Grupuri',
        'file' => 'Fișier',
        'extra' => 'Extra',
		'parameter_value' => 'Valoarea parametrului',
        'enable_plans' => 'Activați planurile',
        'payment_type' => 'Tip facturare',
        'paypal_client_id' => 'ID client Paypal',
        'paypal_secret' => 'Secret Paypal',
        'paypal_currency' => 'Valută Paypal',
        'paypal_payment_name' => 'Nume facturare Paypal',
        'objects' => 'Obiecte',
        'duration_value' => 'Durată',
        'permissions' => 'Permisiuni',
        'plan' => 'Plan',
        'default_billing_plan' => 'Planul de facturare implicit',
        'sensor_group_id' => 'Grupa senzorilor',
        'daylight_saving_time' => 'Ora de vară',
        'phone_number' => 'Număr de telefon',
        'action' => 'Acțiune',
        'time' => 'Timp',
        'order' => 'Comandă',
		'geocoder_api' => 'API geocoder',
        'geocoder_cache' => 'Geocoder cache',
        'geocoder_cache_days' => 'Geocoder cache days',
        'geocoder_cache_delete' => 'Delete geocoder cache',
        'api_key' => 'Cheia API',
        'api_url' => 'API url',
        'map_center_latitude' => 'Latitudine pt. centrul harții',
        'map_center_longitude' => 'Longitudine pt. centrul harții',
        'map_zoom_level' => 'Nivel de mărire a hărții',
        'dst_type' => 'Tip',
        'provider' => 'Furnizor',
		'week_start_day'     => 'Ziua inițială a săptămânii (implicită)',
        'ip' => 'IP',
        'gprs_templates_only' => 'Afișați numai comenzile GPRS',
        'icon_type' => 'Tipul pictogramei',
        'on_setflag_1' => 'Caracterul de pornire',
        'on_setflag_2' => 'Numărul de caractere',
        'on_setflag_3' => 'Valoarea parametrului',
        'domain' => 'Domeniu',
        'auth_id' => 'ID Autentificare',
        'auth_token' => 'Cod Autentificare',
        'senders_phone' => 'Numărul de telefon al expeditorului',

        'database_clear_status' => 'Curatarea automată a istoricului',
        'database_clear_days' => 'Zile de păstrat',
        'ignition_detection' => 'Detectarea aprinderii prin',
        'background' => 'Fundal',
        'login_page_text_color' => 'Culoarea textului paginii de conectare',
        'login_page_background_color' => 'Culoarea de fundal a paginii de conectare',
        'welcome_text' => 'Textul de întâmpinare',
        'bottom_text' => 'Textul de jos',
        'apple_store_link' => 'Linkul AppleStore',
        'google_play_link' => 'Linkul GooglePlay',
    ),

);
