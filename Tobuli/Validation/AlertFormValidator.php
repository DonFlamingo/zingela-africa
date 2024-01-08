<?php namespace Tobuli\Validation;

class AlertFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'name' => 'required',
            //'email' => 'required',
            'devices' => 'required|array',
            'geofences' => 'array'
        ],
        'update' => [
            'name' => 'required',
            //'email' => 'required',
            'devices' => 'required|array',
            'geofences' => 'array'
        ]
    ];

}   //end of class


//EOF