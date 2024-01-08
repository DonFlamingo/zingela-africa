<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Update200 {
    use \Tobuli\Updates\UpdateTransport;

    function setUp()
    {
        try {
            DB::statement("ALTER TABLE tracker_ports ADD active BOOLEAN NOT NULL DEFAULT TRUE FIRST, ADD INDEX (active);");
        }
        catch (Exception $e) {}

        return 'OK';
    }
}