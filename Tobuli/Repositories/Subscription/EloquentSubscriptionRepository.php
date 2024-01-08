<?php namespace Tobuli\Repositories\Subscription;

use Tobuli\Entities\Subscription as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentSubscriptionRepository extends EloquentRepository implements SubscriptionRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
        $this->searchable = [
            'name',
            'period_name',
            'devices_limit',
            'days',
            'price'
        ];
    }

    public function  whereTrial() {
        return $this->entity->where('trial', 1)->first();
    }
}