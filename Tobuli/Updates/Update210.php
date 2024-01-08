<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Update210 {
    use \Tobuli\Updates\UpdateTransport;

    function setUp()
    {
        try {
            DB::statement("ALTER TABLE reports ADD daily_time VARCHAR(5) NOT NULL DEFAULT '00:00' AFTER daily_email_sent, ADD weekly_time VARCHAR(5) NOT NULL DEFAULT '00:00' AFTER daily_time;");
        }
        catch (\Exception $e) {}

        return 'OK';
    }
}