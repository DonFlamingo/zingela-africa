<?php namespace Tobuli\Entities;

use Eloquent;

class DeviceService extends Eloquent {
	protected $table = 'device_services';

    protected $fillable = array(
        'user_id',
        'device_id',
        'name',
        'expiration_by',
        'interval',
        'last_service',
        'trigger_event_left',
        'renew_after_expiration',
        'expires',
        'expires_date',
        'remind',
        'remind_date',
        'event_sent',
        'expired',
        'email',
        'mobile_phone'
    );

    public $timestamps = false;

    public function device() {
        return $this->hasOne('Tobuli\Entities\Device', 'id', 'device_id');
    }

    public function user() {
        return $this->hasOne('Tobuli\Entities\User', 'id', 'user_id');
    }
}
