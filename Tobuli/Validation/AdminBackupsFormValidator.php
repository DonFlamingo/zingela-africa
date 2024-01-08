<?php namespace Tobuli\Validation;

class AdminBackupsFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'update' => [
            'ftp_server' => 'required',
            'ftp_port' => 'required|integer',
            'ftp_username' => 'required',
            'ftp_path' => 'required',
            'period' => 'required',
            'hour' => 'required',
        ]
    ];

}   //end of class


//EOF