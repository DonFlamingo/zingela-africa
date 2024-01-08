<?php namespace ModalHelpers;

use Facades\Repositories\AlertDeviceRepo;
use Facades\Repositories\AlertGeofenceRepo;
use Facades\Repositories\AlertRepo;
use Facades\Repositories\DeviceRepo;
use Facades\Repositories\EventCustomRepo;
use Facades\Repositories\GeofenceRepo;
use Facades\Repositories\UserRepo;
use Facades\Validators\AlertFormValidator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Tobuli\Exceptions\ValidationException;

class AlertModalHelper extends ModalHelper
{
    public function get()
    {
        $alerts = AlertRepo::getWithWhere(['devices', 'drivers', 'geofences', 'events_custom'], ['user_id' => $this->user->id]);
        if ($this->api) {
            $alerts = $alerts->toArray();
            foreach ($alerts as $key => $alert) {
                $drivers = [];
                foreach ($alert['drivers'] as $driver)
                    array_push($drivers, $driver['id']);

                $alerts[$key]['drivers'] = $drivers;

                $devices = [];
                foreach ($alert['devices'] as $device)
                    array_push($devices, $device['id']);

                $alerts[$key]['devices'] = $devices;

                $geofences = [];
                foreach ($alert['geofences'] as $geofence)
                    array_push($geofences, ['id' => $geofence['id'], 'zone' => $geofence['pivot']['zone']]);

                $alerts[$key]['geofences'] = $geofences;

                $events_custom = [];
                foreach ($alert['events_custom'] as $event)
                    array_push($events_custom, ['id' => $event['id'], 'protocol' => $event['protocol'], 'type' => is_null($event['user_id']) ? 2 : 1, 'message' => $event['message']]);

                $alerts[$key]['events_custom'] = $events_custom;
            }
        }

        if (!$this->user->perm('alerts', 'view'))
            $alerts = [];

        return compact('alerts');
    }

    public function createData()
    {
        if (!$this->user->perm('alerts', 'edit'))
            return !$this->api ? modalError(trans('front.dont_have_permission')) : ['status' => 0, 'perm' => 0];

        $devices = UserRepo::getDevices($this->user->id)->lists('name', 'id')->all();
        $geofences = GeofenceRepo::whereUserId($this->user->id)->lists('name', 'id')->all();
        $drivers = UserRepo::getDrivers($this->user->id)->lists('name', 'id')->all();

        if (empty($devices))
            return $this->api ? ['status' => 0, 'errors' => ['id' => trans('front.must_have_one_device')]] : modal(trans('front.must_have_one_device'));

        $alert_zones = [
            '3' => trans('front.combined'),
            '1' => trans('front.zone_in'),
            '2' => trans('front.zone_out')
        ];
        $alert_fuel_type = Config::get('tobuli.alert_fuel_type');
        $alert_distance = Config::get('tobuli.alert_distance');

        $event_types = [
            '1' => trans('front.custom_events'),
            '2' => trans('front.system_events')
        ];
        $event_protocols = ['-' => '- '.trans('validation.attributes.protocol').' -'];

        if ($this->api) {
            $alert_zones = [
                '1' => trans('front.zone_in'),
                '2' => trans('front.zone_out')
            ];
            $devices = apiArray($devices);
            $geofences = apiArray($geofences);
            $drivers = apiArray($drivers);
            $alert_zones = apiArray($alert_zones);
            $alert_fuel_type = apiArray($alert_fuel_type);
            $alert_distance = apiArray($alert_distance);
            $event_types = apiArray($event_types);
            $event_protocols = apiArray($event_protocols);
        }

        return compact('devices', 'geofences', 'drivers', 'alert_zones', 'alert_fuel_type', 'alert_distance', 'event_types', 'event_protocols');
    }

    public function create()
    {
        $this->data['mobile_phone'] = (isset($this->data['mobile_phone']) ? $this->data['mobile_phone'] : null);
        if ($this->api) {
            $this->data['devices'] = json_decode($this->data['devices'], TRUE);
            $this->data['drivers'] = (isset($this->data['drivers']) && !empty($this->data['drivers'])) ? json_decode($this->data['drivers'], TRUE) : '';
            $this->data['geofences'] = (isset($this->data['geofences']) && !empty($this->data['geofences'])) ? json_decode($this->data['geofences'], TRUE): '';
            $this->data['events_custom'] = (isset($this->data['events_custom']) && !empty($this->data['events_custom'])) ? json_decode($this->data['events_custom'], TRUE) : '';
        }

        try
        {
            if (!$this->api && !$this->user->perm('alerts', 'edit'))
                throw new ValidationException(['id' => trans('front.dont_have_permission')]);

            $this->validate('create');
            beginTransaction();
            try {

                $alert = AlertRepo::create([
                    'user_id' => $this->user->id,
                    'name' => $this->data['name'],
                    'email' => $this->data['email'],
                    'mobile_phone' => $this->data['mobile_phone'],
                    'overspeed_speed' => (isset($this->data['overspeed']['speed']) ? $this->data['overspeed']['speed'] : 0),
                    'overspeed_distance' => (isset($this->data['overspeed']['distance']) ? $this->data['overspeed']['distance'] : 0),
                    'ac_alarm' => (isset($this->data['ac_alarm']) ? $this->data['ac_alarm'] : 0)
                ]);


                foreach ($this->data['devices'] as $key=>$id) {
                    AlertDeviceRepo::create([
                        'alert_id' => $alert->id,
                        'device_id' => $id,
                    ]);
                }

                if (isset($this->data['geofences']) && is_array($this->data['geofences']) && !empty($this->data['geofences'])) {
                    foreach ($this->data['geofences'] as $key => $item) {
                        $insert_arr = [
                            'alert_id' => $alert->id,
                            'geofence_id' => $item['id'],
                            'time_from' => $this->data['time_from'],
                            'time_to' => $this->data['time_to'],
                        ];

                        if ($item['zone'] == 3) {
                            AlertGeofenceRepo::create(
                                $insert_arr + ['zone' => 1]
                            );

                            AlertGeofenceRepo::create(
                                $insert_arr + ['zone' => 2]
                            );
                        }
                        else {
                            AlertGeofenceRepo::create(
                                $insert_arr + ['zone' => $item['zone']]
                            );
                        }
                    }
                }
                //print_r($this->data); die;
                if (isset($this->data['drivers']) && is_array($this->data['drivers']) && !empty($this->data['drivers']))
                    $alert->drivers()->sync($this->data['drivers']);

                if (isset($this->data['events_custom']) && is_array($this->data['events_custom']) && !empty($this->data['events_custom'])) {
                    $protocols = DeviceRepo::getProtocols($this->data['devices']);
                    $events = EventCustomRepo::whereProtocols($this->data['events_custom'], $protocols->lists('protocol', 'protocol')->all());
                    $alert->events_custom()->sync($events->lists('id', 'id')->all());
                }
            }
            catch (\Exception $e) {
                rollbackTransaction();
                throw new ValidationException(['id' => trans('global.unexpected_db_error')]);
            }

            $devices = DeviceRepo::getWhereIn($this->data['devices']);
            foreach($devices as $device)
                clearCache($device->imei, ['alerts', 'overspeeds', 'custom_events']);

            commitTransaction();

            return ['status' => 1 , 'alert' => ['id' =>$alert->id ]];
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

    public function editData()
    {
        if (!$this->api && !$this->user->perm('alerts', 'edit'))
            return modalError(trans('front.dont_have_permission'));

        $id = array_key_exists('alert_id', $this->data) ? $this->data['alert_id'] : request()->route('alerts');
        $item = AlertRepo::findWithAttributes($id);
        if (empty($item) || $item->user_id != $this->user->id)
            return $this->api ? ['status' => 0, 'errors' => ['id' => dontExist('global.alert')]] : modal(dontExist('global.alert'), 'danger');

        $devices = UserRepo::getDevices($this->user->id)->lists('name', 'id')->all();
        $geofences = GeofenceRepo::whereUserId($this->user->id)->lists('name', 'id')->all();
        $drivers = UserRepo::getDrivers($this->user->id)->lists('name', 'id')->all();

        if (empty($devices))
            return $this->api ? ['status' => 0, 'errors' => ['id' => trans('front.must_have_one_device')]] : modal(trans('front.must_have_one_device'));

        $alert_zones = [
            '3' => trans('front.combined'),
            '1' => trans('front.zone_in'),
            '2' => trans('front.zone_out')
        ];
        $alert_fuel_type = Config::get('tobuli.alert_fuel_type');
        $alert_distance = Config::get('tobuli.alert_distance');

        $event_types = [
            '1' => trans('front.custom_events'),
            '2' => trans('front.system_events')
        ];

        $protocols = DeviceRepo::getProtocols($item->devices->lists('id', 'id')->all())->lists('protocol', 'protocol')->all();
        $event_protocols = ['-' => '- '.trans('validation.attributes.protocol').' -'] + EventCustomRepo::getProtocols($this->user->id, $protocols)->lists('protocol', 'protocol')->all();

        $geo_arr = [];

        foreach ($item->geofences as $geofence) {
            if (!array_key_exists($geofence->id, $geo_arr)) {
                $geo_arr[$geofence->id] = [
                    'name' => $geofence->name,
                    'zones' => [],
                    'time_from' => $geofence->pivot->time_from,
                    'time_to' => $geofence->pivot->time_to,
                ];
            }

            if (!array_key_exists(3, $geo_arr[$geofence->id]['zones']) && (($geofence->pivot->zone == 1 && array_key_exists(2, $geo_arr[$geofence->id]['zones']) || ($geofence->pivot->zone == 2 && array_key_exists(1, $geo_arr[$geofence->id]['zones']))))) {
                if (array_key_exists(2, $geo_arr[$geofence->id]['zones']))
                    unset($geo_arr[$geofence->id]['zones'][2]);

                if (array_key_exists(1, $geo_arr[$geofence->id]['zones']))
                    unset($geo_arr[$geofence->id]['zones'][1]);

                $geo_arr[$geofence->id]['zones'][3] = 3;
            }
            else {
                if (!array_key_exists(3, $geo_arr[$geofence->id]['zones']))
                    $geo_arr[$geofence->id]['zones'][$geofence->pivot->zone] = $geofence->pivot->zone;
            }
        }

        if ($this->api) {
            $alert_zones = [
                '1' => trans('front.zone_in'),
                '2' => trans('front.zone_out')
            ];
            $devices = apiArray($devices);
            $geofences = apiArray($geofences);
            $drivers = apiArray($drivers);
            $alert_zones = apiArray($alert_zones);
            $alert_fuel_type = apiArray($alert_fuel_type);
            $alert_distance = apiArray($alert_distance);
            $event_types = apiArray($event_types);
            $event_protocols = apiArray([]);
        }

        return compact('item', 'devices', 'geofences', 'drivers', 'alert_zones', 'alert_fuel_type', 'alert_distance', 'event_types', 'event_protocols', 'geo_arr');
    }

    public function edit()
    {
        $this->data['mobile_phone'] = (isset($this->data['mobile_phone']) ? $this->data['mobile_phone'] : null);
        if ($this->api) {
            $this->data['devices'] = json_decode($this->data['devices'], TRUE);
            $this->data['drivers'] = json_decode($this->data['drivers'], TRUE);
            $this->data['geofences'] = json_decode($this->data['geofences'], TRUE);
            $this->data['events_custom'] = json_decode($this->data['events_custom'], TRUE);
        }

        $alert = AlertRepo::findWithAttributes($this->data['id']);

        try
        {
            if (!$this->api && !$this->user->perm('alerts', 'edit'))
                throw new ValidationException(['id' => trans('front.dont_have_permission')]);
            
            if (empty($alert) || $alert->user_id != $this->user->id)
                throw new ValidationException(['id' => dontExist('global.alert')]);

            $this->validate('update');
            beginTransaction();
            try {
                AlertRepo::update($alert->id, [
                    'name' => $this->data['name'],
                    'email' => $this->data['email'],
                    'mobile_phone' => $this->data['mobile_phone'],
                    'overspeed_speed' => (isset($this->data['overspeed']['speed']) ? $this->data['overspeed']['speed'] : 0),
                    'overspeed_distance' => (isset($this->data['overspeed']['distance']) ? $this->data['overspeed']['distance'] : 0),
                    'ac_alarm' => (isset($this->data['ac_alarm']) ? $this->data['ac_alarm'] : 0)
                ]);


                AlertGeofenceRepo::deleteWhereAlertId($alert->id);

                $alertDevices = $alert->devices->lists('id','id')->all();
                $newDevices = array_diff($this->data['devices'], $alertDevices);
                $unselectedDevices = array_diff($alertDevices, $this->data['devices']);

                if (!empty($unselectedDevices))
                    AlertDeviceRepo::deleteWhereDevicesId($unselectedDevices, $alert->id);

                if (!empty($newDevices)) {
                    foreach ($newDevices as $key => $id) {
                        AlertDeviceRepo::create([
                            'alert_id' => $alert->id,
                            'device_id' => $id,
                        ]);
                    }
                }


                if (isset($this->data['geofences']) && is_array($this->data['geofences']) && !empty($this->data['geofences'])) {
                    foreach ($this->data['geofences'] as $key => $item) {
                        $insert_arr = [
                            'alert_id' => $alert->id,
                            'geofence_id' => $item['id'],
                            'time_from' => $this->data['time_from'],
                            'time_to' => $this->data['time_to'],
                        ];

                        if ($item['zone'] == 3) {
                            AlertGeofenceRepo::create(
                                $insert_arr + ['zone' => 1]
                            );

                            AlertGeofenceRepo::create(
                                $insert_arr + ['zone' => 2]
                            );
                        }
                        else {
                            AlertGeofenceRepo::create(
                                $insert_arr + ['zone' => $item['zone']]
                            );
                        }
                    }
                }

                if (empty($this->data['drivers']))
                    $this->data['drivers'] = [];
                $alert->drivers()->sync(is_array($this->data['drivers']) ? $this->data['drivers'] : []);

                $protocols = DeviceRepo::getProtocols($this->data['devices']);
                $events = EventCustomRepo::whereProtocols(isset($this->data['events_custom']) ? $this->data['events_custom'] : [], $protocols->lists('protocol', 'protocol')->all());
                $alert->events_custom()->sync($events->lists('id', 'id')->all());
            }
            catch (\Exception $e) {
                rollbackTransaction();
                throw new ValidationException(['id' => trans('global.unexpected_db_error')]);
            }

            if (!is_array($this->data['devices']))
                $this->data['devices'] = [];
            if (!is_array($alertDevices))
                $alertDevices = [];

            $devices_ids = array_flip(array_flip(array_merge($alertDevices, $this->data['devices'])));
            if (!empty($devices_ids)) {
                $devices = DeviceRepo::getWhereIn($devices_ids);
                foreach($devices as $device) {
                    clearCache($device->imei, ['alerts', 'overspeeds', 'custom_events']);
                }
            }

            commitTransaction();

            return ['status' => 1];
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

    private function validate($type)
    {
        if (!isset($this->data['id']))
            $this->data['id'] = NULL;
        //print_r($this->data['email']); die;
        # Clean string, remove empty entries
        $arr['email'] = array_flip(explode(';', $this->data['email']));
        unset($arr['email']['']);
        $arr['email'] = array_flip($arr['email']);
        $arr['email'] = array_map('trim', $arr['email']);

        $arr['mobile_phone'] = array_flip(explode(';', $this->data['mobile_phone']));
        unset($arr['mobile_phone']['']);
        $arr['mobile_phone'] = array_flip($arr['mobile_phone']);
        $arr['mobile_phone'] = array_map('trim', $arr['mobile_phone']);

        # Regenerate string
        $this->data['email'] = implode(';', $arr['email']);
        $this->data['mobile_phone'] = implode(';', $arr['mobile_phone']);

        $validator = Validator::make($arr, ['email' => 'array_max:5']);
        $validator->each('email', ['email']);
        if ($validator->fails())
            throw new ValidationException(['email' => $validator->errors()->first()]);

        $validator = Validator::make($arr, ['mobile_phone' => 'array_max:5']);
        if ($validator->fails())
            throw new ValidationException(['mobile_phone' => $validator->errors()->first()]);

        AlertFormValidator::validate($type, $this->data, $this->data['id']);
    }

    public function changeActive()
    {
        if (!$this->api && !$this->user->perm('alerts', 'edit'))
            return ['status' => 1];
        
        $item = AlertRepo::findWithAttributes($this->data['id']);
        if (empty($item) && $item->user_id == $this->user->id)
            return ['status' => 0, 'errors' => ['alert' => dontExist('front.alert')]];

        AlertRepo::update($item->id, ['active' => ($this->data['active'] == 'true')]);

        return ['status' => 1];
    }

    public function doDestroy($id) {
        $item = AlertRepo::find($id);
        if (empty($item) || $item->user_id != $this->user->id)
            return modal(dontExist('front.alert'), 'danger');

        return compact('item');
    }

    public function destroy()
    {
        if (!$this->user->perm('alerts', 'remove'))
            return ['status' => 0, 'perm' => 0, 'errors' => ['id' => dontExist('global.alert')]];

        $id = array_key_exists('alert_id', $this->data) ? $this->data['alert_id'] : $this->data['id'];
        $item = AlertRepo::findWithAttributes($id);
        if (empty($item) || $item->user_id != $this->user->id)
            return ['status' => 0, 'errors' => ['id' => dontExist('global.alert')]];

        $alertDevices = $item->devices->lists('id','id')->all();
        if (!empty($alertDevices)) {
            $devices = DeviceRepo::getWhereIn($alertDevices, 'id');
            foreach ($devices as $device)
                clearCache($device->imei, ['alerts', 'overspeeds', 'custom_events']);
        }

        AlertRepo::delete($id);

        return ['status' => 1];
    }
}