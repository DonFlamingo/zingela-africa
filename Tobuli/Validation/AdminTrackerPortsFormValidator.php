<?php namespace Tobuli\Validation;

class AdminTrackerPortsFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'update' => [
            'port' => 'required|integer|unique:tracker_ports,port,%s,name',
        ]
    ];

}   //end of class


//EOF