<?php namespace Tobuli\Repositories\AlertFuelConsumption;

use Tobuli\Entities\AlertFuelConsumption as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentAlertFuelConsumptionRepository extends EloquentRepository implements AlertFuelConsumptionRepositoryInterface {

    public function __construct( Entity $entity ) {
        $this->entity = $entity;
    }

    public function deleteWhereAlertId($alert_id) {
        return Entity::where('alert_id', $alert_id)->delete();
    }

    public function wherePeriodNotDone() {
        return Entity::whereRaw('`to` < CURDATE() AND `done` = 0')->with('alert', 'alert.user')->orderBy('id', 'desc')->limit(15)->get();
    }
}