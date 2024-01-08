<?php namespace Tobuli\Validation;

class HistoryFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'device_id' => 'required',
            'from_date' => 'required|date',
            'to_date'   => 'required|date',
            'from_time' => 'required|date_format:"H:i"',
            'to_time'   => 'required|date_format:"H:i"',
        ]
    ];

}
