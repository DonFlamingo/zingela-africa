<?php namespace Tobuli\Repositories\DeviceSensor;

use Tobuli\Repositories\EloquentRepository;
use Tobuli\Entities\DeviceSensor as Entity;

class EloquentDeviceSensorRepository extends EloquentRepository implements DeviceSensorRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
        $this->searchable = [];
    }

    public function whereUserId($user_id) {
        return Entity::join('user_device_pivot', 'user_device_pivot.device_id', '=', 'device_sensors.device_id')
                ->where('user_device_pivot.user_id', $user_id)
                ->groupBy('device_sensors.type','device_sensors.name')
                ->get();
    }
}