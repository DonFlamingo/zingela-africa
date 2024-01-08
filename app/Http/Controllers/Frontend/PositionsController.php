<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tobuli\Repositories\Device\DeviceRepositoryInterface as Device;
use Tobuli\Repositories\TraccarDevice\TraccarDeviceRepositoryInterface as TraccarDevice;
use Tobuli\Repositories\Config\ConfigRepositoryInterface as Config;
use Tobuli\Repositories\Event\EventRepositoryInterface as Event;
use Tobuli\Repositories\Geofence\GeofenceRepositoryInterface as Geofence;
use Tobuli\Repositories\EmailTemplate\EmailTemplateRepositoryInterface as EmailTemplate;
use Tobuli\Repositories\SmsTemplate\SmsTemplateRepositoryInterface as SmsTemplate;
use Tobuli\Repositories\EventCustom\EventCustomRepositoryInterface as EventCustom;
use Tobuli\Repositories\AlertDevice\AlertDeviceRepositoryInterface as AlertDevice;
use Tobuli\Repositories\TraccarPosition\TraccarPositionRepositoryInterface as TraccarPosition;
use Tobuli\Repositories\UserDriver\UserDriverRepositoryInterface as UserDriver;
use Tobuli\Repositories\Timezone\TimezoneRepositoryInterface as Timezone;

class PositionsController extends Controller {
    /**
     * @var Device
     */
    private $device;
    /**
     * @var TraccarDevice
     */
    private $traccarDevice;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var Event
     */
    private $event;
    /**
     * @var Geofence
     */
    private $geofence;
    /**
     * @var EmailTemplate
     */
    private $emailTemplate;
    /**
     * @var SmsTemplate
     */
    private $smsTemplate;
    /**
     * @var EventCustom
     */
    private $eventCustom;
    /**
     * @var AlertDevice
     */
    private $alertDevice;
    /**
     * @var TraccarPosition
     */
    private $traccarPosition;
    /**
     * @var UserDriver
     */
    private $userDriver;
    /**
     * @var Timezone
     */
    private $timezone;

    private $address = null;
    private $lang = [];
    private $geofences = [];
    private $template;
    private $sms_template;

    function __construct(Device $device, TraccarDevice $traccarDevice, Config $config, Event $event, Geofence $geofence, EmailTemplate $emailTemplate, SmsTemplate $smsTemplate, EventCustom $eventCustom, AlertDevice $alertDevice, TraccarPosition $traccarPosition, UserDriver $userDriver, Timezone $timezone) {
        $this->device = $device;
        $this->traccarDevice = $traccarDevice;
        $this->config = $config;
        $this->event = $event;
        $this->geofence = $geofence;
        $this->emailTemplate = $emailTemplate;
        $this->smsTemplate = $smsTemplate;
        $this->eventCustom = $eventCustom;
        $this->alertDevice = $alertDevice;
        $this->traccarPosition = $traccarPosition;
        $this->userDriver = $userDriver;
        $this->timezone = $timezone;

        # Load
        $this->lang = [];
        $dirs = File::directories(app_path().'/lang');
        foreach ($dirs as $dir) {
            $lg = explode('/', $dir);
            end($lg);
            $this->lang[$lg[key($lg)]] = require($dir.'/front.php');
        }
    }

    public function insert() {
        $input = Input::all();
        if (!isset($input['key']) || $input['key'] != 'Hdaiohaguywhga12344hdsbsdsfsd')
            return;

        $min_distance = 0.02;

        $fix_time = $input['fixTime'];
        $imei = $input['uniqueId'];
        $this->latitude = $input['latitude'];
        $this->longitude = $input['longitude'];
        $other = $input['attributes'];
        $speed = $input['speed'] * 1.852;
        $speed_in_miles = kilometersToMiles($speed);
        $time = date('Y-m-d H:i:s', $fix_time / 1000);
        $altitude = $input['altitude'];
        $course = $input['course'];
        $protocol = $input['protocol'];
        $valid = $input['valid'];

        $inserted_id = NULL;
        $inserted_new_position = FALSE;
        $ack = ($fix_time == 0);
        if ($ack)
            $time = date('Y-m-d H:i:s', $input['deviceTime'] / 1000);

        $other_arr = json_decode($other, true);
        $other_xml = '<info>';
        foreach ($other_arr as $key => $value) {
            $value = is_bool($value) ? ($value ? 'true' : 'false') : $value;
            $other_xml .= "<{$key}>{$value}</$key>";
        }
        $other_xml .= '</info>';

        preg_match_all('~<([^/][^>]*?)>~', $other_xml, $tags_arr, PREG_PATTERN_ORDER);
        $tags_arr = array_flip(array_flip($tags_arr['1']));

        $device = $this->device->getWithFirst(['users'], ['imei' => $imei]);
        if (empty($device) || $device->deleted || $device->users->isEmpty())
            return;

        $users_ids = $device->users->lists('id', 'id')->all();
        $users = [];

        foreach ($device->users as $user)
            $users[$user->id] = $user;

        $traccar_device = $this->traccarDevice->find($device->traccar_device_id);
        if (empty($traccar_device))
            return;

        if (!$ack) {
            $last = DB::connection('traccar_mysql')->select(DB::raw("SELECT * FROM positions_{$traccar_device->id} WHERE (time < '{$time}' OR time = '{$time}') ORDER BY time desc, id desc LIMIT 1"));
            if (is_array($last))
                $last = current($last);

            $lastest_positions = isset($last->latest_positions) ? $last->latest_positions : '';

            if ($speed > 200)
                $speed = $last->speed;

            if (isset($last->latitude) && isset($last->longitude))
                $distance = getDistance($this->latitude, $this->longitude, $last->latitude, $last->longitude);
            else
                $distance = 0;
            if (isset($last->speed) && $last->latitude == $this->latitude && $last->longitude == $this->longitude && $speed > 0)
                $speed = 0;

            if ($distance >= $min_distance OR !isset($last->other) OR round($speed, 7) != round($last->speed, 7)) {
                $inserted_new_position = TRUE;
                $inserted_id = DB::connection('traccar_mysql')->table("positions_{$traccar_device->id}")->insertGetId([
                    'device_id' => $traccar_device->id,
                    'time' => $time,
                    'server_time' => date('Y-m-d H:i:s'),
                    'valid' => $valid,
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                    'altitude' => $altitude,
                    'speed' => $speed,
                    'course' => $course,
                    'other' => $other_xml,
                    'protocol' => $protocol
                ]);
            }
            else {
                if (isset($last->other) && $other_xml != $last->other) {
                    DB::connection('traccar_mysql')->table("positions_{$traccar_device->id}")
                        ->where('id', '=', $last->id)
                        ->update([
                            'valid' => '1',
                            'latitude' => $this->latitude,
                            'longitude' => $this->longitude,
                            'altitude' => $altitude,
                            'speed' => $speed,
                            'course' => $course,
                            'other' => $other_xml,
                            'protocol' => $protocol
                        ]);
                }
            }

            if (!isset($last->time) OR strtotime($time) > strtotime($last->time)) {
                if ($distance >= $min_distance)
                    $lastest_positions = $lastest_positions.$this->latitude.'/'.$this->longitude.';';

                DB::connection('traccar_mysql')->table("devices")
                    ->where('id', '=', $traccar_device->id)
                    ->update([
                        'time' => $time,
                        'server_time' => date('Y-m-d H:i:s'),
                        'lastValidLatitude' => $this->latitude,
                        'lastValidLongitude' => $this->longitude,
                        'altitude' => $altitude,
                        'speed' => $speed,
                        'course' => $course,
                        'other' => $other_xml,
                        'protocol' => $protocol,
                        'latest_positions' => $lastest_positions
                    ]);
            }
        }
        else {
            $last = DB::connection('traccar_mysql')->select(DB::raw("SELECT * FROM positions_{$traccar_device->id} ORDER BY time desc, id desc LIMIT 1"));
            if (is_array($last))
                $last = current($last);

            if (!empty($last)) {
                DB::connection('traccar_mysql')->table("positions_{$traccar_device->id}")
                    ->where('id', '=', $last->id)
                    ->update([
                        'other' => $other_xml,
                    ]);

                if (strtotime($time) > strtotime($last->time)) {
                    DB::connection('traccar_mysql')->table("devices")
                        ->where('id', '=', $traccar_device->id)
                        ->update([
                            'time' => $time,
                            'ack_time' => date('Y-m-d H:i:s'),
                            'speed' => 0,
                            'other' => $other_xml,
                        ]);
                }
            }
        }

        if (empty($inserted_id))
            $inserted_id = $last->id;

        # Sensors
        $sensors = DB::table('device_sensors')
            ->whereIn('tag_name', $tags_arr)
            ->where('device_id', $device->id)
            ->get();

        if (count($sensors)) {
            $insert_sensor_data = FALSE;
            $table_name = 'sensors_'.$device->traccar_device_id;
            if (!Schema::connection('sensors_mysql')->hasTable($table_name)) {
                Schema::connection('sensors_mysql')->create($table_name, function(Blueprint $table)
                {
                    $table->bigIncrements('id');
                    $table->bigInteger('position_id')->unsigned()->index();
                    $table->text('other')->nullable();
                    $table->datetime('time')->nullable()->index();
                    $table->datetime('server_time')->nullable()->index();
                });
            }
            foreach ($sensors as $sensor) {
                preg_match('/<'.preg_quote($sensor->tag_name, '/').'>(.*?)<\/'.preg_quote($sensor->tag_name, '/').'>/s', $other, $matches);
                if (isset($matches['1'])) {
                    $sensor_update = TRUE;
                    if ($sensor->type == 'acc' || $sensor->type == 'door' || $sensor->type == 'engine') {
                        if ($sensor->on_value != $matches['1'] && $sensor->off_value != $matches['1'])
                            $sensor_update = FALSE;
                    }

                    if ($sensor->type == 'ignition' && !checkCondition($sensor->on_type, $matches['1'], $sensor->on_tag_value) && !checkCondition($sensor->off_type, $matches['1'], $sensor->off_tag_value))
                        $sensor_update = FALSE;

                    if ($sensor_update) {
                        $update_arr = [
                            'value' => $matches['1'],
                        ];
                        if ($sensor->type == 'odometer' && $sensor->odometer_value_by == 'connected_odometer')
                            $update_arr['value_formula'] = solveEquation($matches['1'], $sensor->formula);

                        if ($sensor->value != $matches['1'])
                            $insert_sensor_data = TRUE;

                        DB::table('device_sensors')
                            ->where('id', $sensor->id)
                            ->update($update_arr);
                    }
                }
            }
            if ($insert_sensor_data && !$inserted_new_position) {
                DB::connection('sensors_mysql')
                    ->table($table_name)
                    ->insert([
                        'position_id' => $inserted_id,
                        'other' => $other_xml,
                        'time' => $time,
                        'server_time' => date('Y-m-d H:i:s')
                    ]);
            }
        }

        /*if (isset($last->other) && $other_xml == $last->other)
            return;*/

        $timezones = $this->timezone->getList();
        $this->template = $this->emailTemplate->whereName('event');
        $this->sms_template = $this->smsTemplate->whereName('event');

        $device_current = [];


        # Zone in/out
        $items = DB::table('devices')
            ->select(
                DB::Raw("GROUP_CONCAT(geofences.id ORDER BY geofences.id ASC SEPARATOR ',') as geofences_ids,
            user_device.user_id,
            user_device.current_geofences,
            user_device.timezone_id as device_timezone_id
        "))
            ->leftJoin('user_device_pivot as user_device', 'devices.id', '=', 'user_device.device_id')
            ->leftJoin('geofences as geofences', function($query) {
                $query->on('user_device.user_id', '=', 'geofences.user_id');
                $query->on(DB::Raw("ST_Contains(geofences.polygon, POINT({$this->latitude}, {$this->longitude}))"), DB::raw(''), DB::raw(''));
            })
            ->where('devices.id', '=', $device->id)
            ->groupBy("devices.id", "user_device.user_id")
            ->get();

        foreach ($items as $item) {
            if (is_null($item->geofences_ids) && is_null($item->current_geofences))
                continue;

            $user = $users[$item->user_id];
            $timezone = isset($timezones[$user->timezone_id]) ? $timezones[$user->timezone_id] : $timezones['17'];
            $timezone = isset($timezones[$device->timezone_id]) ? $timezones[$device->timezone_id] : $timezone;
            $sms_gateway_arr = [
                'status' => $user->sms_gateway,
                'url' => $user->sms_gateway_url,
                'params' => $user->sms_gateway_params
            ];

            if (is_null($item->geofences_ids)) {
                $cur_geofences = [];
            } else {
                $cur_geofences = explode(',', $item->geofences_ids);
            }

            $geofences = !is_null($item->current_geofences) ? explode(',', $item->current_geofences) : [];

            $all_geofences = array_merge($geofences, $cur_geofences);
            $alerts = DB::table('alert_geofence')
                ->select('alert_geofence.*', 'alerts.id', 'alerts.ac_alarm', 'alerts.email', 'alerts.mobile_phone')
                ->join('alerts', 'alert_geofence.alert_id', '=', 'alerts.id')
                ->join('alert_device', 'alerts.id', '=', 'alert_device.alert_id')
                ->whereIn('alert_geofence.geofence_id', $all_geofences)
                ->where('alert_device.device_id', $device->id)
                ->where('alerts.user_id', $item->user_id)
                ->where('alerts.active', 1)
                ->get();

            if (!empty($alerts)) {
                $left_geofences = array_flip(array_diff($geofences, $cur_geofences));
                $entered_geofences = array_flip(array_diff($cur_geofences, $geofences));

                foreach ($alerts as $alert) {
                    $sms_gateway_arr['mobile_phone'] = $alert->mobile_phone;
                    // Check Zone Out
                    if ($alert->zone == 2 && isset($left_geofences[$alert->geofence_id])) {
                        $geofence_name = $this->getGeofenceName($alert->geofence_id);
                        $this->event->create([
                            'user_id' => $item->user_id,
                            'geofence_id' => $alert->geofence_id,
                            'position_id' => $inserted_id,
                            'alert_id' => $alert->id,
                            'device_id' => $device->id,
                            'message' => $this->lang[$user->lang]['zone_out'],
                            'address' => $this->getAddress(),
                            'altitude' => $altitude,
                            'course' => $course,
                            'latitude' => $this->latitude,
                            'longitude' => $this->longitude,
                            'speed' => $speed,
                            'time' => $time
                        ]);

                        sendEmailTemplate($this->template, $alert->email, [
                            '[event]' => $this->lang[$user->lang]['zone_out'],
                            '[geofence]' => $geofence_name,
                            '[device]' => htmlentities($device->name),
                            '[address]' => $this->getAddress(),
                            '[position]' => $this->latitude . '&deg;, ' . $this->longitude . '&deg;',
                            '[altitude]' => $user->unit_of_altitude == 'ft' ? round(metersToFeets($altitude)).' '.$this->lang[$user->lang]['ft'] : round($altitude) .' '.$this->lang[$user->lang]['mt'],
                            '[speed]' => $user->unit_of_distance == 'mi' ? round(kilometersToMiles($speed)).' '.$this->lang[$user->lang]['dis_h_mi'] : round($speed) .' '.$this->lang[$user->lang]['dis_h_km'],
                            '[time]' => datetime($time, TRUE, $timezone)
                        ], 'front::Emails.template', $sms_gateway_arr, $this->sms_template, $item->user_id);
                    } // Check Zone In
                    elseif ($alert->zone == 1 && isset($entered_geofences[$alert->geofence_id])) {
                        $geofence_name = $this->getGeofenceName($alert->geofence_id);
                        $this->event->create([
                            'user_id' => $item->user_id,
                            'geofence_id' => $alert->geofence_id,
                            'position_id' => $inserted_id,
                            'alert_id' => $alert->id,
                            'device_id' => $device->id,
                            'message' => $this->lang[$user->lang]['zone_in'],
                            'address' => $this->getAddress(),
                            'altitude' => $altitude,
                            'course' => $course,
                            'latitude' => $this->latitude,
                            'longitude' => $this->longitude,
                            'speed' => $speed,
                            'time' => $time
                        ]);

                        sendEmailTemplate($this->template, $alert->email, [
                            '[event]' => $this->lang[$user->lang]['zone_in'],
                            '[geofence]' => $geofence_name,
                            '[device]' => htmlentities($device->name),
                            '[address]' => $this->getAddress(),
                            '[position]' => $this->latitude . '&deg;, ' . $this->longitude . '&deg;',
                            '[altitude]' => $user->unit_of_altitude == 'ft' ? round(metersToFeets($altitude)).' '.$this->lang[$user->lang]['ft'] : round($altitude) .' '.$this->lang[$user->lang]['mt'],
                            '[speed]' =>$user->unit_of_distance == 'mi' ? round(kilometersToMiles($speed)).' '.$this->lang[$user->lang]['dis_h_mi'] : round($speed) .' '.$this->lang[$user->lang]['dis_h_km'],
                            '[time]' => datetime($time, TRUE, $timezone)
                        ], 'front::Emails.template', $sms_gateway_arr, $this->sms_template, $item->user_id);
                    }

                    $device_current[$item->user_id]['geofences'] = $cur_geofences;
                }
            }
        }

        # Overspeeds
        $alerts = DB::table('alert_device')
            ->select('alerts.id', 'alerts.user_id', 'alerts.email', 'alerts.mobile_phone', 'alerts.overspeed_distance', 'alerts.overspeed_speed')
            ->join('alerts', 'alert_device.alert_id', '=', 'alerts.id')
            ->where('alert_device.device_id', $device->id)
            ->where('alerts.active', 1)
            ->whereRaw("alerts.overspeed_speed > '0' AND (alerts.overspeed_distance = '1' AND alerts.overspeed_speed < '{$speed}' OR alerts.overspeed_distance = '2' AND alerts.overspeed_speed < '{$speed_in_miles}') AND alert_device.overspeed < '".date('Y-m-d H:i:s', (time() - 900))."'")
            ->groupBy('alert_device.id')
            ->get();

        if (!empty($alerts)) {
            foreach ($alerts as $alert) {
                $user = $users[$alert->user_id];
                $timezone = isset($timezones[$user->timezone_id]) ? $timezones[$user->timezone_id] : $timezones['17'];
                $timezone = isset($timezones[$device->timezone_id]) ? $timezones[$device->timezone_id] : $timezone;
                $sms_gateway_arr = [
                    'status' => $user->sms_gateway,
                    'url' => $user->sms_gateway_url,
                    'params' => $user->sms_gateway_params
                ];

                $sms_gateway_arr['mobile_phone'] = $alert->mobile_phone;

                $speed_with = $alert->overspeed_speed.' '.($alert->overspeed_distance == 1 ? $this->lang[$user->lang]['km'] : $this->lang[$user->lang]['mi']);
                $this->event->create([
                    'user_id' => $alert->user_id,
                    'geofence_id' => null,
                    'position_id' => $inserted_id,
                    'alert_id' => $alert->id,
                    'device_id' => $device->id,
                    'message' => $this->lang[$user->lang]['overspeed'].'('.$speed_with.')',
                    'address' => $this->getAddress(),
                    'altitude' => $altitude,
                    'course' => $course,
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                    'speed' => $speed,
                    'time' => $time,
                ]);

                sendEmailTemplate($this->template, $alert->email, [
                    '[event]' => $this->lang[$user->lang]['overspeed'].'('.$speed_with.')',
                    '[geofence]' => '',
                    '[device]' => htmlentities($device->name),
                    '[address]' => $this->getAddress(),
                    '[position]' => $this->latitude . '&deg;, ' . $this->longitude . '&deg;',
                    '[altitude]' => $user->unit_of_altitude == 'ft' ? round(metersToFeets($altitude)).' '.$this->lang[$user->lang]['ft'] : round($altitude) .' '.$this->lang[$user->lang]['mt'],
                    '[speed]' => $user->unit_of_distance == 'mi' ? round(kilometersToMiles($speed)).' '.$this->lang[$user->lang]['dis_h_mi'] : round($speed) .' '.$this->lang[$user->lang]['dis_h_km'],
                    '[time]' => datetime($time, TRUE, $timezone)
                ], 'front::Emails.template', $sms_gateway_arr, $this->sms_template, $alert->user_id);

                $this->alertDevice->updateWhere([
                    'alert_id' => $alert->id,
                    'device_id' => $device->id
                ], [
                    'overspeed' => date('Y-m-d H:i:s')
                ]);
            }
        }


        # Custom events
        $alerts = DB::table('alerts')
            ->select('events_custom.always', 'events_custom.id as event_id', 'events_custom.message', 'events_custom.conditions', 'alerts.*')
            ->join('alert_device', function($query) use($device) {
                $query->on('alerts.id', '=', 'alert_device.alert_id');
                $query->where('alert_device.device_id', '=', $device->id);
            })
            ->join('alert_event_pivot', function($query) {
                $query->on('alerts.id', '=', 'alert_event_pivot.alert_id');
            })
            ->join('events_custom', function($query) use($protocol) {
                $query->on('alert_event_pivot.event_id', '=', 'events_custom.id');
            })
            ->join('event_custom_tags', function($query) {
                $query->on('events_custom.id', '=', 'event_custom_tags.event_custom_id');
            })
            ->where(function($query) use($users_ids) {
                $query->whereIn('events_custom.user_id', $users_ids);
                $query->orWhere('events_custom.user_id', '=', null);
            })
            ->where('events_custom.protocol', '=', $protocol)
            ->groupBy('events_custom.id')
            ->where('alerts.active', 1)
            ->get();

        if (!empty($alerts)) {
            foreach ($alerts as $alert) {
                $user = $users[$alert->user_id];
                $timezone = isset($timezones[$user->timezone_id]) ? $timezones[$user->timezone_id] : $timezones['17'];
                $timezone = isset($timezones[$device->timezone_id]) ? $timezones[$device->timezone_id] : $timezone;
                $sms_gateway_arr = [
                    'status' => $user->sms_gateway,
                    'url' => $user->sms_gateway_url,
                    'params' => $user->sms_gateway_params
                ];
                $current_events = explode(',', $user->pivot->current_events);
                $current_events = array_flip($current_events);

                $conditions = unserialize($alert->conditions);
                foreach ($conditions as $condition) {
                    $send_event = FALSE;
                    preg_match_all('/<'.preg_quote($condition['tag'], '/').'>(.*?)<\/'.preg_quote($condition['tag'], '/').'>/s', $other_xml, $matches);
                    if (count($matches['1'])) {
                        foreach($matches['1'] as $key => $text) {
                            if ($condition['tag'] == 'rfid' && $protocol == 'meitrack')
                                $text = hexdec($text);

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
                    $device_current[$user->id]['events'][] = $alert->event_id;
                    if (isset($current_events[$alert->event_id]))
                        continue;
                }

                $sms_gateway_arr['mobile_phone'] = $alert->mobile_phone;
                $this->event->create([
                    'user_id' => $alert->user_id,
                    'position_id' => $inserted_id,
                    'alert_id' => $alert->id,
                    'device_id' => $device->id,
                    'message' => $alert->message,
                    'address' => $this->getAddress(),
                    'altitude' => $altitude,
                    'course' => $course,
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                    'speed' => $speed,
                    'time' => $time,
                ]);

                sendEmailTemplate($this->template, $alert->email, [
                    '[event]' => $alert->message,
                    '[geofence]' => '',
                    '[device]' => htmlentities($device->name),
                    '[address]' => $this->getAddress(),
                    '[position]' => $this->latitude . '&deg;, ' . $this->longitude . '&deg;',
                    '[altitude]' => $user->unit_of_altitude == 'ft' ? round(metersToFeets($altitude)).' '.$this->lang[$user->lang]['ft'] : round($altitude) .' '.$this->lang[$user->lang]['mt'],
                    '[speed]' => $user->unit_of_distance == 'mi' ? round(kilometersToMiles($speed)).' '.$this->lang[$user->lang]['dis_h_mi'] : round($speed) .' '.$this->lang[$user->lang]['dis_h_km'],
                    '[time]' => datetime($time, TRUE, $timezone)
                ], 'front::Emails.template', $sms_gateway_arr, $this->sms_template, $alert->user_id);
            }
        }

        # Current driver
        preg_match('/<rfid>(.*?)<\/rfid>/s', $other_xml, $driver_rfid);
        if (isset($driver_rfid['1']) && !empty($driver_rfid['1'])) {
            $rfid = $driver_rfid['1'];
            if ($protocol == 'meitrack')
                $rfid = hexdec($rfid);

            $drivers = DB::table('user_drivers')
                ->whereIn('user_id', $users_ids)
                ->where('rfid', '=', $rfid)
                ->get();

            foreach ($drivers as $driver) {
                $user = $users[$driver->user_id];
                $timezone = isset($timezones[$user->timezone_id]) ? $timezones[$user->timezone_id] : $timezones['17'];
                $timezone = isset($timezones[$device->timezone_id]) ? $timezones[$device->timezone_id] : $timezone;
                $sms_gateway_arr = [
                    'status' => $user->sms_gateway,
                    'url' => $user->sms_gateway_url,
                    'params' => $user->sms_gateway_params
                ];

                if ($user->pivot->current_driver_id == $driver->id)
                    continue;

                $device_current[$user->id]['driver'] = $driver->id;

                $alerts = DB::table('alert_driver_pivot')
                    ->select('alerts.id', 'alerts.mobile_phone', 'alerts.email')
                    ->join('alerts', 'alert_driver_pivot.alert_id', '=', 'alerts.id')
                    ->where('alert_driver_pivot.driver_id', $driver->id)
                    ->where('alerts.active', 1)
                    ->get();

                foreach ($alerts as $alert) {
                    $sms_gateway_arr['mobile_phone'] = $alert->mobile_phone;

                    $this->event->create([
                        'user_id' => $driver->user_id,
                        'position_id' => $inserted_id,
                        'alert_id' => $alert->id,
                        'device_id' => $device->id,
                        'message' => sprintf($this->lang[$user->lang]['driver_alert'], $driver->name),
                        'address' => $this->getAddress(),
                        'altitude' => $altitude,
                        'course' => $course,
                        'latitude' => $this->latitude,
                        'longitude' => $this->longitude,
                        'speed' => $speed,
                        'time' => $time
                    ]);

                    sendEmailTemplate($this->template, $alert->email, [
                        '[event]' => sprintf($this->lang[$user->lang]['driver_alert'], $driver->name),
                        '[geofence]' => NULL,
                        '[device]' => htmlentities($device->name),
                        '[address]' => $this->getAddress(),
                        '[position]' => $this->latitude . '&deg;, ' . $this->longitude . '&deg;',
                        '[altitude]' => $user->unit_of_altitude == 'ft' ? round(metersToFeets($altitude)).' '.$this->lang[$user->lang]['ft'] : round($altitude) .' '.$this->lang[$user->lang]['mt'],
                        '[speed]' => $user->unit_of_distance == 'mi' ? round(kilometersToMiles($speed)).' '.$this->lang[$user->lang]['dis_h_mi'] : round($speed) .' '.$this->lang[$user->lang]['dis_h_km'],
                        '[time]' => datetime($time, TRUE, $timezone)
                    ], 'front::Emails.template', $sms_gateway_arr, $this->sms_template, $driver->user_id);
                }
            }
        }

        foreach ($users_ids as $user_id) {
            $updated = FALSE;
            $tries = 0;
            while (!$updated && $tries < 5) {
                try {
                    $update_arr = [];
                    if (isset($device_current[$user_id])) {
                        $current = $device_current[$user_id];
                        $update_arr = [
                            'current_geofences' => isset($current['geofences']) ? implode(',', $current['geofences']) : null,
                            'current_events' => isset($current['events']) ? implode(',', $current['events']) : null,
                            'current_driver_id' => isset($current['driver']) ? $current['driver'] : $users[$user_id]->current_driver_id,
                        ];
                    }
                    if (!empty($update_arr)) {
                        DB::table('user_device_pivot')
                            ->where([
                                'device_id' => $device->id,
                                'user_id' => $user_id
                            ])
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

    private function getGeofenceName($id) {
        if (isset($this->geofences[$id]))
            return $this->geofences[$id];

        $geofence = $this->geofence->find($id);
        $this->geofences[$id] = htmlentities($geofence->name);
        return $geofence->name;
    }

    private function getAddress() {
        if (!is_null($this->address))
            return $this->address;

        $address = @json_decode(@file_get_contents('http://ztx.lt/app/gmaps/index.php?format=json&lat='.$this->latitude.'&lon='.$this->longitude), true);
        $this->address = isset($address['display_name']) ? $address['display_name'] : '-';
        return $this->address;
    }
}
