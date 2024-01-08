<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Third Party Services
	|--------------------------------------------------------------------------
	|
	| This file is for storing the credentials for third party services such
	| as Stripe, Mailgun, Mandrill, and others. This file provides a sane
	| default location for this type of information, allowing packages
	| to have a conventional place to find your various credentials.
	|
	*/

	'mailgun' => [
		'domain' => '',
		'secret' => '',
	],

	'mandrill' => [
		'secret' => '',
	],

	'ses' => [
		'key' => '',
		'secret' => '',
		'region' => 'us-east-1',
	],

	'stripe' => [
		'model'  => 'App\User',
		'secret' => '',
	],

    'streetView' => [
        'keys' => [
            'AIzaSyBOyK0lNo29oT7NMbSdBqTq5Gjp-I3m8BY',
            'AIzaSyCUnOC5jK2u2Od2qbCNIlk6WRPYWL4YTEw',
            'AIzaSyBhX68rUqWMOM6R74irxUJ8EXxThXjZaNo'
        ]
    ],
];
