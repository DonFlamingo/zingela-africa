<?php namespace Tobuli\Validation;

class SensorFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'acc' => [
            'sensor_name' => 'required',
            'sensor_type' => 'required',
            'tag_name' => 'required',
            'on_value' => 'required',
            'off_value' => 'required',
        ],
        'acc_setflag' => [
            'sensor_name' => 'required',
            'sensor_type' => 'required',
            'tag_name' => 'required',
            'on_setflag_1' => 'required|numeric|min:0',
            'on_setflag_2' => 'required|numeric|min:1',
            'on_setflag_3' => 'required',
            'off_setflag_1' => 'required|numeric|min:0',
            'off_setflag_2' => 'required|numeric|min:1',
            'off_setflag_3' => 'required',
        ],
        'battery' => [
            'sensor_name' => 'required',
            'sensor_type' => 'required',
            'tag_name' => 'required',
        ],
        'door' => [
            'sensor_name' => 'required',
            'sensor_type' => 'required',
            'tag_name' => 'required',
            'on_tag_value' => 'required',
            'off_tag_value' => 'required',
        ],
        'door_setflag' => [
            'sensor_name' => 'required',
            'sensor_type' => 'required',
            'tag_name' => 'required',
            'on_tag_setflag_1' => 'required|numeric|min:0',
            'on_tag_setflag_2' => 'required|numeric|min:1',
            'on_tag_setflag_3' => 'required',
            'off_tag_setflag_1' => 'required|numeric|min:0',
            'off_tag_setflag_2' => 'required|numeric|min:1',
            'off_tag_setflag_3' => 'required',
        ],
        'engine' => [
            'sensor_name' => 'required',
            'sensor_type' => 'required',
            'tag_name' => 'required',
            'on_tag_value' => 'required',
            'off_tag_value' => 'required',
        ],
        'engine_setflag' => [
            'sensor_name' => 'required',
            'sensor_type' => 'required',
            'tag_name' => 'required',
            'on_tag_setflag_1' => 'required|numeric|min:0',
            'on_tag_setflag_2' => 'required|numeric|min:1',
            'on_tag_setflag_3' => 'required',
            'off_tag_setflag_1' => 'required|numeric|min:0',
            'off_tag_setflag_2' => 'required|numeric|min:1',
            'off_tag_setflag_3' => 'required',
        ],
        'fuel_tank' => [
            'sensor_name' => 'required',
            'sensor_type' => 'required',
            'tag_name' => 'required',
            'full_tank' => 'required',
            'full_tank_value' => 'required',
        ],
        'fuel_tank_calibration' => [
            'sensor_name' => 'required',
            'sensor_type' => 'required',
            'tag_name' => 'required',
            'calibrations' => 'required|array|array_max:50',
        ],
        'gsm' => [
            'sensor_name' => 'required',
            'sensor_type' => 'required',
            'tag_name' => 'required',
            'min_value' => 'required|numeric|min:0',
            'max_value' => 'required|numeric|min:0',
        ],
        'odometer' => [
            'sensor_name' => 'required',
            'sensor_type' => 'required',
        ],
        'satellites' => [
            'sensor_name' => 'required',
            'sensor_type' => 'required',
            'tag_name' => 'required',
        ],
        'tachometer' => [
            'sensor_name' => 'required',
            'sensor_type' => 'required',
            'tag_name' => 'required',
            'formula' => 'required'
        ],
        'temperature' => [
            'sensor_name' => 'required',
            'sensor_type' => 'required',
            'tag_name' => 'required',
            'formula' => 'required',
        ],
        'ignition' => [
            'sensor_name' => 'required',
            'sensor_type' => 'required',
            'tag_name' => 'required',
            'on_tag_value' => 'required',
            'off_tag_value' => 'required',
        ],
        'ignition_setflag' => [
            'sensor_name' => 'required',
            'sensor_type' => 'required',
            'tag_name' => 'required',
            'on_tag_setflag_1' => 'required|numeric|min:0',
            'on_tag_setflag_2' => 'required|numeric|min:1',
            'on_tag_setflag_3' => 'required',
            'off_tag_setflag_1' => 'required|numeric|min:0',
            'off_tag_setflag_2' => 'required|numeric|min:1',
            'off_tag_setflag_3' => 'required',
        ],
        'engine_hours' => [
            'sensor_name' => 'required',
            'sensor_type' => 'required',
            'tag_name' => 'required',
        ],
        'harsh_acceleration' => [
            'sensor_name' => 'required',
            'sensor_type' => 'required',
            'tag_name' => 'required',
            'parameter_value' => 'required',
        ],
        'harsh_acceleration_setflag' => [
            'sensor_name' => 'required',
            'sensor_type' => 'required',
            'tag_name' => 'required',
            'value_setflag_1' => 'required|numeric|min:0',
            'value_setflag_2' => 'required|numeric|min:1',
        ],
        'harsh_breaking' => [
            'sensor_name' => 'required',
            'sensor_type' => 'required',
            'tag_name' => 'required',
            'parameter_value' => 'required',
        ],
        'harsh_breaking_setflag' => [
            'sensor_name' => 'required',
            'sensor_type' => 'required',
            'tag_name' => 'required',
            'value_setflag_1' => 'required|numeric|min:0',
            'value_setflag_2' => 'required|numeric|min:1',
        ],
        'drive_business' => [
            'sensor_name' => 'required',
            'sensor_type' => 'required',
            'tag_name' => 'required',
            'on_tag_value' => 'required',
            'off_tag_value' => 'required',
        ],
        'drive_private' => [
            'sensor_name' => 'required',
            'sensor_type' => 'required',
            'tag_name' => 'required',
            'on_tag_value' => 'required',
            'off_tag_value' => 'required',
        ],
    ];

}   //end of class


//EOF