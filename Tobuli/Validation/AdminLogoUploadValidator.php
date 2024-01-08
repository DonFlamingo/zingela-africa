<?php namespace Tobuli\Validation;

class AdminLogoUploadValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'update' => [
            'login_page_logo' => 'image|mimes:jpeg,jpg,png,gif|max:20000',
            'frontpage_logo' => 'image|mimes:jpeg,jpg,png,gif|max:20000',
            'favicon' => 'mimes:ico|max:2000',
            'background' => 'mimes:jpeg,jpg,png,gif|max:20000',

            'welcome_text' => 'max:255',
            'bottom_text' => 'max:2000',
            'apple_store_link' => 'url',
            'google_play_link' => 'url',
        ]
    ];

}   //end of class


//EOF