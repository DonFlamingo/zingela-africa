<?php namespace Tobuli\Validation;

class SendTestSmsFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'mobile_phone' => 'required',
            'message' => 'required',
        ]
    ];

}   //end of class


//EOF