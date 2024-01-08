<?php namespace App\Console\Commands;
ini_set('memory_limit', '-1');
set_time_limit(0);

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use Tobuli\Helpers\PolygonHelper;
use App\Console\ProcessManager;
use Bugsnag\BugsnagLaravel\BugsnagFacade as Bugsnag;
use Illuminate\Support\Facades\Redis;
class InsertCommand extends Command {

    const MIN_DISTANCE = 0.02;

    protected $redis;

    protected $processManager;

    protected $device;

    protected $lastPosition;

    protected $keysCount;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'insert:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';


    public function __construct()
    {
        parent::__construct();

        try {
            //$this->redis = new \Redis();
            //$this->redis->connect('127.0.0.1', 6379);
			$this->redis = Redis;
			
        }
        catch (\Exception $e) {
            $this->redis = FALSE;
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        if (!$this->redis) {
            echo "Redis not running";
            return;
        }

        $timeout = config('tobuli.process.insert_timeout');
        $limit = config('tobuli.process.insert_limit');

        $this->processManager = new ProcessManager('insert:run', $timeout, $limit);

        if (!$this->processManager->canProcess())
        {
            echo "Cant process.";
            return;
        }

        DB::disableQueryLog();

        while ( $this->processManager->canProcess() )
        {
            $this->processData();

            if ( $this->keysCount && $this->keysCount > 1000 ) {
                usleep(1000);
            } else {
                sleep(1);
            }

        }
    }

    private function processData() {
        $keys = $this->redis->keys('position.*');

        asort($keys);

        $keys = array_slice($keys, 0, 10000);

        $this->keysCount = count( $keys );

        foreach($keys as $key) {
            $data = $this->getData($key);

            if (empty($data)) {
                $this->redis->del($key);
                continue;
            }

            if (!$this->processManager->lock($data['deviceId']))
                continue;

            $this->insert($data);

            $this->redis->del($key);

            $this->processManager->unlock($data['deviceId']);
        }
    }

    private function getData($key)
    {
        $value = $this->redis->get($key);

        $data = json_decode($value, true);

        if (!$data)
            return false;

        if (!empty($data['imei']))
            $data['deviceId'] = $data['imei'];

        if (!empty($data['uniqueId']))
            $data['deviceId'] = $data['uniqueId'];

        if (empty($data['deviceId']))
            return false;

        return $data;
    }
    private function insert($data)
    {
        try {
            if (!$this->prepare($data))
                return;

            $this->sensors($data);
            $this->position($data);
            $this->alerts($data);
        } catch (\Exception $e) {
            Bugsnag::notifyException($e);
        }
    }

    private function prepare(&$data)
    {
        $this->device = null;
        $this->lastPosition = null;

        if (empty($data))
            return FALSE;

        if (!isset($data['attributes']))
            return FALSE;

        $data['speed'] = $data['speed'] * 1.852;

        $data = array_merge([
            'altitude' => 0,
            'course' => 0,
            'latitude' => null,
            'longitude' => null,
            'speed' => 0,
            'valid' => 1,

            'speed_in_miles' => kilometersToMiles($data['speed']),
            'imei' => $data['deviceId'],
            'ack' => empty($data['fixTime']),
            'other' => $data['attributes'],
            'time' => date('Y-m-d H:i:s', $data['fixTime'] / 1000)
        ], $data);

        if ($data['ack']) {
            if (!empty($data['deviceTime'])) {
                $data['time'] = date('Y-m-d H:i:s', $data['deviceTime'] / 1000);
            }
            else {
                $data['time'] = null;
            }
        }

        if (!$data['ack'] && (strtotime($data['time']) - time() > 259200 || $data['latitude'] == 0 || $data['longitude'] == 0))
            return FALSE;

        $deviceQuery = DB::connection('traccar_mysql')
            ->table('devices')
            ->select('devices.*', 'web_devices.parameters', 'web_devices.id as web_device_id', 'web_devices.detect_engine', 'web_devices.engine_hours')
            ->join('tracking_web.devices as web_devices', 'devices.id', '=', 'web_devices.traccar_device_id');

        if ( $data['protocol'] == 'tk103' && strlen($data['imei']) > 11 ) {
            $deviceQuery->where('devices.uniqueId', 'like', '%' . substr($data['imei'], -11));
        } else {
            $deviceQuery->where('devices.uniqueId', $data['imei']);
        }
        $this->device = $deviceQuery->first();

        if ( !$this->device ) {
            try{
                DB::connection('traccar_mysql')->select(
                    DB::raw("
                  INSERT INTO `unregistered_devices_log` (imei, port, times)
                  SELECT :imei AS imei, port, 1 as times FROM tracking_web.tracker_ports WHERE `name` = :protocol LIMIT 1
                  ON DUPLICATE KEY UPDATE times = (times + 1)"),
                    [
                        'imei'     => $data['imei'],
                        'protocol' => $data['protocol']
                    ]
                );
            } catch(\Exception $e){}

            return FALSE;
        }

        if (is_null($data['time'])) {
            $data['time'] = $this->device->time;
        }

        $data['history'] = !is_null($this->device->time) && strtotime($data['time']) < strtotime($this->device->time);

        if (empty($this->device->latestPosition_id)) {
            $lastPosition = $this->lastPosition();

            if ($lastPosition) {
                DB::connection('traccar_mysql')
                    ->table('devices')
                    ->where('devices.id', $this->device->id)
                    ->update(['latestPosition_id' => $lastPosition->id]);

                $this->device->latestPosition_id = $lastPosition->id;
            }
        }


        $other_arr = $data['other'];
        $other_arr = is_array($other_arr) ? $other_arr : [];
        $other_arr = array_change_key_case($other_arr, CASE_LOWER);

        $merged_protocols = ['gt02', 'gt06', 'gps103', 'h02'];

        if ( in_array($data['protocol'], $merged_protocols) ) {
            $lastPosition = $this->lastPosition();

            if ( !empty($lastPosition->other) ) {
                try{
                    $xml_object = simplexml_load_string($lastPosition->other);
                } catch (\Exception $e) {
                    $xml_object = FALSE;
                }

                if ( $xml_object && $last_other = json_decode(json_encode((array) $xml_object ), true) ) {
                    $other_arr = array_merge($last_other, $other_arr);
                }
            }
        }

        $other_xml = '<info>';
        foreach ($other_arr as $key => $value) {
            if (is_numeric($key)) continue;
            if (is_array($value)) continue;

            $value = is_bool($value) ? ($value ? 'true' : 'false') : $value;
            $other_xml .= "<{$key}>{$value}</$key>";
        }
        $other_xml .= '</info>';

        $params = empty($this->device->parameters) ? [] : array_flip(json_decode($this->device->parameters, true));
        $params = array_map(function($val) { return strtolower($val); }, $params);

        $merge = array_keys(array_merge($other_arr, $params));
        if (count($params) != count($merge)) {
            $this->updateDevice([
                'parameters' => json_encode($merge)
            ]);
        }

        $data['other_arr'] = $other_arr;
        $data['other_xml'] = $other_xml;

        return TRUE;
    }

    private function sensors(&$data) {
        $engine_status = FALSE;

        if (!$data['history']) {
            preg_match_all('~<([^/][^>]*?)>~', $data['other_xml'], $tags_arr, PREG_PATTERN_ORDER);
            $tags_arr = array_flip(array_flip($tags_arr['1']));

            # SENSORS
            $engine_sensor = NULL;
            $detect_engine = $this->device->engine_hours;
            if ($this->device->engine_hours == 'engine_hours')
                $detect_engine = $this->device->detect_engine;

            if ($detect_engine != 'gps') {
                $engine_sensor = $this->getDeviceEngineSensor($detect_engine);

                if (!empty($engine_sensor))
                    $engine_status = getSensorValueBool(NULL, $engine_sensor, $engine_sensor['value']);
                else
                    $engine_status = TRUE;
            } else {
                $engine_status = TRUE;
            }

            if (!empty($tags_arr)) {
                $sensors = $this->getDeviceSensors($tags_arr);

                if (!empty($sensors)) {
                    foreach ($sensors as $sensor) {
                        preg_match('/<' . preg_quote($sensor['tag_name'], '/') . '>(.*?)<\/' . preg_quote($sensor['tag_name'], '/') . '>/s', $data['other_xml'], $matches);
                        if (isset($matches['1'])) {
                            $value = $matches['1'];
                            $update_value = $value;
                            $value_number = parseNumber($value);

                            preg_match('/\%SETFLAG\[([0-9]+)\,([0-9]+)\]\%/', $sensor['formula'], $match);
                            if (isset($match['1']) && isset($match['2'])) {
                                $sensor['formula'] = str_replace($match['0'], '[value]', $sensor['formula']);
                                $formula_value = parseNumber(substr($value, $match['1'], $match['2']));
                            } else {
                                $formula_value = $value_number;
                            }

                            if ($sensor['type'] == 'acc') {
                                preg_match('/\%SETFLAG\[([0-9]+)\,([0-9]+)\,([\s\S]+)\]\%/', $sensor['on_value'], $match);
                                if (isset($match['1']) && isset($match['2']) && isset($match['3'])) {
                                    $sensor['on_value'] = $match['3'];
                                    $on_value = substr($value, $match['1'], $match['2']);
                                } else {
                                    $on_value = $value;
                                }

                                preg_match('/\%SETFLAG\[([0-9]+)\,([0-9]+)\,([\s\S]+)\]\%/', $sensor['off_value'], $match);
                                if (isset($match['1']) && isset($match['2']) && isset($match['3'])) {
                                    $sensor['off_value'] = $match['3'];
                                    $off_value = substr($value, $match['1'], $match['2']);
                                } else {
                                    $off_value = $value;
                                }
                            }

                            if (in_array($sensor['type'], ['ignition', 'door', 'engine', 'drive_business', 'drive_private'])) {
                                preg_match('/\%SETFLAG\[([0-9]+)\,([0-9]+)\,([\s\S]+)\]\%/', $sensor['on_tag_value'], $match);
                                if (isset($match['1']) && isset($match['2']) && isset($match['3'])) {
                                    $sensor['on_tag_value'] = $match['3'];
                                    $on_tag_value = substr($value, $match['1'], $match['2']);
                                } else {
                                    $on_tag_value = $value;
                                }

                                preg_match('/\%SETFLAG\[([0-9]+)\,([0-9]+)\,([\s\S]+)\]\%/', $sensor['off_tag_value'], $match);
                                if (isset($match['1']) && isset($match['2']) && isset($match['3'])) {
                                    $sensor['off_tag_value'] = $match['3'];
                                    $off_tag_value = substr($value, $match['1'], $match['2']);
                                } else {
                                    $off_tag_value = $value;
                                }
                            }

                            $sensor_update = TRUE;
                            if ($sensor['type'] == 'acc') {
                                if ($sensor['on_value'] != $on_value && $sensor['off_value'] != $off_value)
                                    $sensor_update = FALSE;
                            }


                            if (in_array($sensor['type'], ['ignition', 'door', 'engine', 'drive_business', 'drive_private']) && !checkCondition($sensor['on_type'], $on_tag_value, $sensor['on_tag_value']) && !checkCondition($sensor['off_type'], $off_tag_value, $sensor['off_tag_value']))
                                $sensor_update = FALSE;

                            if ($sensor_update) {
                                if ($sensor['id'] == $engine_sensor['id'])
                                    $engine_status = getSensorValueBool(NULL, $sensor, $update_value);

                                $update_arr = [
                                    'value' => $update_value,
                                ];

                                if ($sensor['type'] == 'odometer' && $sensor['odometer_value_by'] == 'connected_odometer')
                                    $update_arr['value_formula'] = solveEquation($formula_value, $sensor['formula']);

                                if ((isset($sensor['value_formula']) && isset($update_arr['value_formula']) && $sensor['value_formula'] != $update_arr['value_formula']) || $sensor['value'] != $update_arr['value']) {

                                    DB::table('device_sensors')
                                        ->where('id', '=', $sensor['id'])
                                        ->update($update_arr);

                                    if (in_array($sensor['type'], ['acc', 'engine', 'ignition'])) {
                                        $table_name = 'engine_hours_' . $this->device->id;

                                        try {
                                            DB::connection('engine_hours_mysql')
                                                ->table($table_name)
                                                ->insert([
                                                    'sensor_id' => $sensor['id'],
                                                    'other' => $data['other_xml'],
                                                    'time' => $data['time']
                                                ]);
                                        } catch(\Exception $e){
                                            $table = DB::connection('engine_hours_mysql')->select( DB::raw("SHOW TABLES LIKE '$table_name'") );

                                            if (!$table) {
                                                try {
                                                    $sql = "CREATE TABLE IF NOT EXISTS `$table_name`(
                                                  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
                                                  `sensor_id` int(10) unsigned NOT NULL,
                                                  `other` text,
                                                  `time` datetime DEFAULT NULL,
                                                  PRIMARY KEY (`id`),
                                                  INDEX `sensor_id` (`sensor_id`),
                                                  INDEX `time` (`time`)
                                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

                                                    DB::connection('engine_hours_mysql')->select(DB::raw($sql));
                                                } catch(\Exception $e){}
                                            }

                                            DB::connection('engine_hours_mysql')
                                                ->table($table_name)
                                                ->insert([
                                                    'sensor_id' => $sensor['id'],
                                                    'other' => $data['other_xml'],
                                                    'time' => $data['time']
                                                ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    unset($sensors);
                }
            }
        }

        $data['engine_status'] = $engine_status;
    }

    private function position(&$data) {
        $inserted_new_position = FALSE;

        if (!$data['ack']) {
            if (!$data['history']) {
                $last = $this->device;
                $last->latitude = $this->device->lastValidLatitude;
                $last->longitude = $this->device->lastValidLongitude;
            }
            else {
                $last = DB::connection('traccar_mysql')
                    ->table('positions_'.$this->device->id)
                    ->select('*')
                    ->where('time', '<=', $data['time'])
                    ->orderBy('time', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                if ( !empty($last) )
                    $last->latestPosition_id = $last->id;
            }

            if (empty($data['latitude']) && empty($data['longitude'])) {
                $data['latitude']= $last->latitude;
                $data['longitude'] = $last->longitude;
            }

            $lastest_positions = $last ? $this->device->latest_positions : '';

            if ($data['speed'] > 200)
                $data['speed'] = $last ? $last->speed : 200;

            if (isset($last->latitude) && isset($last->longitude))
                $distance = getDistance($data['latitude'], $data['longitude'], $last->latitude, $last->longitude);
            else
                $distance = 0;

            if ($distance >= self::MIN_DISTANCE OR !$last OR empty($this->device->latestPosition_id) OR round($data['speed'], 10) != round($last->speed, 10)) {
                $inserted_new_position = TRUE;

                $last_insert_id = DB::connection('traccar_mysql')
                    ->table('positions_'.$this->device->id)
                    ->insertGetId([
                        'device_id' => $this->device->id,
                        'time' => $data['time'],
                        'server_time' => date('Y-m-d H:i:s'),
                        'valid' => 1,
                        'latitude' => $data['latitude'],
                        'longitude' => $data['longitude'],
                        'altitude' => $data['altitude'],
                        'speed' => $data['speed'],
                        'course' => $data['course'],
                        'other' => $data['other_xml'],
                        'protocol' => $data['protocol'],
                        'distance' => $distance
                    ]);
            }
            else {
                DB::connection('traccar_mysql')
                    ->table('positions_'.$this->device->id)
                    ->where('id', '=', $last->latestPosition_id)
                    ->update([
                        'server_time' => date('Y-m-d H:i:s'),
                        'valid' => 2,
                        'latitude' => $data['latitude'],
                        'longitude' => $data['longitude'],
                        'altitude' => $data['altitude'],
                        'speed' => $data['speed'],
                        'course' => $data['course'],
                        'other' => $data['other_xml'],
                        'protocol' => $data['protocol']
                    ]);
            }

            if (is_null($this->device->lastValidLatitude) OR !$data['history']) {
                if ($distance >= self::MIN_DISTANCE)
                    $lastest_positions = $data['latitude'].'/'.$data['longitude'].';'.$lastest_positions;

                $lastest_positions = implode(';', array_slice(explode(';', $lastest_positions), 0, 15));

                DB::connection('traccar_mysql')
                    ->table('devices')
                    ->where('id', '=', $this->device->id)
                    ->update([
                        'time' => $data['time'],
                        'server_time' => date('Y-m-d H:i:s'),
                        'lastValidLatitude' => $data['latitude'],
                        'lastValidLongitude' => $data['longitude'],
                        'altitude' => $data['altitude'],
                        'speed' => $data['speed'],
                        'course' => $data['course'],
                        'other' => $data['other_xml'],
                        'protocol' => $data['protocol'],
                        'latest_positions' => $lastest_positions,
                        'latestPosition_id' => (!$data['history'] && isset($last_insert_id)) ? $last_insert_id : $this->device->latestPosition_id
                    ]);
            }
        }
        else {
            $last = $this->device;

            if (!$data['history']) {
                DB::connection('traccar_mysql')
                    ->table('devices')
                    ->where('id', '=', $this->device->id)
                    ->update([
                        'time' => $data['time'],
                        'ack_time' => date('Y-m-d H:i:s'),
                        'speed' => 0,
                        'other' => $data['other_xml'],
                    ]);
            }
        }

        if (!isset($last_insert_id))
            $last_insert_id = $last->latestPosition_id;

        if ($last_insert_id && (!isset($last->other) || $last->other != $data['other_xml'] || $inserted_new_position)) {
            $table_name = 'sensors_'.$this->device->id;

            try {
                DB::connection('sensors_mysql')
                    ->table($table_name)
                    ->insert([
                        'position_id' => $last_insert_id,
                        'other' => $data['other_xml'],
                        'time' => $data['time']
                    ]);
            } catch(\Exception $e){
                $table = DB::connection('sensors_mysql')->select( DB::raw("SHOW TABLES LIKE '$table_name'") );

                if (!$table) {
                    try {
                        $sql = "CREATE TABLE IF NOT EXISTS `$table_name`(
                      `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
                      `position_id` BIGINT(20) unsigned NOT NULL,
                      `other` text,
                      `time` datetime DEFAULT NULL,
                      PRIMARY KEY (`id`),
                      INDEX `position_id` (`position_id`),
                      INDEX `time` (`time`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

                        DB::connection('sensors_mysql')->select(DB::raw($sql));
                    } catch(\Exception $e){}
                }

                DB::connection('sensors_mysql')
                    ->table($table_name)
                    ->insert([
                        'position_id' => $last_insert_id,
                        'other' => $data['other_xml'],
                        'time' => $data['time']
                    ]);
            }
        }

        if (!$inserted_new_position && isset($last) && $last->other != $data['other_xml']) {
            DB::connection('traccar_mysql')
                ->table('positions_'.$this->device->id)
                ->where('id', '=', $last->latestPosition_id)
                ->update([
                    'other' => $data['other_xml'],
                ]);
        }

        $data['last_insert_id'] = $last_insert_id;
    }

    private function alerts(&$data) {
        if ( $this->keysCount && $this->keysCount > 100000 ) {
            return;
        }

        if ($data['history'])
            return;

        $polygonHelper = new PolygonHelper();

        $users_timezones = [];
        $device_current = [];
        $polygons = [];
        $point_in_polygon = [];
        $point = $data['latitude'].' '.$data['longitude'];

        $timezones = $this->getTimazones();
        $users = $this->getUsers();

        if (!$data['ack'] && $data['engine_status']) {
            # Zone in/out
            $alerts = DB::table('alert_device')
                ->select(
                    'geofences.coordinates',
                    'geofences.id',
                    'geofences.name AS geofence_name',
                    'alert_geofence.zone',
                    'alert_geofence.time_from',
                    'alert_geofence.time_to',
                    'alerts.user_id',
                    'alerts.id as alert_id',
                    'alerts.email',
                    'alerts.mobile_phone',
                    'user_device.timezone_id as device_timezone_id')
                ->join('alerts', function ($join) {
                    $join->on('alert_device.alert_id', '=', 'alerts.id')
                        ->where('alerts.active', '=', 1);
                })
                ->join('user_device_pivot as user_device', function ($join) {
                    $join->on('alert_device.device_id', '=', 'user_device.device_id')
                        ->on('alerts.user_id', '=', 'user_device.user_id');
                })
                ->join('alert_geofence', 'alerts.id', '=', 'alert_geofence.alert_id')
                ->join('geofences', 'alert_geofence.geofence_id', '=', 'geofences.id')
                ->where('alert_device.device_id', '=', $this->device->web_device_id)
                ->groupBy('geofences.id', 'alert_geofence.zone')
                ->get();

            if (!empty($alerts)) {
                foreach ($alerts as $item) {
                    $geo_id = $item->id;

                    if ($users[$item->user_id]['timezone_id'] == 0)
                        $users[$item->user_id]['timezone_id'] = 57;

                    if (!array_key_exists($item->user_id, $users))
                        continue;

                    if (!array_key_exists($item->user_id, $users_timezones)) {
                        $user_dst = DB::table('users_dst')
                            ->where('user_id', $item->user_id)
                            ->whereNull('type')
                            ->first();

                        if (empty($user_dst)) {
                            $users_timezones[$item->user_id] = $timezones[$users[$item->user_id]['timezone_id']];
                        }
                        else {
                            $dst_date_from = $user_dst->date_from;
                            $dst_date_to = $user_dst->date_to;
                            if ($user_dst->type == 'automatic') {
                                $dst_time = DB::table('timezones_dst')
                                    ->where('id', $user_dst->country_id)
                                    ->first();

                                if (!empty($dst_time)) {
                                    $dst_date_from = date("m-d", strtotime($dst_time->from_period." ".date('Y'))).' '.$dst_time->from_time;
                                    $dst_date_to = date("m-d", strtotime($dst_time->to_period." ".date('Y'))).' '.$dst_time->to_time;
                                }
                            }
                            elseif ($user_dst->type == 'other') {
                                $dst_date_from = date("m-d", strtotime($user_dst->week_pos_from.' '.$user_dst->week_day_from.' of '.$user_dst->month_from.' '.date('Y')."")).' '.$user_dst->time_from;
                                $dst_date_to = date("m-d", strtotime($user_dst->week_pos_to.' '.$user_dst->week_day_to.' of '.$user_dst->month_to.' '.date('Y')."")).' '.$user_dst->time_to;
                            }

                            $users_timezones[$item->user_id] = $timezones[$users[$item->user_id]['timezone_id']];
                            $zone = $users_timezones[$item->user_id];
                            if (strpos($zone, ' ') !== false) {
                                list($hours, $minutes) = explode(' ', $zone);
                            }
                            else {
                                $hours = $zone;
                                $minutes = '';
                            }
                            $dst_zone = trim((intval(str_replace('hours', ' ', $hours)) + 1).'hours '.(!empty($minutes) ? $minutes : ''));
                            if (substr($dst_zone, 0, 1) != '-')
                                $dst_zone = '+'.$dst_zone;

                            $date_from = strtotime(tdate(date('Y-m-d H:i:s'), $dst_zone));
                            $date_to = strtotime(tdate(date('Y-m-d H:i:s'), $zone));

                            $dst = FALSE;
                            $year = date('Y');
                            $from = strtotime($year.'-'.$dst_date_from);
                            $to = strtotime($year.'-'.$dst_date_to);

                            if ($to < $from) {
                                if ($date_from > $from || $date_to < $to)
                                    $dst = TRUE;
                            }
                            else {
                                if ($date_from > $from && $date_to < $to)
                                    $dst = TRUE;
                            }

                            if ($dst)
                                $users_timezones[$item->user_id] = $dst_zone;
                        }
                    }

                    $geo_available = FALSE;
                    $cur_time = strtotime(tdate(date('Y-m-d H:i:s'), $users_timezones[$item->user_id]));
                    $year = date('Y-m-d', $cur_time);
                    $from = strtotime($year.' '.$item->time_from);
                    $to = strtotime($year.' '.$item->time_to);

                    if ($to < $from) {
                        if ($cur_time > $from || $cur_time < $to)
                            $geo_available = TRUE;
                    }
                    else {
                        if (($cur_time > $from && $cur_time < $to) || ($to == $from))
                            $geo_available = TRUE;
                    }

                    if (!$geo_available)
                        continue;

                    if (!isset($item->polygon_cords_arr)) {
                        if (!array_key_exists($geo_id, $polygons))
                            $polygons[$geo_id] = parsePolygon(json_decode($item->coordinates, TRUE));
                        $item->polygon_cords_arr = $polygons[$geo_id];
                    }
                    if (!array_key_exists($geo_id, $point_in_polygon))
                        $point_in_polygon[$geo_id] = $polygonHelper->pointInPolygon($point, $item->polygon_cords_arr);

                    $user_geofences = array_flip(explode(',', $users[$item->user_id]['current_geofences']));

                    if (!array_key_exists($item->user_id, $device_current))
                        $device_current[$item->user_id]['geofences'] = [];

                    if ($point_in_polygon[$geo_id] && !array_key_exists($geo_id, $device_current[$item->user_id]['geofences']))
                        $device_current[$item->user_id]['geofences'][$geo_id] = $geo_id;

                    // Zone in
                    if ($item->zone == 1 && $point_in_polygon[$geo_id] && !array_key_exists($geo_id, $user_geofences)) {
                        DB::table('events')->insert([
                            'user_id' => $item->user_id,
                            'geofence_id' => $geo_id,
                            'position_id' => $data['last_insert_id'],
                            'alert_id' => $item->alert_id,
                            'device_id' => $this->device->web_device_id,
                            'message' => 'zone_in',
                            'type' => 'zone_in',
                            'altitude' => $data['altitude'],
                            'course' => $data['course'],
                            'latitude' => $data['latitude'],
                            'longitude' => $data['longitude'],
                            'speed' => $data['speed'],
                            'time' => $data['time'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);

                        DB::table('events_queue')->insert([
                            'user_id' => $item->user_id,
                            'device_id' => $this->device->web_device_id,
                            'type' => 'zone_in',
                            'data' => json_encode([
                                'altitude' => $data['altitude'],
                                'course' => $data['course'],
                                'latitude' => $data['latitude'],
                                'longitude' => $data['longitude'],
                                'speed' => $data['speed'],
                                'time' => $data['time'],
                                'geofence' => htmlentities($item->geofence_name),
                                'device_name' => htmlentities($this->device->name),
                                'email' => $item->email,
                                'mobile_phone' => $item->mobile_phone
                            ]),
                        ]);
                    }

                    // Zone out
                    if ($item->zone == 2 && !$point_in_polygon[$geo_id] && array_key_exists($geo_id, $user_geofences)) {
                        DB::table('events')->insert([
                            'user_id' => $item->user_id,
                            'geofence_id' => $geo_id,
                            'position_id' => $data['last_insert_id'],
                            'alert_id' => $item->alert_id,
                            'device_id' => $this->device->web_device_id,
                            'message' => 'zone_out',
                            'type' => 'zone_out',
                            'altitude' => $data['altitude'],
                            'course' => $data['course'],
                            'latitude' => $data['latitude'],
                            'longitude' => $data['longitude'],
                            'speed' => $data['speed'],
                            'time' => $data['time'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);

                        DB::table('events_queue')->insert([
                            'user_id' => $item->user_id,
                            'device_id' => $this->device->web_device_id,
                            'type' => 'zone_out',
                            'data' => json_encode([
                                'altitude' => $data['altitude'],
                                'course' => $data['course'],
                                'latitude' => $data['latitude'],
                                'longitude' => $data['longitude'],
                                'speed' => $data['speed'],
                                'time' => $data['time'],
                                'geofence' => htmlentities($item->geofence_name),
                                'device_name' => htmlentities($this->device->name),
                                'email' => $item->email,
                                'mobile_phone' => $item->mobile_phone
                            ]),
                        ]);
                    }
                }
            }

            # Overspeeds
            $alerts = DB::table('alert_device')
                ->select(
                    'alerts.id',
                    'alerts.user_id',
                    'alerts.email',
                    'alerts.mobile_phone',
                    'alerts.overspeed_distance',
                    'alerts.overspeed_speed')
                ->join('alerts', function ($join) {
                    $join->on('alert_device.alert_id', '=', 'alerts.id')
                        ->where('alerts.active', '=', 1);
                })
                ->where('alert_device.device_id', '=', $this->device->web_device_id)
                ->where('alerts.overspeed_speed', '>', 0)
                ->where(function($query) use ($data){
                    $query
                        ->where('alerts.overspeed_distance', 1)
                        ->where('alerts.overspeed_speed', '<', $data['speed'])
                        ->orWhere(function($q) use ($data){
                            $q->where('alerts.overspeed_distance', 2)->where('alerts.overspeed_speed', '<', $data['speed_in_miles']);
                        });
                })
                ->where('alert_device.overspeed', '<', date('Y-m-d H:i:s', (time() - 900)))
                ->groupBy('alert_device.id')
                ->get();

            if (!empty($alerts)) {
                foreach ($alerts as $alert) {
                    DB::table('events')->insert([
                        'user_id' => $alert->user_id,
                        'geofence_id' => null,
                        'position_id' => $data['last_insert_id'],
                        'alert_id' => $alert->id,
                        'device_id' => $this->device->web_device_id,
                        'type' => 'overspeed',
                        'message' => json_encode([
                            'overspeed_speed' => $alert->overspeed_speed,
                            'overspeed_distance' => $alert->overspeed_distance,
                        ]),
                        'altitude' => $data['altitude'],
                        'course' => $data['course'],
                        'latitude' => $data['latitude'],
                        'longitude' => $data['longitude'],
                        'speed' => $data['speed'],
                        'time' => $data['time'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                    DB::table('events_queue')->insert([
                        'user_id' => $alert->user_id,
                        'device_id' => $this->device->web_device_id,
                        'type' => 'overspeed',
                        'data' => json_encode([
                            'altitude' => $data['altitude'],
                            'course' => $data['course'],
                            'latitude' => $data['latitude'],
                            'longitude' => $data['longitude'],
                            'speed' => $data['speed'],
                            'time' => $data['time'],
                            'overspeed_speed' => $alert->overspeed_speed,
                            'overspeed_distance' => $alert->overspeed_distance,
                            'device_name' => htmlentities($this->device->name),
                            'email' => $alert->email,
                            'mobile_phone' => $alert->mobile_phone
                        ]),
                    ]);

                    DB::table('alert_device')
                        ->where('alert_id', $alert->id)
                        ->where('device_id',$this->device->web_device_id)
                        ->update([
                            'overspeed' => date('Y-m-d H:i:s')
                        ]);
                }
            }
        }

        $users_ids = array_keys($users);

        # Custom events
        $alerts = DB::table('alerts')
            ->select(
                'events_custom.always',
                'events_custom.id as event_id',
                'events_custom.message',
                'events_custom.conditions',
                'alerts.*')
            ->join('alert_device', function ($join) {
                $join->on('alert_device.alert_id', '=', 'alerts.id')
                    ->where('alerts.active', '=', 1);
            })
            ->join('alert_event_pivot', 'alerts.id', '=', 'alert_event_pivot.alert_id')
            ->join('events_custom', 'alert_event_pivot.event_id', '=', 'events_custom.id')
            ->join('event_custom_tags', 'events_custom.id', '=', 'event_custom_tags.event_custom_id')
            ->where('alert_device.device_id', '=', $this->device->web_device_id)
            ->where('events_custom.protocol', '=', $data['protocol'])
            ->where(function($query) use($users_ids){
                $query->whereIn('events_custom.user_id', $users_ids)->orWhereNull('events_custom.user_id');
            })
            ->groupBy('alerts.user_id', 'events_custom.id')
            ->get();

        if (!empty($alerts)) {
            foreach ($alerts as $alert) {
                $send_event = FALSE;

                if (!isset($users[$alert->user_id]))
                    continue;
                $user = $users[$alert->user_id];
                $current_events = explode(',', $user['current_events']);
                $current_events = array_flip($current_events);

                $conditions = unserialize($alert->conditions);
                foreach ($conditions as $condition) {
                    $send_event = FALSE;
                    preg_match_all('/<'.preg_quote($condition['tag'], '/').'>(.*?)<\/'.preg_quote($condition['tag'], '/').'>/s', $data['other_xml'], $matches);
                    if (count($matches['1'])) {
                        foreach($matches['1'] as $key => $text) {
                            if ($condition['tag'] == 'rfid' && $data['protocol'] == 'meitrack')
                                $text = hexdec($text);

                            preg_match('/\%SETFLAG\[([0-9]+)\,([0-9]+)\,([\s\S]+)\]\%/', $condition['tag_value'], $match);
                            if (isset($match['1']) && isset($match['2']) && isset($match['3'])) {
                                $condition['tag_value'] = $match['3'];
                                $text = substr($text, $match['1'], $match['2']);
                            }

                            $send_event = checkCondition($condition['type'], $text, $condition['tag_value']);

                            if ($send_event)
                                break;
                        }
                    }

                    if (!$send_event)
                        break;
                }

                if (!$send_event)
                    continue;


                if (!$alert->always) {
                    $device_current[$alert->user_id]['events'][] = $alert->event_id;
                    if (isset($current_events[$alert->event_id]))
                        continue;
                }

                DB::table('events')->insert([
                    'user_id' => $alert->user_id,
                    'geofence_id' => null,
                    'position_id' => $data['last_insert_id'],
                    'alert_id' => $alert->id,
                    'device_id' => $this->device->web_device_id,
                    'type' => 'custom',
                    'message' => $alert->message,
                    'altitude' => $data['altitude'],
                    'course' => $data['course'],
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'speed' => $data['speed'],
                    'time' => $data['time'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                DB::table('events_queue')->insert([
                    'user_id' => $alert->user_id,
                    'device_id' => $this->device->web_device_id,
                    'type' => 'custom',
                    'data' => json_encode([
                        'altitude' => $data['altitude'],
                        'course' => $data['course'],
                        'latitude' => $data['latitude'],
                        'longitude' => $data['longitude'],
                        'speed' => $data['speed'],
                        'time' => $data['time'],
                        'message' => $alert->message,
                        'device_name' => htmlentities($this->device->name),
                        'email' => $alert->email,
                        'mobile_phone' => $alert->mobile_phone
                    ]),
                ]);
            }
        }

        # Current driver
        preg_match('/<io78>(.*?)<\/io78>/s', $data['other_xml'], $driver_rfid);
        if (!($data['protocol'] == 'teltonika' && isset($driver_rfid['1']) && !empty($driver_rfid['1'])))
            preg_match('/<rfid>(.*?)<\/rfid>/s', $data['other_xml'], $driver_rfid);

        if ($data['protocol'] == 'fox')
            preg_match('/<status-data>(.*?)<\/status-data>/s', $data['other_xml'], $driver_rfid);

        if ($data['protocol'] == 'ruptela')
            preg_match('/<io34>(.*?)<\/io34>/s', $data['other_xml'], $driver_rfid);

        if (isset($driver_rfid['1'])) {
            $rfid = $driver_rfid['1'];
            $raw_rfid = $rfid;
            if ($data['protocol'] == 'meitrack')
                $rfid = hexdec($rfid);
            if ($data['protocol'] == 'teltonika')
                $rfid = teltonikaIbutton($rfid);

            $drivers = DB::table('user_drivers')->whereIn('user_id', $users_ids)->where('rfid', $rfid)->get();

            if ($data['protocol'] == 'teltonika' && empty($drivers)) {
                $drivers = DB::table('user_drivers')->whereIn('user_id', $users_ids)->where('rfid', $raw_rfid)->get();
            }

            foreach ($drivers as $driver) {
                $user = $users[$driver->user_id];

                if ($user['current_driver_id'] == $driver->id)
                    continue;

                $alerts = DB::table('alert_driver_pivot')
                    ->select('alerts.*')
                    ->join('alerts', 'alerts.id', '=', 'alert_driver_pivot.alert_id')
                    ->where('alerts.active', '=', 1)
                    ->where('alert_driver_pivot.driver_id', '=', $driver->id)
                    ->where('alerts.user_id', '=', $user['id'])
                    ->groupBy('alerts.id')
                    ->get();

                foreach ($alerts as $alert) {
                    DB::table('events')->insert([
                        'user_id' => $alert->user_id,
                        'geofence_id' => null,
                        'position_id' => $data['last_insert_id'],
                        'alert_id' => $alert->id,
                        'device_id' => $this->device->web_device_id,
                        'type' => 'driver',
                        'message' => $driver->name,
                        'altitude' => $data['altitude'],
                        'course' => $data['course'],
                        'latitude' => $data['latitude'],
                        'longitude' => $data['longitude'],
                        'speed' => $data['speed'],
                        'time' => $data['time'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                    DB::table('events_queue')->insert([
                        'user_id' => $alert->user_id,
                        'device_id' => $this->device->web_device_id,
                        'type' => 'driver',
                        'data' => json_encode([
                            'altitude' => $data['altitude'],
                            'course' => $data['course'],
                            'latitude' => $data['latitude'],
                            'longitude' => $data['longitude'],
                            'speed' => $data['speed'],
                            'time' => $data['time'],
                            'driver' => $driver->name,
                            'device_name' => htmlentities($this->device->name),
                            'email' => $alert->email,
                            'mobile_phone' => $alert->mobile_phone
                        ]),
                    ]);
                }

                $device_current[$user['id']]['driver'] = $driver->id;

                DB::table('user_driver_position_pivot')->insert([
                    'driver_id' => $driver->id,
                    'device_id' => $this->device->web_device_id,
                    'date' => date('Y-m-d H:i:s')
                ]);
            }
        }

        foreach ($users as $user) {
            $updated = FALSE;
            $tries = 0;
            while (!$updated && $tries < 5) {
                try {
                    $update_arr = [];
                    if (array_key_exists($user['id'], $device_current)) {
                        $current = $device_current[$user['id']];
                        if (array_key_exists('geofences', $current)) {
                            $current_geo = implode(',', $current['geofences']);
                            if (empty($user['current_geofences']) != empty($current['geofences']) || $user['current_geofences'] != $current_geo) {
                                $update_arr['current_geofences'] = $current_geo;
                            }
                            $user['current_geofences'] = $current_geo;
                        }
                        else {
                            if (!$data['ack'] && $data['engine_status']) {
                                if (!empty($user['current_geofences']))
                                    $update_arr['current_geofences'] = '';
                                $user['current_geofences'] = '';
                            }
                        }
                        if (array_key_exists('events', $current)) {
                            $current_events = implode(',', $current['events']);
                            if (empty($user['current_events']) != empty($current['events']) || $user['current_events'] != $current_events) {
                                $update_arr['current_events'] = $current_events;
                            }
                            $user['current_events'] = $current_events;
                        }
                        else {
                            if (!empty($user['current_events']))
                                $update_arr['current_events'] = '';
                            $user['current_events'] = '';
                        }
                        if (array_key_exists('driver', $current)) {
                            if ($user['current_driver_id'] != $current['driver']) {
                                $update_arr['current_driver_id'] = $current['driver'];
                            }
                            $user['current_driver_id'] = $current['driver'];
                        }
                        else {
                            $user['current_driver_id'] = '';
                        }
                    }
                    else {
                        if (!$data['ack'] && $data['engine_status']) {
                            if (!empty($user['current_geofences']))
                                $update_arr['current_geofences'] = '';
                            $user['current_geofences'] = '';
                        }
                        if (!empty($user['current_events']))
                            $update_arr['current_events'] = '';
                        $user['current_events'] = '';
                    }

                    if (!empty($update_arr)) {
                        DB::table('user_device_pivot')
                            ->where('user_id', $user['id'])
                            ->where('device_id', $this->device->web_device_id)
                            ->update($update_arr);
                    }

                    $updated = TRUE;
                }
                catch(\Exception $e) {
                    usleep(10000);
                }
                $tries++;
            }
        }
    }

    private function lastPosition() {
        if (empty($this->lastPosition)) {
            $this->lastPosition = DB::connection('traccar_mysql')
                ->table('positions_'.$this->device->id)
                ->select('*')
                ->orderBy('time', 'desc')
                ->orderBy('id', 'desc')
                ->first();
        }

        return $this->lastPosition;
    }

    private function updateDevice($data) {
        DB::table('devices')->where('id', '=', $this->device->web_device_id)->update($data);
    }

    private function getTimazones() {
        $timezones = FALSE;

        try {
            $timezones = $this->redis->get('timezones');
        }
        catch (Exception $e) {}

        if (!$timezones) {
            $timezones = DB::table('timezones')->select('id','zone')->get();

            $timezones_arr = [];

            foreach ($timezones as $timezone)
                $timezones_arr[$timezone->id] = $timezone->zone;

            $timezones = json_encode($timezones_arr);

            unset($timezones_arr);

            try {
                $this->redis->set('timezones', $timezones);
            }
            catch (Exception $e) {}
        }

        return json_decode($timezones, TRUE);
    }

    private function getUsers() {
        $users_arr = DB::table('user_device_pivot')
            ->select(
                'user_device_pivot.user_id as id',
                'user_device_pivot.current_events',
                'user_device_pivot.current_geofences',
                'user_device_pivot.current_driver_id',
                'users.timezone_id')
            ->join('users', 'user_device_pivot.user_id', '=', 'users.id')
            ->where('user_device_pivot.device_id', '=', $this->device->web_device_id)
            ->get();

        $users_arr = json_decode(json_encode($users_arr), true);
        $users = [];

        foreach ($users_arr as $user) {
            $users[$user['id']] = $user;
        }

        unset($users_arr);

        return $users;
    }

    private function getDeviceSensors($tags_arr) {
        $key = 'device_sensors.' . $this->device->web_device_id . '.' . md5(json_encode($tags_arr));

        $sensors = FALSE;

        try {
            $sensors = $this->redis->get( $key );
        }
        catch (\Exception $e) {}

        if ( !$sensors ) {
            $sensors = DB::table('device_sensors')
                ->where('device_id', '=', $this->device->web_device_id)
                ->whereIn('tag_name', $tags_arr)
                ->get();

            $sensors = json_encode($sensors);

            try {
                $this->redis->set( $key, $sensors );
                $this->redis->setTimeout( $key, 60 );
            }
            catch (\Exception $e) {}
        }

        return json_decode($sensors, TRUE);
    }

    private function getDeviceEngineSensor($detect_engine) {
        $key = 'device_engine_sensors.' . $this->device->web_device_id . '.' . $detect_engine;

        $engine_sensor = FALSE;

        try {
            $engine_sensor = $this->redis->get( $key );
        }
        catch (\Exception $e) {}

        if ( !$engine_sensor ) {
            $engine_sensor = DB::table('device_sensors')
                ->where('device_id', '=', $this->device->web_device_id)
                ->where('type', '=', $detect_engine)
                ->orderBy('id', 'desc')
                ->first();
            $engine_sensor = json_encode($engine_sensor);

            try {
                $this->redis->set( $key, $engine_sensor );
                $this->redis->setTimeout( $key, 60 );
            }
            catch (Exception $e) {}
        }

        return json_decode($engine_sensor, TRUE);
    }
}
