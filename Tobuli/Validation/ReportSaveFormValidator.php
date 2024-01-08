<?php namespace Tobuli\Validation;

class ReportSaveFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'devices' => 'required|array',
            'title' => 'required',
            'speed_limit' => 'numeric',
            'geofences' => 'array'
        ]
    ];

}   //end of class


//EOF