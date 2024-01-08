<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Update205 {
    use \Tobuli\Updates\UpdateTransport;

    function setUp()
    {
        DB::statement("ALTER TABLE device_sensors CHANGE odometer_value odometer_value FLOAT(13,2) UNSIGNED NULL DEFAULT NULL;");

        return 'OK';
    }
}