<?php namespace Tobuli\Repositories\GeofenceGroup;

use Tobuli\Repositories\EloquentRepositoryInterface;

interface GeofenceGroupRepositoryInterface extends EloquentRepositoryInterface {
    public function deleteUsersWhereNotIn($arr, $user_id, $id = 'id');
}