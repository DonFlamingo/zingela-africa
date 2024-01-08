<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Update208 {
    use \Tobuli\Updates\UpdateTransport;

    function setUp()
    {
        try {
            DB::statement("ALTER TABLE users ADD week_start_day TINYINT(3) NOT NULL DEFAULT '1';");
        }
        catch (\Exception $e) {}

        return 'OK';
    }
}