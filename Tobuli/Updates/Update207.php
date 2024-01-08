<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Update207 {
    use \Tobuli\Updates\UpdateTransport;

    function setUp()
    {
        try {
            DB::connection('traccar_mysql')->statement("ALTER TABLE unregistered_devices_log CHANGE date date TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;");
        }
        catch (\Exception $e) {}

        return 'OK';
    }
}