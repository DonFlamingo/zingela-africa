<?php namespace Tobuli\Entities;

use Eloquent;

class TraccarPosition extends Eloquent {
    protected $connection = 'traccar_mysql';

	protected $table = 'positions';

    protected $fillable = array();

    public $timestamps = false;

    public function device() {
        return $this->hasOne('Tobuli\Entities\Device', 'traccar_device_id', 'device_id');
    }

    public function getSpeedAttribute($value)
    {
        return float($value);
    }

}
