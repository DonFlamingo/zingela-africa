<?php namespace Tobuli\Validation;

class ObjectsListSettingsFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'update' => [
            'columns' => 'required|array'
        ]
    ];

}   //end of class


//EOF