<?php namespace Tobuli\Validation;

use Illuminate\Validation\Factory as IlluminateValidator;

class AdminDatabaseClearFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules;

    function __construct( IlluminateValidator $validator ) {
        parent::__construct( $validator );

        $this->rules = [
            'update' => [
                'status' => 'integer',
                'days'   => 'required|integer|min:' . config('tobuli.min_database_clear_days'),
            ]
        ];
    }

}   //end of class
