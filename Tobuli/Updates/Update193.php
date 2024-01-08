<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Update193 {
    use \Tobuli\Updates\UpdateTransport;

    function setUp() {
        if (!Schema::hasTable('sensor_groups')) {
            DB::statement("CREATE TABLE sensor_groups (
  id int(10) unsigned NOT NULL,
  title varchar(100) NOT NULL,
  count int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
            
            DB::statement("ALTER TABLE sensor_groups
  ADD PRIMARY KEY (id),
  ADD KEY name (title);");
            
            DB::statement("ALTER TABLE sensor_groups
  MODIFY id int(10) unsigned NOT NULL AUTO_INCREMENT;");
        }

        if (!Schema::hasTable('sensor_group_sensors')) {
            DB::statement("CREATE TABLE sensor_group_sensors (
  id int(10) unsigned NOT NULL,
  group_id int(10) unsigned NOT NULL,
  name varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  type varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  tag_name varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  add_to_history tinyint(1) NOT NULL DEFAULT '0',
  on_value varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  off_value varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  shown_value_by varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  fuel_tank_name varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  full_tank varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  full_tank_value varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  min_value varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  max_value varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  formula varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  odometer_value_by varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  odometer_value double(8,2) unsigned DEFAULT NULL,
  odometer_value_unit varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'km',
  temperature_max varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  temperature_max_value varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  temperature_min varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  temperature_min_value varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  value varchar(255) COLLATE utf8_unicode_ci DEFAULT '-',
  value_formula int(11) NOT NULL DEFAULT '0',
  show_in_popup tinyint(1) NOT NULL DEFAULT '0',
  unit_of_measurement varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  on_tag_value varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  off_tag_value varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  on_type tinyint(4) DEFAULT NULL,
  off_type tinyint(4) DEFAULT NULL,
  calibrations mediumtext COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

            DB::statement("ALTER TABLE sensor_group_sensors
  ADD PRIMARY KEY (id),
  ADD KEY sensor_group_sensors_group_id_index (group_id),
  ADD KEY sensor_group_sensors_type_index (type),
  ADD KEY sensor_group_sensors_tag_name_index (tag_name),
  ADD KEY sensor_group_sensors_add_to_history_index (add_to_history),
  ADD KEY sensor_group_sensors_show_in_popup_index (show_in_popup);");

            DB::statement("ALTER TABLE sensor_group_sensors
  MODIFY id int(10) unsigned NOT NULL AUTO_INCREMENT;");
            
            DB::statement("ALTER TABLE sensor_group_sensors
  ADD CONSTRAINT sensor_group_sensors_group_id_foreign FOREIGN KEY (group_id) REFERENCES sensor_groups (id) ON DELETE CASCADE;");
        }
        
        return 'OK';
    }
}