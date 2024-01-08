<?php namespace ModalHelpers;

ini_set('memory_limit', '-1');
set_time_limit(0);

use Facades\Repositories\DeviceRepo;
use Facades\Repositories\EventRepo;
use Facades\Repositories\GeofenceRepo;
use Facades\Repositories\ReportRepo;
use Facades\Repositories\TimezoneRepo;
use Facades\Repositories\TraccarPositionRepo;
use Facades\Repositories\UserRepo;
use Facades\Validators\ReportFormValidator;
use Facades\Validators\ReportSaveFormValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Helpers\ReportHelper;

use Carbon\Carbon;

class ReportModalHelper extends ModalHelper
{
    private $types = [];

    function __construct()
    {
        parent::__construct();

        $this->types = [
            '1' => trans('front.general_information'),
            '2' => trans('front.general_information_merged'),
            '16' => trans('front.general_information_merged_custom'),
            '3' => trans('front.drives_and_stops'),
            '18' => trans('front.drives_and_stops').' / '.trans('front.geofences'),
            '19' => trans('front.drives_and_stops').' / '.trans('front.drivers'),
            '21' => trans('front.drives_and_stops').' / '.trans('front.drivers') . ' (Business)',
            '22' => trans('front.drives_and_stops').' / '.trans('front.drivers') . ' (Private)',
            '4' => trans('front.travel_sheet'),
            '5' => trans('front.overspeeds'),
            '6' => trans('front.underspeeds'),
            '7' => trans('front.geofence_in_out'),
            '15' => trans('front.geofence_in_out_24_mode'),
            '20' => trans('front.geofence_in_out').' ('.trans('front.ignition_on_off').')',
            '8' => trans('front.events'),
            /*'9' => trans('front.service'),*/
            '10' => trans('front.fuel_level'),
            '11' => trans('front.fuel_fillings'),
            '12' => trans('front.fuel_thefts'),
            '13' => trans('front.temperature'),
            '14' => trans('front.rag'),
        ];
    }

    public function get()
    {
        $reports = ReportRepo::searchAndPaginate(['filter' => ['user_id' => $this->user->id]], 'id', 'desc', 10);
        $types = $this->types;

        if ($this->api) {
            $reports = $reports->toArray();
            $reports['url'] = route('api.get_reports');
            foreach ($reports['data'] as &$item) {
                $item['devices'] = array_pluck($item['devices'], 'id');
                $geofences = [];
                foreach ($item['geofences'] as $geofence)
                    array_push($geofences, $geofence['id']);
                $item['geofences'] = $geofences;
            }
            $new_arr = [];
            foreach ($types as $id => $title) {
                array_push($new_arr, ['id' => $id, 'title' => $title]);
            }
            $types = $new_arr;
        }

        return compact('reports', 'types');
    }

    public function createData()
    {
        $devices = UserRepo::getDevices($this->user->id);
        foreach ($devices as $key => $device) {
            if ($device['expiration_date'] != '0000-00-00' && strtotime($device['expiration_date']) < strtotime(date('Y-m-d')))
                unset($devices[$key]);
        }
        if (empty($devices))
            return $this->api ? ['status' => 0, 'errors' => ['id' => trans('front.no_devices')]] : view('front::Layouts.partials.modal_warning')->with(['type' => 'alert', 'message' => trans('front.no_devices')]);

        $geofences = GeofenceRepo::getWhere(['user_id' => $this->user->id]);

        $formats = [
            'html' => trans('front.html'),
            'xls' => trans('front.xls'),
            'pdf' => trans('front.pdf'),
            'pdf_land' => trans('front.pdf_land'),
        ];

        $stops = [
            '1' => '> 1 '.trans('front.minute_short'),
            '2' => '> 2 '.trans('front.minute_short'),
            '5' => '> 5 '.trans('front.minute_short'),
            '10' => '> 10 '.trans('front.minute_short'),
            '20' => '> 20 '.trans('front.minute_short'),
            '30' => '> 30 '.trans('front.minute_short'),
            '60' => '> 1 '.trans('front.hour_short'),
            '120' => '> 2 '.trans('front.hour_short'),
            '300' => '> 5 '.trans('front.hour_short'),
        ];

        $filters = [
            '0' => '',
            '1' => trans('front.today'),
            '2' => trans('front.yesterday'),
            '3' => trans('front.before_2_days'),
            '4' => trans('front.before_3_days'),
            '5' => trans('front.this_week'),
            '6' => trans('front.last_week'),
            '7' => trans('front.this_month'),
            '8' => trans('front.last_month'),
        ];

        $types = $this->types;
        $types_list = $this->types;

        if ( ! settings('plugins.business_private_drive.status') ) {
            unset( $types_list['21'], $types_list['22'] );
        }

        if ($this->api) {
            $formats = apiArray($formats);
            $stops = apiArray($stops);
            $filters = apiArray($filters);
            $types = apiArray($types);
        }

        $reports = ReportRepo::searchAndPaginate(['filter' => ['user_id' => $this->user->id]], 'id', 'desc', 10);
        $reports->setPath(route('reports.index'));

        if ($this->api) {
            $reports = $reports->toArray();
            $reports['url'] = route('api.get_reports');
            $geofences = $geofences->toArray();
            foreach ($geofences as &$geo)
                unset($geo['polygon']);

            //devices list return as array, not object
            $devices = array_values( $devices->all() );
        }

        return compact('devices', 'geofences', 'formats', 'stops', 'filters', 'types', 'types_list', 'reports');
    }

    public function create()
    {
        try
        {
            if ($this->api) {
                $this->data['devices'] = json_decode($this->data['devices'], TRUE);
                $this->data['geofences'] = json_decode($this->data['geofences'], TRUE);
            }

            $arr['send_to_email'] = array_flip(explode(';', $this->data['send_to_email']));
            unset($arr['send_to_email']['']);
            $arr['send_to_email'] = array_flip($arr['send_to_email']);
            $arr['send_to_email'] = array_map('trim', $arr['send_to_email']);

            # Regenerate string
            $this->data['send_to_email'] = implode(';', $arr['send_to_email']);

            $validator = Validator::make($arr, ['send_to_email' => 'array_max:5']);
            $validator->each('send_to_email', ['email']);
            if ($validator->fails())
                throw new ValidationException(['send_to_email' => $validator->errors()->first()]);

            if ($this->data['type'] == '7') {
                $validator = Validator::make($this->data, ['geofences' => 'required']);
                if ($validator->fails())
                    throw new ValidationException(['geofences' => $validator->errors()->first()]);
            }

            if ($this->data['type'] == '5' || $this->data['type'] == '6') {
                $validator = Validator::make($this->data, ['speed_limit' => 'required']);
                if ($validator->fails())
                    throw new ValidationException(['speed_limit' => $validator->errors()->first()]);
            }

            if (isset($this->data['daily']) || isset($this->data['weekly'])) {
                $validator = Validator::make($arr, ['send_to_email' => 'required']);
                if ($validator->fails())
                    throw new ValidationException(['send_to_email' => $validator->errors()->first()]);
            }


            ReportSaveFormValidator::validate('create', $this->data);

            $now = Carbon::parse( tdate(date('Y-m-d H:i:s'), NULL, FALSE, 'Y-m-d') );
            $days = $now->diffInDays( Carbon::parse( $this->data['date_from'] ) , false);
            $this->data['from_format'] = $days . ' days ' . (empty($this->data['from_time']) ? '00:00' : $this->data['from_time']);
            $days = $now->diffInDays( Carbon::parse( $this->data['date_to'] ) , false);
            $this->data['to_format'] = $days . ' days ' . (empty($this->data['to_time']) ? '00:00' : $this->data['to_time']);

            /*
            echo "<pre>";
            var_dump(
                $now,
                $this->data['date_from'],
                $this->data['date_to'],
                Carbon::parse( tdate($this->data['date_from'], NULL, TRUE, 'Y-m-d') ),
                Carbon::parse( tdate($this->data['date_to'], NULL, TRUE, 'Y-m-d') )
            );
            dd('exit');
            */

            if ( ! $this->api ) {
                $this->data['date_from'] .= ' ' . (empty($this->data['from_time']) ? '00:00' : $this->data['from_time']);
                $this->data['date_to']   .= ' ' . (empty($this->data['to_time']) ? '00:00' : $this->data['to_time']);
            }

            $this->data['email'] = $this->data['send_to_email'];

            $daily_time = '00:00';
            if (isset($this->data['daily_time']) && preg_match("/(2[0-4]|[01][1-9]|10):([0-5][0-9])/", $this->data['daily_time']))
                $daily_time = $this->data['daily_time'];

            $this->data['daily_time'] = $daily_time;

            $weekly_time = '00:00';
            if (isset($this->data['weekly_time']) && preg_match("/(2[0-4]|[01][1-9]|10):([0-5][0-9])/", $this->data['weekly_time']))
                $weekly_time = $this->data['weekly_time'];

            $this->data['weekly_time'] = $weekly_time;

            if ( !empty($this->data['id']) && empty(ReportRepo::find($this->data['id'])) ) {
                unset($this->data['id']);
            }

            if (empty($this->data['id']))
                $item = ReportRepo::create($this->data + [
                        'user_id' => $this->user->id,
                        'daily_email_sent' => date('Y-m-d', strtotime('-1 day')),
                        'weekly_email_sent' => date("Y-m-d",strtotime('monday this week'))
                    ]);
            else {
                $item = ReportRepo::findWhere(['id' => $this->data['id'], 'user_id' => $this->user->id]);
                if (!empty($item))
                    ReportRepo::update($item->id, $this->data);
            }

            if (!empty($item)) {
                if (isset($this->data['devices']) && is_array($this->data['devices']) && !empty($this->data['devices']))
                    $item->devices()->sync($this->data['devices']);

                if (isset($this->data['geofences']) && is_array($this->data['geofences']) && !empty($this->data['geofences']))
                    $item->geofences()->sync($this->data['geofences']);
            }
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }

        return ['status' => $this->api ? 1 : 2];
    }

    public function generate($data = NULL)
    {
        if (is_null($data))
            $data = $this->data;

        try
        {
            ReportFormValidator::validate('create', $this->data);
            
            $data['date_from'] .= ( empty($data['from_time']) ? '' : ' ' . $data['from_time']);
            $data['date_to']   .= ( empty($data['to_time']) ? '' : ' ' . $data['to_time']);

            if (strtotime($data['date_from']) > strtotime($data['date_to'])) {
                $message = str_replace(':attribute', trans('validation.attributes.date_to'), trans('validation.after'));
                $message = str_replace(':date', trans('validation.attributes.date_from'), $message);
                throw new ValidationException(['date_to' => $message]);
            }

            if ($data['type'] == '7' || $data['type'] == '15' || $data['type'] == '20') {
                $validator = Validator::make($data, ['geofences' => 'required']);
                if ($validator->fails())
                    throw new ValidationException(['geofences' => $validator->errors()->first()]);
            }

            if ($data['type'] == '5' || $data['type'] == '6') {
                $validator = Validator::make($data, ['speed_limit' => 'required']);
                if ($validator->fails())
                    throw new ValidationException(['speed_limit' => $validator->errors()->first()]);
            }

            if (!isset($data['generate'])) {
                unset($data['_token']);
                unset($data['from_time']);
                unset($data['to_time']);
                return ['status' => 3, 'url' => route($this->api ? 'api.generate_report' : 'reports.update').'?'.http_build_query($data + ['generate' => 1], '', '&')];
            }

            $timezones = TimezoneRepo::getList();
            $items = [];

            $data['unit_of_distance'] = $this->user->unit_of_distance;
            $data['unit_of_altitude'] = $this->user->unit_of_altitude;
            $data['user_id'] = $this->user->id;
            $data['logo'] = 1;
            $data['lang'] = $this->user->lang;
            require(base_path('Tobuli/Helpers/Arabic.php'));
            $data['arabic'] = new \I18N_Arabic('Glyphs');

            $report_name = mb_convert_encoding($this->types[$data['type']].'_'.$data['date_from'].'_'.$data['date_to'].'_'.$data['user_id'], 'ASCII');
            $report_name = strtr($report_name, [
                ' ' => '_',
                '-' => '_',
                ':' => '_',
                '/' => '_'
            ]);

            # Devices
            $arr = [];
            $devices = DeviceRepo::getWhereInWith($data['devices'], 'id', ['sensors', 'users'])->toArray();
            foreach ($devices as $device) {
                $arr[$device['id']] = $device;
            }
            $devices = $arr;
            unset($arr);


            # User geofences
            if ($data['type'] != 7 && $data['type'] != 15 && $data['type'] != 20)
                $geofences = GeofenceRepo::getWhere(['user_id' => $data['user_id']]);
            else
                $geofences = GeofenceRepo::getWhereIn($data['geofences']);

            $reportHelper = new ReportHelper($data, $geofences);

            foreach ($data['devices'] as $key => $device) {
                $engine_sensor = NULL;
                $detect_engine = $devices[$device]['engine_hours'];
                if ($devices[$device]['engine_hours'] == 'engine_hours')
                    $detect_engine = $devices[$device]['detect_engine'];

                if ($detect_engine != 'gps') {
                    foreach ($devices[$device]['sensors'] as $key => $sensor) {
                        if ($sensor['type'] == $detect_engine)
                            $engine_sensor = $sensor;
                    }
                }

                $timezone_id = getUserTimezone($devices[$device]['users'], $this->user->id);
                $timezone = !isset($timezones[$timezone_id]) ? $this->user->timezone->zone : $timezones[$timezone_id];
                $reportHelper->setData([
                    'zone' => $timezone
                ]);
                $date_from = tdate($data['date_from'], timezoneReverse($timezone));
                $date_to = tdate($data['date_to'], timezoneReverse($timezone));

                $engine_status = 0;
                if (!empty($engine_sensor)) {
                    $table = 'engine_hours_'.$devices[$device]['traccar_device_id'];
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

                $reportHelper->data['stop_speed'] = $devices[$device]['min_moving_speed'];
                if ($data['type'] == 7) { # Geofence in/out
                    $items_result = TraccarPositionRepo::searchWithSensors($this->user->id, $devices[$device]['traccar_device_id'], $date_from, $date_to);

                    if (!empty($items_result))
                        $items[$device] = $reportHelper->generateGeofences($items_result, $date_from, $date_to);

                    unset($items_result);
                }
                elseif ($data['type'] == 8) { # Events
                    $items_result = EventRepo::getBetween($data['user_id'], $device, $date_from, $date_to);
                    if (!empty($items_result))
                        $items[$device] = $reportHelper->generateEvents($items_result->toArray());

                    unset($items_result);
                }
                elseif ($data['type'] == 14) {
                    $items_result = TraccarPositionRepo::searchWithSensors($data['user_id'], $devices[$device]['traccar_device_id'], $date_from, $date_to);

                    $driver_history = getDevicesDrivers($data['user_id'], $device, $date_from, $date_to, '>=', NULL, TRUE);
                    $last_dr = getDevicesDrivers($data['user_id'], $device, $date_from, NULL, '<=', 1);
                    if (!empty($last_dr)) {
                        if (!is_array($driver_history))
                            $driver_history = [];

                        $last_dr = end($last_dr);
                        $driver_history[] = $last_dr;
                    }

                    $rag_sensors = [];
                    foreach ($devices[$device]['sensors'] as $key => $sensor) {
                        if ($sensor['type'] == 'harsh_acceleration' || $sensor['type'] == 'harsh_breaking')
                            array_push($rag_sensors, $sensor);
                    }

                    $items[$device] = $reportHelper->generateRag($items_result, $driver_history, $devices[$device], $rag_sensors, $date_from, $date_to);
                }
                elseif ($data['type'] == 15) { # Geofence in/out 24 mode
                    $items_result = TraccarPositionRepo::searchWithSensors($data['user_id'], $devices[$device]['traccar_device_id'], $date_from, $date_to);

                    if (!empty($items_result))
                        $items[$device] = $reportHelper->generateGeofences24($items_result, $date_from, $date_to);

                    unset($items_result);
                }
                elseif ($data['type'] == 16) {
                    $items_result = TraccarPositionRepo::searchWithSensors($data['user_id'], $devices[$device]['traccar_device_id'], $date_from, $date_to);

                    if (!empty($items_result))
                        $items[$device] = $reportHelper->generateGeneralCustom($items_result, $date_from, $date_to, $devices[$device], $devices[$device]['sensors']);
                    unset($items_result);
                }
                elseif ($data['type'] == 20) { # Geofence in/out engine on/off
                    $items_result = TraccarPositionRepo::searchWithSensors($this->user->id, $devices[$device]['traccar_device_id'], $date_from, $date_to);

                    if (!empty($items_result))
                        $items[$device] = $reportHelper->generateGeofencesEngine($items_result, $date_from, $date_to, $devices[$device], $devices[$device]['sensors']);

                    unset($items_result);
                }
                else {
                    $items_result = TraccarPositionRepo::searchWithSensors($this->user->id, $devices[$device]['traccar_device_id'], $date_from, $date_to);

                    if (!empty($items_result)) {
                        $sensors = NULL;
                        $driver_history = NULL;

                        if (in_array($data['type'], [1, 2, 3, 4, 10, 11, 12, 13, 18, 19, 21, 22])) {
                            # Odometer
                            if (count($devices[$device]['sensors'])) {
                                foreach ($devices[$device]['sensors'] as $key => $sensor) {
                                    if ($sensor['type'] == 'odometer') {
                                        if ($sensor['odometer_value_by'] == 'virtual_odometer') {
                                            $result = TraccarPositionRepo::sumDistanceHigher($devices[$device]['traccar_device_id'], $date_to)->sum;
                                            $sensor['odometer_value'] = round($sensor['odometer_value'] - $result);
                                        }
                                    }
                                    $sensors[] = $sensor;
                                }
                            }
                        }

                        if (in_array($data['type'], [1, 2, 3, 10, 11, 12, 13, 14, 19, 21, 22])) {
                            $driver_history = getDevicesDrivers($data['user_id'], $device, $date_from, $date_to, '>=', NULL, TRUE);
                            $last_dr = getDevicesDrivers($data['user_id'], $device, $date_from, NULL, '<=', 1);
                            if (!empty($last_dr)) {
                                if (!is_array($driver_history))
                                    $driver_history = [];

                                $last_dr = end($last_dr);
                                $driver_history[] = $last_dr;
                            }
                        }

                        $items[$device] = $reportHelper->generate($items_result, $sensors, $driver_history, $devices[$device], $date_from, $date_to, $engine_status);
                    }

                    unset($items_result);
                }
            }

            unset($reportHelper);



            if (in_array($data['type'], [19, 21, 22])) {
                $arr = [
                    'items' => [],
                    'devices' => $devices,
                    'data' => $data
                ];

                foreach ($items as $device_id => $item) {
                    foreach ($item->getItems() as $it) {
                        $arr['items'][$it['driver']]['items'][strtotime($it['raw_time'])] = $it + ['device' => $device_id];
                        if (!array_key_exists('total', $arr['items'][$it['driver']])) {
                            $arr['items'][$it['driver']]['total'] = [
                                'drive' => 0,
                                'stop' => 0,
                                'distance' => 0,
                                'fuel' => 0,
                                'engine_work' => 0,
                                'engine_idle' => 0
                            ];
                        }
                        $arr['items'][$it['driver']]['total']['distance'] += $it['distance'];
                        $arr['items'][$it['driver']]['total']['fuel'] += $it['fuel_consumption'];
                        $arr['items'][$it['driver']]['total']['engine_work'] += $it['engine_work'];
                        $arr['items'][$it['driver']]['total']['engine_idle'] += $it['engine_idle'];
                        if ($it['status'] == 1) {
                            $arr['items'][$it['driver']]['total']['drive'] += $it['time_seconds'];
                        }
                        elseif ($it['status'] == 2) {
                            $arr['items'][$it['driver']]['total']['stop'] += $it['time_seconds'];
                        }

                        if ( empty($arr['items'][$it['driver']]['total']['fuel_sensor']) ) {
                            $fuel_sensor_id = null;

                            if (isset($item->fuel_consumption) && is_array($item->fuel_consumption)) {
                                reset($item->fuel_consumption);
                                $fuel_sensor_id = key($item->fuel_consumption);
                            }

                            if ( isset($item->sensors_arr[$fuel_sensor_id]) ) {
                                $arr['items'][$it['driver']]['total']['fuel_sensor'] = $item->sensors_arr[$fuel_sensor_id];
                            }
                        }
                    }


                }
                $items = $arr;
            }

            $types = $this->types;


            if ($data['format'] == 'html') {
                $type = $data['type'] == 13 ? 10 : $data['type'];
                if ($data['type'] == 13 || $data['type'] == 10)
                    $data['sensors_var'] = $data['type'] == 13 ? 'temperature_sensors' : 'fuel_tank_sensors';

                $html = view('front::Reports.parse.type_'.$type)->with(compact('devices', 'items', 'types', 'data'))->render();
                header('Content-disposition: attachment; filename="'.utf8_encode($report_name).'.html"');
                header('Content-type: text/html');

                echo $html;
            }
            elseif ($data['format'] == 'pdf' || $data['format'] == 'pdf_land') {
                $stop = FALSE;
                $change_page_size = ($data['format'] == 'pdf_land');
                $tries = 1;
                while (!$stop) {
                    try {
                        if ($change_page_size)
                            $pdf = PDF::loadView('front::Reports.parse.type_'.$data['type'], compact('devices', 'items', 'types', 'data'))->setPaper('A4', 'landscape');
                        else
                            $pdf = PDF::loadView('front::Reports.parse.type_'.$data['type'], compact('devices', 'items', 'types', 'data'));

                        $pdf->setTimeout( config('snappy.timeout') );

                        return $pdf->download($report_name.'.pdf');
                    }
                    catch (\Exception $e) {
                        Bugsnag::notifyException($e);
/*
                        if ($e instanceof \DOMPDF_Exception && $e->getMessage() == 'Frame not found in cellmap') {
                            $change_page_size = TRUE;
                        }
*/
                        $tries++;
                        if ($tries > 2)
                            $stop = TRUE;
                        sleep(1);
                    }
                }
                return 'Sorry can\'t generate, too mutch data.';
            }
            elseif ($data['format'] == 'xls') {
            	$type = $data['type'] == 13 ? 10 : $data['type'];
                try {
                    Excel::create($report_name, function($excel) use ($items, $devices, $types, $data, $type) {
                        $excel->sheet('Report', function($sheet) use ($items, $devices, $types, $data, $type) {
                            $sheet->loadView('front::Reports.parse.type_'.$type, compact('devices', 'items', 'types', 'data'));
                        });
                    })->export('xls');
                }
                catch(\Exception $e) {
                    //Bugsnag::notifyException($e);
                	return $e;
                    return 'Sorry can\'t generate, too mutch data.';
                }
            }
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

    public function doDestroy($id)
    {
        $item = ReportRepo::find($id);
        if (empty($item) || $item->user_id != $this->user->id)
            return modal(dontExist('front.report'), 'danger');

        return compact('item');
    }

    public function destroy()
    {
        $id = array_key_exists('report_id', $this->data) ? $this->data['report_id'] : $this->data['id'];
        $item = ReportRepo::find($id);
        if (empty($item) || $item->user_id != $this->user->id)
            return ['status' => 0, 'errors' => ['id' => dontExist('front.report')]];

        ReportRepo::delete($id);

        return ['status' => 1];
    }


    //generate json
    public function generateJson($data = NULL)
    {
        if (is_null($data))
            $data = $this->data;

        try
        {
            ReportFormValidator::validate('create', $this->data);
            
            $data['date_from'] .= ( empty($data['from_time']) ? '' : ' ' . $data['from_time']);
            $data['date_to']   .= ( empty($data['to_time']) ? '' : ' ' . $data['to_time']);

            if (strtotime($data['date_from']) > strtotime($data['date_to'])) {
                $message = str_replace(':attribute', trans('validation.attributes.date_to'), trans('validation.after'));
                $message = str_replace(':date', trans('validation.attributes.date_from'), $message);
                throw new ValidationException(['date_to' => $message]);
            }

            if ($data['type'] == '7' || $data['type'] == '15' || $data['type'] == '20') {
                $validator = Validator::make($data, ['geofences' => 'required']);
                if ($validator->fails())
                    throw new ValidationException(['geofences' => $validator->errors()->first()]);
            }

            if ($data['type'] == '5' || $data['type'] == '6') {
                $validator = Validator::make($data, ['speed_limit' => 'required']);
                if ($validator->fails())
                    throw new ValidationException(['speed_limit' => $validator->errors()->first()]);
            }

            if (!isset($data['generate'])) {
                unset($data['_token']);
                unset($data['from_time']);
                unset($data['to_time']);
                return ['status' => 3, 'url' => route($this->api ? 'api.generate_report' : 'reports.update').'?'.http_build_query($data + ['generate' => 1], '', '&')];
            }

            $timezones = TimezoneRepo::getList();
            $items = [];

            $data['unit_of_distance'] = $this->user->unit_of_distance;
            $data['unit_of_altitude'] = $this->user->unit_of_altitude;
            $data['user_id'] = $this->user->id;
            $data['logo'] = 1;
            $data['lang'] = $this->user->lang;
            require(base_path('Tobuli/Helpers/Arabic.php'));
            $data['arabic'] = new \I18N_Arabic('Glyphs');

            $report_name = mb_convert_encoding($this->types[$data['type']].'_'.$data['date_from'].'_'.$data['date_to'].'_'.$data['user_id'], 'ASCII');
            $report_name = strtr($report_name, [
                ' ' => '_',
                '-' => '_',
                ':' => '_',
                '/' => '_'
            ]);

            # Devices
            $arr = [];
            $devices = DeviceRepo::getWhereInWith($data['devices'], 'id', ['sensors', 'users'])->toArray();
            foreach ($devices as $device) {
                $arr[$device['id']] = $device;
            }
            $devices = $arr;
            unset($arr);


            # User geofences
            if ($data['type'] != 7 && $data['type'] != 15 && $data['type'] != 20)
                $geofences = GeofenceRepo::getWhere(['user_id' => $data['user_id']]);
            else
                $geofences = GeofenceRepo::getWhereIn($data['geofences']);

            $reportHelper = new ReportHelper($data, $geofences);

            foreach ($data['devices'] as $key => $device) {
                $engine_sensor = NULL;
                $detect_engine = $devices[$device]['engine_hours'];
                if ($devices[$device]['engine_hours'] == 'engine_hours')
                    $detect_engine = $devices[$device]['detect_engine'];

                if ($detect_engine != 'gps') {
                    foreach ($devices[$device]['sensors'] as $key => $sensor) {
                        if ($sensor['type'] == $detect_engine)
                            $engine_sensor = $sensor;
                    }
                }

                $timezone_id = getUserTimezone($devices[$device]['users'], $this->user->id);
                $timezone = !isset($timezones[$timezone_id]) ? $this->user->timezone->zone : $timezones[$timezone_id];
                $reportHelper->setData([
                    'zone' => $timezone
                ]);
                $date_from = tdate($data['date_from'], timezoneReverse($timezone));
                $date_to = tdate($data['date_to'], timezoneReverse($timezone));

                $engine_status = 0;
                if (!empty($engine_sensor)) {
                    $table = 'engine_hours_'.$devices[$device]['traccar_device_id'];
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

                $reportHelper->data['stop_speed'] = $devices[$device]['min_moving_speed'];
                if ($data['type'] == 7) { # Geofence in/out
                    $items_result = TraccarPositionRepo::searchWithSensors($this->user->id, $devices[$device]['traccar_device_id'], $date_from, $date_to);

                    if (!empty($items_result))
                        $items[$device] = $reportHelper->generateGeofences($items_result, $date_from, $date_to);

                    unset($items_result);
                }
                elseif ($data['type'] == 8) { # Events
                    $items_result = EventRepo::getBetween($data['user_id'], $device, $date_from, $date_to);
                    if (!empty($items_result))
                        $items[$device] = $reportHelper->generateEvents($items_result->toArray());

                    unset($items_result);
                }
                elseif ($data['type'] == 14) {
                    $items_result = TraccarPositionRepo::searchWithSensors($data['user_id'], $devices[$device]['traccar_device_id'], $date_from, $date_to);

                    $driver_history = getDevicesDrivers($data['user_id'], $device, $date_from, $date_to, '>=', NULL, TRUE);
                    $last_dr = getDevicesDrivers($data['user_id'], $device, $date_from, NULL, '<=', 1);
                    if (!empty($last_dr)) {
                        if (!is_array($driver_history))
                            $driver_history = [];

                        $last_dr = end($last_dr);
                        $driver_history[] = $last_dr;
                    }

                    $rag_sensors = [];
                    foreach ($devices[$device]['sensors'] as $key => $sensor) {
                        if ($sensor['type'] == 'harsh_acceleration' || $sensor['type'] == 'harsh_breaking')
                            array_push($rag_sensors, $sensor);
                    }

                    $items[$device] = $reportHelper->generateRag($items_result, $driver_history, $devices[$device], $rag_sensors, $date_from, $date_to);
                }
                elseif ($data['type'] == 15) { # Geofence in/out 24 mode
                    $items_result = TraccarPositionRepo::searchWithSensors($data['user_id'], $devices[$device]['traccar_device_id'], $date_from, $date_to);

                    if (!empty($items_result))
                        $items[$device] = $reportHelper->generateGeofences24($items_result, $date_from, $date_to);

                    unset($items_result);
                }
                elseif ($data['type'] == 16) {
                    $items_result = TraccarPositionRepo::searchWithSensors($data['user_id'], $devices[$device]['traccar_device_id'], $date_from, $date_to);

                    if (!empty($items_result))
                        $items[$device] = $reportHelper->generateGeneralCustom($items_result, $date_from, $date_to, $devices[$device], $devices[$device]['sensors']);
                    unset($items_result);
                }
                elseif ($data['type'] == 20) { # Geofence in/out engine on/off
                    $items_result = TraccarPositionRepo::searchWithSensors($this->user->id, $devices[$device]['traccar_device_id'], $date_from, $date_to);

                    if (!empty($items_result))
                        $items[$device] = $reportHelper->generateGeofencesEngine($items_result, $date_from, $date_to, $devices[$device], $devices[$device]['sensors']);

                    unset($items_result);
                }
                else {
                    $items_result = TraccarPositionRepo::searchWithSensors($this->user->id, $devices[$device]['traccar_device_id'], $date_from, $date_to);

                    if (!empty($items_result)) {
                        $sensors = NULL;
                        $driver_history = NULL;

                        if (in_array($data['type'], [1, 2, 3, 4, 10, 11, 12, 13, 18, 19, 21, 22])) {
                            # Odometer
                            if (count($devices[$device]['sensors'])) {
                                foreach ($devices[$device]['sensors'] as $key => $sensor) {
                                    if ($sensor['type'] == 'odometer') {
                                        if ($sensor['odometer_value_by'] == 'virtual_odometer') {
                                            $result = TraccarPositionRepo::sumDistanceHigher($devices[$device]['traccar_device_id'], $date_to)->sum;
                                            $sensor['odometer_value'] = round($sensor['odometer_value'] - $result);
                                        }
                                    }
                                    $sensors[] = $sensor;
                                }
                            }
                        }

                        if (in_array($data['type'], [1, 2, 3, 10, 11, 12, 13, 14, 19, 21, 22])) {
                            $driver_history = getDevicesDrivers($data['user_id'], $device, $date_from, $date_to, '>=', NULL, TRUE);
                            $last_dr = getDevicesDrivers($data['user_id'], $device, $date_from, NULL, '<=', 1);
                            if (!empty($last_dr)) {
                                if (!is_array($driver_history))
                                    $driver_history = [];

                                $last_dr = end($last_dr);
                                $driver_history[] = $last_dr;
                            }
                        }

                        $items[$device] = $reportHelper->generate($items_result, $sensors, $driver_history, $devices[$device], $date_from, $date_to, $engine_status);
                    }

                    unset($items_result);
                }
            }

            unset($reportHelper);



            if (in_array($data['type'], [19, 21, 22])) {
                $arr = [
                    'items' => [],
                    'devices' => $devices,
                    'data' => $data
                ];

                foreach ($items as $device_id => $item) {
                    foreach ($item->getItems() as $it) {
                        $arr['items'][$it['driver']]['items'][strtotime($it['raw_time'])] = $it + ['device' => $device_id];
                        if (!array_key_exists('total', $arr['items'][$it['driver']])) {
                            $arr['items'][$it['driver']]['total'] = [
                                'drive' => 0,
                                'stop' => 0,
                                'distance' => 0,
                                'fuel' => 0,
                                'engine_work' => 0,
                                'engine_idle' => 0
                            ];
                        }
                        $arr['items'][$it['driver']]['total']['distance'] += $it['distance'];
                        $arr['items'][$it['driver']]['total']['fuel'] += $it['fuel_consumption'];
                        $arr['items'][$it['driver']]['total']['engine_work'] += $it['engine_work'];
                        $arr['items'][$it['driver']]['total']['engine_idle'] += $it['engine_idle'];
                        if ($it['status'] == 1) {
                            $arr['items'][$it['driver']]['total']['drive'] += $it['time_seconds'];
                        }
                        elseif ($it['status'] == 2) {
                            $arr['items'][$it['driver']]['total']['stop'] += $it['time_seconds'];
                        }

                        if ( empty($arr['items'][$it['driver']]['total']['fuel_sensor']) ) {
                            $fuel_sensor_id = null;

                            if (isset($item->fuel_consumption) && is_array($item->fuel_consumption)) {
                                reset($item->fuel_consumption);
                                $fuel_sensor_id = key($item->fuel_consumption);
                            }

                            if ( isset($item->sensors_arr[$fuel_sensor_id]) ) {
                                $arr['items'][$it['driver']]['total']['fuel_sensor'] = $item->sensors_arr[$fuel_sensor_id];
                            }
                        }
                    }


                }
                $items = $arr;
            }

            $types = $this->types;

            //get_summery response
            $type = $data['type'] == 13 ? 10 : $data['type'];
            if ($data['type'] == 13 || $data['type'] == 10)
                $data['sensors_var'] = $data['type'] == 13 ? 'temperature_sensors' : 'fuel_tank_sensors';

            $summery = [];
            foreach($items as $device){
                $summery= ['Route start' => $device->route_start,
                'Route end' => $device->route_end,
                'Route length' => $device->distance_sum,
                'Move duration' => $device->move_duration,
                'Stop duration' => $device->stop_duration,
                'Top speed' => $device->top_speed,
                'Average speed' => $device->average_speed,
                'Overspeed count' => $device->overspeed_count,
                'Engine hours' => $device->engine_hours,
                'Engine work' => $device->engine_work,
                'Engine idle' => $device->engine_idle];
            } 
            foreach($devices as $device){
                $summery += ['Device' => $device['name'],
                            'Fuel consumption (GPS)' => $device['fuel_quantity'],
                            'Fuel cost (GPS)' => $device['fuel_price']];
            }   
            
            return($summery);
            
            
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

}