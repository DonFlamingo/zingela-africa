<?php namespace Tobuli\Entities;

use Eloquent;
use Carbon\Carbon;

class Report extends Eloquent {
	protected $table = 'reports';

    protected $fillable = [
        'user_id',
        'title',
        'type',
        'format',
        'show_addresses',
        'zones_instead',
        'stops',
        'speed_limit',
        'daily',
        'daily_time',
        'weekly',
        'weekly_time',
        'email',
        'weekly_email_sent',
        'daily_email_sent',
        'from_format',
        'to_format'
    ];

    protected $appends = array('from_formated', 'to_formated');

    public $timestamps = false;

    public function devices() {
        return $this->belongsToMany('Tobuli\Entities\Device', 'report_device_pivot', 'report_id', 'device_id');
    }

    public function geofences() {
        return $this->belongsToMany('Tobuli\Entities\Geofence', 'report_geofence_pivot', 'report_id', 'geofence_id');
    }

    public function getFromFormatedAttribute()
    {
        if ($this->from_format)
            return Carbon::parse( $this->from_format )->format('Y-m-d H:i');
        else
            return Carbon::parse( '00:00:00' )->format('Y-m-d H:i');
    }

    public function getToFormatedAttribute()
    {
        if ($this->to_format)
            return Carbon::parse( $this->to_format )->format('Y-m-d H:i');
        else
            return Carbon::parse( '23:45:00' )->format('Y-m-d H:i');
    }
}
