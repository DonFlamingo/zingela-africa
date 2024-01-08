<?php namespace Tobuli\Repositories\TraccarDevice;

use Tobuli\Repositories\EloquentRepositoryInterface;

interface TraccarDeviceRepositoryInterface extends EloquentRepositoryInterface {

    public function getWhereTime($time);

}