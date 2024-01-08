<?php namespace Tobuli\Repositories\Timezone;

use Tobuli\Repositories\EloquentRepositoryInterface;

interface TimezoneRepositoryInterface extends EloquentRepositoryInterface {
    public function order();
    public function getList();
}