<?php namespace Tobuli\Validation;

class ClientFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
            'available_maps' => 'required',
            'devices_limit' => 'integer',
            'expiration_date' => 'date'
        ],
        'update' => [
            'email' => 'required|email|unique:users,email,%s',
            'password' => 'confirmed',
            'available_maps' => 'required',
            'devices_limit' => 'integer',
            'expiration_date' => 'date'
        ]
    ];

}   //end of class


//EOF