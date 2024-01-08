<?php namespace Tobuli\Entities;

use Eloquent;

class DeviceExpense extends Eloquent {
	protected $table = 'expenses';

    protected $fillable = array(
        'user_id',
        'name',
        'date',
        'quantity',
        'cost',
        'supplier',
        'buyer',
        'odometer',
        'engine_hours',
        'description',
    );

    public $timestamps = false;

    public function devices() {
        return $this->belongsToMany('Tobuli\Entities\Device', 'expenses_device_pivot', 'expense_id', 'device_id');
    }

    public function user() {
        return $this->hasOne('Tobuli\Entities\User', 'id', 'user_id');
    }
}
