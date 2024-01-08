<?php namespace Tobuli\Validation;

class ExpensesFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'name' => 'required',
            'devices' => 'required|array',
            'date' => 'required|date',
            'quantity' => 'required|numeric',
            'cost' => 'required|numeric'
        ],
        'update' => [
            'name' => 'required',
            'devices' => 'required|array',
            'date' => 'required|date',
            'quantity' => 'required|numeric',
            'cost' => 'required|numeric'
        ]
    ];

}   //end of class


//EOF