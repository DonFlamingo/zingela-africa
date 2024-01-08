<?php namespace Tobuli\Entities;

use Eloquent;

class DeviceGroup extends Eloquent {
	protected $table = 'device_groups';

    protected $fillable = array('title', 'user_id');

    public $timestamps = false;

}
