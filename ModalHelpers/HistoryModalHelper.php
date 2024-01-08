<?php namespace ModalHelpers;

use Facades\Repositories\DeviceRepo;
use Facades\Repositories\EventRepo;
use Facades\Repositories\TimezoneRepo;
use Facades\Repositories\TraccarPositionRepo;
use Facades\Validators\HistoryFormValidator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Helpers\HistoryHelper;

class HistoryModalHelper extends ModalHelper {

    public function get() {
        ini_set("memory_limit", "-1");
        try {
            if (!$this->user->perm('history', 'view')) {
                if (!$this->api) {
                    throw new ValidationException(['id' => trans('front.dont_have_permission')]);
                }
                else {
                    return ['status' => 0, 'perm' => 0];
                }
            }

            HistoryFormValidator::validate('create', $this->data);

            $device = DeviceRepo::find($this->data['device_id']);
            if (empty($device))
                throw new ValidationException(['id' => dontExist('global.device')]);

            if ((!$device->users->contains($this->user->id) && !isAdmin()))
                throw new ValidationException(['id' => dontExist('global.device')]);

            if (!$this->user->perm('history', 'view'))
                throw new ValidationException(['id' => trans('front.dont_have_permission')]);

            $this->data['device_id'] = $device['traccar_device_id'];
            $diff = dateDiff($this->data['from_date'].' '.$this->data['from_time'], $this->data['to_date'].' '.$this->data['to_time']);
            if ($diff > Config::get('tobuli.max_history_period_days'))
                throw new ValidationException(['id' => strtr(trans('front.to_large_date_diff'), [':days' => Config::get('tobuli.max_history_period_days')])]);
            if ($device['expiration_date'] != '0000-00-00' && strtotime($device['expiration_date']) < strtotime(date('Y-m-d')))
                throw new ValidationException(['id' =>  trans('front.expired')]);
        }
        catch (ValidationException $e) {
            return $this->api ? ['status' => 0, 'errors' => $e->getErrors()] : current(current(current($e->getErrors())));
        }

        $timezone_id = getUserTimezone($device->users, $this->user->id);
        $timezone_id = is_null($timezone_id) ? NULL : $timezone_id;

        if (is_null($timezone_id))
            $timezone = $this->user->timezone->zone;
        else {
            $timezone = TimezoneRepo::find($timezone_id);
            $timezone = empty($timezone) ? $this->user->timezone->zone : $timezone->zone;
        }
        $zone = timezoneReverse($timezone);

        $date_from = tdate($this->data['from_date'].' '.$this->data['from_time'], $zone);
        $date_to = tdate($this->data['to_date'].' '.$this->data['to_time'], $zone);
        $driver_history = getDevicesDrivers($this->user->id, $device->id, $date_from, $date_to);
        $last_dr = getDevicesDrivers($this->user->id, $device->id, $date_from, NULL, '<=', 1);
        if (!empty($last_dr)) {
            if (!is_array($driver_history))
                $driver_history = [];

            $last_dr = end($last_dr);
            $driver_history[] = $last_dr;
        }

        $events = EventRepo::search(['device_id' => $device->id, 'user_id' => $this->user->id] + $this->data, $zone);
        $positions = TraccarPositionRepo::searchWithSensors($this->user->id, $this->data['device_id'], $date_from, $date_to);

        $engine_sensor = NULL;
        $detect_engine = $device['engine_hours'];
        if ($device['engine_hours'] == 'engine_hours')
            $detect_engine = $device['detect_engine'];

        if ($detect_engine != 'gps') {
            foreach ($device['sensors'] as $key => $sensor) {
                if ($sensor['type'] == $detect_engine)
                    $engine_sensor = $sensor;
            }
        }

        $engine_status = 0;
        if (!empty($engine_sensor)) {
            $table = 'engine_hours_'.$device['traccar_device_id'];
            if (Schema::connection('engine_hours_mysql')->hasTable($table))
                $item = DB::connection('engine_hours_mysql')
                    ->table($table)
                    ->select('other')
                    ->where('sensor_id', '=', $engine_sensor['id'])
                    ->where('time', '<=', $date_from)
                    ->orderBy('time', 'desc')
                    ->first();
            if (!empty($item))
                $engine_status = getSensorValueBool($item->other, $engine_sensor) == 1 ? 1 : 0;
        }

        $history = new HistoryHelper();
        $history->engine_status = $engine_status;
        $history->setEngineHoursType(['engine_hours' => $device['engine_hours'], 'detect_engine' => $device['detect_engine']]);
        $history->api = $this->api;
        $history->history = 1;
        $history->date_from = $date_from;
        $history->date_to = $date_to;
        $history->setSensors($device['sensors']);
        $history->setUnitOfDistance($this->user->unit_of_distance);
        $history->setUnitOfAltitude($this->user->unit_of_altitude);
        $history->setTimezone($timezone);
        $history->parse($positions);
        unset($positions);
        $items = $history->getItems();
        $cords = $history->getCords();
        $sensors = $history->getSensors();
        $sensors_values = $this->api ? [] : $history->getSensorsValues();
        $fuel_consumption_arr = $history->fuel_consumption;
        $distance_sum = $history->distance_sum;
        $top_speed = $history->top_speed;
        $move_duration = $history->move_duration;
        $stop_duration = $history->stop_duration;
        unset($history);

        $arr_sen = [];
        foreach ($sensors as $key => $sensor) {
            $sen_id = array_key_exists('id', $sensor) ? $sensor['id'] : $key;
            $arr_sen[$sen_id] = $sensor;
        }

        if (count($items)) {
            // Add route start and finish item
            reset($items);
            $first_item = current($items);
            $time = $first_item['show'];
            reset($first_item['items']);
            $first = key($first_item['items']);
            if ($first_item['status'] == 1) {
                if (!isset($cords[$first])) {
                    $first = current($first_item['items']);
                    $time = $first['time'];
                }
                else {
                    $time = $cords[$first]['time'];
                }
            }

            if ($this->api) {
                array_unshift($items, [
                    'status' => 3,
                    'time' => null,
                    'show' => $time,
                    'distance' => 0,
                    'raw_time' => $first_item['raw_time'],
                    'items' => [current($first_item['items'])]
                ]);

                $last = end($items);
                end($last['items']);
                $last_cord = current($last['items']);
                if ($last['status'] == 2)
                    $last_cord['time'] = date('Y-m-d H:i:s', strtotime($last_cord['time']));
                array_push($items, [
                    'status' => 4,
                    'time' => null,
                    'distance' => 0,
                    'show' => $last_cord['time'],
                    'raw_time' => $last_cord['raw_time'],
                    'items' => [$last_cord]
                ]);
            }
            else {
                array_unshift($items, [
                    'status' => 3,
                    'time' => 0,
                    'show' => $time,
                    'raw_time' => $first_item['raw_time'],
                    'items' => [$first => '']
                ]);

                $last = end($items);
                end($last['items']);
                $last_id = key($last['items']);
                $last_cord = $cords[$last_id];

                array_push($items, [
                    'status' => 4,
                    'time' => 0,
                    'show' => $last_cord['time'],
                    'raw_time' => $last_cord['raw_time'],
                    'items' => [$last_id => '']
                ]);
            }

            // Insert events
            if (count($events)) {
                $new_arr = [];
                foreach ($items as $item_key => $item) {
                    unset($items[$item_key]);
                    $event = current($events);
                    $event['time'] = tdate($event['time'], $timezone);


                    // If Item time is higher than current event time, we insert all events with lower time, before item
                    if (strtotime($item['raw_time']) > strtotime($event['time'])) {
                        foreach ($events as $key => $event) {
                            $event['message'] = parseEventMessage($event['message'], $event['type']);
                            $event['time'] = tdate($event['time'], $timezone);
                            if (empty($event['speed'])) {
                                $event['speed'] = 0;
                            } else {
                                $event['speed'] = round($this->user->unit_of_distance == 'mi' ? kilometersToMiles($event['speed']) : $event['speed']);
                            }
                            if (strtotime($item['raw_time']) < strtotime($event['time'])) // if event time is higher break cikle
                                break;
                            // We add event to items list, and remove from events list
                            unset($events[$key]);
                            $ev_id = 'event' . $event['id'];
                            if ($this->api) {
                                $new_arr[] = [
                                    'status' => 5,
                                    'time' => null,
                                    'distance' => 0,
                                    'show' => datetime($event['time'], FALSE),
                                    'raw_time' => $event['time'],
                                    'items' => [[
                                        'other' => '',
                                        'speed' => $event['speed'],
                                        'time' => datetime($event['time'], FALSE),
                                        'raw_time' => $event['time'],
                                        'lat' => strval($event['latitude']),
                                        'lng' => strval($event['longitude'])
                                    ]]
                                ];
                            }
                            else {
                                $new_arr[] = [
                                    'status' => 5,
                                    'time' => 0,
                                    'show' => datetime($event['time'], FALSE),
                                    'raw_time' => $event['time'],
                                    'items' => [$ev_id => '']
                                ];
                                $event['lat'] = $event['latitude'];
                                $event['lng'] = $event['longitude'];
                                unset($event['latitude'], $event['longitude']);
                                $cords[$ev_id] = $event + ['event' => 1]; // Event flag, for history graph to skip it
                            }
                        }
                    }
                    $new_arr[] = $item;
                }

                // If there are left events whitch time werent lower than all items, we add them to the end of the items list
                if (count($events)) {
                    foreach ($events as $key => $event) {
                        $event['message'] = parseEventMessage($event['message'], $event['type']);
                        $event['time'] = tdate($event['time'], $timezone);
                        if (empty($event['speed'])) {
                            $event['speed'] = 0;
                        } else {
                            $event['speed'] = round($this->user->unit_of_distance == 'mi' ? kilometersToMiles($event['speed']) : $event['speed']);
                        }
                        unset($events[$key]);
                        $ev_id = 'event' . $event['id'];
                        if ($this->api) {
                            $new_arr[] = [
                                'status' => 5,
                                'time' => null,
                                'distance' => 0,
                                'show' => datetime($event['time'], FALSE),
                                'raw_time' => $event['time'],
                                'items' => [[
                                    'other' => '',
                                    'speed' => $event['speed'],
                                    'time' => datetime($event['time'], FALSE),
                                    'raw_time' => $event['time'],
                                    'lat' => strval($event['latitude']),
                                    'lng' => strval($event['longitude'])
                                ]]
                            ];
                        }
                        else {
                            $new_arr[] = [
                                'status' => 5,
                                'time' => 0,
                                'show' => datetime($event['time'], FALSE),
                                'raw_time' => $event['time'],
                                'items' => [$ev_id => '']
                            ];
                            $event['lat'] = $event['latitude'];
                            $event['lng'] = $event['longitude'];
                            unset($event['latitude'], $event['longitude']);
                            $cords[$ev_id] = $event + ['event' => 1]; // Event flag, for history graph to skip it
                        }
                    }
                }

                $items = $new_arr;
            }
        }

        $driver_history = array_reverse($driver_history);
        foreach ($items as $key => &$item) {
            $item['driver'] = NULL;

            if (!empty($driver_history)) {
                foreach($driver_history as $driver) {
                    if (strtotime($item['show']) < strtotime(tdate(date('Y-m-d H:i:s', $driver->date), $timezone)))
                        break;

                    $item['driver'] = $this->api ? $driver : $driver->name;
                }

                if (empty($item['driver']))
                    $item['driver'] = $this->api ? $driver : $driver->name;
            }

            unset($item['start_position'], $item['stop_position']);
        }

        # Snap to road
        if ($this->data['snap_to_road'] == 'true')
            snapToRoad($items, $cords);

        $images = [
            '1' => asset('assets/img/route_drive.png'),
            '2' => asset('assets/img/route_stop.png'),
            '3' => asset('assets/img/route_start.png'),
            '4' => asset('assets/img/route_end.png'),
            '5' => asset('assets/img/route_event.png')
        ];

        $item_class = [
            '1' => [
                'tr' => 'drive-action',
                'class' => 'action-icon blue',
                'sym' => 'D'
            ],
            '2' => [
                'tr' => 'park-action',
                'class' => 'action-icon grey',
                'sym' => 'P'
            ],
            '3' => [
                'tr' => '',
                'class' => 'action-icon white',
                'sym' => '<i class="fa fa-arrow-down"></i>'
            ],
            '4' => [
                'tr' => '',
                'class' => 'action-icon white',
                'sym' => '<i class="fa fa-flag-o"></i>'
            ],
            '5' => [
                'tr' => 'event-action',
                'class' => 'action-icon red',
                'sym' => 'E'
            ]
        ];

        if ($this->api) {
            $item_class = [
                '1' => 'drive',
                '2' => 'stop',
                '3' => 'start',
                '4' => 'end',
                '5' => 'event'
            ];
            $images = apiArray($images);
            $item_class = apiArray($item_class);

            if( method_exists ( $device, 'toArray' ) ) {
                $device = $device->toArray();
                $arr = [];
                foreach ($device['sensors'] as $key => $sensor) {
                    $arr[] = $sensor;
                }
                $device['sensors'] = $arr;
            }
        }

        $fuel_consumption = $device['fuel_measurement_id'] == 1 ? number_format(($distance_sum * $device['fuel_per_km']), 2, '.', '').' '.trans('front.liters') : number_format(litersToGallons(($distance_sum * $device['fuel_per_km'])), 2, '.', '').' '.trans('front.gallons');
        $top_speed = number_format($top_speed, 2, '.', '').' '.$this->user->distance_unit_hour;
        $distance_sum = number_format(($this->user->unit_of_distance == 'mi' ? kilometersToMiles($distance_sum) : $distance_sum), 2, '.', '').' '.trans('front.'.$this->user->unit_of_distance);

        if ($this->api)
            $cords=[];


        $arr = [];
        if ($this->api) {
            foreach ($fuel_consumption_arr as $id => $value) {
                $key = array_search('sensor_' . $id, array_column($sensors, 'id'));

                if ($key === FALSE) continue;

                array_push($arr, ['name' => $sensors[$key]['name'], 'value' => $value . ' ' . $sensors[$key]['sufix']]);
            }
        } else {
            foreach ($fuel_consumption_arr as $id => $value)
                array_push($arr, ['name' => $sensors[$id]['name'], 'value' => $value.' '.$sensors[$id]['sufix']]);
        }

        $fuel_consumption_arr = $arr;
        unset($arr);

        return compact('items', 'cords', 'distance_sum', 'top_speed', 'move_duration', 'stop_duration', 'fuel_consumption', 'device', 'sensors', 'item_class', 'images', 'sensors_values', 'fuel_consumption_arr');
    }

    public function getMessages() {
        $device = DeviceRepo::find($this->data['device_id']);
        $sorting = isset($this->data['sorting']) && $this->data['sorting'] == 'true' ? 'desc' : 'asc';
        try {
            if (empty($device) || (!$device->users->contains($this->user->id) && !isAdmin()))
                throw new ValidationException(['id' => dontExist('global.device')]);
        }
        catch (ValidationException $e) {
            return $this->api ? ['status' => 0, 'errors' => $e->getErrors()] : current($e->getErrors());
        }

        $this->data['device_id'] = $device['traccar_device_id'];
        $pagination_limits = [
            '50' => 50,
            '100' => 100,
            '200' => 200,
            '400' => 400
        ];
        if (!isset($pagination_limits[$this->data['limit']]))
            $this->data['limit'] = 50;

        if ($this->api) {
            $limit = $this->data['limit'];
        }
        else {
            $limit = 0;
            if (Session::has('history_page_limit'))
                $limit = Session::get('history_page_limit');

            if ($this->data['limit'] != $limit) {
                $limit = $this->data['limit'];
                Session::put('history_page_limit', $limit);
            }
        }

        $timezone_id = getUserTimezone($device->users, $this->user->id);
        $timezone_id = is_null($timezone_id) ? NULL : $timezone_id;

        if (is_null($timezone_id))
            $timezone = $this->user->timezone->zone;
        else {
            $timezone = TimezoneRepo::find($timezone_id);
            $timezone = empty($timezone) ? $this->user->timezone->zone : $timezone->zone;
        }
        $zone = timezoneReverse($timezone);

        $date_from = tdate($this->data['from_date'].' '.$this->data['from_time'], $zone);
        $date_to = tdate($this->data['to_date'].' '.$this->data['to_time'], $zone);
        $from_timestamp = strtotime($date_from);
        $to_timestamp = strtotime($date_to);

        $messages = TraccarPositionRepo::searchWithSensors($this->user->id, $this->data['device_id'], $date_from, $date_to, $sorting);

        $sensors = [];
        foreach ($device['sensors'] as $sensor) {
            if ($sensor['show_in_popup'] || $sensor['add_to_history'])
                $sensors[] = $sensor;
        }

        $parameters = [];

        foreach ($messages as $key => &$message) {
            $timestamp = $message['sort'];
            if ($timestamp < $from_timestamp || $timestamp > $to_timestamp) {
                unset($messages[$key]);
                continue;
            }

            $message['time'] = datetime($message['time'], true, $timezone);

            if ($this->user->unit_of_distance == 'mi')
                $message['speed'] = kilometersToMiles($message['speed']);

            $message['speed'] = round($message['speed']);

            # Convert altitude if users unit of altitude is feets
            if ($this->user->unit_of_altitude == 'ft')
                $message['altitude'] = metersToFeets($message['altitude']);

            $message['altitude'] = round($message['altitude']);

            $message['other_arr'] = [];
            if (!empty($message['other'])) {
                $message['other_arr'] = parseXML($message['other']);
            }

            $message['other_array'] = parseXMLToArray($message['other']);
            if (!empty($message['other_array'])) {
                foreach($message['other_array'] as $el => $oval) {
                    if (array_key_exists($el, $parameters) || empty($el))
                        continue;

                    $parameters[$el] = '';
                }
            }

            $message['popup_sensors'] = [];
            $message['sensors_value'] = [];
            foreach ($sensors as $sensor) {
                $message['sensors_value'][$sensor['id']] = getSensorValue($message['other'], $sensor);
                if ($sensor['show_in_popup']) {
                    $message['popup_sensors'][$sensor['id']] = ['name' => getSensorName($sensor), 'value' => $message['sensors_value'][$sensor['id']]];
                }
            }
        }

        if (!isset($this->data['page']))
            $this->data['page'] = 1;

        $nr = count($messages);
        $messages = array_slice($messages, ($this->data['page'] - 1) * $limit, $limit);
        $messages = new Paginator($messages, $nr, $limit);
        $page = $messages->currentPage();
        $total_pages = $messages->lastPage();

        if ($this->api) {
            $messages = $messages->toArray();
            $messages['url'] = route('api.get_history_messages');
        }

        $pagination = smartPaginate($page, $total_pages);

        return compact('messages', 'sensors', 'pagination_limits', 'limit', 'sorting', 'parameters') + ($this->api ? [] : compact('page', 'total_pages', 'pagination', 'parameters'));
    }

    public function deletePositions() {
        if (!$this->user->perm('history', 'remove')) {
            return $this->api ? ['status' => 0, 'perm' => 0] : trans('front.dont_have_permission');
        }

        $device = DeviceRepo::find($this->data['device_id']);
        if (empty($device) || (!$device->users->contains($this->user->id) && !isAdmin()))
            return;

        if (!empty($this->data['ids'])) {
            foreach ($this->data['ids'] as $id) {
                list($id, $sensor_id) = explode('-', $id);
                if (!empty($sensor_id)) {
                    DB::connection('sensors_mysql')->table('sensors_'.$device->traccar_device_id)->where('id', '=', $sensor_id)->delete();
                }
                else {
                    DB::connection('traccar_mysql')->table('positions_'.$device->traccar_device_id)->where('id', '=', $id)->delete();
                    DB::connection('sensors_mysql')->table('sensors_'.$device->traccar_device_id)->where('position_id', '=', $id)->delete();
                }
            }
        }
    }
}