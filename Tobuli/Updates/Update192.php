<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Update192 {
    use \Tobuli\Updates\UpdateTransport;

    function setUp() {
        if (!Schema::hasColumn('device_sensors', 'odometer_value_unit')) {
            DB::statement("ALTER TABLE device_sensors ADD odometer_value_unit VARCHAR(2) NOT NULL DEFAULT 'km' AFTER odometer_value;");
        }
        
        return 'OK';
    }
}