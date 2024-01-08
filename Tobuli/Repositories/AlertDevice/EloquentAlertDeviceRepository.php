<?php namespace Tobuli\Repositories\AlertDevice;

use Tobuli\Entities\AlertDevice as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentAlertDeviceRepository extends EloquentRepository implements AlertDeviceRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }

    public function deleteWhereDevicesId(array $ids, $alert_id) {
        return Entity::whereIn('device_id', $ids)->where('alert_id', $alert_id)->delete();
    }

    public function getAlertDevices($alert_id)
    {
        return Entity::where('alert_id', $alert_id)->with('device')->get()->toArray();
    }
}