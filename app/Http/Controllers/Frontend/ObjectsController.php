<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Curl;
use Facades\ModalHelpers\DeviceModalHelper;
use Facades\Repositories\DeviceGroupRepo;
use Facades\Repositories\DeviceRepo;
use Facades\Repositories\GeofenceGroupRepo;
use Facades\Repositories\MapIconRepo;
use Facades\Repositories\TimezoneRepo;
use Facades\Repositories\UserDriverRepo;
use Facades\Repositories\UserRepo;
use Facades\Repositories\TraccarPositionRepo;
use Tobuli\Helpers\HistoryHelper;
use Illuminate\Support\Facades\Config;
use ModalHelpers\SendCommandModalHelper;
use Tobuli\Entities\User;
use Tobuli\Entities\UserMapIcon;
use Tobuli\Entities\Device;
use Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ObjectsController extends Controller {

    private function stopTime(Device $device)
    {
        $time = '0'.trans('front.h');
        
        if (empty($device) )
            return $time;

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

        return $time;
    }

    public function index() {
        $version = Config::get('tobuli.version');
        $devices = [];
        if ($this->user->perm('devices', 'view'))
            $devices = UserRepo::getDevices($this->user->id);
            $devices_find = $devices;
        if (!empty($devices))
            $devices = $devices->lists('name', 'id')->all();

        $history = [
            'start' => date('Y-m-d', strtotime(tdate(date('Y-m-d H:i:s')))),
            'end' => date('Y-m-d', strtotime(tdate(date('Y-m-d H:i:s')))),
            'end_time' => '23:45',
            'def_device' => NULL
        ];

        $poi_find = UserMapIcon::where('user_id', Auth::user()->id)->get();

        $mapIcons = MapIconRepo::all();

        $geofence_groups = ['0' => trans('front.ungrouped')] + GeofenceGroupRepo::getWhere(['user_id' => $this->user->id])->lists('title', 'id')->all();

        return view('front::Objects.index')->with(compact('devices', 'history', 'version', 'geofence_groups', 'mapIcons', 'devices_find', 'poi_find'));
    }

    public function items() {
        $timezones = Cache::remember('timezones_list', 60 * 24 * 30 * 525600, function() {
            return TimezoneRepo::getList();
        });
        $device_groups = ['0' => trans('front.ungrouped')] + DeviceGroupRepo::getWhere(['user_id' => $this->user->id])->lists('title', 'id')->all();
        $device_groups_opened = array_flip(json_decode($this->user->open_device_groups, TRUE));

        $grouped = [];
        $drivers = [];

        if ( $this->user->perm('devices', 'view') ) {

            if(!Cache::has('devices_front_'. Auth::user()->id)) {
                $devices = UserRepo::getDevices($this->user->id);
                
                    foreach ($devices as $key => $device) {
                                $devices[$key]->active = $device->pivot->active;
                
                                $devices[$key]->speed = $device->getSpeed();
                                $devices[$key]->online = $device->getStatus();
                                $devices[$key]->lat = $device->lat;
                                $devices[$key]->lng = $device->lng;
                                $devices[$key]->course = $device->course;
                                $devices[$key]->altitude = $device->altitude;
                                $devices[$key]->protocol = $device->protocol;
                                $devices[$key]->time = $device->time;
                                $devices[$key]->timestamp = $device->timestamp;
                                $devices[$key]->acktimestamp = $device->acktimestamp;
                                $devices[$key]->formatSensors = $device->getFormatSensors();
                                $devices[$key]->formatServices = $device->getFormatServices();
                                $devices[$key]->tail = $device->tail;
                                if (!empty($devices[$key]->pivot->current_driver_id))
                                    $devices[$key]->driver = UserDriverRepo::getWhere(['id' => $devices[$key]->pivot->current_driver_id]);
                    }

                    Cache::put('devices_front_'. Auth::user()->id, $devices, 10);        

             }

             else {
                $devices = Cache::get('devices_front_'. Auth::user()->id);
             }


            if (request()->wantsJson())
                return response()->json($devices);

            $grouped = [];
            $drivers = [];
            foreach ($devices as $device) {
                $group_id = empty($device->pivot->group_id) || !array_key_exists($device->pivot->group_id, $device_groups) ? 0 : $device->pivot->group_id;
                $grouped[$group_id][] = $device;

                if (!empty($device->pivot->current_driver_id))
                    $drivers[] = $device->pivot->current_driver_id;
            }
            unset($devices);

            if (!empty($drivers))
                $drivers = UserDriverRepo::getWhereIn($drivers)->lists('name', 'id')->all();
        }

        return view('front::Objects.items')->with(compact('grouped', 'device_groups', 'drivers', 'timezones', 'device_groups_opened'));
    }

    public function itemsJson() {
        $data = DeviceModalHelper::itemsJson();

        return $data;
    }

    public function changeGroupStatus() {

        $device_groups_opened = array_flip(json_decode($this->user->open_device_groups, TRUE));

        if (isset($device_groups_opened[$this->data['id']])) {
            unset($device_groups_opened[$this->data['id']]);
            $device_groups_opened = array_flip($device_groups_opened);
        }
        else {
            $device_groups_opened = array_flip($device_groups_opened);
            array_push($device_groups_opened, $this->data['id']);
        }

        UserRepo::update($this->user->id, [
            'open_device_groups' => json_encode($device_groups_opened)
        ]);
    }

    public function changeAlarmStatus()
    {
        if (!array_key_exists('id', $this->data) && array_key_exists('device_id', $this->data))
            $this->data['id'] = $this->data['device_id'];
        $item = DeviceRepo::find($this->data['id']);
        if (empty($item) || (!$item->users->contains($this->user->id) && !isAdmin()))
            return ['status' => 0];

        $table = 'sensors_'.$item->traccar_device_id;
        if (Schema::connection('sensors_mysql')->hasTable($table))
            $position = DB::connection('sensors_mysql')->table($table)->select('time')->orderBy('time', 'desc')->first();

        $sendCommandModalHelper = new SendCommandModalHelper();
        $sendCommandModalHelper->setData([
            'device_id' => $item->id,
            'type' => $item->alarm == 0 ? 'alarmArm' : 'alarmDisarm'
        ]);
        $result = $sendCommandModalHelper->gprsCreate();

        $alarm = $item->alarm;

        if ($result['status'] == 1) {
            $tr = TRUE;
            $times = 1;
            $val = '';
            if (isset($position)) {
                while($tr && $times < 6) {
                    $positions = DB::connection('sensors_mysql')->table($table)->select('other')->where('time', '>', $position->time)->orderBy('time', 'asc')->get();
                    if ($times >= 5)
                        $positions = DB::connection('sensors_mysql')->table($table)->select('other')->orderBy('time', 'desc')->limit(2)->get();
                    foreach ($positions as $pos) {
                        preg_match('/<'.preg_quote('alarm', '/').'>(.*?)<\/'.preg_quote('alarm', '/').'>/s', $pos->other, $matches);
                        if (!isset($matches['1']))
                            continue;

                        $val = $matches['1'];
                        if ($val == 'lt' || $val == 'mt' || $val == 'lf') {
                            $tr = FALSE;
                            break;
                        }
                    }

                    $times++;
                    sleep(1);
                }
            }

            $status = 0;

            if (!$tr) {
                if (($item->alarm == 0 && $val == 'lt') || ($item->alarm == 1 && $val == 'mt')) {
                    $status = 1;
                    $alarm = $item->alarm == 1 ? 0 : 1;
                    DeviceRepo::update($item->id, [
                        'alarm' => $alarm
                    ]);
                }
            }

            return ['status' => $status, 'alarm' => intval($alarm), 'error' => trans('front.unexpected_error')];
        }
        else {
            return ['status' => 0, 'alarm' => intval($alarm), 'error' => isset($result['error']) ? $result['error'] : ''];
        }
    }

    public function alarmPosition()
    {
        $item = DeviceRepo::find($this->data['id']);
        if (empty($item) || (!$item->users->contains($this->user->id) && !isAdmin()))
            return response()->json(['status' => 0]);

        $sendCommandModalHelper = new SendCommandModalHelper();
        $sendCommandModalHelper->setData([
            'device_id' => $item->id,
            'type' => 'positionSingle'
        ]);
        $result = $sendCommandModalHelper->gprsCreate();

        if ($result['status'] == 1)
            return ['status' => 1];
        else
            return ['status' => 0, 'error' => isset($result['error']) ? $result['error'] : ''];
    }

    public function showAddress() {
        $curl = new Curl;
        $curl->follow_redirects = false;
        $curl->options['CURLOPT_SSL_VERIFYPEER'] = false;

        $response = json_decode($curl->get('http://maps.google.com/maps/api/geocode/json?sensor=false&address='.urlencode($this->data['address'])), TRUE);
        if (is_array($response) && array_key_exists('results', $response) && !empty($response['results'])) {
            $arr = current($response['results']);

            return ['status' => 1, 'location' => $arr['geometry']['location']];
        }

        return ['status' => 0, 'error' => trans('front.address_not_found')];
    }

    private function wpe_radians($deg) {
      return $deg * M_PI / 180;
    }

    private function getRhumbLineBearing($lat1, $lon1, $lat2, $lon2) {
      //difference in longitudinal coordinates
      $dLon = deg2rad($lon2) - deg2rad($lon1);

      //difference in the phi of latitudinal coordinates
      $dPhi = log(tan(deg2rad($lat2) / 2 + pi() / 4) / tan(deg2rad($lat1) / 2 + pi() / 4));

      //we need to recalculate $dLon if it is greater than pi
      if(abs($dLon) > pi()) {
        if($dLon > 0) {
            $dLon = (2 * pi() - $dLon) * -1;
          }
        else {
          $dLon = 2 * pi() + $dLon;
        }
      }
      //return the angle, normalized
      return (rad2deg(atan2($dLon, $dPhi)) + 360) % 360;
    }

    private function getCompassDirection( $bearing )
    {
      static $cardinals = array( 'N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW', 'N' );
      return $cardinals[round( $bearing / 45 )];
    }

    public function findAssetsVehicle(Request $request) {
      $vehicle = $request->findAsset_vehicle;

      $vehicle_data = explode('|', $vehicle);

      $radius = $request->findAsset_vehicle_distance;

      $vehicles = User::with(['devices' => function ($query) use ($vehicle_data) {
        $query->where('id', '!=', $vehicle_data[0]);
      }])->where('id', Auth::user()->id)->first();
      $lat1 = $vehicle_data[2];
      $lng1 = $vehicle_data[3];

      $query_name = $vehicle_data[1];
      $resultArray = [];

      foreach($vehicles->devices as $device) {
        $lat2 = $device->traccar->lastValidLatitude;
        $lng2 = $device->traccar->lastValidLongitude;
        $distance = 3959 * acos(cos($this->wpe_radians($lat1)) * cos($this->wpe_radians($lat2)) * cos($this->wpe_radians($lng2) - $this->wpe_radians($lng1)) + sin($this->wpe_radians($lat1)) * sin($this->wpe_radians($lat2)));
        if ($distance < $radius){
          $bearing = $this->getRhumbLineBearing($lat1, $lng1, $lat2, $lng2);
          $direction = $this->getCompassDirection( $bearing );
            $resultArray[] =  array( 'name' => $device->name , 'location' => ['lat' => $device->traccar->lastValidLatitude , 'lng' => $device->traccar->lastValidLongitude] , 'icon' => $device->icon, 'distance' => $distance, 'distance_text' => $this->getDistanceUnit($distance) . ' from ' .  $query_name, 'direction_text' => $bearing . '&deg; ' . $direction . ' from ' . $query_name);
        }

      }
      if(empty($resultArray)) {
        return response()->json([
          'message' => 'No vehicle found',
        ], 404);
      }

      $resultArrayCol = collect($resultArray)->sortBy('distance');

      $resultSorted = $resultArrayCol->values()->all();


      return $resultSorted;

    }
    private function getDistanceUnit($distance) {
      if(Auth::user()->unit_of_distance == "mi") {
        return round($distance) . 'mi';
      }
      return round($distance * 1.60934) . 'km';
    }

    public function findAssetsPoi(Request $request) {
      $poi = $request->findAsset_poi;

      $poi_data = explode('|', $poi);

      $coordinates = json_decode($poi_data[0]);
      $radius = $request->findAsset_poi_distance;

      $vehicles = User::with('devices')->where('id', Auth::user()->id)->first();
      $lat1 = $coordinates->lat;
      $lng1 = $coordinates->lng;

      $query_name = $poi_data[1];
      $resultArray = [];


      foreach($vehicles->devices as $device) {

        $lat2 = $device->traccar->lastValidLatitude;
        $lng2 = $device->traccar->lastValidLongitude;
        $distance = 3959 * acos(cos($this->wpe_radians($lat1)) * cos($this->wpe_radians($lat2)) * cos($this->wpe_radians($lng2) - $this->wpe_radians($lng1)) + sin($this->wpe_radians($lat1)) * sin($this->wpe_radians($lat2)));
        if ($distance < $radius){
          $bearing = $this->getRhumbLineBearing($lat1, $lng1, $lat2, $lng2);
          $direction = $this->getCompassDirection( $bearing );
            $resultArray[] =  array( 'name' => $device->name , 'location' => ['lat' => $device->traccar->lastValidLatitude , 'lng' => $device->traccar->lastValidLongitude] , 'icon' => $device->icon, 'distance' => $distance, 'distance_text' => $this->getDistanceUnit($distance) . ' from ' .  $query_name . ' POI', 'direction_text' => $bearing . '&deg; ' . $direction . ' from ' . $query_name . ' POI');
        }
      }
      if(empty($resultArray)) {
        return response()->json([
          'message' => 'No vehicle found',
        ], 404);
      }
      $resultArrayCol = collect($resultArray)->sortBy('distance');

      $resultSorted = $resultArrayCol->values()->all();


      return $resultSorted;

    }
}
