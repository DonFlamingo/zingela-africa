<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Update209 {
    use \Tobuli\Updates\UpdateTransport;

    function setUp()
    {
        try {
            DB::statement("ALTER TABLE devices ADD gprs_templates_only BOOLEAN NOT NULL DEFAULT FALSE;");
        }
        catch (\Exception $e) {}

        return 'OK';
    }
}