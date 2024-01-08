<?php namespace Tobuli\Entities;

use Eloquent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Device extends Eloquent {
	protected $table = 'devices';

    protected $fillable = array(
        'deleted',
        'traccar_device_id',
        'name',
        'imei',
        'icon_id',
        'fuel_measurement_id',
        'parking_mode',
        'fuel_quantity',
        'fuel_price',
        'fuel_per_km',
        'sim_number',
        'device_model',
        'plate_number',
        'vin',
        'registration_number',
        'object_owner',
        'additional_notes',
        'expiration_date',
        'tail_color',
        'tail_length',
        'engine_hours',
        'detect_engine',
        'min_moving_speed',
        'min_fuel_fillings',
        'min_fuel_thefts',
        'snap_to_road',
        'gprs_templates_only',
        'icon_colors',
        'parameters'
    );

    protected $appends = [
        //'lat',
        //'lng',
        //'speed',
        //'course',
        //'altitude',
        //'protocol',
        //'time'
    ];

    private $_traccarData;

    public function icon()
    {
        return $this->hasOne('Tobuli\Entities\DeviceIcon', 'id', 'icon_id');
    }

    public function traccar()
    {
        return $this->hasOne('Tobuli\Entities\TraccarDevice', 'id', 'traccar_device_id');
    }

    public function alerts() {
        return $this->hasMany('Tobuli\Entities\AlertDevice', 'device_id');
    }

    public function events() {
        return $this->hasMany('Tobuli\Entities\Event', 'device_id');
    }

    public function users() {
        return $this->belongsToMany('Tobuli\Entities\User', 'user_device_pivot', 'device_id', 'user_id')->withPivot('group_id', 'timezone_id', 'current_driver_id', 'current_events');
    }

    public function driver() {
        return $this->belongsToMany('Tobuli\Entities\UserDriver', 'user_device_pivot', 'device_id', 'current_driver_id');
    }

    public function sensors() {
        return $this->hasMany('Tobuli\Entities\DeviceSensor', 'device_id');
    }

    public function services() {
        return $this->hasMany('Tobuli\Entities\DeviceService', 'device_id');
    }

    public function setIconColorsAttribute($value)
    {
        $this->attributes['icon_colors'] = json_encode($value);
    }

    public function getIconColorsAttribute($value)
    {
        return json_decode($value, TRUE);
    }

    public function isExpired()
    {
        return $this->expiration_date != '0000-00-00' && strtotime($this->expiration_date) < strtotime(date('Y-m-d'));
    }


    public function getSpeed() {
        $speed = 0;

        if (isset($this->traccarData->speed) && $this->getStatus() == 'online')
            $speed = Auth::User()->unit_of_distance == 'mi' ? kilometersToMiles($this->traccarData->speed) : $this->traccarData->speed;

        return round($speed);
    }
    public function getStatus()
    {
        return getDeviceStatus($this);
    }
    public function getStatusColor()
    {
        return getDeviceStatusColor($this, $this->getStatus());
    }
    public function getTraccarDataAttribute()
    {
        if ( ! isset($this->_traccarData) ) {
            $this->_traccarData = $this->traccar;
        }

        return $this->_traccarData;
    }

    public function getTimeAttribute()
    {
        if ($this->isExpired())
            return trans('front.expired');

        $time = $this->traccarData && $this->traccarData->time ? $this->traccarData->time : null;

        if (empty($time) || substr($time, 0, 4) == '0000')
            return trans('front.not_connected');

        $timezone = null;

        if ( $this->pivot ) {
            $timezones = \Facades\Repositories\TimezoneRepo::getList();
            $timezone = isset($timezones[$this->pivot->timezone_id]) ? $timezones[$this->pivot->timezone_id] : NULL;
        }

        return datetime($time, TRUE, $timezone);
    }

    public function getOnlineAttribute() {
        return $this->getStatus();
    }

    public function getLatAttribute() {
        return cord(isset($this->traccarData->lastValidLatitude) ? $this->traccarData->lastValidLatitude : 0);
    }
    public function getLngAttribute() {
        return cord(isset($this->traccarData->lastValidLongitude) ? $this->traccarData->lastValidLongitude : 0);
    }
    public function getCourseAttribute() {
        $course = 0;

        if (isset($this->traccarData->course))
            $course = $this->traccarData->course;

        return round($course);
    }
    public function getAltitudeAttribute() {
        $altitude = 0;

        if (isset($this->traccarData->altitude))
            $altitude = Auth::User()->unit_of_altitude == 'ft' ? metersToFeets($this->traccarData->altitude) : $this->traccarData->altitude;

        return round($altitude);
    }
    public function getTailAttribute() {
        $tail_length = $this->getStatus() ? $this->tail_length : 0;

        return prepareDeviceTail(isset($this->traccarData->latest_positions) ? $this->traccarData->latest_positions : '', $tail_length);
    }

    public function getProtocolAttribute() {
        return (isset($this->traccarData->protocol) && Auth::User()->perm('protocol', 'view')) ? $this->traccarData->protocol : null;
    }

    public function getTimestampAttribute() {
        if ($this->isExpired())
            return 0;

        return isset($this->traccarData->server_time) ? strtotime($this->traccarData->server_time) : 0;
    }

    public function getAckTimeAttribute() {
        if ($this->isExpired())
            return 0;

        return isset($this->traccarData->ack_time) ? strtotime($this->traccarData->ack_time) : 0;
    }

    public function getServerTimeAttribute() {
        if ($this->isExpired())
            return null;

        return isset($this->traccarData->server_time) ? $this->traccarData->server_time : null;
    }

    public function getFormatSensors() {
        $parameters = isset($this->traccarData->other) ? $this->traccarData->other : null;

        $values = [
            'odometer' => [
                'value' => 0,
                'sufix' => ''
            ],
            'engine_hours' => [
                'value' => 0,
                'sufix' => ''
            ]
        ];

        return formatSensors($parameters, $this->sensors, $values);
    }

    public function getFormatServices() {
        $values = [
            'odometer' => [
                'value' => 0,
                'sufix' => ''
            ],
            'engine_hours' => [
                'value' => 0,
                'sufix' => ''
            ]
        ];

        return formatServices($this->services, $values);
    }


}
