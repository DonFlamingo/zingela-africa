<?php namespace Tobuli\Helpers;

class ReportHelper {
    private $geofences = [];
    private $geofences_list = [];
    private $polygonHelper;
    private $engine_status = 0;
    public $data = [
        'zones_instead' => FALSE,
        'show_addresses' => FALSE,
        'stops' => 1,
        'speed_limit' => 0,
        'stop_speed' => 6,
        'unit_of_distance' => 'km',
        'unit_of_altitude' => 'mt',
        'timezone' => 0
    ];

    function __construct($data, $geofences = []) {
        $this->data = array_merge($this->data, $data);
        if (!empty($geofences)) {
            $this->geofences_list = $geofences->lists('name', 'id');
            $this->geofences = $geofences->toArray();
            $this->setGeofencesPolygons();
        }

        $this->polygonHelper = new PolygonHelper();
    }

    public function generate($items, $sensors = null, $driver_history = null, $device, $date_from, $date_to, $engine_status) {
        if (!is_array($items[0]))
            $items = json_decode(json_encode((array) $items), true);

        $history = new HistoryHelper();
        $history->report_type = $this->data['type'];
        $history->date_from = $date_from;
        $history->date_to = $date_to;
        $history->engine_status = $engine_status;
        if (!is_null($sensors))
            $history->setSensors($sensors);
        if (!is_null($driver_history))
            $history->setDrivers($driver_history);
        if (!is_null($device))
            $history->setEngineHoursType(['engine_hours' => $device['engine_hours'], 'detect_engine' => $device['detect_engine']]);

        $history->setStopSpeed($this->data['stop_speed']);
        $history->setStopMinutes($this->data['stops']);
        $history->setUnitOfDistance($this->data['unit_of_distance']);
        $history->setUnitOfAltitude($this->data['unit_of_altitude']);
        $history->setTimezone($this->data['zone']);
        $history->speed_limit = $this->data['speed_limit'];
        $history->show_addresses = boolval($this->data['show_addresses']);
        if ($this->data['type'] == 5)
            $history->getOverspeeds = 1;
        if ($this->data['type'] == 6)
            $history->getUnderspeeds = 1;
        if ($this->data['type'] == 11)
            $history->setMinFuelFillings($device['min_fuel_fillings']);
        if ($this->data['type'] == 12)
            $history->setMinFuelThefts($device['min_fuel_thefts']);
        $history->setGeofences($this->geofences, $this->data['zones_instead']);

        $history->parse($items);
        unset($items);

        return $history;
    }

    public function generateService($items, $services, $sensors) {

        if (!is_array($items[0]))
            $items = json_decode(json_encode((array) $items), true);

        $sensor_odometer = 0;
        
        foreach($sensors as $sensor){
            if($sensor['type'] == 'odometer'){
                $sensor_odometer = explode(' ', $sensor['value']);
                $sensor_odometer = $sensor_odometer[0];
            }
        }
        $list = [];
        foreach (reset($items) as $key=>$item) {
            $arr = [];

            $arr['name'] = $item['name'];
            
            if($item['expiration_by'] == 'days'){
                $arr['days'] = $item['interval'];
                $arr['days_left'] = $services[$key]['value'];
            }else{
                $arr['days'] = '';
                $arr['days_left'] = '';
            }

            if($item['expiration_by'] == 'odometer'){
                $arr['odometer'] = $item['interval'];
                $odometer_left = $item['expires'] - $sensor_odometer;
                $arr['odometer_left'] = $odometer_left.' km';
            }else{
                $arr['odometer'] = '';
                $arr['odometer_left'] = '';
            }

            if($item['expiration_by'] == 'engine_hours'){
                $arr['engine_hours'] = $item['interval'];
                $arr['engine_hours_left'] = $services[$key]['value'];
            }else{
                $arr['engine_hours'] = '';
                $arr['engine_hours_left'] = '';
            }

            array_push($list, $arr);
        }

        #echo '<pre>'.print_r($list, true).'</pre>';

        return $list;

    }

    public function generateExpenses($items, $device) {
        if (!is_array($items[0]))
            $items = json_decode(json_encode((array) $items), true);

        
        $list = [];


        foreach (reset($items) as $item) {
            
            if(count($item['devices']) > 0){
                foreach($item['devices'] as $item_device){
                    if($item_device['id'] == $device){
                        
                        $arr = [];
                        $arr['date'] = $item['date'];
                        $arr['name'] = $item['name'];
                        $arr['quantity'] = $item['quantity'];
                        $arr['cost'] = $item['cost'];
                        $arr['supplier'] = $item['supplier'];
                        $arr['buyer'] = $item['buyer'];
                        $arr['odometer'] = $item['odometer'];
                        $arr['engine_hours'] = $item['engine_hours'];
                        $arr['description'] = $item['description'];
                        array_push($list, $arr);
                    }
                }
            }
            
        }

        return $list;

    }

    public function generateGeofences($items, $date_from, $date_to) {
        if (!is_array($items[0]))
            $items = json_decode(json_encode((array) $items), true);

        if (empty($this->geofences))
            return FALSE;

        # Main list
        $arr = [];

        # Current list
        $current_arr = [];

        # Last geofences ids
        $last = [];

        $from_timestamp = strtotime($date_from);
        $to_timestamp = strtotime($date_to);

        foreach ($items as $item) {
            if (isset($item['sensor_time']) && !is_null($item['sensor_time']) && strtotime($item['sensor_time']) > strtotime($item['time'])) {
                $item['time'] = $item['sensor_time'];
                $item['other'] = $item['sensor_other'];
            }
            $timestamp = strtotime($item['time']);

            if ($from_timestamp > $timestamp)
                continue;

            if ($to_timestamp < $timestamp) {
                break;
            }

            $item['time'] = tdate($item['time'], $this->data['zone']);
            $current = $this->getCurrentGeofences($item);

            $entered_geofences = array_flip(array_diff($current, $last));
            $left_geofences = array_flip(array_diff($last, $current));

            foreach ($entered_geofences as $id => $value) {
                $current_arr[$id] = [
                    'start' => date('Y-m-d', strtotime($item['time'])),
                    'name' => $this->geofences_list[$id],
                    'position' => [
                        'address' => ($this->data['show_addresses'] ? getGeoAddress($item['latitude'], $item['longitude'], '') : ''),
                        'lat' => $item['latitude'],
                        'lng' => $item['longitude']
                    ],
                    'end' => '-',
                    'duration' => '-'
                ];
            }

            foreach ($left_geofences as $id => $value) {
                $current_arr[$id]['end'] =  date('Y-m-d', strtotime($item['time']));
                $current_arr[$id]['duration'] = secondsToTime(strtotime($current_arr[$id]['end']) - strtotime($current_arr[$id]['start']));
                $arr[] = $current_arr[$id];
                unset($current_arr[$id]);
            }


            $last = $current;
        }

        foreach ($current_arr as &$geofence) {
            $geofence['end'] =  date('Y-m-d', strtotime($item['time']));
            $geofence['duration'] = secondsToTime(strtotime($geofence['end']) - strtotime($geofence['start']));
            $arr[] = $geofence;
            unset($geofence);
        }

        return $arr;
    }

    public function generateGeofencesEngine($items, $date_from, $date_to, $device, $sensors) {
        if (!is_array($items[0]))
            $items = json_decode(json_encode((array) $items), true);

        if (empty($this->geofences))
            return FALSE;

        $detect_engine = $device['engine_hours'] == 'engine_hours' ? $device['detect_engine'] : $device['engine_hours'];

        if (!empty($sensors) && !empty($detect_engine) && $detect_engine != 'gps') {
            foreach ($sensors as $isensor) {
                if ($isensor['type'] == $detect_engine) {
                    $sensor = $isensor;
                    break;
                }
            }
        }

        # Total engine on/off in geofences
        $totals = [];

        # Main list
        $arr = [];

        # Current list
        $current_arr = [];

        # Last geofences ids
        $last = [];

        $engine_status = 0;
        $engine_status_changed = false;

        $from_timestamp = strtotime($date_from);
        $to_timestamp = strtotime($date_to);

        foreach ($items as $item) {
            if (isset($item['sensor_time']) && !is_null($item['sensor_time']) && strtotime($item['sensor_time']) > strtotime($item['time'])) {
                $item['time'] = $item['sensor_time'];
                $item['other'] = $item['sensor_other'];
            }
            $timestamp = strtotime($item['time']);

            if ($from_timestamp > $timestamp)
                continue;

            if ($to_timestamp < $timestamp) {
                break;
            }

            $item['time'] = tdate($item['time'], $this->data['zone']);
            $current = $this->getCurrentGeofences($item);

            $entered_geofences = array_flip(array_diff($current, $last));
            $left_geofences = array_flip(array_diff($last, $current));


            if (!empty($sensor))
                $engine = getSensorValueBool($item['other'], $sensor);
            else
                $engine = $item['speed'] > $device['min_moving_speed'] ? 1 : 0;

            $engine_status_changed = ($engine == 1 || $engine == 0) && $engine_status != $engine;

            if ($engine == 1 || $engine == 0)
            {
                $engine_status = $engine;
            }

            if ( empty($last_item) )
                $last_item = $item;


            foreach ($entered_geofences as $id => $value) {
                $current_arr[$id] = [
                    'start' => $item['time'],
                    'name' => $this->geofences_list[$id],
                    'geofence_id' => $id,
                    'position' => [
                        'address' => ($this->data['show_addresses'] ? getGeoAddress($item['latitude'], $item['longitude'], '') : ''),
                        'lat' => $item['latitude'],
                        'lng' => $item['longitude']
                    ],
                    'end' => '-',
                    'duration' => '-',
                ];
            }

            foreach ($current_arr as $id => $value) {
                if ( !isset($current_arr[$id]['duration_engine_on']) ) {
                    $current_arr[$id]['duration_engine_on'] = 0;
                }
                if ( !isset($current_arr[$id]['duration_engine_off']) ) {
                    $current_arr[$id]['duration_engine_off'] = 0;
                }

                $duration_time = strtotime($item['time']) - strtotime($last_item['time']);

                if ($engine_status_changed) {
                    if ($engine_status) {
                        $current_arr[$id]['duration_engine_off'] += $duration_time;
                    } else {
                        $current_arr[$id]['duration_engine_on'] += $duration_time;
                    }
                } else {
                    if ($engine_status) {
                        $current_arr[$id]['duration_engine_on'] += $duration_time;
                    } else {
                        $current_arr[$id]['duration_engine_off'] += $duration_time;
                    }
                }
            }

            foreach ($left_geofences as $id => $value) {
                $current_arr[$id]['end'] = $item['time'];
                $current_arr[$id]['duration'] = secondsToTime(strtotime($current_arr[$id]['end']) - strtotime($current_arr[$id]['start']));
                $arr[] = $current_arr[$id];
                unset($current_arr[$id]);
            }

            $last = $current;
            $last_item = $item;
        }

        foreach ($current_arr as &$geofence) {
            $geofence['end'] = $last_item['time'];
            $geofence['duration'] = secondsToTime(strtotime($geofence['end']) - strtotime($geofence['start']));
            $arr[] = $geofence;
            unset($geofence);
        }

        foreach ($arr as &$geofence) {
            if (empty($totals[$geofence['geofence_id']])) {
                $totals[$geofence['geofence_id']] = [
                    'name'                => $this->geofences_list[$geofence['geofence_id']],
                    'duration_engine_on'  => 0,
                    'duration_engine_off' => 0,
                ];
            }
            $totals[$geofence['geofence_id']]['duration_engine_on'] += $geofence['duration_engine_on'];
            $totals[$geofence['geofence_id']]['duration_engine_off'] += $geofence['duration_engine_off'];

            $geofence['duration_engine_on'] = secondsToTime($geofence['duration_engine_on']);
            $geofence['duration_engine_off'] = secondsToTime($geofence['duration_engine_off']);
        }

        foreach ($totals as &$geofence) {
            $geofence['duration_engine_on'] = secondsToTime($geofence['duration_engine_on']);
            $geofence['duration_engine_off'] = secondsToTime($geofence['duration_engine_off']);
        }

        return [
            'items' => $arr,
            'totals' => $totals
        ];
    }

    public function generateGeneralCustom($items, $date_from, $date_to, $device, $sensors) {
        if (!is_array($items[0]))
            $items = json_decode(json_encode((array) $items), true);
        $device_engine = ['engine_hours' => $device['engine_hours'], 'detect_engine' => $device['detect_engine']];
        foreach ($sensors as $key => $sensor) {
            if ($sensor['type'] == $device_engine['engine_hours'])
                $device_engine['engine_hours_sensor'] = $sensor;
            if ($sensor['type'] == $device_engine['detect_engine'])
                $device_engine['detect_engine_sensor'] = $sensor;
        }
        $stop_speed = 6;
        $from_timestamp = strtotime($date_from);
        $to_timestamp = strtotime($date_to);
        $last_item = NULL;
        $arr = [];
        $status = 2;
        $action_time = 0;
        foreach ($items as &$item) {
            $timestamp = strtotime($item['time']);
            if ($from_timestamp > $timestamp)
                continue;
            if ($to_timestamp < $timestamp) {
                break;
            }
            $item['time'] = tdate($item['time'], $this->data['zone']);
            if (is_null($last_item))
                $item['distance'] = 0;
            else {
                $item['distance'] = getDistance($item['latitude'], $item['longitude'], $last_item['latitude'], $last_item['longitude']);
            }

            if (!empty($last_item)) {
                $time = strtotime($item['time']) - strtotime($last_item['time']);
                if ($time <= 10 && $last_item['speed'] > 5 && $item['speed'] == 0)
                    $item['speed'] = $last_item['speed'];
                $last = end($arr);
                if (empty($last)) {
                    $arr[] = [
                        'date' => date('Y-m-d', strtotime($item['time'])),
                        'start' => null,
                        'end' => '-',
                        'distance' => 0,
                        'stop_duration' => 0,
                        'move_duration' => 0,
                        'engine_idle' => 0,
                        'engine_work' => 0,
                        'overspeed_count' => 0
                    ];
                    $last = end($arr);
                }
                $last_key = key($arr);
                if ($last['date'] == date('Y-m-d', strtotime($item['time']))) {
                    //if (date('Y-m-d', strtotime($last_item['time'])) == date('Y-m-d', strtotime($item['time'])))
                    $this->countEngineHours($last_item, $item, $arr[$last_key], $time, $device_engine);
                    $arr[$last_key]['distance'] += $item['distance'];
                    if ($this->data['speed_limit'] && $item['speed'] > $this->data['speed_limit'])
                        $arr[$last_key]['overspeed_count']++;

                    if ($item['speed'] < $stop_speed) {
                        if ($status == 1)
                            $arr[$last_key]['end'] = $item['time'];
                        # If object was already stoped add time
                        if ($status == 2) {
                            $arr[$last_key]['stop_duration'] += $time;
                            $action_time += $time;
                        }
                        else {
                            # If last object didnt move distance
                            if (($action_time + $time) < 4) {
                                $arr[$last_key]['move_duration'] -= $action_time;
                                $arr[$last_key]['stop_duration'] += $action_time + $time;
                                $action_time = 0;
                            }
                            else {
                                $arr[$last_key]['move_duration'] += $time;
                                $action_time = 0;
                            }
                            $status = 2;
                        }
                    }
                    else {
                        if (is_null($arr[$last_key]['start']))
                            $arr[$last_key]['start'] = $item['time'];
                        if ($status == 1) {
                            $arr[$last_key]['move_duration'] += $time;
                        }
                        else {
                            // If last item stood less than needed, delete it
                            if (($action_time + $time) <= $this->data['stops'] * 60) {
                                $arr[$last_key]['stop_duration'] -= $action_time;
                                $arr[$last_key]['move_duration'] += $action_time;
                                $action_time = 0;
                            }
                            else {
                                $arr[$last_key]['stop_duration'] += $time;
                                $action_time = 0;
                            }
                            $status = 1;
                        }
                    }
                }
                else {
                    if ($arr[$last_key]['end'] == '-')
                        $arr[$last_key]['end'] = $last_item['time'];
                    $arr[] = [
                        'date' => date('Y-m-d', strtotime($item['time'])),
                        'start' => null,
                        'end' => '-',
                        'distance' => 0,
                        'stop_duration' => 0,
                        'move_duration' => 0,
                        'engine_idle' => 0,
                        'engine_work' => 0,
                        'overspeed_count' => 0
                    ];
                }
            }
            $last_item = $item;
        }
        foreach ($arr as &$item) {
            $item['distance'] = float($item['distance']);
            $item['engine_idle'] = secondsToTime($item['engine_idle']);
            $item['engine_work'] = secondsToTime($item['engine_work']);
            $item['stop_duration'] = secondsToTime($item['stop_duration']);
            $item['move_duration'] = secondsToTime($item['move_duration']);
            $item['start'] =  date('Y-m-d h:i', strtotime($item['start']));
            $item['end'] =  date('Y-m-d h:i', strtotime($item['end']));
            if ($device_engine['engine_hours'] == 'gps')
                $item['move_duration'] = $item['engine_work'];
        }
        return $arr;
    }

    public function countEngineHours($last_item, $item, &$arr_item, $time, $engine) {
        if ($engine['engine_hours'] == 'gps') {
            if ($time > 300)
                return;

            $this->sumEngineWork($item['speed'], $last_item['speed'], $arr_item, $time);
        }
        elseif ($engine['engine_hours'] == 'engine_hours') {
            if (!isset($engine['engine_hours_sensor']))
                return;

            $engine_hours = getSensorValueRaw($item['other'], $engine['engine_hours_sensor']);
            if (!is_null($engine_hours))
                $arr_item['engine_hours'] += $engine_hours;

            # Engine work
            if ($engine['detect_engine'] == 'gps') {
                if ($time > 300)
                    return;

                $this->sumEngineWork($item['speed'], $last_item['speed'], $arr_item, $time);
            }
            else {
                if (!isset($engine['detect_engine_sensor']))
                    return;

                $engine = getSensorValueBool($item['other'], $engine['detect_engine_sensor']);
                if ($engine == 0 || $engine == 1)
                    $this->engine_status = $engine;

                if (!$this->engine_status)
                    return;

                $this->sumEngineWork($item['speed'], $last_item['speed'], $arr_item, $time);
            }
        }
        else {
            if (!isset($engine['engine_hours_sensor']))
                return;

            $engine = getSensorValueBool($item['other'], $engine['engine_hours_sensor']);
            if ($engine == 0 || $engine == 1)
                $this->engine_status = $engine;

            if (!$this->engine_status)
                return;

            $this->sumEngineWork($item['speed'], $last_item['speed'], $arr_item, $time);
        }
    }

    private function sumEngineWork($speed, $last_speed, &$arr_item, $time) {
        if ($speed > 0)
            $arr_item['engine_work'] += $time;

        if ($last_speed <= 0 && $speed <= 0)
            $arr_item['engine_idle'] += $time;
    }

    public function generateGeofences24($items, $date_from, $date_to) {
        if (!is_array($items[0]))
            $items = json_decode(json_encode((array) $items), true);

        if (empty($this->geofences))
            return FALSE;

        # Main list
        $arr = [];

        # Current list
        $current_arr = [];

        # Last geofences ids
        $last = [];

        $from_timestamp = strtotime($date_from);
        $to_timestamp = strtotime($date_to);

        foreach ($items as $item) {
            if (isset($item['sensor_time']) && !is_null($item['sensor_time']) && strtotime($item['sensor_time']) > strtotime($item['time'])) {
                $item['time'] = $item['sensor_time'];
                $item['other'] = $item['sensor_other'];
            }
            $timestamp = strtotime($item['time']);

            if ($from_timestamp > $timestamp)
                continue;

            if ($to_timestamp < $timestamp) {
                break;
            }

            $item['time'] = tdate($item['time'], $this->data['zone']);
            $current = $this->getCurrentGeofences($item);

            $entered_geofences = array_flip(array_diff($current, $last));
            $left_geofences = array_flip(array_diff($last, $current));

            foreach ($entered_geofences as $id => $value) {
                $current_arr[$id] = [
                    'start' => $item['time'],
                    'name' => $this->geofences_list[$id],
                    'position' => [
                        'address' => ($this->data['show_addresses'] ? getGeoAddress($item['latitude'], $item['longitude'], '') : ''),
                        'lat' => $item['latitude'],
                        'lng' => $item['longitude']
                    ],
                    'end' => '-',
                    'duration' => '-'
                ];
            }

            foreach ($left_geofences as $id => $value) {
                $current_arr[$id]['end'] = splitTimeAtMidnight($current_arr[$id]['start'], $item['time']);
                $current_arr[$id]['duration'] = (!is_array($current_arr[$id]['end']) ? secondsToTime(strtotime($current_arr[$id]['end']) - strtotime($current_arr[$id]['start'])) : '');

                $arr[] = $current_arr[$id];
                unset($current_arr[$id]);
            }


            $last = $current;
        }

        foreach ($current_arr as &$geofence) {
            $geofence['end'] = splitTimeAtMidnight($geofence['start'], $item['time']);;
            $geofence['duration'] = !is_array($geofence['end']) ? secondsToTime(strtotime($geofence['end']) - strtotime($geofence['start'])) : $geofence['end'];
            $arr[] = $geofence;
            unset($geofence);
        }

        return $arr;
    }

    private function setGeofencesPolygons() {
        //dd($this->geofences);

        foreach ($this->geofences as &$geofence) {
            $geofence['polygon_cords_arr'] = parsePolygon(json_decode($geofence['coordinates'], TRUE));
        }
    }

    private function getCurrentGeofences($item) {
        $arr = [];
        foreach ($this->geofences as $geofence) {
            $point = $item['latitude'].' '.$item['longitude'];
            if ($this->polygonHelper->pointInPolygon($point, $geofence['polygon_cords_arr']))
                array_push($arr, $geofence['id']);
        }

        return $arr;
    }

    public function generateEvents($items) {
        $history = new HistoryHelper();
        $history->show_addresses = boolval($this->data['show_addresses']);
        $history->setGeofences($this->geofences, $this->data['zones_instead']);

        foreach ($items as &$item) {
            $item['message'] = parseEventMessage($item['message'], $item['type']);
            $item['time'] = tdate($item['time'], $this->data['zone']);
            $item['address'] = $history->getAddress([
                'lat' => $item['latitude'],
                'lng' => $item['longitude'],
                'address' => $item['address'],
            ]);
        }

        return $items;
    }

    public function generateRag($items, $driver_history = null, $device, $sensors, $date_from, $date_to) {
        if (!empty($item) && !is_array($items[0]))
            $items = json_decode(json_encode((array) $items), true);

        $data = [];
        $last = NULL;
        $last_over = FALSE;
        $current_driver = NULL;
        $from_timestamp = strtotime($date_from);
        $to_timestamp = strtotime($date_to);
        foreach ($items as $item) {
            $timestamp = $item['sort'];
            if ($from_timestamp > $timestamp)
                continue;

            if ($to_timestamp < $timestamp) {
                break;
            }

            if (!is_null($last)) {
                $time = $item['sort'] - $last['sort'];
                $distance = getDistance($item['latitude'], $item['longitude'], $last['latitude'], $last['longitude']);
            }
            else {
                $distance = 0;
            }

            if (!empty($driver_history)) {
                foreach ($driver_history as $driver) {
                    if ($timestamp <= $driver->date)
                        continue;

                    $current_driver = $driver->name;
                }
            }

            end($data);
            $key = key($data);
            $ld = current($data);
            if ($ld === false) {
                array_push($data, [
                    'name' => $current_driver,
                    'time' => 0,
                    'hb' => 0,
                    'ha' => 0,
                    'distance' => $distance
                ]);

                end($data);
                $key = key($data);
                $ld = current($data);
            }
            else
                $data[$key]['distance'] += $distance;

            if (!empty($sensors)) {
                foreach ($sensors as $sensor) {
                    preg_match('/<'.preg_quote($sensor['tag_name'], '/').'>(.*?)<\/'.preg_quote($sensor['tag_name'], '/').'>/s', $item['other'], $matches);
                    if (isset($matches['1'])) {
                        $value = $matches['1'];
                        preg_match('/\%SETFLAG\[([0-9]+)\,([0-9]+)\]\%/', $sensor['formula'], $match);
                        if (isset($match['1']) && isset($match['2']))
                            $value = substr($value, $match['1'], $match['2']);

                        if ($value == $sensor['on_value']) {
                            if ($sensor['type'] == 'harsh_acceleration')
                                $data[$key]['ha']++;
                            else
                                $data[$key]['hb']++;
                        }
                    }
                }
            }

            if ($last_over) {
                if ($ld['name'] != $current_driver) {
                    $data[$key]['time'] += $time;
                    array_push($data, [
                        'name' => $current_driver,
                        'time' => 0,
                        'hb' => 0,
                        'ha' => 0,
                        'distance' => 0
                    ]);
                }
                else {
                    $data[$key]['time'] += $time;
                }
                $last_over = FALSE;
            }
            if ($this->data['speed_limit'] && round($item['speed']) > $this->data['speed_limit']) {
                $last_over = TRUE;
            }

            $last = $item;
        }

        return $data;
    }

    public function setData(array $data) {
        $this->data = array_merge($this->data, $data);
    }
}