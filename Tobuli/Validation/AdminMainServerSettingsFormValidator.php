<?php namespace Tobuli\Validation;

class AdminMainServerSettingsFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'update' => [
            'server_name' => 'required',
            'default_maps' => 'required',
        ]
    ];

}   //end of class


//EOF