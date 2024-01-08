<?php namespace Tobuli\Repositories\User;

use Tobuli\Repositories\EloquentRepositoryInterface;

interface UserRepositoryInterface extends EloquentRepositoryInterface {
    public function getOtherManagers($user_id);

    public function getDevices($user_id);

    public function getDevicesSms($user_id);

    public function getDrivers($user_id);
}