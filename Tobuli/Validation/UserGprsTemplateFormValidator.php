<?php namespace Tobuli\Validation;

class UserGprsTemplateFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'title' => 'required',
            'message' => 'required'
        ],
        'update' => [
            'title' => 'required',
            'message' => 'required'
        ]
    ];

}   //end of class


//EOF