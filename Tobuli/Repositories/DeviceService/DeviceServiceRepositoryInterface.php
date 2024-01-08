<?php namespace Tobuli\Repositories\DeviceService;

use Tobuli\Repositories\EloquentRepositoryInterface;

interface DeviceServiceRepositoryInterface extends EloquentRepositoryInterface {
    public function getBetween($user_id, $device_id, $from, $to);
}