<?php namespace Tobuli\Validation;

class UserAccountFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'update' => [
            'email' => 'required|email|unique:users,email,%s',
            'password' => 'confirmed'
        ]
    ];
}   //end of class


//EOF