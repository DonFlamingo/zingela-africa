<?php namespace Tobuli\Repositories\AlertGeofence;

use Tobuli\Entities\AlertGeofence as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentAlertGeofenceRepository extends EloquentRepository implements AlertGeofenceRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }

    public function deleteWhereAlertId($alert_id) {
        return Entity::where('alert_id', $alert_id)->delete();
    }
}