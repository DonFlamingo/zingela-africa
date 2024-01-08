<?php namespace Tobuli\Repositories\GeofenceGroup;

use Tobuli\Entities\GeofenceGroup as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentGeofenceGroupRepository extends EloquentRepository implements GeofenceGroupRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }

    public function deleteUsersWhereNotIn($arr, $user_id, $id = 'id') {
        return $this->entity->where('user_id', $user_id)->whereNotIn($id, $arr)->delete();
    }
}