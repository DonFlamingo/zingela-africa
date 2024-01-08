<?php namespace Tobuli\Validation;

class AdminSensorGroupFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'title' => 'required|unique:sensor_groups,title',
        ],
        'update' => [
            'title' => 'required|unique:sensor_groups,title,%s',
        ]
    ];

}   //end of class


//EOF