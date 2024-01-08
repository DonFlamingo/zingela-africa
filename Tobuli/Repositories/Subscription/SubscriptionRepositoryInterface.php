<?php namespace Tobuli\Repositories\Subscription;

use Tobuli\Repositories\EloquentRepositoryInterface;

interface SubscriptionRepositoryInterface extends EloquentRepositoryInterface {
    public function whereTrial();
}