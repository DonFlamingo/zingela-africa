<?php

class Update190 {
    use \Tobuli\Updates\UpdateTransport;

    function setUp() {
        if (!Schema::hasTable('billing_plans')) {
            DB::statement("CREATE TABLE billing_plans (
  id int(10) unsigned NOT NULL,
  title varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  price double(8,2) unsigned NOT NULL,
  objects int(10) unsigned NOT NULL,
  duration_type varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  duration_value int(10) unsigned NOT NULL,
  days int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
            DB::statement("ALTER TABLE billing_plans
  ADD PRIMARY KEY (id);");
            DB::statement("ALTER TABLE billing_plans
  MODIFY id int(10) unsigned NOT NULL AUTO_INCREMENT;");
        }
        if (!Schema::hasTable('billing_plan_permissions')) {
            DB::statement("CREATE TABLE billing_plan_permissions (
plan_id int(10) unsigned NOT NULL,
name varchar(50) NOT NULL,
view tinyint(1) NOT NULL DEFAULT '0',
edit tinyint(1) NOT NULL DEFAULT '0',
remove tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
            DB::statement("ALTER TABLE billing_plan_permissions
ADD KEY plan_id (plan_id);");
            DB::statement("ALTER TABLE billing_plan_permissions
ADD CONSTRAINT billing_plan_permissions_ibfk_1 FOREIGN KEY (plan_id) REFERENCES billing_plans (id) ON DELETE CASCADE;");
        }

        if (!Schema::hasTable('user_permissions')) {
            DB::statement("CREATE TABLE user_permissions (
  user_id int(10) unsigned NOT NULL,
  name varchar(50) NOT NULL,
  view tinyint(1) NOT NULL DEFAULT '0',
  edit tinyint(1) NOT NULL DEFAULT '0',
  remove tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
            DB::statement("ALTER TABLE user_permissions
  ADD KEY user_id (user_id);");
            DB::statement("ALTER TABLE user_permissions
  ADD CONSTRAINT user_permissions_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE;");
        }

        if (Schema::hasTable('user_permissions')) {
            $users = DB::table('users')->get();
            foreach ($users as $user) {
                $perms = [];
                if ($user->permission_to_add_devices) {
                    $perms[] = [
                        'user_id' => $user->id,
                        'name' => 'devices',
                        'view' => 1,
                        'edit' => 1,
                        'remove' => 1,
                    ];
                }

                if ($user->permission_to_view_history) {
                    $perms[] = [
                        'user_id' => $user->id,
                        'name' => 'history',
                        'view' => 1,
                        'edit' => 0,
                        'remove' => 0,
                    ];
                }

                if ($user->permission_to_view_protocol) {
                    $perms[] = [
                        'user_id' => $user->id,
                        'name' => 'protocol',
                        'view' => 1,
                        'edit' => 0,
                        'remove' => 0,
                    ];
                }

                if ($user->permission_to_send_command) {
                    $perms[] = [
                        'user_id' => $user->id,
                        'name' => 'send_command',
                        'view' => 1,
                        'edit' => 0,
                        'remove' => 0,
                    ];
                }

                if ($user->permission_to_use_sms_gateway) {
                    $perms[] = [
                        'user_id' => $user->id,
                        'name' => 'sms_gateway',
                        'view' => 1,
                        'edit' => 0,
                        'remove' => 0,
                    ];
                }

                if (!empty($perms))
                    DB::table('user_permissions')->insert($perms);

                if (!empty($perms) && Schema::hasColumn('users', 'permission_to_add_devices')) {
                    DB::table('users')->where('id', '=', $user->id)->update([
                        'permission_to_add_devices' => 0,
                        'permission_to_view_history' => 0,
                        'permission_to_view_protocol' => 0,
                        'permission_to_send_command' => 0,
                        'permission_to_use_sms_gateway' => 0,
                    ]);
                }
            }
        }

        if (Schema::hasColumn('users', 'permission_to_add_devices'))
            DB::statement("ALTER TABLE users DROP COLUMN permission_to_add_devices;");

        if (Schema::hasColumn('users', 'permission_to_view_history'))
            DB::statement("ALTER TABLE users DROP COLUMN permission_to_view_history;");

        if (Schema::hasColumn('users', 'permission_to_view_protocol'))
            DB::statement("ALTER TABLE users DROP COLUMN permission_to_view_protocol;");

        if (Schema::hasColumn('users', 'permission_to_send_command'))
            DB::statement("ALTER TABLE users DROP COLUMN permission_to_send_command;");

        if (Schema::hasColumn('users', 'permission_to_use_sms_gateway'))
            DB::statement("ALTER TABLE users DROP COLUMN permission_to_use_sms_gateway;");



        if (Schema::hasTable('billing_plan_permissions') && Schema::hasColumn('billing_plans', 'permissions')) {
            $plans = DB::table('billing_plans')->get();
            foreach ($plans as $plan) {
                $perms = [];
                $plan_perms = json_decode($plan->permissions, TRUE);
                if (empty($plan_perms))
                    continue;

                if ($plan_perms['permission_to_add_devices']) {
                    $perms[] = [
                        'plan_id' => $plan->id,
                        'name' => 'devices',
                        'view' => 1,
                        'edit' => 1,
                        'remove' => 1,
                    ];
                }

                if ($plan_perms['permission_to_view_history']) {
                    $perms[] = [
                        'plan_id' => $plan->id,
                        'name' => 'history',
                        'view' => 1,
                        'edit' => 0,
                        'remove' => 0,
                    ];
                }

                if ($plan_perms['permission_to_view_protocol']) {
                    $perms[] = [
                        'plan_id' => $plan->id,
                        'name' => 'protocol',
                        'view' => 1,
                        'edit' => 0,
                        'remove' => 0,
                    ];
                }

                if ($plan_perms['permission_to_send_command']) {
                    $perms[] = [
                        'plan_id' => $plan->id,
                        'name' => 'send_command',
                        'view' => 1,
                        'edit' => 0,
                        'remove' => 0,
                    ];
                }

                if ($plan_perms['permission_to_use_sms_gateway']) {
                    $perms[] = [
                        'plan_id' => $plan->id,
                        'name' => 'sms_gateway',
                        'view' => 1,
                        'edit' => 0,
                        'remove' => 0,
                    ];
                }

                if (!empty($perms))
                    DB::table('billing_plan_permissions')->insert($perms);

                DB::table('billing_plans')->where('id', '=', $plan->id)->update(['permissions' => json_encode([])]);
            }
        }

        if (Schema::hasColumn('billing_plans', 'permissions')) {
            DB::statement("ALTER TABLE billing_plans DROP COLUMN permissions;");
        }

        $settings = unserialize(DB::table('configs')->where('title', 'main_settings')->first()->value);

        $settings['permissions'] = [
            'devices' => array_key_exists('permission_to_add_devices', $settings) && $settings['permission_to_add_devices'] ? 1 : 0,
            'history' => array_key_exists('permission_to_view_history', $settings) && $settings['permission_to_view_history'] ? 1 : 0,
            'protocol' => array_key_exists('permission_to_view_protocol', $settings) && $settings['permission_to_view_protocol'] ? 1 : 0,
            'send_command' => array_key_exists('permission_to_send_command', $settings) && $settings['permission_to_send_command'] ? 1 : 0,
            'sms_gateway' => array_key_exists('permission_to_use_sms_gateway', $settings) && $settings['permission_to_use_sms_gateway'] ? 1 : 0,
        ];

        if (array_key_exists('permission_to_add_devices', $settings))
            unset($settings['permission_to_add_devices']);

        if (array_key_exists('permission_to_view_history', $settings))
            unset($settings['permission_to_view_history']);

        if (array_key_exists('permission_to_view_protocol', $settings))
            unset($settings['permission_to_view_protocol']);

        if (array_key_exists('permission_to_send_command', $settings))
            unset($settings['permission_to_send_command']);

        if (array_key_exists('permission_to_use_sms_gateway', $settings))
            unset($settings['permission_to_use_sms_gateway']);

        DB::table('configs')->where('title', 'main_settings')->update([
            'value' => serialize($settings)
        ]);
        
        return 'OK';
    }
}