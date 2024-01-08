<?php namespace Tobuli\Validation;

class ServiceFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'name' => 'required',
            'interval' => 'required_if:odometer_interval_ac,1|numeric|min:1',
            //'last_service' => 'numeric|min:0',
            'trigger_event_left' => 'numeric|min:1|lesser_than:interval',
        ],
        'update' => [
            'name' => 'required',
            'interval' => 'required_if:odometer_interval_ac,1|numeric|min:1',
            //'last_service' => 'numeric|min:0',
            'trigger_event_left' => 'numeric|min:1|lesser_than:interval',
        ],
    ];

}   //end of class


//EOF