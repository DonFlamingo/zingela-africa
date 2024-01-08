<?php namespace Tobuli\Entities;

use Eloquent;

class Alert extends Eloquent {
	protected $table = 'alerts';

    protected $fillable = array(
        'active',
        'user_id',
        'name',
        'email',
        'mobile_phone',
        'overspeed_speed',
        'overspeed_distance',
        'ac_alarm'
    );

    public function geofences() {
        return $this->belongsToMany('Tobuli\Entities\Geofence')->withPivot('zone', 'time_from', 'time_to');
    }

    public function devices() {
        return $this->belongsToMany('Tobuli\Entities\Device');
    }

    public function fuel_consumptions() {
        return $this->hasMany('Tobuli\Entities\AlertFuelConsumption', 'alert_id');
    }

    public function user() {
        return $this->hasOne('Tobuli\Entities\User', 'id', 'user_id');
    }

    public function drivers() {
        return $this->belongsToMany('Tobuli\Entities\UserDriver', 'alert_driver_pivot', 'alert_id', 'driver_id');
    }

    public function events_custom() {
        return $this->belongsToMany('Tobuli\Entities\EventCustom', 'alert_event_pivot', 'alert_id', 'event_id');
    }
}
