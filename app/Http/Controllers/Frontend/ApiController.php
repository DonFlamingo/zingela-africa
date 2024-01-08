<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Request;
use Facades\ModalHelpers\DeviceModalHelper;
use Facades\Repositories\DeviceGroupRepo;
use Facades\Repositories\SmsEventQueueRepo;
use Facades\Repositories\UserDriverRepo;
use Facades\Repositories\UserRepo;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Mockery\CountValidator\Exception;
use Tobuli\Entities\Device;
use Validator;
use DB;


class ApiController extends Controller
{
    // public function login()
    // {
    //     $validator = Validator::make(request()->all(), [
    //         'password' => 'required',
    //     ]);

    //     if ($validator->fails())
    //         return response()->json(['status' => 0, 'errors' => $validator->errors()], 422);

    //     if (request()->has('phone') && Auth::attempt(['phone' => $this->data['phone'], 'password' => $this->data['password']], ['active' => '1'])) {
    //         if (empty(Auth::User()->api_hash)) {
    //             while (!empty(UserRepo::findWhere(['api_hash' => $hash = Hash::make(Auth::User()->email . ':' . $this->data['password'])]))) ;
    //             Auth::User()->api_hash = $hash;
    //             Auth::User()->save();
    //         }
    //         return ['status' => 1, 'user_api_hash' => Auth::User()->api_hash,"data"=>Auth::user()];

    //     }

    //     if (request()->has('email') && Auth::attempt(['email' => $this->data['email'], 'password' => $this->data['password']], ['active' => '1'])) {
    //         if (empty(Auth::User()->api_hash)) {
    //             while (!empty(UserRepo::findWhere(['api_hash' => $hash = Hash::make(Auth::User()->email . ':' . $this->data['password'])]))) ;
    //             Auth::User()->api_hash = $hash;
    //             Auth::User()->save();
    //         }
    //         return ['status' => 1, 'user_api_hash' => Auth::User()->api_hash,'user'=>Auth::user()];
    //     }

    //     return response()->json(['status' => 0, 'message' => trans('front.login_failed')], 401);
    // }

    public function login()
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails())
            return response()->json(['status' => 0, 'errors' => $validator->errors()], 422);
        if($this->data['login_device_id'] == null){
            return response([
                "message_err" => "Device Id is required"
            ],422);
        }
    
        if($this->data['email'] != "apptest@trackit.com"){
            if(DB::table("users")->where("email",$this->data['email'])->value('login_device_id'))
            {
                if(DB::table("users")->where("email",$this->data['email'])->value('login_device_id') != $this->data['login_device_id']){
                    return response([
                        "message_error" => "please logout from another device"
                    ],403);
                }
            }
        }

        if (Auth::attempt(['email' => $this->data['email'], 'password' => $this->data['password']], ['active' => '1'])) {
            if (empty(Auth::User()->api_hash)) {
                while (!empty(UserRepo::findWhere(['api_hash' => $hash = Hash::make(Auth::User()->email . ':' . $this->data['password'])]))) ;
                Auth::User()->api_hash = $hash;
                Auth::User()->save();
            }
            DB::table("users")->where("id",Auth::user()->id)->update([
                "login_device_id" => $this->data['login_device_id']
            ]);
            return ['status' => 1, 'user_api_hash' => Auth::User()->api_hash,"user" => Auth::user()];
        }

        return response()->json(['status' => 0, 'message' => trans('front.login_failed')], 401);
    }

    public function logout(){
        $id = Auth::user()->id;
        try{
            DB::table("users")->where("id",$id)->update([
                "login_device_id" => null
            ]);
            Auth::logout();
            return response([
                "message" => "Logged Out Succesfully"
            ]);
        }catch(Exception $e){
            return response([
                "message_err" => "Something went wrong"
            ],500);
        }
    }


    public function getSmsEvents()
    {
        UserRepo::updateWhere(['id' => $this->user->id], ['sms_gateway_app_date' => date('Y-m-d H:i:s')]);
        $items = SmsEventQueueRepo::getWhereSelect(['user_id' => $this->user->id], ['id', 'phone', 'message'], 'created_at')->toArray();


        if (!empty($items))
            SmsEventQueueRepo::deleteWhereIn(array_pluck($items, 'id'));

        return [
            'status' => 1,
            'items' => $items
        ];
    }

    #
    # Devices
    #

    public function getDevices(\ModalHelpers\DeviceModalHelper $deviceModalHelper)
    {
        $devices = UserRepo::getDevices($this->user->id);
        $device_groups = ['0' => trans('front.ungrouped')] + DeviceGroupRepo::getWhere(['user_id' => $this->user->id])->lists('title', 'id')->all();

        $userDrivers = UserDriverRepo::getWhere([
            'user_id' => $this->user->id
        ])->lists('name', 'id')->all();

        $grouped = [];

        foreach ($devices as $device) {
            $group_id = empty($device->pivot->group_id) ? 0 : $device->pivot->group_id;
            if (!isset($grouped[$group_id])) {
                $grouped[$group_id] = [
                    'title' => $device_groups[$group_id],
                    'items' => []
                ];
            }

            $device = json_decode(json_encode($device), TRUE);

            $device_sensor = null;
            if (!empty($device['sensors'])) {
                foreach ($device['sensors'] as $sensor) {
                    if (!in_array($sensor['type'], ['acc', 'engine', 'ignition'])) continue;

                    $device_sensor = $sensor;
                    break;
                }
            }

            $device = array_merge($device, [
                'active' => $device['pivot']['active'],
                'user_id' => $device['pivot']['user_id'],
                'group_id' => $device['pivot']['group_id'],
                'current_driver_id' => $device['pivot']['current_driver_id'],
                'timezone_id' => $device['pivot']['timezone_id'],

                'icon_type' => isset($device['icon']['type']) ? $device['icon']['type'] : NULL,

                'sensor_type' => isset($device_sensor['type']) ? $device_sensor['type'] : NULL,
                'sensor_tag_name' => isset($device_sensor['tag_name']) ? $device_sensor['tag_name'] : NULL,
                'sensor_on_value' => isset($device_sensor['on_value']) ? $device_sensor['on_value'] : NULL,
                'sensor_off_value' => isset($device_sensor['off_value']) ? $device_sensor['off_value'] : NULL,
                'sensor_on_tag_value' => isset($device_sensor['on_tag_value']) ? $device_sensor['on_tag_value'] : NULL,
                'sensor_off_tag_value' => isset($device_sensor['off_tag_value']) ? $device_sensor['off_tag_value'] : NULL,
                'sensor_value' => isset($device_sensor['value']) ? $device_sensor['value'] : NULL,
                'sensor_on_type' => isset($device_sensor['on_type']) ? $device_sensor['on_type'] : NULL,
                'sensor_off_type' => isset($device_sensor['off_type']) ? $device_sensor['off_type'] : NULL,

                'other' => isset($device['traccar']['other']) ? $device['traccar']['other'] : NULL,
                'time' => isset($device['traccar']['time']) ? $device['traccar']['time'] : NULL,
                'server_time' => isset($device['traccar']['server_time']) ? $device['traccar']['server_time'] : NULL,
                'ack_time' => isset($device['traccar']['ack_time']) ? $device['traccar']['ack_time'] : NULL,
                'speed' => isset($device['traccar']['speed']) ? $device['traccar']['speed'] : NULL,
                'altitude' => isset($device['traccar']['altitude']) ? $device['traccar']['altitude'] : NULL,
                'latest_positions' => isset($device['traccar']['latest_positions']) ? $device['traccar']['latest_positions'] : NULL,
                'lastValidLatitude' => isset($device['traccar']['lastValidLatitude']) ? $device['traccar']['lastValidLatitude'] : NULL,
                'lastValidLongitude' => isset($device['traccar']['lastValidLongitude']) ? $device['traccar']['lastValidLongitude'] : NULL,
                'course' => isset($device['traccar']['course']) ? $device['traccar']['course'] : NULL,
                'power' => isset($device['traccar']['power']) ? $device['traccar']['power'] : NULL,
                'protocol' => isset($device['traccar']['protocol']) ? $device['traccar']['protocol'] : NULL,
            ]);

            $deviceModalHelper->generateJson($grouped[$group_id]['items'], $device, $userDrivers, FALSE, TRUE);
        }

        unset($devices);

        $grouped = array_values($grouped);

        if (!$this->user->perm('devices', 'view'))
            $grouped = [];

        return $grouped;
    }

    public function getSingleDevice(\ModalHelpers\DeviceModalHelper $deviceModalHelper)
    {
        try {
            $valid = Validator::make($this->data, [
                'device_id' => 'required|exists:devices,id'
            ]);
            if ($valid->fails()) {
                return ['status' => 0, 'message' => $valid->errors()->first()];
            } else {
                $devices = UserRepo::getDevices($this->user->id);

                $devices = json_decode($devices);
                $device_groups = ['0' => trans('front.ungrouped')] + DeviceGroupRepo::getWhere(['user_id' => $this->user->id])->lists('title', 'id')->all();

                $userDrivers = UserDriverRepo::getWhere([
                    'user_id' => $this->user->id
                ])->lists('name', 'id')->all();

                $grouped = [];

                foreach ($devices as $device) {
                    if ($this->data['device_id'] == $device->id) {
                        $group_id = empty($device->pivot->group_id) ? 0 : $device->pivot->group_id;
                        if (!isset($grouped[$group_id])) {
                            $grouped[$group_id] = [
                                'title' => $device_groups[$group_id],
                                'items' => []
                            ];
                        }

                        $device = json_decode(json_encode($device), TRUE);

                        $device_sensor = null;
                        if (!empty($device['sensors'])) {
                            foreach ($device['sensors'] as $sensor) {
                                if (!in_array($sensor['type'], ['acc', 'engine', 'ignition'])) continue;

                                $device_sensor = $sensor;
                                break;
                            }
                        }

                        $device = array_merge($device, [
                            'active' => $device['pivot']['active'],
                            'user_id' => $device['pivot']['user_id'],
                            'group_id' => $device['pivot']['group_id'],
                            'current_driver_id' => $device['pivot']['current_driver_id'],
                            'timezone_id' => $device['pivot']['timezone_id'],

                            'icon_type' => isset($device['icon']['type']) ? $device['icon']['type'] : NULL,

                            'sensor_type' => isset($device_sensor['type']) ? $device_sensor['type'] : NULL,
                            'sensor_tag_name' => isset($device_sensor['tag_name']) ? $device_sensor['tag_name'] : NULL,
                            'sensor_on_value' => isset($device_sensor['on_value']) ? $device_sensor['on_value'] : NULL,
                            'sensor_off_value' => isset($device_sensor['off_value']) ? $device_sensor['off_value'] : NULL,
                            'sensor_on_tag_value' => isset($device_sensor['on_tag_value']) ? $device_sensor['on_tag_value'] : NULL,
                            'sensor_off_tag_value' => isset($device_sensor['off_tag_value']) ? $device_sensor['off_tag_value'] : NULL,
                            'sensor_value' => isset($device_sensor['value']) ? $device_sensor['value'] : NULL,
                            'sensor_on_type' => isset($device_sensor['on_type']) ? $device_sensor['on_type'] : NULL,
                            'sensor_off_type' => isset($device_sensor['off_type']) ? $device_sensor['off_type'] : NULL,

                            'other' => isset($device['traccar']['other']) ? $device['traccar']['other'] : NULL,
                            'time' => isset($device['traccar']['time']) ? $device['traccar']['time'] : NULL,
                            'server_time' => isset($device['traccar']['server_time']) ? $device['traccar']['server_time'] : NULL,
                            'ack_time' => isset($device['traccar']['ack_time']) ? $device['traccar']['ack_time'] : NULL,
                            'speed' => isset($device['traccar']['speed']) ? $device['traccar']['speed'] : NULL,
                            'altitude' => isset($device['traccar']['altitude']) ? $device['traccar']['altitude'] : NULL,
                            'latest_positions' => isset($device['traccar']['latest_positions']) ? $device['traccar']['latest_positions'] : NULL,
                            'lastValidLatitude' => isset($device['traccar']['lastValidLatitude']) ? $device['traccar']['lastValidLatitude'] : NULL,
                            'lastValidLongitude' => isset($device['traccar']['lastValidLongitude']) ? $device['traccar']['lastValidLongitude'] : NULL,
                            'course' => isset($device['traccar']['course']) ? $device['traccar']['course'] : NULL,
                            'power' => isset($device['traccar']['power']) ? $device['traccar']['power'] : NULL,
                            'protocol' => isset($device['traccar']['protocol']) ? $device['traccar']['protocol'] : NULL,
                        ]);

                        $deviceModalHelper->generateJson($grouped[$group_id]['items'], $device, $userDrivers, FALSE, TRUE);
                    }
                }

                unset($devices);

                $grouped = array_values($grouped);

                if (!$this->user->perm('devices', 'view'))
                    $grouped = [];
                if (isset($this->data['device_id']) && !empty($this->data['device_id']) && !empty($grouped)) {
                    return ['status' => '1', 'device' => $grouped[0]['items'][0]];
                }

                return $grouped;
            }
        } catch (\Exception $e) {
            return ['status' => '0', 'message' => $e->getMessage()];
        }
    }

    public function updateParkingMode(){

      $device=  Device::where('id',$this->data['device_id']);
      $device->update(['parking_mode' => $this->data['mode']]);
      return response()->json([
          "error"=>false,
          'message'=>"Mode changed successfully"
      ]);
    }
    public function parkingStatus(){

      $device=  Device::where('id',$this->data['device_id']);
      return response()->json([
          "error"=>false,
          'message'=>$device->parking_mode
      ]);
    }

    public function getDevicesJson()
    {
        $data = DeviceModalHelper::itemsJson();

        return $data;
    }


    public function getUserData()
    {
        $dStart = new \DateTime(date('Y-m-d H:i:s'));
        $dEnd = new \DateTime($this->user->subscription_expiration);
        $dDiff = $dStart->diff($dEnd);
        $days_left = $dDiff->days;

        $plan = Config::get('tobuli.plans.' . $this->user->devices_limit);
        if (empty($plan)) {
            $plan = isset($this->user->billing_plan->title) ? $this->user->billing_plan->title : NULL;
            if (empty($plan))
                $plan = trans('admin.group_' . $this->user->group_id);
        }

        return [
            'user' => $this->user,
            'email' => $this->user->email,
            'expiration_date' => $this->user->subscription_expiration != '0000-00-00 00:00:00' ? datetime($this->user->subscription_expiration) : NULL,
            'days_left' => $this->user->subscription_expiration != '0000-00-00 00:00:00' ? $days_left : NULL,
            'plan' => $plan,
            'devices_limit' => intval($this->user->devices_limit)
        ];
    }

    public function setDeviceExpiration()
    {
        if (!isAdmin())
            return response()->json(['status' => 0, 'error' => trans('front.dont_have_permission')], 403);

        $validator = Validator::make(request()->all(), [
            'imei' => 'required',
            'expiration_date' => 'required|date',
        ]);

        if ($validator->fails())
            return response()->json(['status' => 0, 'errors' => $validator->errors()], 400);

        $device = \Tobuli\Entities\Device::where('imei', request()->get('imei'))->first();

        if (!$device)
            return response()->json(['status' => 0, 'errors' => ['imei' => dontExist('global.device')]], 400);

        $device->expiration_date = request()->get('expiration_date');
        $device->save();

        return response()->json(['status' => 1], 200);
    }

    public function geoAddress()
    {
        return getGeoAddress($this->data['lat'], $this->data['lon']);
    }

    public function setFcmToken()
    {

        $validator = Validator::make(request()->all(), ['token' => 'required']);

        if ($validator->fails())
            throw new ValidationException($validator->errors());

        //$token = \Tobuli\Entities\FCMToken::where('user_id', $this->user->id)->first();

        //if (!$token) {
        // $token = $this->data['token'];
        // } else {
        //$token = $token . ',' . $this->data['token'];
        //}
        $old_token = \DB::table("fcm_tokens")->where('token', $this->data['token'])->where('user_id', $this->user->id)->first();
        if (empty($old_token)) {
            \DB::table("fcm_tokens")->insert([
                "user_id" => $this->user->id,
                "token" => $this->data['token'],
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s')
            ]);;
        }

        return response()->json(['status' => 1], 200);
    }

    public function getServicesKeys()
    {
        dd('hello');
        $services = [];

        $services['maps']['google']['key'] = settings('main_settings.google_maps_key');

        return response()->json(['status' => 1, 'items' => $services], 200);
    }

    public function __call($name, $arguments)
    {
        list($class, $method) = explode('#', $name);

        try {
            try {
                $class = App::make("App\Http\Controllers\Frontend\\" . $class);
                $response = App::call([$class, $method]);
            } catch (\ReflectionException $e) {
                return response()->json(['status' => 0, 'message' => 'Method does not exist!'], 500);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Server error: ' . $e->getMessage() . ' (' . $e->getFile() . ':' . $e->getLine() . ')'], 500);
        }


        $status_code = 200;

        return response()->json($response, $status_code);
    }

    public function changePassword(\Illuminate\Http\Request $request){
        $validator = Validator::make(request()->all(), [
            "password" => "required",
            "confirm_password" => "required|same:password"
        ]);

        if ($validator->fails())
            return response()->json(['status' => 0, 'errors' => $validator->errors()], 422);


        $user_id = Auth::user()->id;
        try{
            \DB::table("users")->where("id",$user_id)->update([
                "password" => bcrypt($request->password)
            ]);
            return response()->json(['status' => 1, 'message' => 'Password changed successfully']);
        }catch(\Exception $e){
            return response([
                "message" => $e->getMessage(),
                "status" => $e->getCode()
            ],$e->getCode());
        }
    }
}
