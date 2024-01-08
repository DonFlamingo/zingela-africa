<?php namespace Tobuli\Repositories\SensorGroupSensor;

use Tobuli\Entities\SensorGroupSensor as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentSensorGroupSensorRepository extends EloquentRepository implements SensorGroupSensorRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }
}