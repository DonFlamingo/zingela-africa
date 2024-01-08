<?php namespace Tobuli\Repositories\DeviceService;

use Tobuli\Repositories\EloquentRepository;
use Tobuli\Entities\DeviceService as Entity;

class EloquentDeviceServiceRepository extends EloquentRepository implements DeviceServiceRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
        $this->searchable = [];
    }
    public function getBetween($user_id, $device_id, $from, $to) {
        return $this->entity
            ->whereBetween('remind_date', [$from, $to])
            ->where([
                'user_id' => $user_id,
                'device_id' => $device_id
            ])
            ->orderBy('remind_date', 'asc')
            ->get();
    }
    public function deleteDeviceWhereNotIn($arr, $device_id, $id = 'id') {
        return $this->entity->where('device_id', $device_id)->whereNotIn($id, $arr)->delete();
    }
}