<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Facades\ModalHelpers\DeviceModalHelper;
use Facades\Repositories\DeviceRepo;
use Facades\Repositories\UserRepo;
use Facades\Repositories\TraccarPositionRepo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tobuli\Helpers\HistoryHelper;
use Cache;

class DevicesController extends Controller
{

    public function create()
    {
        $data = DeviceModalHelper::createData();

        return is_array($data) && !$this->api ? view('front::Devices.create')->with($data) : $data;
    }

    public function store()
    {
    	Cache::forget('devices_front_'. Auth::user()->id);
        
        return DeviceModalHelper::create();
    }

    public function edit($id = NULL, $admin = FALSE)
    {
		
        $data = DeviceModalHelper::editData();

        return is_array($data) && !$this->api ? view('front::Devices.edit')->with(array_merge($data, ['admin' => $admin])) : $data;
    }

    public function update()
    {
		
    	Cache::forget('devices_front_'. Auth::user()->id);
		
        return DeviceModalHelper::edit();
    }

    public function changeActive()
    {
    	Cache::forget('devices_front_'. Auth::user()->id);
    
        return DeviceModalHelper::changeActive();
    }

    public function destroy()
    {
    	Cache::forget('devices_front_'. Auth::user()->id);
    
        return DeviceModalHelper::destroy();
    }

    public function stopTime($device_id = NULL)
    {
        if (is_null($device_id))
            $device_id = request()->get('device_id');

        $time = '0'.trans('front.h');
        $device = DeviceRepo::getWithFirst(['traccar', 'users', 'sensors'], ['id' => $device_id]);
        if (empty($device) || (!$device->users->contains($this->user->id) && !isAdmin()))
            return ['time' => $time];

        $timestamp = time() - 43200;
        $date_from = date('Y-m-d H:i:s', $timestamp);
        $date_to = date('Y-m-d H:i:s', strtotime('+ 1 day'));
        //$date_from = tdate(date('Y-m-d H:i:s', $timestamp), $this->user->timezone->zone);
        //$date_to = tdate(date('Y-m-d H:i:s', strtotime('+ 1 day', strtotime(date('Y-m-d')))), $this->user->timezone->zone);

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

        if (strtotime($device->traccar->server_time) < $timestamp) {
            $time = '12+'.trans('front.h');
        }
        else {
            $positions = TraccarPositionRepo::searchWithSensors($this->user->id, $device->traccar_device_id, $date_from, $date_to);
            $history = new HistoryHelper();
            $history->engine_status = $engine_status;
            $history->setEngineHoursType(['engine_hours' => $device['engine_hours'], 'detect_engine' => $device['detect_engine']]);
            $history->api = TRUE;
            $history->history = 1;
            $history->date_from = $date_from;
            $history->date_to = $date_to;
            $history->setSensors($device['sensors']);
            $history->setTimezone($this->user->timezone->zone);
            $history->parse($positions);
            $items = $history->getItems();
            $items = array_reverse($items);

            foreach ($items as $item) {
                if ($item['status'] == 1) {
                    break;
                }
                if ($item['status'] == 2) {
                    $time = $item['time'];
                    $cur = strtotime(tdate(date("Y-m-d H:i:s"), $this->user->timezone->zone)) - strtotime($item['raw_time']);
                    if ($cur > 0)
                        $time = secondsToTime($cur);
                    break;
                }
            }
        }

        return ['time' => $time];
    }

    public function followMap($device_id)
    {
        $item = UserRepo::getDevice($this->user->id, $device_id);

        if (empty($item) || (!$item->users->contains($this->user->id) && !isAdmin()))
            return modal(dontExist('global.device'), 'danger');

        $item->lat = $item->lat;
        $item->lng = $item->lng;
        $item->speed = $item->speed;
        $item->course = $item->course;
        $item->altitude = $item->altitude;
        $item->protocol = $item->protocol;
        $item->time = $item->time;
        $item->timestamp = $item->timestamp;
        $item->acktimestamp = $item->acktimestamp;

        return view('front::Devices.follow_map', compact('item'));
    }

    public function getDevice(){
       // die("sdff");
        return DeviceModalHelper::getDevice();
    }
}
