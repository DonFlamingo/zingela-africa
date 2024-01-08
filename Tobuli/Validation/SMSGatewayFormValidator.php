<?php namespace Tobuli\Validation;

class SMSGatewayFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'app' => [
        ],
        'post' => [
        ],
        'get' => [
        ],
        'plivo' => [
            'auth_id' => 'required',
            'auth_token' => 'required',
            'senders_phone' => 'required',
        ],
    ];

}   //end of class


//EOF