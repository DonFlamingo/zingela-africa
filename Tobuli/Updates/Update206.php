<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Update206 {
    use \Tobuli\Updates\UpdateTransport;

    function setUp()
    {
        try {
            DB::connection('traccar_mysql')->statement("ALTER TABLE unregistered_devices_log ADD port INT(10) NULL AFTER imei, ADD ip VARCHAR(50) NULL AFTER port;");
        }
        catch (\Exception $e) {}

        return 'OK';
    }
}