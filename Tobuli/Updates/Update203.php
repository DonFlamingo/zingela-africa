<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Update203 {
    use \Tobuli\Updates\UpdateTransport;

    function setUp()
    {
        try {
            DB::statement("DELETE user_device_pivot FROM user_device_pivot LEFT JOIN users ON user_device_pivot.user_id = users.id WHERE users.id is NULL;");
            DB::statement("ALTER TABLE user_device_pivot ADD FOREIGN KEY (user_id) REFERENCES tracking_web.users(id) ON DELETE CASCADE ON UPDATE RESTRICT;");
        }
        catch (\Exception $e) {}

        return 'OK';
    }
}