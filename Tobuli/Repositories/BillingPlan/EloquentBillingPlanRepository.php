<?php namespace Tobuli\Repositories\BillingPlan;

use Tobuli\Entities\BillingPlan as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentBillingPlanRepository extends EloquentRepository implements BillingPlanRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }
}