<?php namespace Tobuli\Validation;

class AdminNewUserDefaultsFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'update' => [
            'devices_limit' => 'integer',
            'subscription_expiration_after_days' => 'integer'
        ],
    ];

}   //end of class


//EOF