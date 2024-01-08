<?php namespace Tobuli\Repositories\User;

use Tobuli\Repositories\EloquentRepository;
use Tobuli\Entities\User as Entity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class EloquentUserRepository extends EloquentRepository implements UserRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
        $this->searchable = [
            'users.email',
            'users.admin_id'
        ];
    }

    public function searchAndPaginate(array $data, $sort_by, $sort = 'asc', $limit = 50)
    {
        $data = $this->generateSearchData($data);
        $sort = array_merge([
            'sort' => $sort,
            'sort_by' => $sort_by
        ], $data['sorting']);

        if ( isset($_GET{'page'} ) ) {
            $page = $_GET{'page'};
            $offset = $limit * ($page - 1);
        }else {
            $page = 1;
            $offset = 0;
        }

        $items = DB::table('users')
            ->select(DB::Raw('COUNT(DISTINCT users.id) as count'))
            ->where('users.email', '!=', 'admin@atrams.com')
            ->where(function ($query) use ($data) {
                if (!empty($data['search_phrase']) && empty($date['search_device'])) {
                    foreach ($this->searchable as $column) {
                        $query->orWhere($column, 'like', '%' . $data['search_phrase'] . '%');
                    }
                }

                if (count($data['filter'])) {
                    foreach ($data['filter'] as $key=>$value) {
                        $query->where($key, $value);
                    }
                }

            })->where(function ($query) {
                if (Auth::User()->group_id == 3) {
                    $user_id = Auth::User()->id;
                    //$query->whereRaw("(users.manager_id = '{$user_id}' OR users.id = '{$user_id}')");
                    $query->whereRaw("users.manager_id = '{$user_id}'");
                }
            });
        if (!empty($data['search_device'])) {
            $items = $items->leftJoin('user_device_pivot', 'users.id', '=', 'user_device_pivot.user_id')
                ->join('devices', function($join) use ($data) {
                $join->on('user_device_pivot.device_id', '=', 'devices.id')->where('devices.imei', 'LIKE', "%".$data['search_device']."%");
            });
        }

        $count = $items->first()->count;
        if ($offset > $count) {
            $page = 1;
            $offset = 0;
        }
        unset($items);
        $items = DB::table('users')
            ->orderBy($sort['sort_by'], $sort['sort'])
            ->select(DB::Raw('users.*, COUNT(DISTINCT devices.id) as devices, billing_plans.title as billing_plan, managers.email as manager_email'))
            ->leftJoin('billing_plans', 'users.billing_plan_id', '=', 'billing_plans.id')
            ->leftJoin('users as managers', 'users.manager_id', '=', 'managers.id')
            ->leftJoin('user_device_pivot', 'users.id', '=', 'user_device_pivot.user_id')
            ->where('users.email', '!=', 'admin@atrams.com')
            ->where(function ($query) use ($data) {
                if (!empty($data['search_phrase']) && empty($date['search_device'])) {
                    foreach ($this->searchable as $column) {
                        $query->orWhere($column, 'like', '%' . $data['search_phrase'] . '%');
                    }
                }

                if (count($data['filter'])) {
                    foreach ($data['filter'] as $key=>$value) {
                        $query->where($key, $value);
                    }
                }

            })->where(function ($query) {
                if (Auth::User()->group_id == 3) {
                    $user_id = Auth::User()->id;
                    //$query->whereRaw("(users.manager_id = '{$user_id}' OR users.id = '{$user_id}')");
                    $query->whereRaw("users.manager_id = '{$user_id}'");
                }
            })
            ->groupBy('users.id');
        if (!empty($data['search_device'])) {
            $items->join('devices', function($join) use ($data) {
                $join->on('user_device_pivot.device_id', '=', 'devices.id')->where('devices.imei', 'LIKE', "%".$data['search_device']."%");
            });
        }
        else {
            $items->leftJoin('devices', function($query) {
                $query->on('user_device_pivot.device_id', '=', 'devices.id');
                $query->where('devices.deleted', '=', '0');
            });
        }

        $items = $items->take($limit)->skip($offset)->get();
        foreach ($items as &$item) {
            if (empty($item->manager_id)) {
                $item->subusers = DB::table('users')
                    ->select(DB::Raw('COUNT(DISTINCT id) as count'))
                    ->where('manager_id', '=', $item->id)
                    ->first()->count;
                $item->manager_email = NULL;
            }
            else {
                $item->subusers = 0;
                $manager = DB::table('users')
                    ->select('email')
                    ->where('id', '=', $item->manager_id)
                    ->first();
                $item->manager_email = !empty($manager) ? $manager->email : NULL;
            }
        }
        $items = new Paginator($items, $count, $limit, $page, [
            'path'  => Request::url(),
            'query' => Request::query(),
        ]);

        $items->sorting = $sort;

        return $items;
    }

    protected function generateSearchData($data)
    {
        return array_merge([
            'sorting' => [],
            'search_phrase' => '',
            'search_device',
            'filter' => []
        ], $data);
    }

    public function getOtherManagers($user_id) {
        return $this->entity->where('group_id', 3)->where('id', '!=', $user_id)->get();
    }

    // public function getDevices($user_id) {
    //     return $this->entity->with('devices', 'devices.sensors', 'devices.services')->find($user_id)->devices;
    // }
     public function getDevices($user_id) {
        $devices = $this->entity->with('devices', 'devices.sensors', 'devices.services')->find($user_id)->devices;
        $devices->each(function ($device) {
            $device->parking_mode = (int) $device->parking_mode;
        });
        return $devices;
    }

    public function getDevice($user_id, $device_id) {
        $user = $this->entity->find($user_id);

        if (!$user)
            return null;

        return $user->devices()->with('sensors', 'services')->find($device_id);
    }

    public function getDevicesProtocols($user_id) {
        $items = DB::table('user_device_pivot')
            ->select('devices.id', 'traccar_devices.protocol')
            ->join('devices', 'user_device_pivot.device_id', '=', 'devices.id')
            ->join('tracking_traccar.devices as traccar_devices', 'devices.traccar_device_id', '=', 'traccar_devices.id')
            ->where('user_device_pivot.user_id', '=', $user_id)
            ->get();

        $arr = [];
        if (!empty($items)) {
            foreach ($items as $item)
                $arr[$item->id] = $item->protocol;
        }

        return $arr;
    }

    public function getDevicesHigherTime($user_id, $time)
    {
        $data['time'] = intval($time);
        $date = date('Y-m-d H:i:s', $time);
        $items = DB::select(DB::raw("SELECT user_device_pivot.device_id as id FROM user_device_pivot JOIN devices ON user_device_pivot.device_id = devices.id JOIN tracking_traccar.devices as traccar ON devices.traccar_device_id = traccar.id WHERE user_device_pivot.user_id='".Auth::User()->id."' AND (traccar.server_time >= '$date' OR traccar.ack_time >= '$date') GROUP BY traccar.id"));
        $device_ids = [];
        foreach ($items as $item)
            $device_ids[$item->id] = $item->id;

        if (empty($device_ids))
            return [];

        $items = DB::select(DB::raw("SELECT
user_device_pivot.active,
user_device_pivot.group_id,
user_device_pivot.current_driver_id,
user_device_pivot.timezone_id,
device_icons.type as icon_type,
users.id as user_id,
users.unit_of_distance,
users.unit_of_altitude,
users.timezone_id as user_timezone_id,
devices.*,
device_sensors.type as sensor_type,
device_sensors.tag_name as sensor_tag_name,
device_sensors.on_value as sensor_on_value,
device_sensors.off_value as sensor_off_value,
device_sensors.on_tag_value as sensor_on_tag_value,
device_sensors.off_tag_value as sensor_off_tag_value,
device_sensors.value as sensor_value,
device_sensors.on_type as sensor_on_type,
device_sensors.off_type as sensor_off_type,
traccar.other,
traccar.time,
traccar.server_time,
traccar.ack_time,
traccar.speed,
traccar.altitude,
traccar.latest_positions,
traccar.lastValidLatitude,
traccar.lastValidLongitude,
traccar.course,
traccar.power,
traccar.protocol
FROM devices
JOIN user_device_pivot ON user_device_pivot.device_id = devices.id AND user_device_pivot.user_id='".Auth::User()->id."'
JOIN users ON user_device_pivot.user_id = users.id
LEFT JOIN device_sensors ON devices.id = device_sensors.device_id AND IF(devices.engine_hours = 'engine_hours', devices.detect_engine = device_sensors.type, devices.engine_hours = device_sensors.type) AND device_sensors.type <> 'gps'
LEFT JOIN device_icons ON devices.icon_id = device_icons.id
JOIN tracking_traccar.devices as traccar ON devices.traccar_device_id = traccar.id
WHERE devices.id IN (" . implode($device_ids, ',') . ") GROUP BY devices.id"));

        return json_decode(json_encode($items), TRUE);
    }

    public function getDevicesSms($user_id) {
        return $this->entity->with('devices_sms')->find($user_id)->devices_sms;
    }

    public function getUsers($user) {
        if ($user->email == 'admin@atrams.com')
            return $this->entity->orderby('email')->get();
        else
            return $user->group_id == 3 ? $this->entity->whereRaw("(manager_id = '{$user->id}' OR id = '{$user->id}') AND email != 'admin@atrams.com'")->orderby('email')->get() : $this->entity->whereRaw("email != 'admin@atrams.com'")->where('admin_id',\auth()->user()->id)->orderby('email')->get();
    }

    public function getDrivers($user_id) {
        return $this->entity->with('drivers')->find($user_id)->drivers;
    }

    public function getSettings($user_id, $key) {
        return $this->entity->find($user_id)->getSettings($key);
    }

    public function setSettings($user_id, $key, $value) {
        return $this->entity->find($user_id)->setSettings($key, $value);
    }

    public function getListViewSettings($user_id)
    {
        if (!is_null($user_id))
            $settings = $this->getSettings($user_id, 'listview');

        $fields_trans  = config('tobuli.listview_fields_trans');
        $sensors_trans = config('tobuli.sensors');

        $defaults = config('tobuli.listview');

        $settings = empty($settings) ? $defaults : array_merge($defaults, $settings);

        foreach($settings['columns'] as &$column) {
            if (!empty($column['class']) && $column['class'] == 'sensor') {
                $column['title'] = htmlentities( $sensors_trans[ $column['type'] ], ENT_QUOTES);
            } else {
                $column['title'] = htmlentities( $fields_trans[ $column['field'] ], ENT_QUOTES);
            }
        }

        return $settings;
    }

    public function setListViewSettings($user_id, $settings)
    {
        return $this->setSettings($user_id, 'listview', $settings);
    }
}