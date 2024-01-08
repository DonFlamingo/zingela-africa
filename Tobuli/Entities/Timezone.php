<?php namespace Tobuli\Entities;

use Eloquent;

class Timezone extends Eloquent {

	protected $table = 'timezones';

	protected $fillable = array('title', 'zone', 'order', 'prefix', 'time');

	public $timestamps = false;

    public function getZoneAttribute($value)
    {
        return $value ?: '+0hours';
    }
}