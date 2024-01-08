<?php namespace Tobuli\Repositories\Event;

use Tobuli\Repositories\EloquentRepositoryInterface;

interface EventRepositoryInterface extends EloquentRepositoryInterface {

    public function whereUserId($user_id);

    public function findWithAttributes($id);

    public function getHigherTime($user_id, $time);

    public function search($data);

    public function getBetween($user_id, $device_id, $from, $to);
}