<?php

class Update191 {
    use \Tobuli\Updates\UpdateTransport;

    function setUp() {
        $users = DB::table('users')
            ->leftJoin('user_permissions', function($query) {
                $query->on('users.id', '=', 'user_permissions.user_id');
                $query->where('user_permissions.name', '=', 'devices');
            })
            ->whereNull('user_permissions.name')
            ->get();

        foreach ($users as $user) {
            DB::table('user_permissions')->insert([
                'user_id' => $user->id,
                'name' => 'devices',
                'view' => 1,
                'edit' => 0,
                'remove' => 0,
            ]);
        }
        
        return 'OK';
    }
}