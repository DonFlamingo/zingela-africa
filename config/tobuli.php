<?php
return [
    'version' => '3.0.10.10',

    'key'  => env('key', 'Hdaiohaguywhga12344hdsbsdsfsd'),
    'type' => env('APP_TYPE', 'ss3'),

    'logs_path' => env('logs_path', '/opt/traccar/logs'),

    'main_settings' => [
        'server_name' => env('title', 'GPS Tracker'),
        'available_maps' => [
            '1' => 1,
            '4' => 4,
            '5' => 5,
            '2' => 2,
        ],
        'default_language' => 'en',
        'default_timezone' => 16,
        'default_date_format' => 'Y-m-d',
        'default_time_format' => 'H:i:s',
        'default_unit_of_distance' => 'km',
        'default_unit_of_capacity' => 'lt',
        'default_unit_of_altitude' => 'mt',
        'default_map' => 1,
        'default_object_online_timeout' => 5,
        'allow_users_registration' => 0,

        'devices_limit' => 5,
        'subscription_expiration_after_days' => 30,
        'enable_plans' => 0,
        'payment_type' => 1,
        'paypal_client_id' => '',
        'paypal_secret' => '',
        'paypal_currency' => '',
        'paypal_payment_name' => '',
        'default_billing_plan' => '',
        'dst' => NULL,
        'dst_date_from' => '',
        'dst_date_to' => '',
        'geocoder_api' => 'default',
        'api_key' => '',
        'map_center_latitude' => '51.505',
        'map_center_longitude' => '-0.09',
        'map_zoom_level' => 19,
        'user_permissions' => [],
        'geocoder_cache_enabled' => 1,
        'geocoder_cache_days' => 90,

        'template_color' => 'light-blue',
        'welcome_text' => null,
        'bottom_text' => null,
        'apple_store_link' => null,
        'google_play_link' => null,
    ],

  # Minutes before device is offline
    'device_offline_minutes' => 3,
    #Groups
    'group_admin' => 1,
    'group_client' => 2,
    'alert_zones' => [
        '1' => 'Zone in',
        '2' => 'Zone Out'
    ],
    'alert_fuel_type' => [
        '1' => 'L',
        '2' => 'Gal'
    ],
    'alert_distance' => [
        '1' => 'km',
        '2' => 'mi'
    ],
    'history_time' => [
        '00:00' => '00:00', '00:15' => '00:15', '00:30' => '00:30', '00:45' => '00:45', '01:00' => '01:00', '01:15' => '01:15', '01:30' => '01:30', '01:45' => '01:45',
        '02:00' => '02:00', '02:15' => '02:15', '02:30' => '02:30', '02:45' => '02:45', '03:00' => '03:00', '03:15' => '03:15', '03:30' => '03:30', '03:45' => '03:45',
        '04:00' => '04:00', '04:15' => '04:15', '04:30' => '04:30', '04:45' => '04:45', '05:00' => '05:00', '05:15' => '05:15', '05:30' => '05:30', '05:45' => '05:45',
        '06:00' => '06:00', '06:15' => '06:15', '06:30' => '06:30', '06:45' => '06:45', '07:00' => '07:00', '07:15' => '07:15', '07:30' => '07:30', '07:45' => '07:45',
        '08:00' => '08:00', '08:15' => '08:15', '08:30' => '08:30', '08:45' => '08:45', '09:00' => '09:00', '09:15' => '09:15', '09:30' => '09:30', '09:45' => '09:45',
        '10:00' => '10:00', '10:15' => '10:15', '10:30' => '10:30', '10:45' => '10:45', '11:00' => '11:00', '11:15' => '11:15', '11:30' => '11:30', '11:45' => '11:45',
        '12:00' => '12:00', '12:15' => '12:15', '12:30' => '12:30', '12:45' => '12:45', '13:00' => '13:00', '13:15' => '13:15', '13:30' => '13:30', '13:45' => '13:45',
        '14:00' => '14:00', '14:15' => '14:15', '14:30' => '14:30', '14:45' => '14:45', '15:00' => '15:00', '15:15' => '15:15', '15:30' => '15:30', '15:45' => '15:45',
        '16:00' => '16:00', '16:15' => '16:15', '16:30' => '16:30', '16:45' => '16:45', '17:00' => '17:00', '17:15' => '17:15', '17:30' => '17:30', '17:45' => '17:45',
        '18:00' => '18:00', '18:15' => '18:15', '18:30' => '18:30', '18:45' => '18:45', '19:00' => '19:00', '19:15' => '19:15', '19:30' => '19:30', '19:45' => '19:45',
        '20:00' => '20:00', '20:15' => '20:15', '20:30' => '20:30', '20:45' => '20:45', '21:00' => '21:00', '21:15' => '21:15', '21:30' => '21:30', '21:45' => '21:45',
        '22:00' => '22:00', '22:15' => '22:15', '22:30' => '22:30', '22:45' => '22:45', '23:00' => '23:00', '23:15' => '23:15', '23:30' => '23:30', '23:45' => '23:45'
    ],
    'frontend' => 'http://www.atrams.com',
    'frontend_shop' => 'http://www.atrams.com/en/gps-trackers-shop',
    'frontend_login' => 'http://www.atrams.com/en/sign-in',
    'frontend_subscriptions' => 'http://www.atrams.com/subscriptions',
    'frontend_curl' => 'http://www.atrams.com/api/',
    'frontend_curl_password' => '{6M/cEF$]mVhP?zLa',
    'frontend_url' => 'http://www.atrams.com/addons/shared_addons/themes/atrams/',
    'frontend_change_password' => 'http://www.atrams.com/en/change_password?email=',

    'maps' => [
        'Google Normal' => 1,
        'OpenStreetMap' => 2,
        'Google Hybrid' => 3,
        'Google Satellite' => 4,
        'Google Terrain' => 5,
        'Yandex' => 6,
        /*'Bing Road' => 7,
        'Bing Aerial' => 8,
        'Bing Hybrid' => 9,*/
    ],
    /*
    'plans' => [
        '1' => trans('front.plan_1').' (1 '.trans('front.objects').')',
        '5' => trans('front.plan_2').' (1-5 '.trans('front.objects').')',
        '25' => trans('front.plan_3').' (1-25 '.trans('front.objects').')',
        '29' => trans('front.plan_4').' (1-29 '.trans('front.objects').')',
    ] */
    'plans' => [],
    'min_database_clear_days' => 30,
    'max_history_period_days' => 100,
    'demos' => [],
    'additional_protocols' => [
        'gpsdata' => 'gpsdata',
        'ios' => 'ios',
        'android' => 'android'
    ],
    'protocols' => [
        'gps103' => 'gps103',
        'tk103' => 'tk103',
        'gl100' => 'gl100',
        'gl200' => 'gl200',
        't55' => 't55',
        'xexun' => 'xexun',
        'totem' => 'totem',
        'enfora' => 'enfora',
        'meiligao' => 'meiligao',
        'maxon' => 'maxon',
        'suntech' => 'suntech',
        'progress' => 'progress',
        'h02' => 'h02',
        'jt600' => 'jt600',
        'ev603' => 'ev603',
        'v680' => 'v680',
        'pt502' => 'pt502',
        'tr20' => 'tr20',
        'navis' => 'navis',
        'meitrack' => 'meitrack',
        'skypatrol' => 'skypatrol',
        'gt02' => 'gt02',
        'gt06' => 'gt06',
        'megastek' => 'megastek',
        'navigil' => 'navigil',
        'gpsgate' => 'gpsgate',
        'teltonika' => 'teltonika',
        'mta6' => 'mta6',
        'mta6can' => 'mta6can',
        'tlt2h' => 'tlt2h',
        'syrus' => 'syrus',
        'wondex' => 'wondex',
        'cellocator' => 'cellocator',
        'galileo' => 'galileo',
        'ywt' => 'ywt',
        'tk102' => 'tk102',
        'intellitrac' => 'intellitrac',
        'xt7' => 'xt7',
        'wialon' => 'wialon',
        'carscop' => 'carscop',
        'apel' => 'apel',
        'manpower' => 'manpower',
        'globalsat' => 'globalsat',
        'atrack' => 'atrack',
        'pt3000' => 'pt3000',
        'ruptela' => 'ruptela',
        'topflytech' => 'topflytech',
        'laipac' => 'laipac',
        'aplicom' => 'aplicom',
        'gotop' => 'gotop',
        'sanav' => 'sanav',
        'gator' => 'gator',
        'noran' => 'noran',
        'm2m' => 'm2m',
        'osmand' => 'osmand',
        'easytrack' => 'easytrack',
        'taip' => 'taip',
        'khd' => 'khd',
        'piligrim' => 'piligrim',
        'stl060' => 'stl060',
        'cartrack' => 'cartrack',
        'minifinder' => 'minifinder',
        'haicom' => 'haicom',
        'eelink' => 'eelink',
        'box' => 'box',
        'freedom' => 'freedom',
        'telik' => 'telik',
        'trackbox' => 'trackbox',
        'visiontek' => 'visiontek',
        'orion' => 'orion',
        'riti' => 'riti',
        'ulbotech' => 'ulbotech',
        'tramigo' => 'tramigo',
        'tr900' => 'tr900',
        'ardi01' => 'ardi01',
        'xt013' => 'xt013',
        'autofon' => 'autofon',
        'gosafe' => 'gosafe',
        'autofon45' => 'autofon45',
        'bce' => 'bce',
        'xirgo' => 'xirgo',
        'calamp' => 'calamp',
        'mtx' => 'mtx',
        'gpsdata' => 'gpsdata'
    ],
    'sensors' => [],
    'units_of_distance' => [],
    'units_of_capacity' => [],

    'units_of_altitude' => [],
    'date_formats' => [
        'Y-m-d' => 'yyyy-mm-dd',
        'm-d-Y' => 'mm-dd-yyyy',
        'd-m-Y' => 'dd-mm-yyyy'
    ],
    'time_formats' => [
        'H:i:s' => '24 hour clock',
        'h:i:s A' => 'AM/PM',
    ],
    'object_online_timeouts' => [],
    'zoom_levels' => [
        '19' => '19', '18' => '18', '17' => '17', '16' => '16', '15' => '15', '14' => '14', '13' => '13', '12' => '12', '11' => '11', '10' => '10', '9' => '9', '8' => '8', '7' => '7', '6' => '6', '5' => '5', '4' => '4', '3' => '3', '2' => '2', '1' => '1', '0' => '0',
    ],
    'permissions' => [
        'devices' => [
            'view' => 1,
            'edit' => 1,
            'remove' => 1,
        ],
        'alerts' => [
            'view' => 1,
            'edit' => 1,
            'remove' => 1,
        ],
        'geofences' => [
            'view' => 1,
            'edit' => 1,
            'remove' => 1,
        ],
        'routes' => [
            'view' => 1,
            'edit' => 1,
            'remove' => 1,
        ],
        'poi' => [
            'view' => 1,
            'edit' => 1,
            'remove' => 1,
        ],
        'sms_gateway' => [
            'view' => 1,
            'edit' => 0,
            'remove' => 0,
        ],
        'protocol' => [
            'view' => 1,
            'edit' => 0,
            'remove' => 0,
        ],
        'send_command' => [
            'view' => 1,
            'edit' => 0,
            'remove' => 0,
        ],
        'history' => [
            'view' => 1,
            'edit' => 0,
            'remove' => 1,
        ],
    ],
    'permissions_modes' => [
        'view' => 1,
        'edit' => 1,
        'remove' => 1
    ],

    'numeric_sensors' => [
        'battery',
        'temperature',
        'tachometer',
        'fuel_tank_calibration',
        'fuel_tank',
        'satellites',
        'odometer',
        'gsm'
    ],
    'listview_fields' => [
        'name' => [
            'field' => 'name',
            'class' => 'device'
        ],
        'imei' => [
            'field' => 'imei',
            'class' => 'device'
        ],
        'status' => [
            'field' => 'status',
            'class' => 'device'
        ],
        'speed' => [
            'field' => 'speed',
            'class' => 'device'
        ],
        'time' => [
            'field' => 'time',
            'class' => 'device'
        ],
        'protocol' => [
            'field' => 'protocol',
            'class' => 'device'
        ],
        'position' => [
            'field' => 'position',
            'class' => 'device'
        ],
        'address' => [
            'field' => 'address',
            'class' => 'device'
        ],
        'sim_number' => [
            'field' => 'sim_number',
            'class' => 'device'
        ],
        'device_model' => [
            'field' => 'device_model',
            'class' => 'device'
        ],
        'plate_number' => [
            'field' => 'plate_number',
            'class' => 'device'
        ],
        'vin' => [
            'field' => 'vin',
            'class' => 'device'
        ],
        'registration_number' => [
            'field' => 'registration_number',
            'class' => 'device'
        ],
        'object_owner' => [
            'field' => 'object_owner',
            'class' => 'device'
        ],
        'group' => [
            'field' => 'group',
            'class' => 'device'
        ],
    ],
    'listview' => [
        'groupby' => 'protocol',
        'columns' => [
            'name' => [
                'field' => 'name',
                'class' => 'device'
            ],
            'status' => [
                'field' => 'status',
                'class' => 'device'
            ],
            'time' => [
                'field' => 'time',
                'class' => 'device'
            ],
            'position' => [
                'field' => 'position',
                'class' => 'device'
            ]
        ]
    ],

    'plugins' => [
        'show_object_info_after' => [
            'status' => 0,
        ],
        'object_listview' => [
            'status' => 0,
        ],
        'business_private_drive' => [
            'status' => 0,
            'options' => [
                'business_color' => [
                    'value' => 'blue'
                ],
                'private_color' => [
                    'value' => 'red'
                ]
            ]
        ],
    ],

    'process' => [
        'insert_timeout' => env('PROC_INSERT_TIMEOUT', 60),
        'insert_limit' => env('PROC_INSERT_LIMIT', 10),
        'reportdaily_timeout' => env('PROC_REPORT_TIMEOUT', 180),
        'reportdaily_limit' => env('PROC_REPORT_LIMIT', 2),
    ],

    'template_colors' => [
        'light-blue'        => 'Light Blue',
        'light-green'       => 'Light Green',
        'light-red'         => 'Light Red',
        'light-orange'      => 'Light Orange',
        'light-win10-blue'  => 'Light Win10 Blue',
    ],

    'widgets' => [
        'default' => true,
        'status' => true,
        'list' => [
            'device', 'sensors', 'services'
        ]
    ],
];