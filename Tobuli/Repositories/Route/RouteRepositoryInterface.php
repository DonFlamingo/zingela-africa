<?php namespace Tobuli\Repositories\Route;

use Tobuli\Repositories\EloquentRepositoryInterface;

interface RouteRepositoryInterface extends EloquentRepositoryInterface {

    public function whereUserId($user_id);

    public function updateWithPolyline($id, $data);

}