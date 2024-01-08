<?php namespace Tobuli\Repositories\SensorGroup;

use Tobuli\Entities\SensorGroup as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentSensorGroupRepository extends EloquentRepository implements SensorGroupRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }
}