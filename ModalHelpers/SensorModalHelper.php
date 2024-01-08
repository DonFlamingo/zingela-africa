<?php namespace ModalHelpers;

use Facades\Repositories\DeviceRepo;
use Facades\Repositories\DeviceSensorRepo;
use Facades\Repositories\EventCustomRepo;
use Facades\Validators\SensorFormValidator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tobuli\Exceptions\ValidationException;
use Validator;

class SensorModalHelper extends ModalHelper {

    public function paginated($device_id) {
        $sensors = DeviceSensorRepo::searchAndPaginate(['filter' => ['device_id' => $device_id]], 'id', 'desc', 10);
        $sensors_arr = Config::get('tobuli.sensors');

        foreach ($sensors as &$sensor)
            $sensor->type_title = $sensors_arr[$sensor->type];

        if ($this->api) {
            $sensors = $sensors->toArray();
            $sensors['url'] = route('api.get_sensors');
        }

        return $sensors;
    }

    public function createData($device_id) {
        $sensors = Config::get('tobuli.sensors');
        ksort($sensors);
        if (!is_null($device_id)) {
            $device = DeviceRepo::find($device_id);
            $params = json_decode($device->parameters, true);
            $params = is_null($params) ? [] : $params;
            $parameters = array_combine($params, $params);
        }
        else {
            $parameters = null;
        }

        return compact('sensors', 'device_id', 'parameters');
    }

    public function create() {
        try
        {
            $this->validate('create', null);

            $arr = $this->formatInput();

            DeviceSensorRepo::create($arr);

            return ['status' => 1];
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

    public function editData() {
        if (array_key_exists('sensor_id', $this->data))
            $sensor_id = $this->data['sensor_id'];
        else
            $sensor_id = request()->route('sensors');

        $item = DeviceSensorRepo::find($sensor_id);
        $device = DeviceRepo::find($item->device_id);
        if (empty($item) || (!$device->users->contains($this->user->id) && !isAdmin()))
            return $this->api ? ['status' => 0, 'errors' => ['id' => dontExist('front.sensor')]] : modal(dontExist('front.sensor'), 'danger');

        $data = $this->createData($item->device_id);

        $item->setflag = FALSE;
        if ($item->type == 'acc') {
            preg_match('/\%SETFLAG\[([0-9]+)\,([0-9]+)\,([\s\S]+)\]\%/', $item->on_value, $match);
            if (isset($match['1']) && isset($match['2']) && isset($match['3'])) {
                $item->setflag = TRUE;
                $item->on_setflag_1 = $match['1'];
                $item->on_setflag_2 = $match['2'];
                $item->on_setflag_3 = $match['3'];
            }
            preg_match('/\%SETFLAG\[([0-9]+)\,([0-9]+)\,([\s\S]+)\]\%/', $item->off_value, $match);
            if (isset($match['1']) && isset($match['2']) && isset($match['3'])) {
                $item->setflag = TRUE;
                $item->off_setflag_1 = $match['1'];
                $item->off_setflag_2 = $match['2'];
                $item->off_setflag_3 = $match['3'];
            }
        }

        if (in_array($item->type, ['ignition', 'door', 'engine', 'drive_business', 'drive_private'])) {
            preg_match('/\%SETFLAG\[([0-9]+)\,([0-9]+)\,([\s\S]+)\]\%/', $item->on_tag_value, $match);
            if (isset($match['1']) && isset($match['2']) && isset($match['3'])) {
                $item->setflag = TRUE;
                $item->on_tag_setflag_1 = $match['1'];
                $item->on_tag_setflag_2 = $match['2'];
                $item->on_tag_setflag_3 = $match['3'];
            }
            preg_match('/\%SETFLAG\[([0-9]+)\,([0-9]+)\,([\s\S]+)\]\%/', $item->off_tag_value, $match);
            if (isset($match['1']) && isset($match['2']) && isset($match['3'])) {
                $item->setflag = TRUE;
                $item->off_tag_setflag_1 = $match['1'];
                $item->off_tag_setflag_2 = $match['2'];
                $item->off_tag_setflag_3 = $match['3'];
            }
        }
        if ($item->type == 'harsh_acceleration' || $item->type == 'harsh_breaking') {
            preg_match('/\%SETFLAG\[([0-9]+)\,([0-9]+)\]\%/', $item->on_value, $match);
            if (isset($match['1']) && isset($match['2'])) {
                $item->setflag = TRUE;
                $item->value_setflag_1 = $match['1'];
                $item->value_setflag_2 = $match['2'];
            }
        }

        $data['item'] = $item;

        return $data;
    }

    public function edit() {
        $item = DeviceSensorRepo::find($this->data['id']);
        $device = DeviceRepo::find($item->device_id);

        try
        {
            if (empty($item) || (!$device->users->contains($this->user->id) && !isAdmin()))
                throw new ValidationException(['id' => dontExist('front.sensor')]);

            $this->validate('update', $item);

            $arr = $this->formatInput();

            if ($this->data['sensor_type'] == 'odometer' && $this->data['odometer_value_by'] == 'connected_odometer' && $item->value > 0)
                $arr['value_formula'] = solveEquation($item->value, $this->data['formula']);

            DeviceSensorRepo::update($item->id, $arr);

            return ['status' => 1];
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

    public function destroy() {
        if (array_key_exists('sensor_id', $this->data))
            $sensor_id = $this->data['sensor_id'];
        else
            $sensor_id = request()->id;

        $item = DeviceSensorRepo::find($sensor_id);
        if (empty($item))
            return ['status' => 0, 'errors' => ['id' => dontExist('front.sensor')]];
        $device = DeviceRepo::find($item->device_id);
        if (empty($item) || (!$device->users->contains($this->user->id) && !isAdmin()))
            return ['status' => 0, 'errors' => ['id' => dontExist('front.sensor')]];

        DeviceSensorRepo::delete($item->id);

        $table_name = 'engine_hours_'.$item->device_id;
        if (Schema::connection('engine_hours_mysql')->hasTable($table_name)) {
            DB::connection('engine_hours_mysql')->table($table_name)->where('sensor_id', '=', $item->id)->delete();
        }

        return ['status' => 1];
    }

    public function getProtocols() {
        if (!$this->api) {
            $devices = isset($this->data['devices']) ? $this->data['devices'] : [];
            $protocols = DeviceRepo::getProtocols($devices)->lists('protocol', 'protocol')->all();
            $protocols = ['-' => '- '.trans('validation.attributes.protocol').' -'] + EventCustomRepo::getProtocols($this->data['type'] == '1' ? $this->user->id : NULL, $protocols)->lists('protocol', 'protocol')->all();
        }
        else {
			//$user_id = $this->data['user_id'];
			$devices = isset($this->data['devices']) ? $this->data['devices'] : [];
			$protocols = DeviceRepo::getProtocols($devices)->lists('protocol', 'protocol')->all();
            $protocols = [
                [
                    'type' => 1,
                    'items' => apiArray(EventCustomRepo::getProtocols($this->user->id, $protocols)->lists('protocol', 'protocol')->all())
                ],
                [
                    'type' => 2,
                    'items' => apiArray(EventCustomRepo::getProtocols(NULL, $protocols)->lists('protocol', 'protocol')->all())
                ],
            ];
        }

        return $protocols;
    }
	
    public function getEvents() {
        $protocol = $this->data['protocol'];
        $where['user_id'] = ($this->data['type'] == '1' ? $this->user->id : NULL);
        if (!empty($protocol) || $protocol != '-')
            $where['protocol'] = $protocol;

        $items = EventCustomRepo::getWhere($where)->lists('message', 'id')->all();
        if ($this->api)
            $items = apiArray($items);

        return $items;
    }

    public function validate($type, $item = NULL) {
        if (empty($this->data['sensor_type']))
            throw new ValidationException(['sensor_type' => str_replace(':attribute', trans('validation.attributes.sensor_type'), trans('validation.required'))]);

        if ($this->data['sensor_type'] == 'harsh_acceleration' || $this->data['sensor_type'] == 'harsh_breaking')
            $this->data['on_value'] = $this->data['parameter_value'];

        $setflag = isset($this->data['setflag']) && $this->data['setflag'] == 1 ? TRUE : FALSE;
        if ($this->data['sensor_type'] != 'acc' && $this->data['sensor_type'] != 'harsh_acceleration' && $this->data['sensor_type'] != 'harsh_breaking' && $this->data['sensor_type'] != 'ignition' && $this->data['sensor_type'] != 'door' && $this->data['sensor_type'] != 'engine')
            $setflag = FALSE;

        SensorFormValidator::validate($this->data['sensor_type'].($setflag ? '_setflag' : ''), $this->data, null, [
            'off_setflag_1' => trans('validation.attributes.on_setflag_1'),
            'off_setflag_2' => trans('validation.attributes.on_setflag_2'),
            'off_setflag_3' => trans('validation.attributes.on_setflag_3'),
            'value_setflag_1' => trans('validation.attributes.on_setflag_1'),
            'value_setflag_2' => trans('validation.attributes.on_setflag_2'),
            'on_tag_setflag_1' => trans('validation.attributes.on_setflag_1'),
            'on_tag_setflag_2' => trans('validation.attributes.on_setflag_2'),
            'on_tag_setflag_3' => trans('validation.attributes.on_setflag_3'),
            'off_tag_setflag_1' => trans('validation.attributes.on_setflag_1'),
            'off_tag_setflag_2' => trans('validation.attributes.on_setflag_2'),
            'off_tag_setflag_3' => trans('validation.attributes.on_setflag_3'),
        ]);

        if (!empty($this->data['device_id']) && ($this->data['sensor_type'] == 'odometer' || $this->data['sensor_type'] == 'acc' || $this->data['sensor_type'] == 'engine' || $this->data['sensor_type'] == 'ignition' || $this->data['sensor_type'] == 'engine_hours')) {
            $sensors_nr = count(DeviceSensorRepo::findWhere([
                'device_id' => $this->data['device_id'],
                'type' => $this->data['sensor_type']
            ]));

            if ($type == 'update' && $item['type'] == $this->data['sensor_type'])
                $sensors_nr--;

            if ($sensors_nr)
                throw new ValidationException(['sensor_type' => trans('front.already_has_sensor')]);
        }

        if ($this->data['sensor_type'] == 'odometer') {
            if ($this->data['odometer_value_by'] == 'virtual_odometer' && empty($this->data['odometer_value']))
                throw new ValidationException(['odometer_value' => str_replace(':attribute', trans('validation.attributes.odometer_value'), trans('validation.required'))]);
            if ($this->data['odometer_value_by'] == 'connected_odometer' && empty($this->data['tag_name']))
                throw new ValidationException(['tag_name' => str_replace(':attribute', trans('validation.attributes.tag_name'), trans('validation.required'))]);
            if ($this->data['odometer_value_by'] == 'connected_odometer' && empty($this->data['formula']))
                throw new ValidationException(['formula' => str_replace(':attribute', trans('validation.attributes.formula'), trans('validation.required'))]);
        }

        if ($this->data['sensor_type'] == 'battery') {
            if ($this->data['shown_value_by'] == 'min_max_values') {
                if ($this->data['min_value'] == '')
                    throw new ValidationException(['min_value' => str_replace(':attribute', trans('validation.attributes.min_value'), trans('validation.required'))]);
                if ($this->data['max_value'] == '')
                    throw new ValidationException(['max_value' => str_replace(':attribute', trans('validation.attributes.max_value'), trans('validation.required'))]);
            }
            if ($this->data['shown_value_by'] == 'formula' && empty($this->data['formula']))
                throw new ValidationException(['formula' => str_replace(':attribute', trans('validation.attributes.formula'), trans('validation.required'))]);
        }

        if ($this->data['sensor_type'] == 'fuel_tank_calibration') {
            if (count($this->data['calibrations']) < 2)
                throw new ValidationException(['calibrations' => trans('front.calibrations_min_items')]);
        }
    }

    public function formatInput() {
        $setflag = isset($this->data['setflag']) && $this->data['setflag'] == 1 ? TRUE : FALSE;
        if ($this->data['sensor_type'] == 'fuel_tank_calibration') {
            asort($this->data['calibrations']);
            foreach ($this->data['calibrations'] as $key => $value) {
                if (!is_numeric($key) || !is_numeric($value))
                    unset($this->data['calibrations'][$key]);
            }
        }

        $type = $this->data['sensor_type'];
        $arr = [
            'user_id' => $this->user->id,
            'device_id' => $this->data['device_id'],
            'name' => $this->data['sensor_name'],
            'type' => $type,
            'tag_name' => NULL,
            'add_to_history' => 0,
            'on_value' => NULL,
            'off_value' => NULL,
            'shown_value_by' => NULL,
            'fuel_tank_name' => NULL,
            'full_tank' => NULL,
            'full_tank_value' => NULL,
            'min_value' => NULL,
            'max_value' => NULL,
            'formula' => NULL,
            'odometer_value_by' => NULL,
            'odometer_value' => NULL,
            'odometer_value_unit' => 'km',
            'show_in_popup' => isset($this->data['show_in_popup']),
            'unit_of_measurement' => $this->data['unit_of_measurement'],
            'calibrations' => NULL,
        ];
        if ($type == 'harsh_acceleration' || $type == 'harsh_breaking') {
            $input_arr = [
                'tag_name' => '',
                'on_value' => '',
                'parameter_value' => '',
                'add_to_history' => '',
            ];

            if ($setflag)
                $this->data['parameter_value'] = "%SETFLAG[".$this->data['value_setflag_1'].",".$this->data['value_setflag_2']."]%";
        }
        if ($type == 'acc') {
            $input_arr = [
                'tag_name' => '',
                'on_value' => '',
                'off_value' => '',
                'add_to_history' => '',
            ];

            if ($setflag) {
                $this->data['on_value'] = "%SETFLAG[".$this->data['on_setflag_1'].",".$this->data['on_setflag_2'].",".$this->data['on_setflag_3']."]%";
                $this->data['off_value'] = "%SETFLAG[".$this->data['off_setflag_1'].",".$this->data['off_setflag_2'].",".$this->data['off_setflag_3']."]%";
            }
        }
        elseif ($type == 'battery') {
            $input_arr = [
                'tag_name' => '',
                'shown_value_by' => '',
                'add_to_history' => '',
            ];
            if ($this->data['shown_value_by'] == 'min_max_values') {
                $input_arr['min_value'] = '';
                $input_arr['max_value'] = '';
            }
            elseif ($this->data['shown_value_by'] == 'formula') {
                $input_arr['formula'] = '';
            }
        }
        elseif ($type == 'fuel_tank') {
            $input_arr = [
                'tag_name' => '',
                'fuel_tank_name' => '',
                'full_tank' => '',
                'full_tank_value' => '',
                'add_to_history' => '',
            ];
        }
        elseif ($type == 'fuel_tank_calibration') {
            $input_arr = [
                'tag_name' => '',
                'fuel_tank_name' => '',
                'calibrations' => '',
                'add_to_history' => '',
            ];
        }
        elseif ($type == 'gsm') {
            $input_arr = [
                'tag_name' => '',
                'min_value' => '',
                'max_value' => '',
                'add_to_history' => '',
            ];
        }
        elseif ($type == 'odometer') {
            $input_arr = [
                'odometer_value_by' => '',
                'add_to_history' => '',
            ];
            if ($this->data['odometer_value_by'] == 'connected_odometer') {
                $input_arr['tag_name'] = '';
                $input_arr['formula'] = '';
            }
            elseif ($this->data['odometer_value_by'] == 'virtual_odometer') {
                $input_arr['odometer_value'] = '';
                $input_arr['odometer_value_unit'] = '';
                if ($this->data['odometer_value_unit'] == 'mi')
                    $this->data['odometer_value'] = milesToKilometers($this->data['odometer_value']);
            }
        }
        elseif ($type == 'satellites') {
            $input_arr = [
                'tag_name' => '',
                'add_to_history' => '',
            ];
        }
        elseif ($type == 'tachometer') {
            $input_arr = [
                'tag_name' => '',
                'formula' => '',
                'add_to_history' => '',
            ];
        }
        elseif ($type == 'temperature') {
            $input_arr = [
                'tag_name' => '',
                'formula' => '',
                'add_to_history' => '',
            ];
        }
        elseif ($type == 'engine_hours') {
            $input_arr = [
                'tag_name' => '',
                'add_to_history' => '',
            ];
        }

        elseif (in_array($type, ['ignition', 'door', 'engine', 'drive_business', 'drive_private'])) {
            $input_arr = [
                'tag_name' => '',
                'on_tag_value' => '',
                'off_tag_value' => '',
                'on_type' => '',
                'off_type' => '',
                'add_to_history' => '',
            ];

            if ($setflag) {
                $this->data['on_tag_value'] = "%SETFLAG[".$this->data['on_tag_setflag_1'].",".$this->data['on_tag_setflag_2'].",".$this->data['on_tag_setflag_3']."]%";
                $this->data['off_tag_value'] = "%SETFLAG[".$this->data['off_tag_setflag_1'].",".$this->data['off_tag_setflag_2'].",".$this->data['off_tag_setflag_3']."]%";
            }
        }

        $this->data['tag_name'] = trim($this->data['tag_name']);

        return array_merge($arr, array_intersect_key($this->data, $input_arr));
    }
    public function getSensors(){
        try{
            $valid =  Validator::make($this->data, [
                 'device_id' => 'required|exists:devices,id'
                 ]);
             if($valid->fails()){
                 return ['status'=> 0 , 'message' => $valid->errors()->first()];
             }else{
                 $device = DeviceSensorRepo::find(['traccar', 'users', 'sensors'], ['id' => $this->data['device_id']])->toArray();
                 print_r($device);
                return ['status'=> '1' , 'device' => $device];
                 //echo(json_encode(array("status" => '1' , 'geofence' => array('id' => $item['id'] ,'user_id' => $item['user_id'] , 'active' => $item['active'] , 'name' => $item['name'] ,'coordinates'=> json_decode($item['coordinates'] , true) , 'polygon_color'=> $item['polygon_color'] )))); die;
             }
         }catch(\Exception $e){
             return ['status' => '0' , 'message' => $e->getMessage()];
         }
    }
}
