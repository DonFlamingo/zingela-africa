<?php namespace Tobuli\Repositories\AlertGeofence;

use Tobuli\Repositories\EloquentRepositoryInterface;

interface AlertGeofenceRepositoryInterface extends EloquentRepositoryInterface {
    public function deleteWhereAlertId($alert_id);
}