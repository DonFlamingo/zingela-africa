<?php namespace Tobuli\Entities;

use Eloquent;

class DeviceIcon extends Eloquent {
	protected $table = 'device_icons';

    protected $fillable = array('path', 'width', 'height', 'type');

    protected $casts = ['id' => 'integer', 'order' => 'integer', 'width' => 'float', 'height' => 'float'];

    public $timestamps = false;

}
