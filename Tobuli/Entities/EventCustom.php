<?php namespace Tobuli\Entities;

use Eloquent;

class EventCustom extends Eloquent {
	protected $table = 'events_custom';

    protected $fillable = array(
        'user_id',
        'protocol',
        'conditions',
        'message',
        'always'
    );

    public $timestamps = false;

    public function setConditionsAttribute($value)
    {
        $this->attributes['conditions'] = serialize($value);
    }

    public function getConditionsAttribute($value)
    {
        return unserialize($value);
    }

    public function tags() {
        return $this->hasMany('Tobuli\Entities\EventCustomTag', 'event_custom_id', 'id');
    }
}
