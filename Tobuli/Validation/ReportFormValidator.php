<?php namespace Tobuli\Validation;

class ReportFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'devices' => 'required|array',
            'date_from' => 'required|date',
            'date_to' => 'required|date',
            'speed_limit' => 'numeric',
            'geofences' => 'array'
        ]
    ];

}   //end of class


//EOF