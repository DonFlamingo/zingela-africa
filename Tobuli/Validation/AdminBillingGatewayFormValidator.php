<?php namespace Tobuli\Validation;

class AdminBillingGatewayFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'update' => [
            'payment_type' => 'required',
            'paypal_client_id' => 'required',
            'paypal_secret' => 'required',
            'paypal_currency' => 'required',
            'paypal_payment_name' => 'required',
        ]
    ];

}   //end of class


//EOF