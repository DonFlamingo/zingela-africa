<?php namespace Tobuli\Validation;

class UserDriverFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'name' => 'required',
            'email' => 'email',
        ],
        'update' => [
            'name' => 'required',
            'email' => 'email',
        ]
    ];

}   //end of class


//EOF