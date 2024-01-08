<?php namespace Tobuli\Validation;

class AdminEmailSettingsFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'use_smtp_server_0' => [
            'from_name' => 'required',
            'noreply_email' => 'required|email',
        ],
        'use_smtp_server_1' => [
            'from_name' => 'required',
            'noreply_email' => 'required',
            'smtp_server_host' => 'required',
            'smtp_server_port' => 'required|numeric',
            'smtp_username' => 'required',
            'smtp_password' => 'required'
        ],
        'sendgrid' => [
            'from_name' => 'required',
            'noreply_email' => 'required',
            'api_key' => 'required',
        ],
        'mailgun' => [
            'from_name' => 'required',
            'noreply_email' => 'required',
            'api_key' => 'required',
            'domain' => 'required',
        ],
    ];

}   //end of class


//EOF