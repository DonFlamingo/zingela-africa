<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

use Facades\ModalHelpers\DeviceModalHelper;
use Facades\Repositories\DeviceGroupRepo;
use Facades\Repositories\DeviceRepo;
use Facades\Repositories\GeofenceGroupRepo;
use Facades\Repositories\MapIconRepo;
use Facades\Repositories\UserDriverRepo;

use Facades\Repositories\UserRepo;
use Facades\Repositories\TimezoneRepo;
use Facades\Validators\ObjectsListSettingsFormValidator;
use Tobuli\Exceptions\ValidationException;


class ObjectsListController extends Controller {

    public function index() {
        if (!settings('plugins.object_listview.status'))
            return modalError(trans('front.dont_have_permission'));

        if (!$this->user->perm('devices', 'view'))
            return modalError(trans('front.dont_have_permission'));

        if (request()->ajax())
            return view('front::ObjectsList.modal');
        else
            return view('front::ObjectsList.index');
    }

    public function items() {
         set_time_limit(300);
        if (!settings('plugins.object_listview.status'))
            return modalError(trans('front.dont_have_permission'));

        if (!$this->user->perm('devices', 'view'))
            return modalError(trans('front.dont_have_permission'));

        $device_groups = ['0' => trans('front.ungrouped')] + DeviceGroupRepo::getWhere(['user_id' => $this->user->id])->lists('title', 'id')->all();
        $timezones = TimezoneRepo::getList();
        $devices = UserRepo::getDevices($this->user->id);

        $settings = UserRepo::getListViewSettings($this->user->id);

        $columns = $settings['columns'];
        $groupby = $settings['groupby'];

        $grouped = [];
        foreach ($devices as &$device) {

            $item = [];
            $address = null;

            $item['protocol'] = isset($device->traccar->protocol) ? $device->traccar->protocol : null;
            $item['group'] = isset($device_groups[$device->pivot->group_id]) ? $device_groups[$device->pivot->group_id] : null;

            foreach ($columns as &$column) {
                if ($column['class'] == 'device') {
                    switch ($column['field']) {
                        case 'status':
                            if (empty($item['status']))
                                $item['status'] = getDeviceStatus($device);
                            $item['status_color'] = getDeviceStatusColor($device, $item['status']);
                            break;
                        case 'speed':
                            if (empty($item['status']))
                                $item['status'] = getDeviceStatus($device);

                            $speed = '0';
                            if (isset($device->traccar->speed) && $item['status'] == 'online')
                                $speed = $device->traccar->speed;

                            $item['speed'] = $this->user->unit_of_distance == 'mi' ? kilometersToMiles($speed) : $speed;

                            $item['speed'] = round( $item['speed'] );

                            $item['speed'] .= ' ' . $this->user->distance_unit_hour;

                            break;
                        case 'time':
                            if ($device->expiration_date != '0000-00-00' && strtotime($device->expiration_date) < strtotime(date('Y-m-d'))) {
                                $item['time'] = trans('front.expired');
                            } else {
                                if (is_null($device->traccar->time)) {
                                    $item['time'] = trans('front.not_connected');
                                } elseif (substr($device->traccar->time, 0, 4) == '0000') {
                                    $item['time'] = trans('front.not_connected');
                                } else {
                                    $item['time'] = datetime($device->traccar->time, TRUE, isset($timezones[$device->pivot->timezone_id]) ? $timezones[$device->pivot->timezone_id] : NULL);
                                }
                            }
                            break;
                        case 'position':
                            $item['lat'] = isset($device->traccar->lastValidLatitude) ? cord($device->traccar->lastValidLatitude) : null;
                            $item['lng'] = isset($device->traccar->lastValidLongitude) ? cord($device->traccar->lastValidLongitude) : null;
                            break;
                        case 'address':
                            if (!$address) {
                                $item['lat'] = isset($device->traccar->lastValidLatitude) ? cord($device->traccar->lastValidLatitude) : null;
                                $item['lng'] = isset($device->traccar->lastValidLongitude) ? cord($device->traccar->lastValidLongitude) : null;

                                if ( $item['lat'] && $item['lng'] ) {
                                    $address = getGeoAddress( $item['lat'], $item['lng'] );
                                }
                            }
                            $item['address'] = $address;
                            break;
                        case 'name':
                        case 'imei':
                        case 'sim_number':
                        case 'device_model':
                        case 'plate_number':
                        case 'vin':
                        case 'registration_number':
                        case 'object_owner':
                        case 'additional_notes':
                            $item[$column['field']] = $device->{$column['field']};
                            break;
                    }
                } elseif ($column['class'] == 'sensor') {
                    $item[$column['field']] = null;

                    if ( $device->sensors ) {
                        foreach ($device->sensors as $sensor) {
                            if ($column['field'] == $sensor->hash) {
                                $column['title'] = $sensor->name;

                                $item[$column['field']] = getSensorValue($sensor->other, json_decode(json_encode($sensor), true) , TRUE);

                                if (!empty($column['color'])) {
                                    foreach ($column['color'] as $color) {
                                        if ($sensor->value >= $color['from'] && $sensor->value <= $color['to']) {
                                            $item['color'][$column['field']] = $color['color'];
                                        }
                                    }
                                }

                                break;
                            }
                        }
                    }
                }

            }

            $grouped[ $item[$groupby] ][] = $item;
        }

        unset($devices);

        return view('front::ObjectsList.list')->with(compact('grouped','columns'));
    }

    public function edit() {
        if (!settings('plugins.object_listview.status'))
            return modalError(trans('front.dont_have_permission'));

        if (!$this->user->perm('devices', 'edit'))
            return modalError(trans('front.dont_have_permission'));

        $numeric_sensors = config('tobuli.numeric_sensors');

        $settings = UserRepo::getListViewSettings($this->user->id);

        $fields = config('tobuli.listview_fields');

        listviewTrans($this->user->id, $settings, $fields);

        return view('front::ObjectsList.edit')->with(compact('fields','settings','numeric_sensors'));
    }

    public function update() {

        if (!settings('plugins.object_listview.status'))
            return modalError(trans('front.dont_have_permission'));

        if (!$this->user->perm('devices', 'edit'))
            return modalError(trans('front.dont_have_permission'));

        try
        {
            ObjectsListSettingsFormValidator::validate('update', $this->data);

            UserRepo::setListViewSettings($this->user->id, request()->only(['columns','groupby']));

            return ['status' => 1];
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }
}
