<?php namespace Tobuli\Entities;

use Eloquent;

class TraccarDevice extends Eloquent {
    protected $connection = 'traccar_mysql';

	protected $table = 'devices';

    protected $fillable = array(
        'name',
        'uniqueId',
        'latestPosition_id',
        'lastValidLatitude',
        'lastValidLongitude',
        'server_time',
        'ack_time',
        'time',
        'speed',
        'other',
        'altitude',
        'power',
        'course',
        'address',
        'protocol',
        'latest_positions'
    );

    public $timestamps = false;

}
