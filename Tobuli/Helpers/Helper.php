<?php
use Tobuli\Entities\SmsEventQueue;
use Tobuli\Exceptions\ValidationException;
use Swift_MailTransport as MailTransport;
use Stanley\Geocodio\Client;
use Illuminate\Support\Facades\Redis as Redis4;

use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

function dontExist($name) {
    return sprintf(trans('global.dont_exist'), trans($name));
}

function datetime($date, $timezone = TRUE, $zone = NULL) {
    if (substr($date, 0, 4) == '0000' || empty($date))
        return trans('front.invalid_date');

    if ($timezone) {
        if (Auth::check() && is_null($zone))
            $zone = Auth::User()->timezone->zone;

        if (is_null($zone))
            $zone = '+0hours';

        return date(settings('main_settings.default_date_format').' '.settings('main_settings.default_time_format'), strtotime($zone, strtotime($date)));
    }

    return date(settings('main_settings.default_date_format').' '.settings('main_settings.default_time_format'), strtotime($date));
}

function tdate($date, $zone = NULL, $reverse = false, $format = 'Y-m-d H:i:s') {
    if (is_null($zone))
        $zone = Auth::User()->timezone->zone;

    if ($reverse)
        $zone = timezoneReverse($zone);

    return date($format, strtotime($zone, strtotime($date)));
}

function beginTransaction() {
    DB::beginTransaction();
    DB::connection('traccar_mysql')->beginTransaction();
}

function rollbackTransaction() {
    DB::connection('traccar_mysql')->rollback();
    DB::rollback();
}

function commitTransaction() {
    DB::commit();
    DB::connection('traccar_mysql')->commit();
}

function modalError($message) {
    return View::make('admin::Layouts.partials.error_modal')->with('error', trans($message));
}

function modal($message, $type = 'warning') {
    return View::make('front::Layouts.partials.modal_warning', [
        'type' => $type,
        'message' => $message
    ]);
}

function isDeviceOnline($time, $ack_time) {
    $minutes = settings('main_settings.default_object_online_timeout') * 60;
    $status = 'offline';
    if ((time() - $minutes) < strtotime($ack_time))
        $status = 'ack';
    if ((time() - $minutes) < strtotime($time))
        $status = 'online';

    return $status;
}

function isAdmin() {
    return Auth::User()->group_id == 1 || Auth::User()->group_id == 3;
}

function idExists($id, $arr) {
    foreach ($arr as $key=>$value) {
        if ($value['id'] == $id)
            return true;
    }

    return false;
}

function kilometersToMiles($km) {
    return round($km / 1.609344);
}

function milesToKilometers($ml) {
    return round($ml * 1.609344);
}

function gallonsToLiters($gallons) {
    if ($gallons <= 0)
        return 0;

    return $gallons * 3.78541178;
}

function litersToGallons($liters) {
    if ($liters <= 0)
        return 0;

    return $liters / 3.78541178;
}

function metersToFeets($meters) {
    if ($meters <= 0)
        return 0;

    return number_format($meters * 3.2808399, 2, '.', FALSE);
}

function float($number) {
    return number_format($number, 2, '.', FALSE);
}

function cord($number) {
    return number_format($number, 7, '.', FALSE);
}

function convertFuelConsumption($type, $fuel_quantity) {
    if ($fuel_quantity <= 0)
        return 0;
    if ($type == 1) {
        return 1 / $fuel_quantity;
    }
    elseif ($type == 2) {
        return gallonsToLiters(1) / milesToKilometers($fuel_quantity);
    }
    else {
        return 0;
    }

}

/**
 * @param $template
 * @param array $to
 * @param null $replace
 * @param string $view
 */
function sendEmailTemplate($template, $to, $replace = NULL, $view = 'front::Emails.template', $sms_gateway = NULL, $sms_template = NULL, $user_id = NULL, $lang = NULL) {
    # Mail
    if (!empty($to)) {
        $to_arr = explode(';', $to);
        $to = !count($to_arr) ? ['0' => $to] : $to_arr;

        $body = $template->note;
        $subjet = $template->title;
        if (!empty($replace)) {
            $body = strtr($body, $replace);
            $subjet = strtr($subjet, $replace);
        }

        \Facades\MailHelper::send($to, $body, $subjet, $lang, TRUE, [], $view);
    }

    # SMS
    if (!is_null($sms_template)) {
        send_sms_template($sms_template, $sms_gateway, $user_id, FALSE, $replace);
    }
}

function sendNotificationToTokens($tokens, $title, $body, $payloadData = null)
{
    $optionBuilder = new OptionsBuilder();
    $optionBuilder->setTimeToLive(60 * 20);
    $option = $optionBuilder->build();

    $notification = null;

    $notificationBuilder = new PayloadNotificationBuilder($title);
    $notificationBuilder->setBody($body)->setSound('default');
    $notification = $notificationBuilder->build();

    $dataBuilder = new PayloadDataBuilder();
    if (!is_null($payloadData)) {
        $dataBuilder->addData($payloadData);
    }
    $data = $dataBuilder->build();

    $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);

    if ($downstreamResponse->tokensToDelete()) {
        FcmToken::whereIn('token', $downstreamResponse->tokensToDelete())->delete();
    }

    if ($retryTokens = $downstreamResponse->tokensToRetry()) {
        sendNotificationToTokens($retryTokens, $title, $body, $payloadData);
    }

    if ($downstreamResponse->tokensToModify()) {
        foreach ($downstreamResponse->tokensToModify() as $old_token => $new_token) {
            FcmToken::where('token', $old_token)->update(['token' => $new_token]);
        }
    }
}

function sendNotification($user_id, \Tobuli\Entities\EventQueue $eventQueue)
{
    if (empty($user_id))
        return;

    $user = User::find($user_id);

    if (!$user)
        return;

    $tokens = $user->fcm_tokens->lists('token')->toArray();

    if (!$tokens)
        return;

    switch ($eventQueue->type) {
        case 'expiring_user':
        case 'expired_user':
            $title = $user->email . ' ' . $eventQueue->event_message;
            $body  = '';
            break;
        default:
            $title = $eventQueue->data['device_name'] . ' ' . $eventQueue->event_message;
            $body  = trans('front.speed') . ': ' . $eventQueue->data['speed'];

            if (in_array($eventQueue->type, ['zone_out', 'zone_in']))
                $body .= "\n" . trans('front.geofence') . ': ' . $eventQueue->data['geofence'];
            break;
    }

    $payload = array_merge($eventQueue->data, ['title' => $title, 'content' => $body]);

    sendNotificationToTokens($tokens, $title, $body, $payload);
}

function isLimited() {
    return false;
}

function secondsToTime($seconds)
{
    // extract hours
    $hours = floor($seconds / (60 * 60));

    // extract minutes
    $divisor_for_minutes = $seconds % (60 * 60);
    $minutes = floor($divisor_for_minutes / 60);

    // extract the remaining seconds
    $divisor_for_seconds = $divisor_for_minutes % 60;
    $seconds = ceil($divisor_for_seconds);

    if ($hours < 0 || $minutes < 0 || $seconds < 0)
        return '0s';

    return ($hours ? "{$hours}h " : '').($minutes ? "{$minutes}min " : '');
//    return ($hours ? "{$hours}h " : '').($minutes ? "{$minutes}min " : '')."{$seconds}s";
}

function mysort($arr) {
    if (count($arr) <= 1)
        return $arr;

    return array_combine(range(0, count($arr)-1), array_values($arr));
}

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    // Uncomment one of the following alternatives
    // $bytes /= pow(1024, $pow);
    $bytes /= (1 << (10 * $pow));

    return round($bytes, $precision) . ' ' . $units[$pow];
}

function getDevicesDrivers($user_id, $device_id, $date_from, $date_to = NULL, $operation = '>=', $limit = NUll, $distinct = FALSE) {
    $query = DB::table('user_driver_position_pivot')
        ->select('user_driver_position_pivot.date', 'user_drivers.*')
        ->join('user_drivers', 'user_driver_position_pivot.driver_id', '=', 'user_drivers.id')
        ->where('user_driver_position_pivot.date', $operation, $date_from)
        ->where('user_driver_position_pivot.device_id', $device_id)
        ->where('user_drivers.user_id', $user_id)
        ->orderBy('user_driver_position_pivot.date', 'desc');

    if ($distinct)
        $query->groupBy('user_driver_position_pivot.driver_id');

    if ($date_to)
        $query->where('user_driver_position_pivot.date', '<=', $date_to);

    if ($limit)
        $query->limit($limit);

    $rows = $query->get();

    if (!empty($rows)) {
        foreach($rows as &$row)
            $row->date = strtotime($row->date);
    }

    return $rows;
}

function formatServices($services, $values) {
    $services_arr = [];
    foreach ($services as $service) {

        if ( is_array($service) ) {
            $service = json_decode(json_encode($service));
        }

        $services_arr[] = [
            'name' => $service->name,
            'value' => serviceExpiration($service, $values)
        ];
    }

    return $services_arr;
}

function dateDiff($date, $date1) {
    $dStart = new DateTime($date);
    $dEnd  = new DateTime($date1);
    $dDiff = $dStart->diff($dEnd);
    $dDiff->format('%r%a');
    return $dDiff->days;
}

function parsePolygon($coordinates) {

    $arr = [];

    if (empty($coordinates))
        return $arr;

    $first = current($coordinates);
    foreach ($coordinates as $cor) {
        array_push($arr, $cor['lat'].' '.$cor['lng']);
    }
    array_push($arr, $first['lat'].' '.$first['lng']);

    return $arr;
}

function timezoneReverse($zone) {
    if (strpos($zone, '+') !== FALSE)
        $zone = str_replace('+', '-', $zone);
    else
        $zone = str_replace('-', '+', $zone);
    return $zone;
}

function prepareDeviceTail($string, $length = 0) {
    $arr = explode(';', $string);
    $tail = [];
    if (count($arr)) {
        $arr = array_reverse(array_slice($arr, 0, $length));
        foreach ($arr as $value) {
            $cords = explode('/', $value);
            if (!isset($cords['1']))
                continue;
            array_push($tail, [
                'lat' => $cords['0'],
                'lng' => $cords['1']
            ]);
        }
    }

    return $tail;
}

function checkCondition($type, $text, $tag_value) {
    $value_number = parseNumber($text);
    $result = FALSE;

    if ($type == 1 && $text == $tag_value)
        $result = TRUE;

    if ($type == 2 && is_numeric($value_number) && $value_number > $tag_value)
        $result = TRUE;

    if ($type == 3 && is_numeric($value_number) && $value_number < $tag_value)
        $result = TRUE;

    return $result;
}

function getGeoAddress($lat, $lon)
{
    $geoAddressHelper = new \Tobuli\Helpers\GeoAddressHelper();
    $address = $geoAddressHelper->getGeoAddress($lat, $lon);

    return $address;
}

function prepareServiceData($input, $values = NULL) {
    $last_service = $input['last_service'];
    if ($input['expiration_by'] == 'days') {
        if (($timestamp = strtotime($last_service)) === false) {
            unset($input['last_service']);
            $last_service = date('Y-m-d', strtotime(tdate(date('Y-m-d H:i:s'), $input['zone'])));
        }

        $input['expires_date'] = date('Y-m-d', strtotime($last_service. ' + '.$input['interval'].' day'));

        if (strtotime(date('Y-m-d', strtotime(tdate(date('Y-m-d H:i:s'), $input['zone'])))) >= strtotime($input['expires_date']) && isset($input['renew_after_expiration'])) {
            $diff = dateDiff($last_service, date('Y-m-d'));

            $times = floor($diff / $input['interval']);
            $input['expires_date'] = date('Y-m-d', strtotime(date('Y-m-d', strtotime(tdate(date('Y-m-d H:i:s'), $input['zone']))). ' + '.($input['interval'] - ($times > 0 ? ($diff - $input['interval'] * $times) : 0)).' day'));
            $input['last_service'] = date('Y-m-d', strtotime($input['expires_date']. ' - '.$input['interval'].' day'));
            $input['event_sent'] = 0;
        }

        $input['remind_date'] = date('Y-m-d', strtotime($input['expires_date']. ' - '.$input['trigger_event_left'].' day'));

        if (strtotime(date('Y-m-d', strtotime(tdate(date('Y-m-d H:i:s'), $input['zone'])))) >= strtotime($input['expires_date']))
            $input['expired'] = 1;
    }
    else {
        $value = $values[$input['expiration_by']];
        $input['last_service'] = (is_numeric($last_service) && $last_service > 0) ? $last_service : 0;
        $input['expires'] = $input['interval'] + $input['last_service'];

        if ($value >= $input['expires'] && isset($input['renew_after_expiration'])) {
            $over = $value - $input['expires'];
            $times = floor($over / $input['interval']);
            $input['expires'] = $input['expires'] + ($input['interval'] - ($times > 0 ? ($over - $input['interval'] * $times) : 0));
            $input['last_service'] = $input['expires'] - $input['interval'];
            $input['event_sent'] = 0;
        }

        $input['remind'] = $input['expires'] - $input['trigger_event_left'];

        if ($value >= $input['expires'])
            $input['expired'] = 1;
    }

    return $input;
}

function serviceExpiration($item, $values = NULL) {
    if ($item->expiration_by == 'days') {
        if (Auth::check())
            $date = date('Y-m-d', strtotime(tdate(date('Y-m-d H:i:s'), Auth::User()->timezone->zone)));
        else
            $date = date('Y-m-d');
        $diff = dateDiff($item->expires_date, date('Y-m-d'));
        if ($diff > 0)
            return trans('validation.attributes.days').' '.trans('front.left').' ('.$diff.')';
        else
            return trans('validation.attributes.days').' '.strtolower(trans('front.expired'));
    }
    elseif ($item->expiration_by == 'odometer') {
        $odometer = $values[$item->expiration_by];
        $diff = $item->expires - $odometer['value'];
        if ($diff > 0)
            return trans('front.odometer').' '.trans('front.left').' ('.$diff.' '.$odometer['sufix'].')';
        else
            return trans('front.odometer').' '.strtolower(trans('front.expired'));
    }
    elseif ($item->expiration_by == 'engine_hours') {
        $engine = $values['engine_hours'];
        $diff = $item->expires - $engine['value'];
        if ($diff > 0)
            return trans('validation.attributes.engine_hours').' '.trans('front.left').' ('.$diff.' '.$engine['sufix'].')';
        else
            return trans('validation.attributes.engine_hours').' '.strtolower(trans('front.expired'));
    }
}

function send_command($post_data) {
    /* //OLD Code
	$headers = [
        'Authorization: Basic ' . base64_encode("admin@admin"),
        'Accept: application/json',
        'Content-Type: application/json'
    ];
    $url = 'http://'.$_SERVER['SERVER_ADDR'].':8082/api/commands';//.(isset($post_data['type']) ? 'send' : 'raw');
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_data));

    return curl_exec($curl);
	*/
	// New Code
	
	$url = 'http://'.$_SERVER['SERVER_ADDR'].':8082/api/commands';
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_PORT => "8082",
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => json_encode($post_data),
		CURLOPT_HTTPHEADER => array(
		"Accept: application/json",
		"Authorization: Basic YWRtaW46RHhiYWRtaW4xMg==",
		"Content-Type: application/json",
		"Postman-Token: c91f3d21-2c3f-4289-a5ae-e370417ce501",
		"cache-control: no-cache"
		),
	));

	return curl_exec($curl);
	/*
	$response = curl_exec($curl);
	$err = curl_error($curl);
	curl_close($curl);
	if ($err) {
	  echo "cURL Error #:" . $err;
	} else {
	  echo $response;
	}
	*/
}

function getDatabaseSize($db_name) {

    $results = DB::select(DB::raw('SHOW VARIABLES WHERE Variable_name = "datadir" OR Variable_name = "innodb_file_per_table"'));

    if (empty($results))
        return 0;

    foreach($results as $variable) {
        if ($variable->Variable_name == 'datadir')
            $dir = $variable->Value;
        if ($variable->Variable_name == 'innodb_file_per_table')
            $innodb_file_per_table = $variable->Value == 'ON' ? true : false;
    }

    if (empty($innodb_file_per_table)) {
        if (empty($dir))
            return 0;

        return exec("du -msh -B1 $dir | cut -f1");
    }

    //calc via DB query (very slow)

    if ( is_array($db_name) ) {
        $dbs = $db_name;
    } else {
        $dbs = [$db_name];
    }

    $where = '';
    foreach($dbs as $db) {
        $where .= '"'.$db.'",';
    }
    $where = trim($where,',');

    $res = DB::select(DB::raw('SELECT table_schema, SUM( data_length + index_length) AS db_size FROM information_schema.TABLES WHERE table_schema IN ('.$where.');'));

    if (empty($res))
        return 0;

    return current($res)->db_size;
}

function getServerInfo() {
    /*$data = explode("\n", file_get_contents("/proc/meminfo"));
    $server = [];
    $meminfo = array();
    foreach ($data as $line) {
        if (empty($line) || strpos($line, ':') === TRUE)
            continue;

        list($key, $val) = explode(":", $line);
        $meminfo[$key] = round(trim(str_replace('kB', '', $val))/1024);
    }*/

    $version = ['version' => '1.80', 'date' => '2016-03-18 15:00:00'];
    $config = DB::table('configs')->where('title', '=', 'server_version')->first();
    if (!empty($config))
        $version = json_decode($config->value, TRUE);

    $server['version'] = $version['version'];
    $server['version_date'] = datetime($version['date']);
    /*$server['ram_total'] = $meminfo['MemTotal'];
    $server['ram_free'] = $meminfo['MemFree'];
    $server['ram_used'] = round($meminfo['MemTotal'] - $meminfo['MemFree']);
    $server['disk_total'] = round(disk_total_space('/')/1024/1024);
    $server['disk_free'] = round(disk_free_space('/')/1024/1024);
    $server['db_used'] = round(getDatabaseSize('tracking_traccar') + getDatabaseSize('tracking_web') + getDatabaseSize('tracking_sensors') + getDatabaseSize('tracking_engine_hours'));
    $server['cpu_load'] = float(exec("grep 'cpu ' /proc/stat | awk '{usage=($2+$4)*100/($2+$4+$5)} END {print usage}'"));*/

    return $server;
}

function getMaps() {
    $maps = Config::get('tobuli.maps');
    if (isset($_ENV['use_slovakia_map']))
        $maps['Tourist map Slovakia'] = 99;
    ksort($maps);
    return array_flip($maps);
}

function getLangs() {
    $langs = File::directories(base_path('resources/lang/'));
    $arr = [];
    foreach ($langs as $lang) {
        $arrr = explode('/', $lang);
        $lang = end($arrr);
        if ($lang == 'de_cs')
            continue;

        $lang_key = $lang;
        if ($lang == 'de' && isset($_ENV['key']) && $_ENV['key'] == 'd13c2c354597c7230228741032959944')
            $lang_key = 'de_cs';

        $arr[$lang_key] = $lang;
    }
    ksort($arr);

    return $arr;
}

function asset_logo_file($type) {
    $file = null;

    if (Session::has('referer_id'))
        $id = Session::get('referer_id');

    if (empty($id) && (Auth::check() && (Auth::User()->group_id == 3 || !empty(Auth::User()->manager_id))))
        $id = Auth::User()->group_id == 3 ? Auth::User()->id : Auth::User()->manager_id;


    if (!empty($id)) {
        switch ($type) {
            case 'logo':
            case 'logo-main':
            case 'background':
                $path = '/var/www/html/images/logos/' . $type . '-' . $id . '.*';
                break;
            case 'favicon':
                $path = '/var/www/html/images/' . $type . '-' . $id . '.ico';
                break;
        }
    }

    if ( !empty($path) ) {
        $file = current(glob($path));
    }

    if ( empty($file) && empty($path) ) {
        $path = '/var/www/html/images/' . $type . '.*';
        $file = current(glob($path));
    }

    return $file;
}

function has_asset_logo($type)
{
    $file = asset_logo_file($type);

    return !empty( $file );
}

function asset_logo($type)
{
    $logo = NULL;
    $file = asset_logo_file($type);
    $time = $file ? filemtime($file) : 0;

    if (Session::has('referer_id'))
        $id = Session::get('referer_id');

    if (empty($id) && (Auth::check() && (Auth::User()->group_id == 3 || !empty(Auth::User()->manager_id))))
        $id = Auth::User()->group_id == 3 ? Auth::User()->id : Auth::User()->manager_id;

    if (!empty($id)) {
        switch ($type) {
            case 'logo':
                $logo = explode('/', current(glob('/var/www/html/images/logos/logo-' . $id . '.*')));
                $logo = end($logo);
                if (!empty($logo))
                    $logo = "logo.php?id=$id&type=logo&t=l" . $time;

                break;

            case 'logo-main':
                $logo = explode('/', current(glob('/var/www/html/images/logos/logo-main-' . $id . '.*')));
                $logo = end($logo);
                if (!empty($logo))
                    $logo = "logo.php?id=$id&type=logo-main&t=m" . $time;

                break;

            case 'background':
                $logo = explode('/', current(glob('/var/www/html/images/logos/background-' . $id . '.*')));
                $logo = end($logo);
                if (!empty($logo))
                    $logo = "logo.php?id=$id&type=background&t=m" . $time;

                break;

            case 'favicon':
                if (file_exists('/var/www/html/images/favicon-' . $id . '.ico'))
                    $logo = "logo.php?id=$id&type=favicon&t=f" . $time;

                break;
        }
    }

    if (empty($logo)) {
        $logo = "logo.php?id=0&type=$type&t=f".$time;
    }

    $path = 'assets/'.$logo;

    if ( App::runningInConsole() )
        $url = \Facades\Server::url() . $path;
    else
        $url = asset( $path );

    return $url;
}

function getFavicon($id = NULL) {
    $logo = NULL;
    if (Session::has('referer_id') && !Auth::check())
        $id = Session::get('referer_id');

    if (!empty($id) || (Auth::check() && (Auth::User()->group_id == 3 || !empty(Auth::User()->manager_id)))) {
        $id = !empty($id) ? $id : (Auth::User()->group_id == 3 ? Auth::User()->id : Auth::User()->manager_id);
        if (file_exists('/var/www/html/images/favicon-'.$id.'.ico'))
            $logo = "logo.php?id=$id&type=favicon&t=f".time();
    }
    if (empty($logo)) {
        $logo = "logo.php?id=0&type=favicon&t=f".time();
    }
    return 'assets/'.$logo;
}

/*
function getMainSetting($key) {
    $defaults = [
        'server_name' => 'GPS Tracker',
        'devices_limit' => 5,
        'subscription_expiration_after_days' => 30,
        'enable_plans' => 0,
        'payment_type' => 1,
        'paypal_client_id' => '',
        'paypal_secret' => '',
        'paypal_currency' => '',
        'paypal_payment_name' => '',
        'default_billing_plan' => '',
        'dst' => NULL,
        'dst_date_from' => '',
        'dst_date_to' => '',
        'geocoder_api' => 'default',
        'api_key' => '',
        'map_center_latitude' => '51.505',
        'map_center_longitude' => '-0.09',
        'map_zoom_level' => 19,
        'user_permissions' => []
    ];

    return array_key_exists($key, $_ENV['main_settings']) ? $_ENV['main_settings'][$key] : $defaults[$key];
}
*/
function getMainSetting($key)
{
    return settings('main_settings.' . $key);
}

function getMainPermission($name, $mode) {
    $mode = trim($mode);
    $modes = Config::get('tobuli.permissions_modes');

    if (!array_key_exists($mode, $modes))
        die('Bad permission');

    $user_permissions = settings('main_settings.user_permissions');

    return $user_permissions && array_key_exists($name, $user_permissions) ? boolval($user_permissions[$name][$mode]) : FALSE;
}

function calibrate($number, $x1, $y1, $x2, $y2) {
    if ($number == $x1)
        return $y1;

    if ($number == $x2)
        return $y2;


    if ($x1 > $x2) {
        $nx1 = $x1;
        $nx2 = $x2;
    }
    else {
        $nx1 = $x2;
        $nx2 = $x1;
    }

    if ($y1 > $y2) {
        $ny1 = $y1;
        $ny2 = $y2;
        $pr = $x2;
    }
    else {
        $ny1 = $y2;
        $ny2 = $y1;
        $pr = $x1;
    }


    $sk = ($pr - $number);
    $sk = $sk < 0 ? -$sk : $sk;

    return (($ny1 - $ny2) / ($nx1 - $nx2)) * $sk + $ny2;
}

function parseUsers($users) {
    $result = '';
    foreach ($users as $user) {
        if (Auth::User()->email != 'admin@atrams.com' && $user == 'admin@atrams.com')
            continue;

        $result .= $user.', ';
    }

    return substr($result, 0, -2);
}

function getUserTimezone($users, $user_id) {
    $timezone_id = '17';
    foreach ($users as $user) {
        if ($user['id'] == $user_id) {
            $timezone_id = $user['pivot']['timezone_id'];
            break;
        }
    }

    return $timezone_id;
}

function radians($deg) {
    return $deg * M_PI / 180;
}

function getDistance($latitude, $longitude, $last_latitude, $last_longitude) {
    if (is_null($latitude) || is_null($longitude) || is_null($last_latitude) || is_null($last_longitude) || ($latitude == $last_latitude && $longitude == $last_longitude))
        return 0;
    $result = rad2deg((acos(cos(radians($last_latitude)) * cos(radians($latitude)) * cos(radians($last_longitude) - radians($longitude)) + sin(radians($last_latitude)) * sin(radians($latitude))))) * 111.045;
    if (is_nan($result))
        $result = 0;

    return $result;
}

function parseNumber($string) {
    preg_match("/-?((?:[0-9]+,)*[0-9]+(?:\.[0-9]+)?)/", $string, $matches);
    if (isset($matches['0']))
        return $matches['0'];

    return '';
}

function parseEventMessage($message, $type) {
    if (!is_null($type)) {
        if ($type == 'zone_in' || $type == 'zone_out')
            $message = trans('front.'.$type);

        if ($type == 'driver')
            $message = trans('front.driver_alert', ['driver' => $message]);

        if ($type == 'overspeed') {
            $data = json_decode($message, true);
            $message = trans('front.overspeed').'('.$data['overspeed_speed'].' '.($data['overspeed_distance'] == 1 ? trans('front.km') : trans('front.mi')).')';
        }
    }

    return $message;
}

function apiArray($arr) {
    $result = [];
    foreach($arr as $id => $value)
        array_push($result, ['id' => $id, 'value' => $value, 'title' => $value]);

    return $result;
}

function clearCache($imei, $prefix) {
    try {
        //$redis = new \Redis();
		$redis = Redis4;
        $redis->connect('127.0.0.1', 6379);
        if (is_array($prefix)) {
            foreach ($prefix as $key => $value) {
                $redis->del($value.'_'.$imei);
            }
        }
        else {
            $redis->del($prefix.'_'.$imei);
        }
        $redis->close();
    }
    catch (Exception $e) {}
}

function snapToRoad(&$items, &$cords) {
    $cord_id = count($cords);
    foreach ($items as $item_key => $item) {
        if (count($item['items']) <= 1)
            continue;

        $path = '';
        $item_cords = array_intersect_key($cords, $item['items']);
        foreach ($item_cords as $item_cord) {
            $path .= $item_cord['lat'].','.$item_cord['lng'].'|';
        }
        $path = substr($path, 0, -1);
        $response = @json_decode(@file_get_contents('https://roads.googleapis.com/v1/snapToRoads?path='.$path . '&interpolate=true&key=AIzaSyDG5ZheVmnPJbn5t0hsEF8e8ZRG-k_X0Xc'), true);

        $i = 0;
        $new_items = [];
        foreach ($item['items'] as $key => $value) {
            while (!isset($response['snappedPoints'][$i]['originalIndex'])) {
                if (!isset($response['snappedPoints'][$i]))
                    break;

                $cord_id++;
                $new_id = 'new'.$cord_id;
                $cords[$new_id] = [
                    'lat' => $response['snappedPoints'][$i]['location']['latitude'],
                    'lng' => $response['snappedPoints'][$i]['location']['longitude']
                ];
                $new_items[$new_id] = '';
                $i++;
            }
            if (!isset($response['snappedPoints'][$i]))
                continue;

            $new_items[$key] = '';
            $cords[$key]['lat'] = $response['snappedPoints'][$i]['location']['latitude'];
            $cords[$key]['lng'] = $response['snappedPoints'][$i]['location']['longitude'];
            $i++;
        }

        if (!empty($new_items))
            $items[$item_key]['items'] = $new_items;
    }
}

function generateConfig($cur_ports) {
  $ports = '';
  foreach ($cur_ports as $port) {
      if (!$port['active'])
          continue;

      $ports .= "<entry key='".$port['name'].".port'>".$port['port']."</entry>\n";
      $extras = json_decode($port['extra'], TRUE);
      if (!empty($extras)) {
          foreach ($extras as $key => $value) {
              $ports .= "<entry key='".$port['name'].".{$key}'>{$value}</entry>\n";
          }
      }
  }

  $rem_cfg = file_get_contents(storage_path() . "/app/configs/config.txt");

  if ( env('DB_HOST', 'localhost') != 'localhost' ) {
      $rem_cfg = strtr($rem_cfg, [
          'mysql://127.0.0.1' => 'mysql://'. env('DB_HOST', 'localhost'),
          'database.user\'>root' => 'database.user\'>'.env('traccar_username', 'root')
      ]);
  }

  $rem_cfg = strtr($rem_cfg, [
      '&' => '&amp;',
      'mysql://127.0.0.1:3306/atrams_traccar' => 'mysql://127.0.0.1:3306/'.$_ENV['traccar_database'],
      'database.user\'>root' => 'database.user\'>' . $_ENV['traccar_username'],
      'database.password\'>secret' => 'database.password\'>' . $_ENV['traccar_password'],
      '[LOGSPATH]' => isset($_ENV['logs_path']) ? $_ENV['logs_path'].'tracker-server.log' : '/opt/traccar/logs/tracker-server.log',
      '[SERVERKEY]' => $_ENV['key'],
      '[LOCALURL]' => (isset($_ENV['app_host']) && $_ENV['app_host'] ) ? $_ENV['app_host'] : 'localhost',
      '[MYSQLPASSWORD]' => $_ENV['traccar_password'],
      '[TRACKERPORTS]' => $ports
  ]);

  if ( isset($_ENV['app_ssl']) && $_ENV['app_ssl'] ) {
      $rem_cfg = str_replace('forward.url\'>http://', 'forward.url\'>https://', $rem_cfg);
  }

  $rem_cfg = strtr($rem_cfg, [
      "<entry key='redis.enable'>false</entry>"  => "<entry key='redis.enable'>true</entry>",
      "<entry key='forward.enable'>true</entry>" => "<entry key='forward.enable'>false</entry>",
  ]);

  file_put_contents('/opt/traccar/conf/traccar.xml', $rem_cfg);
}

function gen_polygon_text($items) {
    $cor_text = NULL;
    foreach($items as $item) {
        $cor_text .= $item['lat'].' '.$item['lng'].',';
    }
    if ($item['lat'] != $items['0']['lat'] || $item['lng'] != $items['0']['lng'])
        $cor_text .= $items['0']['lat'].' '.$items['0']['lng'];
    else
        $cor_text = substr($cor_text, 0, -1);

    return $cor_text;
}

function cmpdate($a, $b) {
    return strcmp($b['date'], $a['date']);
}

function rcmp($a, $b) {
    return strcmp($b['sort'], $a['sort']);
}

function cmp($a, $b) {
    return strcmp($a['sort'], $b['sort']);
}

function setflagFormulaGet($sensor, $value) {
    preg_match('/\%SETFLAG\[([0-9]+)\,([0-9]+)\]\%/', $sensor['formula'], $match);
    if (isset($match['1']) && isset($match['2'])) {
        $sensor['formula'] = str_replace($match['0'], '[value]', $sensor['formula']);
        $value = parseNumber(substr($value, $match['1'], $match['2']));
    }
    else {
        $value = parseNumber($value);
    }

    return [
        'formula' => $sensor['formula'],
        'value' => $value
    ];
}

function setflagWithValueGet($value, $ac_value) {
    preg_match('/\%SETFLAG\[([0-9]+)\,([0-9]+)\,([\s\S]+)\]\%/', $ac_value, $match);
    if (isset($match['1']) && isset($match['2']) && isset($match['3'])) {
        $ac_value = $match['3'];
        $value = substr($value, $match['1'], $match['2']);
    }
    else {
        $value = $value;
    }

    return [
        'ac_value' => $ac_value,
        'value' => $value
    ];
}

function splitTimeAtMidnight($start, $end) {
    $arr = [];
    $start_date = date('Y-m-d', strtotime($start. '+1day'));
    if (date('d', strtotime($end)) != date('d', strtotime($start))) {
        $arr[] = [
            'start' => $start,
            'end' => date('Y-m-d H:i:s', strtotime($start_date)),
            'duration' => secondsToTime(strtotime($start_date) - strtotime($start))
        ];
        $start = $start_date;
        while (date('d', strtotime($end)) != date('d', strtotime($start))) {
            $ends = date('Y-m-d', strtotime($start. '+1day'));
            $arr[] = [
                'start' => date('Y-m-d H:i:s', strtotime($start)),
                'end' => date('Y-m-d H:i:s', strtotime($ends)),
                'duration' => secondsToTime(strtotime($ends) - strtotime($start))
            ];
            $start = $ends;
        }

        $arr[] = [
            'start' => date('Y-m-d H:i:s', strtotime($start)),
            'end' => $end,
            'duration' => secondsToTime(strtotime($end) - strtotime($start))
        ];
    }

    return count($arr) > 0 ? $arr : $end;
}

function stripInvalidXml($value)
{
    $ret = "";
    if (empty($value))
    {
        return $ret;
    }

    $length = strlen($value);
    for ($i=0; $i < $length; $i++)
    {
        $current = ord($value{$i});
        if (($current == 0x9) ||
            ($current == 0xA) ||
            ($current == 0xD) ||
            (($current >= 0x20) && ($current <= 0xD7FF)) ||
            (($current >= 0xE000) && ($current <= 0xFFFD)) ||
            (($current >= 0x10000) && ($current <= 0x10FFFF)))
        {
            $ret .= chr($current);
        }
        else
        {
            $ret .= " ";
        }
    }
    return $ret;
}

function parseXML($text) {
    $arr = [];
    $text = stripInvalidXml($text);

    try {
        $xml = new \SimpleXMLElement($text);
    } catch (\Exception $e) {
        $xml = FALSE;
    }

    if (empty($xml))
        return $arr;

    foreach ($xml as $key => $value) {
        if (is_array($value))
            continue;
        $arr[] = htmlentities($key).': '.htmlentities($value);
    }

    return $arr;
}

function restartTraccar($reason) {
  //$restart =  exec('sudo /opt/traccar/bin/traccar restart');
  $restart =  exec("sudo service traccar restart");
  
  if (strpos($restart, 'running: PID:') !== false) {
      $response = TRUE;
  }
  else {
    $response = FALSE;
  }
  return $response;
}

function parsePorts($ports = NULL) {
  if (empty($ports)) {
      $ports = json_decode(file_get_contents(storage_path() . "/app/configs/ports.json"), TRUE);
  }
  $arr = [];
  foreach ($ports as $port)
      $arr[$port['name']] = $port;
  $ports = $arr;
  unset($arr);

  $cur_ports = json_decode(json_encode(DB::table('tracker_ports')->get()), TRUE);
  $arr = [];
  foreach ($cur_ports as $port) {
      if (!isset($ports[$port['name']])) {
          DB::table('tracker_ports')->where('name', '=', $port['name'])->delete();
          continue;
      }
      $arr[$port['name']] = $port;
  }
  $cur_ports = $arr;
  unset($arr);

  foreach ($ports as $port) {
      if (!isset($cur_ports[$port['name']])) {
          while(!empty(DB::table('tracker_ports')->where('port', '=', $port['port'])->first())) {
              $port['port']++;
          }
          DB::table('tracker_ports')->insert([
              'name' => $port['name'],
              'port' => $port['port'],
              'extra' => $port['extra']
          ]);
      }
      else {
          $extras = json_decode($port['extra'], TRUE);
          if (!empty($extras)) {
              $cur_extras = json_decode($cur_ports[$port['name']]['extra'], TRUE);
              $update = FALSE;
              foreach ($extras as $key => $value) {
                  if (!isset($cur_extras[$key])) {
                      $cur_extras[$key] = $value;
                      $update = TRUE;
                  }
              }

              if ($update) {
                  DB::table('tracker_ports')->where('name', '=', $port['name'])->update([
                      'extra' => json_encode($cur_extras)
                  ]);
              }
          }
      }
  }
}

function getArrValue($arr, $val) {
    return isset($arr[$val]) ? $arr[$val] : '';
}

function updateUsersBillingPlan() {

    $settings = settings('main_settings');

    if (isset($settings['enable_plans']) && $settings['enable_plans']) {
        $plan = DB::table('billing_plans')->find($settings['default_billing_plan']);
        if (!empty($plan)) {
            $update = [
                'billing_plan_id' => $settings['default_billing_plan'],
                'devices_limit' => $plan->objects,
                'subscription_expiration' => date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')." + {$plan->duration_value} {$plan->duration_type}"))
            ];

            DB::table('users')
                ->whereNull('billing_plan_id')
                ->where('group_id', '=', 2)
                ->update($update);
        }
    }
    else {
        DB::table('users')
            ->whereNotNull('billing_plan_id')
            ->where('group_id', '=', 2)
            ->update([
                'billing_plan_id' => NULL,
                'subscription_expiration' => '0000-00-00 00:00:00'
            ]);
    }
}

function getManagerUsedLimit($manager_id, $except = NULL) {
    $query = DB::table('users')
        ->where('manager_id', '=', $manager_id);

    if (!is_null($except))
        $query->where('id', '!=', $except);

    $users_limit =  $query->sum('devices_limit');

    $manager_limit = DB::table('user_device_pivot')
        ->join('devices', function($query) {
            $query->on('user_device_pivot.device_id', '=', 'devices.id');
            $query->where('devices.deleted', '=', '0');
        })
        ->where('user_device_pivot.user_id', '=', $manager_id)
        ->count();

    return $users_limit + $manager_limit;
}

function hasLimit() {
    return (Auth::User()->group_id == 3 && !is_null(Auth::User()->devices_limit));
}

function streetViewLang($lang) {
    if ($lang == 'br')
        $lang = 'pt';

    if ($lang == 'ch')
        $lang = 'es';

    if ($lang == 'de_cs')
        $lang = 'de';

    if ($lang == 'uk')
        $lang = 'en';

    return $lang;
}

function parseTranslations($en_translations, $trans) {
    $out = "<?php

return array(\n";
    foreach ($en_translations as $key => $tran) {
        $tran = array_key_exists($key, $trans) ? $trans[$key] : $tran;
        if (is_array($tran)) {
            $out .= "'".$key."' => [\n";
            foreach ($tran as $skey => $tran) {
                if (is_array($tran)) {
                    $out .= "\t'".$skey."' => [\n";
                    foreach ($tran as $sskey => $tran) {
                        $tran = array_key_exists($key, $trans) && array_key_exists($skey, $trans[$key]) && array_key_exists($sskey, $trans[$key][$skey]) ? $trans[$key][$skey][$sskey] : $tran;
                        $tran = strtr($tran, [
                            "\'" => "'",
                            "\\'" => "'",
                            "\\\'" => "'",
                            "\\\'" => "'",
                            "\\\'" => "'",
                            "\\\'" => "'",
                            "\\\'" => "'",
                        ]);
                        $out .= "\t\t'$sskey' => '".addcslashes($tran, "'")."',\n";
                    }
                    $out .= "\t],\n";
                }
                else {
                    $tran = array_key_exists($key, $trans) && array_key_exists($skey, $trans[$key]) ? $trans[$key][$skey] : $tran;
                    $tran = strtr($tran, [
                        "\'" => "'",
                        "\\'" => "'",
                        "\\\'" => "'",
                        "\\\'" => "'",
                        "\\\'" => "'",
                        "\\\'" => "'",
                        "\\\'" => "'",
                    ]);
                    $out .= "\t'$skey' => '".addcslashes($tran, "'")."',\n";
                }
            }
            $out .= "],\n";
        }
        else {
            $out .= "'$key' => '".addcslashes($tran, "'")."',\n";
        }
    }
    $out .= ");\n";

    return $out;
}

function rtl($string, &$data)
{
    if ($data['format'] == 'pdf' && $data['lang'] == 'ar' && preg_match( "/\p{Arabic}/u", $string ))
        return $data['arabic']->utf8Glyphs($string);

    return $string;
}

function isProcessRunning($pidFile = '/var/run/myfile.pid')
{
    if (!file_exists($pidFile) || !is_file($pidFile)) return false;
    $pid = @file_get_contents($pidFile);
    return $pid && file_exists("/proc/{$pid}");
}

function removePidFile($pidFile = '/var/run/myfile.pid')
{
    unlink($pidFile);
}

class SettingsArray
{
    private $array = [];
    public function __construct(array $array)
    {
        $this->array = $array;
    }

    public function getArray()
    {
        return $this->array;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->array))
            return $this->array[$name] == 1 ? TRUE : FALSE;

        return TRUE;
    }
}

function parseXMLToArray($text) {
    $arr = [];
    try {
        $text = stripInvalidXml($text);
        $xml = new \SimpleXMLElement($text);
        foreach ($xml as $key => $value) {
            if (is_array($value))
                continue;
            $arr[htmlentities($key)] = htmlentities($value);
        }
    } catch (Exception $e) {}

    return $arr;
}

function smartPaginate($page, $total, $limit = 3) {
    $arr = [1];

    if ($page < 1)
        $page = 1;

    if ($page > $total)
        $page = $total;

    if ($page - ($limit + 3) > 0) {
        $arr[] = '.';
        for ($i = $limit; $i > 0; $i--) {
            $arr[] = $page - $i;
        }
    }
    else {
        for ($i = 2; $i < $page; $i++) {
            $arr[] = $i;
        }
    }

    if ($page > 1)
        $arr[] = $page;

    if ($page + ($limit + 2) < $total) {
        for ($i = 1; $i <= $limit; $i++) {
            $arr[] = $page + $i;
        }
        $arr[] = '.';
    }
    else {
        for ($i = 1; $i < $total - $page; $i++) {
            $arr[] = $page + $i;
        }
    }

    if ($page < $total)
        $arr[] = $total;

    return $arr;
}

function deviceIconColor($item, $dev_online, $icon_colors, $sensor) {
    $icon_status = 'offline';
    if ($dev_online == 'ack') {
        $icon_status = 'stopped';
    }
    else {
        if (!empty($sensor) && !empty($sensor['type'])) {
            $sensor_value = getSensorValueBool(NULL, $sensor, $sensor['value']);

            if ($dev_online == 'online' && $sensor_value == 0) {
                $icon_status = 'stopped';
            }
            else {
                if ($dev_online == 'online' && $item['speed'] < $item['min_moving_speed']) {
                    $icon_status = 'stopped';
                }
                elseif ($dev_online == 'online' && $item['speed'] > $item['min_moving_speed']) {
                    $icon_status = 'moving';
                }
            }
        }
        else {
            if ($dev_online == 'online' && $item['speed'] < $item['min_moving_speed']) {
                $icon_status = 'stopped';
            }
            elseif ($dev_online == 'online' && $item['speed'] > $item['min_moving_speed']) {
                $icon_status = 'moving';
            }
        }
    }

    return $icon_colors[$icon_status];
}

function getDeviceStatus($item, $sensor = null) {

    $server_time = isset($item['server_time']) ? $item['server_time'] : null;
    $ack_time = isset($item['ack_time']) ? $item['ack_time'] : null;

    $server_time = $server_time ? $server_time : (isset($item['traccar']['server_time']) ? $item['traccar']['server_time'] : null);
    $ack_time = $ack_time ? $ack_time : (isset($item['traccar']['ack_time']) ? $item['traccar']['ack_time'] : null);

    $speed = isset($item['speed']) ? $item['speed'] : null;
    $speed = !is_null($speed) ? $speed : (isset($item['traccar']['speed']) ? $item['traccar']['speed'] : 0);

    $dev_online = isDeviceOnline($server_time, $ack_time);


    $detect_engine = $item['engine_hours'] == 'engine_hours' ? $item['detect_engine'] : $item['engine_hours'];

    if (!empty($item['sensors']) && !empty($detect_engine) && $detect_engine != 'gps') {
        foreach ($item['sensors'] as $isensor) {
            if ($isensor['type'] == $detect_engine) {
                $sensor = $isensor;
                break;
            }
            /*
            if ($isensor['type'] == 'engine' || $isensor['type'] == 'ignition')
                $sensor = $isensor;

            if ($isensor['type'] == 'acc') {
                $sensor = $isensor;
                break;
            }
            */
        }
    }

    $status = 'offline';

    if ($dev_online == 'ack')
        return $status = 'ack';

    if (!empty($sensor) && !empty($sensor['type'])) {
        $sensor_value = getSensorValueBool(NULL, $sensor, $sensor['value']);

        if ($dev_online == 'online' && $sensor_value == 0) {
            $status = 'ack';
        }
        else {
            if ($dev_online == 'online' && $speed < $item['min_moving_speed']) {
                $status = 'engine';
            }
            elseif ($dev_online == 'online' && $speed > $item['min_moving_speed']) {
                $status = 'online';
            }
        }
    }
    else {
        if ($dev_online == 'online' && $speed < $item['min_moving_speed']) {
            $status = 'ack';
        }
        elseif ($dev_online == 'online' && $speed > $item['min_moving_speed']) {
            $status = 'online';
        }
    }

    return $status;
}

function getDeviceStatusColor($item, $status = null) {

    if ( empty($status) )
        $status = getDeviceStatus($item);

    $icon_colors = is_array($item['icon_colors']) ? $item['icon_colors'] : json_decode($item['icon_colors'], TRUE);

    switch ($status) {
        case 'online':
            $icon_status = 'moving';
            break;
        case 'ack':
            $icon_status = 'stopped';
            break;
        case 'engine':
            $icon_status = 'engine';
            break;
        default:
            $icon_status = 'offline';
    }

    return $icon_colors[$icon_status];
}

/**
 * @param array $array1
 * @param array $array2
 * @return array
 */
function array_merge_recursive_distinct ( array &$array1, array &$array2 )
{
    $merged = $array1;

    foreach ( $array2 as $key => &$value )
    {
        if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) )
        {
            $merged [$key] = array_merge_recursive_distinct ( $merged [$key], $value );
        }
        else
        {
            $merged [$key] = $value;
        }
    }

    return $merged;
}

function teltonikaIbutton($str) {
    $str = dechex($str);
    if (!is_int(strlen($str)/2))
        $str = '0'.$str;

    $arr = str_split(strrev($str), 2);
    $res = '';
    foreach ($arr as $item) {
        $res .= strrev($item);
    }

    return $res;
}

function listviewTrans($user_id, &$settings, &$fields)
{
    $fields_trans    = config('tobuli.listview_fields_trans');
    $sensors_trans   = config('tobuli.sensors');

    $sensors = \Facades\Repositories\DeviceSensorRepo::whereUserId( $user_id );

    foreach($sensors as $sensor) {
        $hash = $sensor->hash;

        $fields[$hash] = [
            'field' => $hash,
            'class' => 'sensor',
            'type'  => $sensor->type,
            'name'  => $sensor->name
        ];

        if ( isset($settings['columns']) ) {
            foreach ($settings['columns'] as &$column) {
                if ( $column['field'] != $hash )
                    continue;

                $column['title'] = $sensor->name . " (" . $column['title'] . ")";
            }
        }
    }

    foreach($fields as &$field) {
        if ($field['class'] == 'sensor') {
            $field['title'] = $field['name'] . " (" . $sensors_trans[$field['type']] . ")";
        } else {
            $field['title'] = $fields_trans[ $field['field'] ];
        }

        $field['title'] = htmlentities( $field['title'], ENT_QUOTES );
    }
}
