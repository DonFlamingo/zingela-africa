<?php namespace Tobuli\Repositories\Device;

use Tobuli\Repositories\EloquentRepositoryInterface;

interface DeviceRepositoryInterface extends EloquentRepositoryInterface {

    public function whereUserId($user_id);

    public function userCount($user_id);

    public function updateWhereIconIds($ids, $data);

    public function whereImei($imei);

    public function clearCache($imei, $prefix);

}