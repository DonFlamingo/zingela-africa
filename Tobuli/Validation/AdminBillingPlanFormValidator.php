<?php namespace Tobuli\Validation;

class AdminBillingPlanFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'title' => 'required',
            'price' => 'required|numeric',
            'objects' => 'required|integer',
            'duration_value' => 'required|integer',
            'duration_value' => 'required|integer',
        ]
    ];

}   //end of class


//EOF