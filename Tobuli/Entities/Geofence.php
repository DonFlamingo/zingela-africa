<?php namespace Tobuli\Entities;

use Eloquent;

class Geofence extends Eloquent {
	protected $table = 'geofences';

    protected $fillable = array('user_id', 'group_id', 'name', 'active', 'polygon_color');

}
