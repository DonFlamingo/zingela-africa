<?php namespace Tobuli\Validation;

class RegistrationFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'email' => 'required|email|unique:users,email'
        ],
        'update' => [
        ]
    ];

}   //end of class


//EOF