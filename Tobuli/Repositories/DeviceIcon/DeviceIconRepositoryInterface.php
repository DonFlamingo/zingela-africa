<?php namespace Tobuli\Repositories\DeviceIcon;

use Tobuli\Repositories\EloquentRepositoryInterface;

interface DeviceIconRepositoryInterface extends EloquentRepositoryInterface {
    public function whereNotInFirst($ids);
}