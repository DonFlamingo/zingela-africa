<?php namespace Tobuli\Repositories\Geofence;

use Tobuli\Repositories\EloquentRepositoryInterface;

interface GeofenceRepositoryInterface extends EloquentRepositoryInterface {

    public function whereUserId($user_id);

    public function updateWithPolygon($id, $data);

}