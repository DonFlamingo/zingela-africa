<?php namespace Tobuli\Validation;

class SendCommandGprsFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'positionPeriodic' => [
            'device_id' => 'required',
            'frequency' => 'required|numeric',
        ],
        'engineStop' => [],
        'engineResume' => [],
        'positionStop' => [],
        'positionSingle' => [],
        'positionPeriodic' => [],
        'rebootDevice' => [],
        'requestPhoto' => [],
        'alarmArm' => [],
        'alarmDisarm' => [],
        'movementAlarm' => [
            'parameter' => 'required'
        ],
        'setTimezone' => [
            'parameter' => 'required|integer'
        ],
        'custom' => [
            'message' => 'required'
        ],
        'sendSms' => [
            'message' => 'required',
            'sim_number' => 'required'
        ],
        # Watch
        'watch_sosNumber' => [
            'phone_number' => 'required'
        ],
        'watch_alarmSos' => [
            'action' => 'required'
        ],
        'watch_alarmBattery' => [
            'action' => 'required'
        ],
        'watch_alarmRemove' => [
            'action' => 'required'
        ],
        'watch_rebootDevice' => [],
        'watch_silenceTime' => [
            'time' => 'required'
        ],
        'watch_alarmClock' => [
            'order' => 'required'
        ],
        'watch_setPhonebook' => [
            'order' => 'required'
        ],
        'watch_requestPhoto' => [],
        'watch_custom' => [
            'message' => 'required'
        ],
        # PT502
        'pt502_engineStop' => [
            'password' => 'required'
        ],
        'pt502_engineResume' => [
            'password' => 'required'
        ],
        'pt502_doorOpen' => [
            'password' => 'required'
        ],
        'pt502_doorClose' => [
            'password' => 'required'
        ],
        'pt502_requestPhoto' => [],
        'pt502_custom' => [
            'message' => 'required'
        ],
    ];

}   //end of class


//EOF