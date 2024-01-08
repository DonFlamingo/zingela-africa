<?php namespace Tobuli\Validation;

class EventCustomFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'protocol' => 'required',
            'message' => 'required',
        ],
        'update' => [
            'protocol' => 'required',
            'message' => 'required',
        ]
    ];

}   //end of class


//EOF