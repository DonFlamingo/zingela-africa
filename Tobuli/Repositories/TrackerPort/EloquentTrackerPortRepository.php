<?php namespace Tobuli\Repositories\TrackerPort;

use Tobuli\Entities\TrackerPort as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentTrackerPortRepository extends EloquentRepository implements TrackerPortRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }
}