<?php namespace Tobuli\Repositories\SmsEventQueue;

use Tobuli\Entities\SmsEventQueue as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentSmsEventQueueRepository extends EloquentRepository implements SmsEventQueueRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }
}