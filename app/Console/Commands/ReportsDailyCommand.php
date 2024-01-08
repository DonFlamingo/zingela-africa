<?php namespace App\Console\Commands;
ini_set('memory_limit', '-1');
set_time_limit(0);

use Facades\Repositories\UserRepo;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Tobuli\Helpers\ReportHelper;
use Tobuli\Repositories\Report\ReportRepositoryInterface as Report;
use Tobuli\Repositories\Device\DeviceRepositoryInterface as Device;
use Tobuli\Repositories\Geofence\GeofenceRepositoryInterface as Geofence;
use Tobuli\Repositories\TraccarPosition\TraccarPositionRepositoryInterface as TraccarPosition;
use Tobuli\Repositories\EmailTemplate\EmailTemplateRepositoryInterface as EmailTemplate;
use Tobuli\Repositories\Timezone\TimezoneRepositoryInterface as Timezone;
use Tobuli\Repositories\Event\EventRepositoryInterface as Event;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Bugsnag\BugsnagLaravel\BugsnagFacade as Bugsnag;

use Facades\Repositories\ReportLogRepo;

use App\Console\ProcessManager;

class ReportsDailyCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'reports:daily';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';
    /**
     * @var Report
     */
    private $report;
    /**
     * @var Device
     */
    private $device;
    /**
     * @var Geofence
     */
    private $geofence;
    /**
     * @var TraccarPosition
     */
    private $traccarPosition;
    /**
     * @var EmailTemplate
     */
    private $emailTemplate;
    /**
     * @var Timezone
     */
    private $timezone;
    /**
     * @var Event
     */
    private $event;

    private $users = [];

    private $processManager;

    /**
     * Create a new command instance.
     *

     */
	public function __construct(Report $report, Device $device, Geofence $geofence, TraccarPosition $traccarPosition, EmailTemplate $emailTemplate, Timezone $timezone, Event $event)
	{
		parent::__construct();
        $this->report = $report;
        $this->device = $device;
        $this->geofence = $geofence;
        $this->traccarPosition = $traccarPosition;
        $this->emailTemplate = $emailTemplate;
        $this->timezone = $timezone;
        $this->event = $event;
    }

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $timeout = config('tobuli.process.reportdaily_timeout');
        $limit = config('tobuli.process.reportdaily_limit');

        $this->processManager = new ProcessManager('reports:daily', $timeout, $limit);

        /*if (!$this->processManager->canProcess())
        {
            echo "Cant process \n";
            return false;
        }*/

        require_once(base_path('Tobuli/Helpers/Arabic.php'));
        $arabic = new \I18N_Arabic('Glyphs');
        
        $timezones = $this->timezone->getList();
        $schedule_type = $this->argument('type');
        $email_template = $this->emailTemplate->whereName('report');

        $date = $schedule_type == 'daily' ? date('Y-m-d H:i:s', strtotime('+1 day')) : date('Y-m-d H:i:s', strtotime('-6 day', strtotime(date('Y-m-d H:i:s'))));
        //echo $date . PHP_EOL;

        $field = $schedule_type == 'daily' ? 'daily_email_sent' : 'weekly_email_sent';
        $reports = DB::table('reports')
            //->select(DB::Raw("DATE(DATE_ADD('$date', INTERVAL timezones.time HOUR_MINUTE))"))
            ->select('reports.*')
            ->where('reports.'.$schedule_type, '=', 1)
            ->whereRaw("reports.{$field} < '$date'")
            ->groupBy('reports.id')
            ->get();

        if (empty($reports))
            return "DONE\n";

        @mkdir(storage_path('cache'));
        @chmod(storage_path('cache'), 0777);

        $reports = json_decode(json_encode((array) $reports), true);

        foreach ($reports as $data) {
            /*
			if (!$this->processManager->canProcess())
                break;

            if (!$this->processManager->lock($data['id']))
                continue;
			*/
            //check
            $each_data = DB::table('reports')
                ->select('reports.*')
                ->where('reports.'.$schedule_type, '=', 1)
                ->where('reports.id', '=', $data['id'])
                ->whereRaw("reports.{$field} < '$date'")
                ->groupBy('reports.id')
                ->first();

            if (empty($each_data)) {
                //$this->processManager->unlock($data['id']);
                continue;
            }
            $data = json_decode(json_encode($each_data), true);

            if (array_key_exists($data['user_id'], $this->users))
                $user = $this->users[$data['user_id']];
            else
                $user = UserRepo::find($data['user_id']);

            $this->users[$user->id] = $user;

            if ($user->subscription_expiration != '0000-00-00 00:00:00' && strtotime($user->subscription_expiration) < time()) {
                continue;
            }

            $data['zone'] = $user->timezone->zone;
            $data['unit_of_distance'] = $user->unit_of_distance;
            $data['unit_of_altitude'] = $user->unit_of_altitude;
            $data['lang'] = $user->lang;
            $data['logo'] = 1;
            $data['arabic'] = $arabic;

            App::setLocale($data['lang']);
            
            $types = [
                '1'  => trans('front.general_information'),
                '2'  => trans('front.general_information_merged'),
                '16' => trans('front.general_information_merged_custom'),
                '3'  => trans('front.drives_and_stops'),
                '18' => trans('front.drives_and_stops').' / '.trans('front.geofences'),
                '19' => trans('front.drives_and_stops').' / '.trans('front.drivers'),
                '21' => trans('front.drives_and_stops').' / '.trans('front.drivers') . ' (Business)',
                '22' => trans('front.drives_and_stops').' / '.trans('front.drivers') . ' (Private)',
                '4'  => trans('front.travel_sheet'),
                '5'  => trans('front.overspeeds'),
                '6'  => trans('front.underspeeds'),
                '7'  => trans('front.geofence_in_out'),
                '15' => trans('front.geofence_in_out_24_mode'),
                '20' => trans('front.geofence_in_out').' ('.trans('front.ignition_on_off').')',
                '8'  => trans('front.events'),
                '10' => trans('front.fuel_level'),
                '11' => trans('front.fuel_fillings'),
                '12' => trans('front.fuel_thefts'),
                '13' => trans('front.temperature'),
                '14' => trans('front.rag'),
            ];


            $last_send = tdate($data[$schedule_type.'_email_sent'], $data['zone'], false);
            $send_time = date('Y-m-d', strtotime($last_send)) .' '. $data[$schedule_type.'_time'];
            $next_send = date("Y-m-d H:i:s", strtotime(date('Y-m-d H:i:s', strtotime($send_time."+ ".($schedule_type == 'daily' ? 1 : 7)." day"))));
            $next_send = tdate($next_send, $data['zone'], true);
            $current_time = date("Y-m-d H:i:s");

/*
            echo "title: {$data['title']}" . PHP_EOL;
            echo "$current_time current_time" . PHP_EOL;
            echo "$last_send last_send" . PHP_EOL;
            echo "$send_time send_time" . PHP_EOL;
            echo "$next_send next_send user time" . PHP_EOL;
*/
            if (strtotime($next_send) > strtotime($current_time))
                continue;

            //echo "sending before" . PHP_EOL;

            if (strtotime($data[$schedule_type.'_email_sent']) > strtotime($next_send))
                continue;

            //echo "sending" . PHP_EOL;

            $items = [];
            $data['date_from'] = date('Y-m-d', strtotime(($schedule_type == 'daily' ? '-1 day' : '-7day'), strtotime(tdate(date('Y-m-d H:i:s'), $data['zone'])))).' '.$data[$schedule_type.'_time'];
            $data['date_to'] = date('Y-m-d', strtotime(tdate(date('Y-m-d H:i:s'), $data['zone']))).' '.$data[$schedule_type.'_time'];

            if ( $schedule_type == 'daily' && !empty($data['from_format']) && !empty($data['to_format']) ) {
                $now_user_time  = strtotime( date('Y-m-d', strtotime(tdate(date('Y-m-d H:i:s'), $data['zone']))) );
                $timestamp_from = strtotime( $data['from_format'], $now_user_time );
                $timestamp_to   = strtotime( $data['to_format'], $now_user_time );

                if ( $timestamp_from && $timestamp_to ) {
                    $data['date_from'] = date('Y-m-d H:i:s', $timestamp_from);
                    $data['date_to']   = date('Y-m-d H:i:s', $timestamp_to);
                }
            }

            $report_name = mb_convert_encoding(
                $types[$data['type']].'_'.
                $data['date_from'].'_'.
                $data['date_to'].'_'.
                $schedule_type.'_'.$data['user_id'], 'ASCII');
            $report_name = strtr($report_name, [
                ' ' => '_',
                '-' => '_',
                ':' => '_',
                '/' => '_'
            ]);

            $report = $this->report->getWithFirst(['devices', 'devices.sensors', 'devices.users', 'geofences'], ['id' => $data['id']]);
            $geofences = $report->geofences;
            $report = $report->toArray();

            $this->report->update($report['id'], [$field => date('Y-m-d H:i:s')]);

            if (empty($report['devices']))
                continue;

            if (in_array($data['type'], [21, 22]) && !settings('plugins.business_private_drive.status')) {
                continue;
            }

            # Devices
            $devices = [];
            foreach ($report['devices'] as $device) {
                if ($device['expiration_date'] != '0000-00-00' && strtotime($device['expiration_date']) < strtotime(date('Y-m-d')))
                    continue;

                $devices[$device['id']] = $device;
            }

            if (empty($devices))
                continue;

            # User geofences
            if ($data['type'] != 7 && $data['type'] != 15 && $data['type'] != 20)
                $geofences = $this->geofence->getWhere(['user_id' => $data['user_id']]);

            if (empty($data['zone']))
                $data['zone'] = '+0hours';

            $reportHelper =  new ReportHelper($data, $geofences);
            foreach ($devices as $device => $value) {
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

                $timezone_id = getUserTimezone($devices[$device]['users'], $data['user_id']);

                if (!array_key_exists($timezone_id, $timezones)) {
                    $timezone = $data['zone'];
                } else {
                    $timezone = $timezones[$timezone_id];
                }

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
                    if (!empty($item->other))
                        $engine_status = getSensorValueBool($item->other, $engine_sensor) == 1 ? 1 : 0;
                }

                if ($data['type'] == 7) { # Geofence in/out
                    $items_result = $this->traccarPosition->searchWithSensors($data['user_id'], $devices[$device]['traccar_device_id'], $date_from, $date_to);

                    if (!empty($items_result))
                        $items[$device] = $reportHelper->generateGeofences($items_result, $date_from, $date_to);

                    unset($items_result);
                }
                elseif ($data['type'] == 8) { # Events
                    $items_result = $this->event->getBetween($data['user_id'], $device, $date_from, $date_to);
                    if (!empty($items_result))
                        $items[$device] = $reportHelper->generateEvents($items_result->toArray());

                    unset($items_result);
                }
                elseif ($data['type'] == 14) {
                    $items_result = $this->traccarPosition->searchWithSensors($data['user_id'], $devices[$device]['traccar_device_id'], $date_from, $date_to);

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
                    $items_result = $this->traccarPosition->searchWithSensors($data['user_id'], $devices[$device]['traccar_device_id'], $date_from, $date_to);

                    if (!empty($items_result))
                        $items[$device] = $reportHelper->generateGeofences24($items_result, $date_from, $date_to);

                    unset($items_result);
                }
                elseif ($data['type'] == 16) {
                    $items_result = $this->traccarPosition->searchWithSensors($data['user_id'], $devices[$device]['traccar_device_id'], $date_from, $date_to);

                    if (!empty($items_result))
                        $items[$device] = $reportHelper->generateGeneralCustom($items_result, $date_from, $date_to, $devices[$device], $devices[$device]['sensors']);

                    unset($items_result);
                }
                elseif ($data['type'] == 20) {
                    $items_result = $this->traccarPosition->searchWithSensors($data['user_id'], $devices[$device]['traccar_device_id'], $date_from, $date_to);

                    if (!empty($items_result))
                        $items[$device] = $reportHelper->generateGeofencesEngine($items_result, $date_from, $date_to, $devices[$device], $devices[$device]['sensors']);

                    unset($items_result);
                }
                else {
                    $items_result = $this->traccarPosition->searchWithSensors($data['user_id'], $devices[$device]['traccar_device_id'], $date_from, $date_to);

                    if (!empty($items_result)) {
                        $sensors = NULL;
                        $driver_history = NULL;
                        if (in_array($data['type'], [1, 2, 3, 4, 10, 11, 12, 13, 18, 19, 21, 22])) {
                            # Odometer
                            if (count($devices[$device]['sensors'])) {
                                foreach ($devices[$device]['sensors'] as $key => $sensor) {
                                    if ($sensor['type'] == 'odometer') {
                                        if ($sensor['odometer_value_by'] == 'virtual_odometer') {
                                            $result = $this->traccarPosition->sumDistanceHigher($devices[$device]['traccar_device_id'], $date_to)->sum;
                                            if ($sensor['odometer_value_unit'] == 'mi')
                                                $result = kilometersToMiles($result);
                                            
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

            $path = base_path('../../storage/cache');

            if ($data['format'] == 'html') {
                $type = $data['type'] == 13 ? 10 : $data['type'];
                if ($data['type'] == 13 || $data['type'] == 10)
                    $data['sensors_var'] = $data['type'] == 13 ? 'temperature_sensors' : 'fuel_tank_sensors';

                $html = View::make('front::Reports.parse.type_'.$type)->with(compact('devices', 'items', 'types', 'data'))->render();
                $filename = $path.'/'.$report_name.'.html';
                $mime = 'text/html';

                try {
                    file_put_contents($filename, $html);
                } catch (\Exception $e) {
                    Bugsnag::notifyException($e);
                }
            }
            elseif ($data['format'] == 'pdf' || $data['format'] == 'pdf_land') {
                $filename = $path.'/'.$report_name.'.pdf';
                $mime = 'application/pdf';
                $stop = FALSE;
                $failed = FALSE;
                $change_page_size = ($data['format'] == 'pdf_land');
                $tries = 1;
                while (!$stop && !$failed) {
                    try {
                        if ($change_page_size)
                            $pdf = PDF::loadView('front::Reports.parse.type_'.$data['type'], compact('devices', 'items', 'types', 'data'))->setPaper('A4', 'landscape');
                        else
                            $pdf = PDF::loadView('front::Reports.parse.type_'.$data['type'], compact('devices', 'items', 'types', 'data'));

                        $pdf->setTimeout(1200);

                        $pdf->save($filename);
                        $stop = TRUE;
                    }
                    catch (\Exception $e) {
                        Bugsnag::notifyException($e);
                        Bugsnag::notifyError('reportDaily', 'type: ' . $data['type']);

                        $tries++;
                        if ($tries > 2)
                            $failed = TRUE;
                        sleep(1);
                    }
                }

                if ($failed)
                    continue;
            }
            elseif ($data['format'] == 'xls') {
                $filename = $path.'/'.$report_name.'.xls';
                $mime = 'application/vnd.ms-excel';
                try {
                    Excel::create($report_name, function($excel) use ($items, $devices, $types, $data) {
                        $excel->sheet('Report', function($sheet) use ($items, $devices, $types, $data) {
                            $sheet->loadView('front::Reports.parse.type_'.$data['type'], compact('devices', 'items', 'types', 'data'));
                        });
                    })->store('xls', $path);
                }
                catch(\Exception $e) {
                    Bugsnag::notifyException($e);
                    continue;
                }
            }

			$reportLog = ReportLogRepo::create([
				'user_id' => $data['user_id'],
                'email' => $report['email'],
				'title' => $report['title'] . ' ' . $data['date_from'].' - '.$data['date_to'],
				'type' => $data['type'],
				'format' => $data['format'],
				'size' => filesize($filename),
				'data' => file_get_contents($filename)
			]);

            $email_body = strtr($email_template->note, [
                '[name]' => $report['title'],
                '[period]' => $data['date_from'].' - '.$data['date_to']
            ]);
            $email_subject = strtr($email_template->title, [
                '[name]' => $report['title']
            ]);

            $to_arr = explode(';', $report['email']);
            $to = !count($to_arr) ? ['0' => $report['email']] : $to_arr;

            if (!empty($to)) {
                $response = \Facades\MailHelper::send($to, $email_body, $email_subject, $data['lang'], false, [$filename]);
                
                if ( $response && !empty($response['status']) )
				    $reportLog->update( ['is_send' => true ] );
                else
                    $reportLog->update( ['error' => empty($response['error']) ? $response['error'] : null ] );
            }

            unlink($filename);

            //$this->processManager->unlock($data['id']);
        }

        echo "DONE\n";
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
    protected function getArguments()
    {
        return array(
            array('type', InputArgument::REQUIRED, 'The type')
        );
    }

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}
}
