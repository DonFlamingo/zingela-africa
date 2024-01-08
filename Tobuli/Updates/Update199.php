<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Update199 {
    use \Tobuli\Updates\UpdateTransport;

    function setUp()
    {
        try {
            DB::connection('traccar_mysql')->statement("ALTER TABLE devices DROP INDEX time;");
            DB::connection('traccar_mysql')->statement("ALTER TABLE devices ADD INDEX (time);");
        }
        catch (Exception $e) {}

        return 'OK';
    }
}