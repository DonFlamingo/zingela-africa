<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Update202 {
    use \Tobuli\Updates\UpdateTransport;

    function setUp()
    {
        if (!Schema::hasColumn('alert_geofence', 'time_from')) {
            DB::statement("ALTER TABLE alert_geofence ADD time_from VARCHAR(5) NOT NULL DEFAULT '00:00' AFTER geofence_id, ADD time_to VARCHAR(5) NOT NULL DEFAULT '00:00' AFTER time_from;");
        }

        return 'OK';
    }
}