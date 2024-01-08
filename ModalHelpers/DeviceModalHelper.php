<?php namespace ModalHelpers;

use Facades\ModalHelpers\SensorModalHelper;
use Facades\ModalHelpers\ServiceModalHelper;
use Facades\Repositories\DeviceFuelMeasurementRepo;
use Facades\Repositories\DeviceGroupRepo;
use Facades\Repositories\DeviceIconRepo;
use Facades\Repositories\DeviceRepo;
use Facades\Repositories\DeviceSensorRepo;
use Facades\Repositories\EventRepo;
use Facades\Repositories\SensorGroupRepo;
use Facades\Repositories\SensorGroupSensorRepo;
use Facades\Repositories\TimezoneRepo;
use Facades\Repositories\TraccarDeviceRepo;
use Facades\Repositories\TraccarPositionRepo;
use Facades\Repositories\UserDriverRepo;
use Facades\Repositories\UserRepo;
use Facades\Validators\DeviceFormValidator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Helpers\HistoryHelper;
use Illuminate\Support\Facades\Artisan;
use Validator;

class DeviceModalHelper extends ModalHelper
{
    private $device_fuel_measurements = [];

    public function __construct()
    {
        parent::__construct();

        $this->device_fuel_measurements = [
            [
                'id' => 1,
                'title' => trans('front.l_km'),
                'fuel_title' => strtolower(trans('front.liter')),
                'distance_title' => trans('front.kilometers'),
            ],
            [
                'id' => 2,
                'title' => trans('front.mpg'),
                'fuel_title' => strtolower(trans('front.gallon')),
                'distance_title' => trans('front.miles'),
            ]
        ];
    }

    public function stopTime($device_id = NULL)
    {
        if (is_null($device_id))
            $device_id = request()->get('device_id');

        $time = '0'.trans('front.h');
        $device = DeviceRepo::getWithFirst(['traccar', 'users', 'sensors'], ['id' => $device_id]);
        if (empty($device) || (!$device->users->contains($this->user->id) && !isAdmin()))
            return  $time;

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

    public function createData() {
        if (request()->get('perm') == null || (request()->get('perm') != null && request()->get('perm') != 1)) {
            if (request()->get('perm') != null && request()->get('perm') != 2) {
                if (!is_null($res = $this->checkDevicesLimit()))
                    return $res;
            }

            if ($this->api && !$this->user->perm('devices', 'edit'))
                return ['status' => 0, 'perm' => 0];
        }

        $icons_type = [
            'arrow' => trans('front.arrow'),
            'rotating' => trans('front.rotating_icon'),
            'icon' => trans('front.icon')
        ];

        $device_icon_colors = [
            'green'  => trans('front.green'),
            'yellow' => trans('front.yellow'),
            'red'    => trans('front.red'),
            'blue'   => trans('front.blue'),
            'orange' => trans('front.orange'),
            'black'  => trans('front.black'),
        ];
        $device_icons = DeviceIconRepo::all();
        $device_icons_grouped = [];

        foreach ($device_icons as $dicon) {
            if ($dicon['type'] == 'arrow')
                continue;

            if (!array_key_exists($dicon['type'], $device_icons_grouped))
                $device_icons_grouped[$dicon['type']] = [];

            $device_icons_grouped[$dicon['type']][] = $dicon;
        }

        $users = UserRepo::getUsers($this->user);
        $device_groups = ['0' => trans('front.ungrouped')] + DeviceGroupRepo::getWhere(['user_id' => $this->user->id])->lists('title', 'id')->all();
        $expiration_date_select = [
            '0000-00-00' => trans('front.unlimited'),
            '1' => trans('validation.attributes.expiration_date')
        ];
        $timezones = ['0' => trans('front.default')] + TimezoneRepo::order()->lists('title', 'id')->all();
        $timezones_arr = [];
        foreach ($timezones as $key => &$timezone) {
            $timezone = str_replace('UTC ', '', $timezone);
            if ($this->api)
                array_push($timezones_arr, ['id' => $key, 'value' => $timezone]);
        }

        $sensor_groups = [];
        if (isAdmin()) {
            $sensor_groups = SensorGroupRepo::getWhere([], 'title');
            $sensor_groups = $sensor_groups->lists('title', 'id')->all();
        }

        $sensor_groups = ['0' => trans('front.none')] + $sensor_groups;

        $device_fuel_measurements = $this->device_fuel_measurements;

        $device_fuel_measurements_select =  [];
        foreach ($device_fuel_measurements as $dfm)
            $device_fuel_measurements_select[$dfm['id']] = $dfm['title'];

        if ($this->api) {
            $timezones = $timezones_arr;
            $device_groups = apiArray($device_groups);
            $sensor_groups = apiArray($sensor_groups);
            $users = $users->toArray();
        }

        return compact('device_groups', 'sensor_groups', 'device_fuel_measurements', 'device_icons', 'users', 'timezones', 'expiration_date_select', 'device_fuel_measurements_select', 'icons_type', 'device_icons_grouped', 'device_icon_colors');
    }

    public function create() {
        $this->data['imei'] = isset($this->data['imei']) ? trim($this->data['imei']) : null;
        $this->data['group_id'] = !empty($this->data['group_id']) ? $this->data['group_id'] : null;
        $this->data['timezone_id'] = empty($this->data['timezone_id']) ? NULL : $this->data['timezone_id'];
        $this->data['snap_to_road'] = isset($this->data['snap_to_road']);
        
        try
        {
            if (!$this->user->perm('devices', 'edit'))
                throw new ValidationException(['id' => trans('front.dont_have_permission')]);

            if (!is_null($res = $this->checkDevicesLimit()))
                throw new ValidationException(['id' => trans('front.devices_limit_reached')]);

            if (array_key_exists('device_icons_type', $this->data) && $this->data['device_icons_type'] == 'arrow')
                $this->data['icon_id'] = 0;

            DeviceFormValidator::validate('create', $this->data);

            $this->data['fuel_per_km'] = convertFuelConsumption($this->data['fuel_measurement_id'], $this->data['fuel_quantity']);

            $item_ex = DeviceRepo::whereImei($this->data['imei']);
            if (!empty($item_ex) && $item_ex->deleted == 0)
                throw new ValidationException(['imei' => str_replace(':attribute', trans('validation.attributes.imei_device'), trans('validation.unique'))]);

            if (isAdmin()) {
                if (empty($this->data['enable_expiration_date']))
                    $this->data['expiration_date'] = '0000-00-00';
            }
            else
                unset($this->data['expiration_date']);

            beginTransaction();
            try {
                $this->data['user_id'][] = $this->user->id;

                if (isset($this->data['user_id']))
                    $this->data['user_id'] = empty($this->data['user_id']) ? ['0' => $this->user->id] : $this->data['user_id'];






                if (empty($item_ex)) {
                    $traccar_item = TraccarDeviceRepo::create([
                        'name' => $this->data['name'],
                        'uniqueId' => $this->data['imei']
                    ]);

                    if (empty($this->data['fuel_quantity']))
                        $this->data['fuel_quantity'] = 0;

                    if (empty($this->data['fuel_price']))
                        $this->data['fuel_price'] = 0;

                    $this->data['gprs_templates_only'] = (array_key_exists('gprs_templates_only', $this->data) && $this->data['gprs_templates_only'] == 1 ? 1 : 0);

                    $device_icon_colors = [
                        'green'  => trans('front.green'),
                        'yellow' => trans('front.yellow'),
                        'red'    => trans('front.red'),
                        'blue'   => trans('front.blue'),
                        'orange' => trans('front.orange'),
                        'black'  => trans('front.black'),
                    ];

                    $this->data['icon_colors'] = [
                        'moving' => 'green',
                        'stopped' => 'red',
                        'offline' => 'black',
                        'engine' => 'blue',
                        'idle' => 'yellow',
                    ];

                    if (array_key_exists('icon_moving', $this->data) && array_key_exists($this->data['icon_moving'], $device_icon_colors))
                        $this->data['icon_colors']['moving'] = $this->data['icon_moving'];

                    if (array_key_exists('icon_stopped', $this->data) && array_key_exists($this->data['icon_stopped'], $device_icon_colors))
                        $this->data['icon_colors']['stopped'] = $this->data['icon_stopped'];

                    if (array_key_exists('icon_offline', $this->data) && array_key_exists($this->data['icon_offline'], $device_icon_colors))
                        $this->data['icon_colors']['offline'] = $this->data['icon_offline'];

                    if (array_key_exists('icon_engine', $this->data) && array_key_exists($this->data['icon_engine'], $device_icon_colors))
                        $this->data['icon_colors']['engine'] = $this->data['icon_engine'];

                    $device = DeviceRepo::create($this->data + ['traccar_device_id' => $traccar_item->id]);
               
                    $this->deviceSyncUsers($device);
                    $this->createSensors($device->id);

                    $table_name = 'positions_'.$traccar_item->id;
                    if (Schema::connection('traccar_mysql')->hasTable($table_name))
                        throw new ValidationException(['id' => trans('global.cant_create_device_database')]);

                    Schema::connection('traccar_mysql')->create($table_name, function(Blueprint $table)
                    {
                        $table->bigIncrements('id');
                        $table->bigInteger('device_id')->unsigned()->index();
                        $table->double('altitude')->nullable();
                        $table->double('course')->nullable();
                        $table->double('latitude')->nullable();
                        $table->double('longitude')->nullable();
                        $table->text('other')->nullable();
                        $table->double('power')->nullable();
                        $table->double('speed')->nullable()->index();
                        $table->datetime('time')->nullable()->index();
                        $table->datetime('server_time')->nullable()->index();
                        $table->tinyInteger('valid')->nullable();
                        $table->double('distance')->nullable();
                        $table->string('protocol', 20)->nullable();
                    });
					
					$this->addDeviceInTraccar($device);
                }
                else {
                    DeviceRepo::update($item_ex->id, $this->data + ['deleted' => 0]);
                    $device = DeviceRepo::find($item_ex->id);
                    $device->users()->sync($this->data['user_id']);
                }

                DB::connection('traccar_mysql')->table('unregistered_devices_log')->where('imei', '=', $this->data['imei'])->delete();
            }
            catch (\Exception $e) {
                rollbackTransaction();
                throw new ValidationException(['id' => trans('global.unexpected_db_error').$e->getMessage()]);
            }

            commitTransaction();
            return ['status' => 1, 'id' => $device->id,"message" =>"Device Created Successfully"];
        }
        catch (ValidationException $e)
        {
            return response(['status' => 0, 'errors' => $e->getErrors()],422);
        }
    }

    public function editData() {
				
        if (array_key_exists('id', $this->data))
            $device_id = $this->data['id'];
        else
            $device_id = request()->route('id');

        if (empty($device_id))
            $device_id = empty($this->data['device_id']) ? NULL : $this->data['device_id'];

        $item = DeviceRepo::find($device_id);
        if (empty($item) || (!$item->users->contains($this->user->id) && !isAdmin()))
            return $this->api ? ['status' => 0, 'errors' => ['id' => dontExist('global.device')]] : modal(dontExist('global.device'), 'danger');

        $users = UserRepo::getUsers($this->user);

        $sel_users = $item->users->lists('id', 'id')->all();
        $group_id = null;
        $timezone_id = null;
        if ($item->users->contains($this->user->id)) {
            foreach ($item->users as $item_user) {
                if ($item_user->id == $this->user->id) {
                    $group_id = $item_user->pivot->group_id;
                    $timezone_id = $item_user->pivot->timezone_id;
                    break;
                }
            }
        }

        $icons_type = [
            'arrow' => trans('front.arrow'),
            'rotating' => trans('front.rotating_icon'),
            'icon' => trans('front.icon')
        ];

        $device_icon_colors = [
            'green'  => trans('front.green'),
            'yellow' => trans('front.yellow'),
            'red'    => trans('front.red'),
            'blue'   => trans('front.blue'),
            'orange' => trans('front.orange'),
            'black'  => trans('front.black'),
        ];

        $device_icons = DeviceIconRepo::all();
        $device_icons_grouped = [];

        foreach ($device_icons as $dicon) {
            if ($dicon['type'] == 'arrow')
                continue;

            if (!array_key_exists($dicon['type'], $device_icons_grouped))
                $device_icons_grouped[$dicon['type']] = [];

            $device_icons_grouped[$dicon['type']][] = $dicon;
        }

        $device_groups = ['0' => trans('front.ungrouped')] + DeviceGroupRepo::getWhere(['user_id' => $this->user->id])->lists('title', 'id')->all();
        $sensors = SensorModalHelper::paginated($item->id);
        $services = ServiceModalHelper::paginated($item->id);
        $expiration_date_select = [
            '0000-00-00' => trans('front.unlimited'),
            '1' => trans('validation.attributes.expiration_date')
        ];

        $has_sensors = DeviceSensorRepo::getWhereInWhere([
            'odometer',
            'acc',
            'engine',
            'ignition',
            'engine_hours'
        ], 'type', ['device_id' => $item->id]);

        $arr = parseSensorsSelect($has_sensors);
        $engine_hours = $arr['engine_hours'];
        $detect_engine = $arr['detect_engine'];
        unset($item->sensors);

        $timezones = ['0' => trans('front.default')] + TimezoneRepo::order()->lists('title', 'id')->all();
        foreach ($timezones as $key => &$timezone)
            $timezone = str_replace('UTC ', '', $timezone);

        $sensor_groups = [];
        if (isAdmin()) {
            $sensor_groups = SensorGroupRepo::getWhere([], 'title');
            $sensor_groups = $sensor_groups->lists('title', 'id')->all();
        }

        $sensor_groups = ['0' => trans('front.none')] + $sensor_groups;

        $device_fuel_measurements = $this->device_fuel_measurements;

        $device_fuel_measurements_select =  [];
        foreach ($device_fuel_measurements as $dfm)
            $device_fuel_measurements_select[$dfm['id']] = $dfm['title'];

        if ($this->api) {
            $device_groups = apiArray($device_groups);
            $timezones = apiArray($timezones);
            $users = $users->toArray();
        }

        return compact('device_id', 'engine_hours', 'detect_engine', 'device_groups', 'sensor_groups', 'item', 'device_fuel_measurements', 'device_icons', 'sensors', 'services', 'expiration_date_select', 'timezones', 'expiration_date_select', 'users', 'sel_users', 'group_id', 'timezone_id', 'device_fuel_measurements_select', 'icons_type', 'device_icons_grouped', 'device_icon_colors');
    }

    public function edit() {
			
        if (empty($this->data['id']))
            $this->data['id'] = empty($this->data['device_id']) ? NULL : $this->data['device_id'];

        $item = DeviceRepo::find($this->data['id']);

        if (empty($item) || (!$item->users->contains($this->user->id) && !isAdmin()))
            return $this->api ? ['status' => 0, 'errors' => ['id' => dontExist('global.device')]] : modal(dontExist('global.device'), 'danger');

        $this->data['group_id'] = !empty($this->data['group_id']) ? $this->data['group_id'] : null;
        $this->data['snap_to_road'] = isset($this->data['snap_to_road']);
		
        $admin_user = UserRepo::findWhere(['email' => 'admin@atrams.com']);
        $sel_users = $item->users->lists('id', 'id');

        if (isAdmin() && isset($this->data['user_id'])) {

            $this->data['user_id'] = array_combine($this->data['user_id'], $this->data['user_id']);
            $this->data['user_id'][] = $this->user->id;

            if ($this->user->group_id == 3) {
                $users = $this->user->subusers()->lists('id', 'id')->all() + [$this->user->id => $this->user->id];
                foreach ($sel_users as $id) {
                    if (array_key_exists($id, $users) && !array_key_exists($id, $this->data['user_id']))
                        unset($this->data['user_id'][$id]);

                    if (!array_key_exists($id, $users) && !array_key_exists($id, $this->data['user_id']))
                        $this->data['user_id'][$id] = $id;
                }
            }
        }
        else {
            unset($this->data['user_id']);
        }

        if (isAdmin()) {
            if (empty($this->data['enable_expiration_date']))
                $this->data['expiration_date'] = '0000-00-00';
        }
        else
            unset($this->data['expiration_date']);

		
        try
        {
            if (!$this->user->perm('devices', 'edit'))
                throw new ValidationException(['id' => trans('front.dont_have_permission')]);

            if (array_key_exists('device_icons_type', $this->data) && $this->data['device_icons_type'] == 'arrow')
                $this->data['icon_id'] = 0;

            DeviceFormValidator::validate('update', $this->data, $item->id);

            $this->data['fuel_per_km'] = convertFuelConsumption($this->data['fuel_measurement_id'], $this->data['fuel_quantity']);

            beginTransaction();
			
            try {
                $this->data['gprs_templates_only'] = (array_key_exists('gprs_templates_only', $this->data) && $this->data['gprs_templates_only'] == 1 ? 1 : 0);

                $device_icon_colors = [
                    'green'  => trans('front.green'),
                    'yellow' => trans('front.yellow'),
                    'red'    => trans('front.red'),
                    'blue'   => trans('front.blue'),
                    'orange' => trans('front.orange'),
                    'black'  => trans('front.black'),
                ];

                $this->data['icon_colors'] = [
                    'moving' => 'green',
                    'stopped' => 'red',
                    'offline' => 'black',
                    'engine' => 'blue',
                    'idle' => 'yellow',
                ];

                if (array_key_exists('icon_moving', $this->data) && array_key_exists($this->data['icon_moving'], $device_icon_colors))
                    $this->data['icon_colors']['moving'] = $this->data['icon_moving'];

                if (array_key_exists('icon_stopped', $this->data) && array_key_exists($this->data['icon_stopped'], $device_icon_colors))
                    $this->data['icon_colors']['stopped'] = $this->data['icon_stopped'];

                if (array_key_exists('icon_offline', $this->data) && array_key_exists($this->data['icon_offline'], $device_icon_colors))
                    $this->data['icon_colors']['offline'] = $this->data['icon_offline'];

                if (array_key_exists('icon_engine', $this->data) && array_key_exists($this->data['icon_engine'], $device_icon_colors))
                    $this->data['icon_colors']['engine'] = $this->data['icon_engine'];

                DeviceRepo::update($item->id, $this->data);

                TraccarDeviceRepo::update($item->traccar_device_id, [
                    'name' => $this->data['name'],
                    'uniqueId' => $this->data['imei']
                ]);

                DB::connection('traccar_mysql')->table('unregistered_devices_log')->where('imei', '=', $this->data['imei'])->delete();
				
                $this->deviceSyncUsers($item);
                $this->createSensors($item->id);
				
				$this->updateDeviceInTraccar($item);
				
            }
            catch (\Exception $e) {
                rollbackTransaction();
                throw new ValidationException(['id' => trans('global.unexpected_db_error').$e->getMessage()]);
            }
			
            if (isset($this->data['user_id']))
                clearCache($item->imei, 'users');
			
            clearCache($item->imei, 'device');
			
            commitTransaction();		
            return ['status' => 1, 'id' => $item->id];
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

    public function destroy() {
        $device_id = array_key_exists('id', $this->data) ? $this->data['id'] : (empty($this->data['device_id']) ? NULL : $this->data['device_id']);

        $item = DeviceRepo::find($device_id);

        if (empty($item) || (!$item->users->contains($this->user->id) && !isAdmin()))
            return $this->api ? ['status' => 0, 'errors' => ['id' => dontExist('global.device')]] : modal(dontExist('global.device'), 'danger');

        beginTransaction();

        try {
            $item->users()->sync([]);

            DB::connection('traccar_mysql')->table('devices')->where('id', '=', $item->traccar_device_id)->delete();
            EventRepo::deleteWhere(['device_id' => $item->id]);
            DeviceRepo::delete($item->id);

            DB::table('user_device_pivot')->where('device_id', $item->id)->delete();
            DB::table('device_sensors')->where('device_id', $item->id)->delete();
            DB::table('device_services')->where('device_id', $item->id)->delete();
            DB::table('user_drivers')->where('device_id', $item->id)->update(['device_id' => null]);

            if (Schema::connection('traccar_mysql')->hasTable('positions_'.$item->traccar_device_id))
                DB::connection('traccar_mysql')->table('positions_'.$item->traccar_device_id)->truncate();

            if (Schema::connection('sensors_mysql')->hasTable('sensors_'.$item->traccar_device_id))
                DB::connection('sensors_mysql')->table('sensors_'.$item->traccar_device_id)->truncate();

            if (Schema::connection('engine_hours_mysql')->hasTable('engine_hours_'.$item->traccar_device_id))
                DB::connection('engine_hours_mysql')->table('engine_hours_'.$item->traccar_device_id)->truncate();

            Schema::connection('traccar_mysql')->dropIfExists('positions_'.$item->traccar_device_id);
            Schema::connection('sensors_mysql')->dropIfExists('sensors_'.$item->traccar_device_id);
            Schema::connection('engine_hours_mysql')->dropIfExists('engine_hours_'.$item->traccar_device_id);
					
            clearCache($item->imei, ['device', 'alerts', 'users']);
            commitTransaction();
			$this->deleteDeviceInTraccar($item);
        }
        catch (\Exception $e) {
            rollbackTransaction();
        }

        return ['status' => 1, 'id' => $item->id, 'deleted' => 1];
    }

    public function changeActive() {
        if ( isDemoUser() )
            return ['status' => 1];

        $items = [];
        if (!array_key_exists('id', $this->data))
            return ['status' => 0, 'errors' => ['id' => 'No id provided']];

        if (is_array($this->data['id']))
            $items = DeviceRepo::getWhereIn($this->data['id']);
        else
            $items[] = DeviceRepo::find($this->data['id']);

        $update_items = [];
        foreach ($items as $key => $item) {
            if ($item->users->contains($this->user->id) || isAdmin())
                $update_items[$item->id] = $item->id;
        }

        if (!empty($update_items)) {
            DB::table('user_device_pivot')->where([
                'user_id' => $this->user->id
            ])->whereIn('device_id', $update_items)->update(['active' => (isset($this->data['active']) && $this->data['active'] != 'false') ? 1 : 0]);
        }
        return ['status' => 1];
    }

    public function itemsJson()
    {
        if ($this->user->id == 0)
            return $this->itemsDemo();

        $userDrivers = UserDriverRepo::getWhere([
            'user_id' => $this->user->id
        ])->lists('name', 'id')->all();

        $time = time();
        if ( empty($this->data['time']) ) {
            $this->data['time'] = $time - 5;
        }

        $this->data['time'] = intval($this->data['time']);

        $devices = UserRepo::getDevicesHigherTime($this->user->id, $this->data['time']);
        $items = array();
        if (!empty($devices)) {
            foreach ($devices as $item) {
                $this->generateJson($items, $item, $userDrivers, TRUE, TRUE);
            }
        }

        $events = EventRepo::getHigherTime($this->user->id, $this->data['time']);
        !empty($events) && $events = $events->toArray();

       foreach ($events as $key=>$event) {
            $events[$key]['time'] = datetime($event['time'], TRUE);
            $events[$key]['speed'] = round($this->user->unit_of_distance == 'mi' ? kilometersToMiles($event['speed']) : $event['speed']);
            $events[$key]['altitude'] = round($this->user->unit_of_altitude == 'ft' ? metersToFeets($event['altitude']) : $event['altitude']);
            $events[$key]['message'] = parseEventMessage($events[$key]['message'], $events[$key]['type']);
       
            $name = htmlentities($events[$key]['device']['name']);
            $events[$key]['device'] = [
                'id' => $events[$key]['device']['id'],
                'name' => $name
            ];
            $events[$key]['device_name'] = $events[$key]['device']['name'];
            $events[$key]['device_id'] = $events[$key]['device']['id'];
       
            if (empty($event['geofence']))
                continue;

            $name = htmlentities($events[$key]['geofence']['name']);
            $events[$key]['geofence'] = [
                'id' => $events[$key]['geofence']['id'],
                'name' => $name
            ];
            $events[$key]['geofence_id'] = $events[$key]['geofence']['id'];

        }

        return ['items' => $items, 'events' => $events, 'time' => $time, 'version' => Config::get('tobuli.version')];
    }

    public function generateJson(&$items, $item, $userDrivers = [], $json = TRUE, $device_info = FALSE) {
        $alarm = null;
        $protocol = null;
        if (isset($item['other']))
            preg_match( '/<protocol>(.*?)<\/protocol>/s', $item['other'], $protocol);

        //$dev_online = isDeviceOnline($item['server_time'], $item['ack_time']);
        $dev_online = getDeviceStatus($item, [
            'type' => $item['sensor_type'],
            'tag_name' => $item['sensor_tag_name'],
            'on_value' => $item['sensor_on_value'],
            'off_value' => $item['sensor_off_value'],
            'on_tag_value' => $item['sensor_on_tag_value'],
            'off_tag_value' => $item['sensor_off_tag_value'],
            'value' => $item['sensor_value'],
            'on_type' => $item['sensor_on_type'],
            'off_type' => $item['sensor_off_type'],
        ]);
        $speed = '0';
        $altitude = '0';
        if (isset($item['speed']) && $dev_online == 'online')
            $speed = $this->user->unit_of_distance == 'mi' ? kilometersToMiles($item['speed']) : $item['speed'];
        if (isset($item['altitude']))
            $altitude = $this->user->unit_of_altitude == 'ft' ? metersToFeets($item['altitude']) : $item['altitude'];

        $driver_id = $item['current_driver_id'];

        $icon_color = 'green';
        if ($item['icon_type'] == 'arrow') {
            $icon_color = getDeviceStatusColor($item, $dev_online);
            /*
            $icon_color = deviceIconColor($item, $dev_online, $icon_colors, [
                'type' => $item['sensor_type'],
                'tag_name' => $item['sensor_tag_name'],
                'on_value' => $item['sensor_on_value'],
                'off_value' => $item['sensor_off_value'],
                'on_tag_value' => $item['sensor_on_tag_value'],
                'off_tag_value' => $item['sensor_off_tag_value'],
                'value' => $item['sensor_value'],
                'on_type' => $item['sensor_on_type'],
                'off_type' => $item['sensor_off_type'],
            ]);
            */
        }

        $values = [
            'odometer' => [
                'value' => 0,
                'sufix' => ''
            ],
            'engine_hours' => [
                'value' => 0,
                'sufix' => ''
            ]
        ];

        if (!$dev_online)
            $item['tail_length'] = 0;

        $tail = prepareDeviceTail(isset($item['latest_positions']) ? $item['latest_positions'] : '', $item['tail_length']);
        $timezones = DB::table('timezones')->select('id', 'zone')->get();
        $timezones_arr = [];
        foreach($timezones as $timezone)
            $timezones_arr[$timezone->id] = $timezone->zone;

        $timezone = !empty($item['timezone_id']) && isset($timezones_arr[$item['timezone_id']])  ? $timezones_arr[$item['timezone_id']] : $this->user->timezone->zone;
        $device_info_arr = $item;
        $device_info_arr['active'] = $device_info_arr['active'];
        $device_info_arr['group_id'] = $device_info_arr['group_id'];
        $device_info_arr['current_driver_id'] = $device_info_arr['current_driver_id'];

        if ($item['expiration_date'] != '0000-00-00' && strtotime($item['expiration_date']) < strtotime(date('Y-m-d'))) {
            $item = array_merge($item, [
                'time' => trans('front.expired'),
                'server_time' => '',
                'other' => ''
            ]);
        }
        else {
            $item['time'] = is_null($item['time']) || substr($item['time'], 0, 4) == '0000' ? trans('front.not_connected') : datetime($item['time'], TRUE, $timezone);
        }

        $device_info_arr['id'] = intval($device_info_arr['id']);
        $device_info_arr['user_id'] = intval($device_info_arr['user_id']);
        $device_info_arr['traccar_device_id'] = intval($device_info_arr['traccar_device_id']);
        $device_info_arr['icon_id'] = intval($device_info_arr['icon_id']);
        $device_info_arr['active'] = intval($device_info_arr['active']);
        $device_info_arr['deleted'] = intval($device_info_arr['deleted']);
        $device_info_arr['fuel_measurement_id'] = intval($device_info_arr['fuel_measurement_id']);
        $device_info_arr['tail_length'] = intval($device_info_arr['tail_length']);
        $device_info_arr['min_moving_speed'] = intval($device_info_arr['min_moving_speed']);
        $device_info_arr['min_fuel_fillings'] = intval($device_info_arr['min_fuel_fillings']);
        $device_info_arr['min_fuel_thefts'] = intval($device_info_arr['min_fuel_thefts']);
        $device_info_arr['snap_to_road'] = intval($device_info_arr['snap_to_road']);
        $device_info_arr['gprs_templates_only'] = intval($device_info_arr['gprs_templates_only']);
        $device_info_arr['group_id'] = intval($device_info_arr['group_id']);
        $device_info_arr['current_driver_id'] = is_null($device_info_arr['current_driver_id']) ? $device_info_arr['current_driver_id'] : intval($device_info_arr['current_driver_id']);
        $device_info_arr['pivot']['user_id'] = intval($device_info_arr['user_id']);
        $device_info_arr['pivot']['device_id'] = intval($device_info_arr['id']);
        $device_info_arr['pivot']['group_id'] = intval($device_info_arr['group_id']);
        $device_info_arr['pivot']['current_driver_id'] = is_null($device_info_arr['current_driver_id']) ? $device_info_arr['current_driver_id'] : intval($device_info_arr['current_driver_id']);
        $device_info_arr['pivot']['timezone_id'] = is_null($device_info_arr['timezone_id']) ? $device_info_arr['timezone_id'] : intval($device_info_arr['timezone_id']);
        $device_info_arr['pivot']['active'] = intval($device_info_arr['active']);

        $sensors = DB::select(DB::raw("SELECT * FROM device_sensors WHERE device_id = '".$item['id']."'"));
        $services = DB::select(DB::raw("SELECT * FROM device_services WHERE device_id = '".$item['id']."'"));

        if ($this->api && $dev_online == 'engine')
            $dev_online = 'ack';

        $items[] = [
                'id' => intval($item['id']),
                'name' => $item['name'],
                'online' => $dev_online,
                'alarm' => isset($item['alarm']) ? $item['alarm'] : '',
                'time' => $item['time'],
                'timestamp' => (!empty($item['server_time']) ? strtotime($item['server_time']) : 0),
                'acktimestamp' => (!empty($item['ack_time']) ? strtotime($item['ack_time']) : 0),
                'speed' => round($speed),
                'lat' => floatval(cord(isset($item['lastValidLatitude']) ? $item['lastValidLatitude'] : 0)),
                'lng' => floatval(cord(isset($item['lastValidLongitude']) ? $item['lastValidLongitude'] : 0)),
                'course' => (isset($item['course']) ? $item['course'] : '-'),
                'power' => (isset($item['power']) ? $item['power'] : '-'),
                'altitude' => round($altitude),
                'address' => '-',
                'protocol' => (isset($item['protocol']) && $this->user->perm('protocol', 'view') ? $item['protocol'] : '-'),
                'driver' => (isset($userDrivers[$driver_id]) ? $userDrivers[$driver_id] : '-'),
                'driver_data' => isset($item['driver']['id']) ? $item['driver'] : [
                    'id' => NULL,
                    'user_id' => NULL,
                    'device_id' => NULL,
                    'name' => NULL,
                    'rfid' => NULL,
                    'phone' => NULL,
                    'email' => NULL,
                    'description' => NULL,
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ],
                //'stop_duration' => $this->stopTime(intval($item['id'])),
                'sensors' => $json ? json_encode(formatSensors($item['other'], $sensors, $values)) : formatSensors($item['other'], $sensors, $values),
                'services' => $json ? json_encode(formatServices($services, $values)) : formatServices($services, $values),
                'tail' => $json ? json_encode($tail) : $tail,
                'distance_unit_hour' => $this->user->distance_unit_hour,
                'unit_of_distance' => $this->user->unit_of_distance,
                'unit_of_altitude' => $this->user->unit_of_altitude,
                'unit_of_capacity' => $this->user->unit_of_capacity,
                'alarm' => is_null($this->user->alarm) ? 0 : $this->user->alarm,
                'icon_color' => $icon_color,
                'icon_colors' => is_array($item['icon_colors']) ? $item['icon_colors'] : json_decode($item['icon_colors'], TRUE)
            ]
            +
            ($device_info ? [
                'device_data' => $device_info_arr
            ] : []);
    }

    private function checkDevicesLimit($user = NULL) {
        if (is_null($user))
            $user = $this->user;

        $devices_count = DeviceRepo::countwhere(['deleted' => 0]);

        if ($user->group_id == 3)
            $user_devices_count = getManagerUsedLimit($user->id);
        else
            $user_devices_count = $user->devices->count();

        if ((!is_null($user->devices_limit) && $user_devices_count >= $user->devices_limit) || (isset($_ENV['limit']) && $_ENV['limit'] > 1 && $devices_count >= $_ENV['limit']))
            return $this->api ? ['status' => 0, 'perm' => 0, 'errors' => ['id' => trans('front.devices_limit_reached')]] : modal(trans('front.devices_limit_reached'));

        return NULL;
    }

    # Sensor groups
    private function createSensors($device_id) {
        if (isAdmin() && isset($this->data['sensor_group_id'])) {
            $group_sensors = SensorGroupSensorRepo::getWhere(['group_id' => $this->data['sensor_group_id']]);
            if (!empty($group_sensors)) {
                foreach ($group_sensors as $sensor) {
                    $sensor = $sensor->toArray();
                    if (!$sensor['show_in_popup'])
                        unset($sensor['show_in_popup']);

                    SensorModalHelper::setData(array_merge([
                        'user_id' => $this->user->id,
                        'device_id' => $device_id,
                        'sensor_type' => $sensor['type'],
                        'sensor_name' => $sensor['name'],
                    ], $sensor));
                    SensorModalHelper::create();
                }
            }
        }
    }

    private function deviceSyncUsers($device) {
        if (isset($this->data['user_id']))
            $device->users()->sync($this->data['user_id']);

        DB::table('user_device_pivot')
            ->where([
                'device_id' => $device->id,
                'user_id' => $this->user->id
            ])
            ->update([
                'group_id' => $this->data['group_id'],
                'timezone_id' => $this->data['timezone_id'] == 0 ? NULL : $this->data['timezone_id']
            ]);
    }
	
	// New Traccar Code
	private function addDeviceInTraccar($device) {
		/*
        $db_ext = DB::connection('traccar_mysql');
		$db_ext->table('tc_devices')->insert([
				'id' => $device->traccar_device_id,
                'name' => $this->data['name'],
                'uniqueid' => $this->data['imei']
            ]);	
		
		$db_ext->table('tc_user_device')->insert([
				'userid' => 1,
                'deviceid' => $device->traccar_device_id                
            ]);	
		*/
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_PORT => "8082",
			CURLOPT_URL => "http://31.220.75.36:8082/api/devices",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => '{"id":"'. $device->traccar_device_id .'","name":"'. $this->data['name'] .'","uniqueId":"'. $this->data['imei'] .'"}',
			CURLOPT_HTTPHEADER => array(
			"Accept: application/json",
			"Authorization: Basic YWRtaW46YWRtaW4=",
			"Content-Type: application/json",
			 "Postman-Token: 128e89b0-558f-4a44-9119-3b20b557cb3b",
			"cache-control: no-cache"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		/*if ($err) {
		  echo "cURL Error #:" . $err;
		} else {
		  echo $response;
		}*/
		return true;	
    }
	
	// New Traccar Code
	private function updateDeviceInTraccar($device) {
        /*$db_ext = DB::connection('traccar_mysql');
		$db_ext->table('tc_devices')->where([
                'id' => $device->traccar_device_id
            ])
            ->update([
                'name' => $this->data['name'],
                'uniqueid' => $this->data['imei']
            ]);
		*/
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_PORT => "8082",
			CURLOPT_URL => "http://31.220.75.36:8082/api/devices/". $device->traccar_device_id ,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "PUT",
			CURLOPT_POSTFIELDS => '{"id":"'. $device->traccar_device_id .'","name":"'. $this->data['name'] .'","uniqueId":"'. $this->data['imei'] .'"}',
			CURLOPT_HTTPHEADER => array(
			"Accept: application/json",
			"Authorization: Basic YWRtaW46YWRtaW4=",
			"Content-Type: application/json",
			"Postman-Token: 0cad2d8a-2467-4a87-93f5-63976325518b",
			"cache-control: no-cache"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  //echo "cURL Error #:" . $err;
		} else {
		  //echo $response;
		}	
		return true;
    }
	
	// New Traccar Code
	private function deleteDeviceInTraccar($device) {
        /*$db_ext = DB::connection('traccar_mysql');
		$db_ext->table('tc_devices')->where([
                'id' => $device->traccar_device_id
            ])
            ->delete();
		*/	
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_PORT => "8082",
			CURLOPT_URL => "http://31.220.75.36:8082/api/devices/". $device->traccar_device_id,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "DELETE",
			CURLOPT_POSTFIELDS => "",
			CURLOPT_HTTPHEADER => array(
			"Accept: application/json",
			"Authorization: Basic YWRtaW46YWRtaW4=",
			"Content-Type: application/json",
			"Postman-Token: 16a7310c-dbbd-49ff-b469-b08541c5798b",
			"cache-control: no-cache"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  //echo "cURL Error #:" . $err;
		} else {
		  //echo $response;
		}
		return true;
    }

    public function getDevice(){
        try{
           $valid =  Validator::make($this->data, [
                'device_id' => 'required|exists:devices,id'
                ]);
            if($valid->fails()){
                return ['status'=> 0 , 'message' => $valid->errors()->first()];
            }else{
                $device = DeviceRepo::getWithFirst(['traccar', 'users', 'sensors'], ['id' => $this->data['device_id']])->toArray();
               return ['status'=> '1' , 'device' => $device];
                //echo(json_encode(array("status" => '1' , 'geofence' => array('id' => $item['id'] ,'user_id' => $item['user_id'] , 'active' => $item['active'] , 'name' => $item['name'] ,'coordinates'=> json_decode($item['coordinates'] , true) , 'polygon_color'=> $item['polygon_color'] )))); die;
            }
        }catch(\Exception $e){
            return ['status' => '0' , 'message' => $e->getMessage()];
        } 
        }
}
