<?php namespace Tobuli\Validation;

class DeviceIconUploadValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'file' => 'required|mimes:jpeg,gif,png|max:20000'
        ]
    ];

}   //end of class


//EOF