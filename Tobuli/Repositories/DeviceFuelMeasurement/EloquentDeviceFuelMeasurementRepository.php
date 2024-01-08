<?php namespace Tobuli\Repositories\DeviceFuelMeasurement;

use Tobuli\Entities\DeviceFuelMeasurement as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentDeviceFuelMeasurementRepository extends EloquentRepository implements DeviceFuelMeasurementRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }

}