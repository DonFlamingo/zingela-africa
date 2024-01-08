<?php namespace Tobuli\Repositories\PositionGeofence;

use Tobuli\Entities\PositionGeofence as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentPositionGeofenceRepository extends EloquentRepository implements PositionGeofenceRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }
}