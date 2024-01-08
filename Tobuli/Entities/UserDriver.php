<?php namespace Tobuli\Entities;

use Eloquent;

class UserDriver extends Eloquent {
	protected $table = 'user_drivers';

    protected $fillable = array(
        'user_id',
        'device_id',
        'name',
        'rfid',
        'phone',
        'email',
        'description'
    );

    public function device() {
        return $this->hasOne('Tobuli\Entities\Device', 'id', 'device_id');
    }
}
