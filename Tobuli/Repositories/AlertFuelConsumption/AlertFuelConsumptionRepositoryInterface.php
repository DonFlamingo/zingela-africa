<?php namespace Tobuli\Repositories\AlertFuelConsumption;

use Tobuli\Repositories\EloquentRepositoryInterface;

interface AlertFuelConsumptionRepositoryInterface extends EloquentRepositoryInterface {
    public function deleteWhereAlertId($alert_id);
    public function wherePeriodNotDone();
}