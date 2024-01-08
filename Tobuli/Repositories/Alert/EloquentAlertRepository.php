<?php namespace Tobuli\Repositories\Alert;

use Tobuli\Entities\Alert as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentAlertRepository extends EloquentRepository implements AlertRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }

    public function whereUserId($user_id) {
        return Entity::where('user_id', $user_id)->get();
    }

    public function findWithAttributes($id) {
        return Entity::where('id', $id)->with('geofences', 'devices', 'fuel_consumptions', 'events_custom')->first();
    }
}