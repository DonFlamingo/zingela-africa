<?php namespace Tobuli\Validation;

class UserAccountSettingsFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'update' => [
            'timezone_id' => 'required|exists:timezones,id',
            'unit_of_distance' => 'required|in:km,mi',
            'unit_of_capacity' => 'required|in:lt,gl'
        ]
    ];
}   //end of class


//EOF