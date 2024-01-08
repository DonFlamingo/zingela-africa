<?php namespace Tobuli\Repositories\AlertDevice;

use Tobuli\Repositories\EloquentRepositoryInterface;

interface AlertDeviceRepositoryInterface extends EloquentRepositoryInterface {
    public function deleteWhereDevicesId(array $ids, $alert_id);

    public function getAlertDevices($alert_id);
}