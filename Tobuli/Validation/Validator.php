<?php namespace Tobuli\Validation;

use Illuminate\Validation\Factory as IlluminateValidator;
use Tobuli\Exceptions\ValidationException;

/**
 * Base Validation class. All entity specific validation classes inherit
 * this class and can override any function for respective specific needs
 */
abstract class Validator {

    /**
     * @var Illuminate\Validation\Factory
     */
    protected $_validator;

    public function __construct( IlluminateValidator $validator ) {
        $this->_validator = $validator;
    }

    public function validate($name, array $data, $id = NULL) {
        $rules = $this->rules[$name];
        !is_null($id) && $rules = $this->applyId($rules, $id);


        //use Laravel's Validator and validate the data
        $validation = $this->_validator->make( $data, $rules);

        if ( $validation->fails() ) {
            //validation failed, throw an exception
            throw new ValidationException( $validation->messages() );
        }

        //all good and shiny
        return true;
    }

    private function applyId($rules, $id) {
        return array_map(function($item) use($id) {
            return sprintf($item, $id);
        }, $rules);
    }

} //end of class

//EOF