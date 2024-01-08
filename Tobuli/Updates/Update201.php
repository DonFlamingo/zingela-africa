<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Update201 {
    use \Tobuli\Updates\UpdateTransport;

    function setUp()
    {
        $users = DB::table('users')->select('users.id')->leftJoin('user_permissions', function ($query) {
            $query->on('users.id', '=', 'user_permissions.user_id');
            $query->where('user_permissions.name', '=', 'poi');
        })->whereNull('user_permissions.name')->get();

        foreach ($users as $user) {
            DB::table('user_permissions')->insert([
                'user_id' => $user->id,
                'name' => 'alerts',
                'view' => 1,
                'edit' => 1,
                'remove' => 1
            ]);

            DB::table('user_permissions')->insert([
                'user_id' => $user->id,
                'name' => 'geofences',
                'view' => 1,
                'edit' => 1,
                'remove' => 1
            ]);

            DB::table('user_permissions')->insert([
                'user_id' => $user->id,
                'name' => 'routes',
                'view' => 1,
                'edit' => 1,
                'remove' => 1
            ]);

            DB::table('user_permissions')->insert([
                'user_id' => $user->id,
                'name' => 'poi',
                'view' => 1,
                'edit' => 1,
                'remove' => 1
            ]);
        }

        return 'OK';
    }
}