<?php namespace Tobuli\Entities;

use Eloquent;

class Event extends Eloquent {
	protected $table = 'events';

    protected $fillable = array(
        'user_id',
        'geofence_id',
        'position_id',
        'alert_id',
        'device_id',
        'type',
        'message',
        'latitude',
        'longitude',
        'time',
        'speed',
        'altitude',
        'power',
        'address',
        'deleted'
    );

    public function geofence() {
        return $this->hasOne('Tobuli\Entities\Geofence', 'id', 'geofence_id');
    }

    public function alert() {
        return $this->hasOne('Tobuli\Entities\Alert', 'id', 'alert_id');
    }

    public function device() {
        return $this->hasOne('Tobuli\Entities\Device', 'id', 'device_id');
    }

}
