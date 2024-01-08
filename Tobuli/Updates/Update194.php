<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Update194 {
    use \Tobuli\Updates\UpdateTransport;

    function setUp() {
        if (!Schema::hasColumn('users', 'dst_date_from')) {
            DB::statement("ALTER TABLE users ADD dst_date_from VARCHAR(11) NULL, ADD dst_date_to VARCHAR(11) NULL AFTER dst_date_from;");
        }
        
        return 'OK';
    }
}