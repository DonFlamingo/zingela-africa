<?php namespace Tobuli\Repositories\Device;

use Dompdf\Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Tobuli\Entities\Device as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentDeviceRepository extends EloquentRepository implements DeviceRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
        $this->searchable = [
            'devices.name',
            'devices.imei'
        ];
    }

    public function find($id) {
        return $this->entity->with('users', 'sensors')->find($id);
    }

    public function whereUserId($user_id) {
        return $this->entity->where(['user_id' => $user_id, 'deleted' => 0])->with('traccar', 'icon')->get();
    }

    public function userCount($user_id) {
        return $this->entity->where(['user_id' => $user_id, 'deleted' => 0])->count();
    }

    public function updateWhereIconIds($ids, $data)
    {
        $this->entity->whereIn('icon_id', $ids)->update($data);
    }

    public function whereImei($imei) {
        return $this->entity->where('imei', $imei)->first();
    }

    public function searchAndPaginateAdmin(array $data, $sort_by, $sort = 'asc', $limit = 10, $where_in)
    {
        $data = $this->generateSearchData($data);
        $sort = array_merge([
            'sort' => $sort,
            'sort_by' => $sort_by
        ], $data['sorting']);
        $traccar_db = Config::get('database.connections.traccar_mysql.database');
        $items = $this->entity
            ->select(['devices.*', 'traccar.server_time', 'traccar.time'])
            ->orderBy($sort['sort_by'], $sort['sort'])

            ->join($traccar_db.'.devices as traccar', 'devices.traccar_device_id', '=', 'traccar.id')
            ->whereHas('users',function ($query){
                $query->where('email',auth()->user()->email);
            })

            ->where(function ($query) use ($data) {
                if (!empty($data['search_phrase'])) {
                    foreach ($this->searchable as $column) {
                        $query->orWhere($column, 'like', '%' . $data['search_phrase'] . '%');
                    }
                }

                if (count($data['filter'])) {
                    foreach ($data['filter'] as $key=>$value) {
                        $query->where($key, $value);
                    }
                }
            })
            ->where('devices.deleted', 0)
            ->groupBy('devices.id');
            if (!empty($where_in)) {
                $items->join("user_device_pivot", 'devices.id', '=', 'user_device_pivot.device_id')
                    ->whereIn('user_device_pivot.user_id', $where_in);
            }
            $items = $items->paginate($limit);

        $items->sorting = $sort;

        return $items;
    }

    public function searchAndPaginate(array $data, $sort_by, $sort = 'asc', $limit = 10)
    {
        $data = $this->generateSearchData($data);
        $sort = array_merge([
            'sort' => $sort,
            'sort_by' => $sort_by
        ], $data['sorting']);
        $traccar_db = Config::get('database.connections.traccar_mysql.database');
        $items = $this->entity
            ->select(['devices.*', 'traccar.server_time', 'traccar.time'])
            ->orderBy($sort['sort_by'], $sort['sort'])
            ->with('users')
            ->join($traccar_db.'.devices as traccar', 'devices.traccar_device_id', '=', 'traccar.id')
            ->where(function ($query) use ($data) {
                if (!empty($data['search_phrase'])) {
                    foreach ($this->searchable as $column) {
                        $query->orWhere($column, 'like', '%' . $data['search_phrase'] . '%');
                    }
                }

                if (count($data['filter'])) {
                    foreach ($data['filter'] as $key=>$value) {
                        $query->where($key, $value);
                    }
                }
            })
            ->where('devices.deleted', 0)
            ->paginate($limit);

        $items->sorting = $sort;

        return $items;
    }

    public function getProtocols($ids) {
        $traccar_db = Config::get('database.connections.traccar_mysql.database');
        return $this->entity
            ->distinct('traccar.protocol')
            ->join($traccar_db.'.devices as traccar', 'devices.traccar_device_id', '=', 'traccar.id')
            ->whereIn('devices.id', $ids)
            ->whereNotNull('traccar.protocol')
            ->get();
    }

    public function clearCache($imei, $prefix) {
        try {
            $redis = new \Redis();
            $redis->connect('127.0.0.1', 6379);
            $redis->del($prefix.'_'.$imei);
            $redis->close();
        }
        catch (\Exception $e) {}
    }
}