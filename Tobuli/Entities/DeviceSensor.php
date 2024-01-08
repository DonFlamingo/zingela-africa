<?php namespace Tobuli\Entities;

use Eloquent;

class DeviceSensor extends Eloquent {
	protected $table = 'device_sensors';

    protected $fillable = array(
        'user_id',
        'device_id',
        'name',
        'type',
        'tag_name',
        'add_to_history',
        'on_value',
        'off_value',
        'shown_value_by',
        'fuel_tank_name',
        'full_tank',
        'full_tank_value',
        'min_value',
        'max_value',
        'formula',
        'odometer_value_by',
        'odometer_value',
        'odometer_value_unit',
        'value',
        'value_formula',
        'show_in_popup',
        'unit_of_measurement',
        'on_tag_value',
        'off_tag_value',
        'on_type',
        'off_type',
        'calibrations'
    );

    public $timestamps = false;

    public function device() {
        return $this->hasOne('Tobuli\Entities\Device', 'id', 'device_id');
    }

    public function getOdometerValueAttribute($value)
    {
        if ($this->odometer_value_unit == 'mi')
            return kilometersToMiles($value);

        return $value;
    }

    public function setCalibrationsAttribute($value)
    {
        $this->attributes['calibrations'] = serialize($value);
    }

    public function getCalibrationsAttribute($value)
    {
        return unserialize($value);
    }

    public function getHashAttribute($value)
    {
        return md5($this->type . $this->name);
    }
}
