<?php namespace Tobuli\Repositories\TraccarPosition;

interface TraccarPositionRepositoryInterface {

    public function search($user_id, $data, $paginate = FALSE, $limit = 50);

    public function sumDistance($device_id, $range);

    public function sumDistanceHigher($device_id, $date_to);

    public function getOldest($device_id);

    public function getNewer($device_id, $position_id);

    public function getBetween($device_id, $from, $to);

}