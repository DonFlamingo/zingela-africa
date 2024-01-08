<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Update198 {
    use \Tobuli\Updates\UpdateTransport;

    function setUp()
    {
        if (!Schema::hasTable('users_dst')) {
            DB::statement("CREATE TABLE users_dst (
  user_id int(10) unsigned NOT NULL,
  country_id int(10) unsigned NOT NULL,
  type varchar(10) NOT NULL,
  date_from varchar(11) DEFAULT NULL,
  date_to varchar(11) DEFAULT NULL,
  month_from varchar(15) DEFAULT NULL,
  week_pos_from varchar(15) DEFAULT NULL,
  week_day_from varchar(15) DEFAULT NULL,
  time_from varchar(5) DEFAULT NULL,
  month_to varchar(15) DEFAULT NULL,
  week_pos_to varchar(15) DEFAULT NULL,
  week_day_to varchar(15) DEFAULT NULL,
  time_to varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
            DB::statement("ALTER TABLE users_dst
  ADD PRIMARY KEY (user_id,type) USING BTREE,
  ADD KEY country_id (country_id);");
            DB::statement("ALTER TABLE users_dst
  MODIFY country_id int(10) unsigned NOT NULL AUTO_INCREMENT;");
            DB::statement("ALTER TABLE users_dst
  ADD CONSTRAINT users_dst_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE;");
        }

        if (!Schema::hasTable('timezones_dst')) {
            DB::statement("CREATE TABLE timezones_dst (
  id int(10) unsigned NOT NULL,
  country varchar(50) NOT NULL,
  from_period varchar(50) NOT NULL,
  from_time varchar(5) DEFAULT NULL,
  to_period varchar(50) NOT NULL,
  to_time varchar(5) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=latin1;");
            DB::statement("INSERT INTO timezones_dst (id, country, from_period, from_time, to_period, to_time) VALUES
(1, 'Akrotiri and Dhekelia(UK)', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(2, 'Albania', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(3, 'Andorra', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(4, 'Australia', 'First Sunday of October', NULL, 'First Sunday of April', NULL),
(5, 'Austria', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(6, 'Bahamas', 'Second Sunday of March', NULL, 'First Sunday November', NULL),
(7, 'Belgium', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(8, 'Bermuda (UK)', 'Second Sunday of March', NULL, 'First Sunday of November', NULL),
(9, 'Bosnia and Herzegovina', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(10, 'Brazil', 'Third Sunday of October', NULL, 'Third Sunday of February', NULL),
(11, 'Bulgaria', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(12, 'Canada', 'Second Sunday of March', NULL, 'First Sunday of November', NULL),
(13, 'Chile', 'August 13', NULL, 'May 14', NULL),
(14, 'Croatia', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(15, 'Cuba', 'Second Sunday of March', NULL, 'First Sunday of November', NULL),
(16, 'Cyprus', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(17, 'Czech Republic', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(18, 'Denmark', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(19, 'Egypt', 'July 8', NULL, 'Last friday of October', NULL),
(20, 'Estonia', 'last Sunday of March', NULL, 'last Sunday of October', NULL),
(21, 'Faroe Islands (DK)', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(22, 'Fiji', 'First Sunday of November', NULL, 'Third Sunday of January', NULL),
(23, 'Finland', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(24, 'France', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(25, 'Germany', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(26, 'Greece', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(27, 'Greenland (DK)', 'last Saturday of March', '22:00', 'last Saturday of October', '23:00'),
(28, 'Guernsey (UK)', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(29, 'Holy See', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(30, 'Hungary', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(31, 'Iran', 'March 21', NULL, 'September 21', NULL),
(32, 'Ireland', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(33, 'Isle of Man (UK)', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(34, 'Israel', 'Last Friday of March', NULL, 'Last Friday of October', NULL),
(35, 'Italy', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(36, 'Jersey (UK)', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(37, 'Jordan', 'Last Friday of March', NULL, 'Last Friday of October', NULL),
(38, 'Kosovo', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(39, 'Latvia', 'last Sunday of March', '01:00', 'last Sunday of  October', '01:00'),
(40, 'Lebanon', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(41, 'Liechtenstein', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(42, 'Lithuania', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(43, 'Luxembourg', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(44, 'Macedonia', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(45, 'Malta', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(46, 'Mexico', 'First Sunday of April', NULL, 'Last Sunday of October', NULL),
(47, 'Moldova', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(48, 'Monaco', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(49, 'Mongolia', 'Last Saturday of March', NULL, 'Last Saturday of September', NULL),
(50, 'Montenegro', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(51, 'Morocco', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(52, 'Namibia', 'First Sunday of September', NULL, 'First Sunday of April', NULL),
(53, 'Netherlands', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(54, 'New Zealand', 'Last Sunday of September', NULL, 'First Sunday of April', NULL),
(55, 'Norway', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(56, 'Paraguay', 'First Sunday of October', NULL, 'Fourth Sunday of March', NULL),
(57, 'Poland', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(58, 'Portugal', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(59, 'Romania', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(60, 'Saint Pierre and Miquelon?(FR)', 'Second Sunday of March', NULL, 'First Sunday of November', NULL),
(61, 'Samoa', 'Last Sunday of September', NULL, 'First Sunday of April', NULL),
(62, 'San Marino', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(63, 'Serbia', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(64, 'Slovakia', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(65, 'Slovenia', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(66, 'Spain', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(67, 'Sweden', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(68, 'Switzerland', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(69, 'Syria', 'Last Friday of March', NULL, 'Last Friday of October', NULL),
(70, 'Turkey', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(71, 'Ukraine', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(72, 'United Kingdom', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(73, 'United States', 'Second Sunday of March', NULL, 'First Sunday of November', NULL),
(74, 'Western Sahara', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL);");

            DB::statement("ALTER TABLE timezones_dst
  ADD PRIMARY KEY (id);");
            DB::statement("ALTER TABLE timezones_dst
  MODIFY id int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=75;");
        }

        if (Schema::hasColumn('users', 'dst_date_from')) {
            $users = DB::table('users')->whereNotNull('dst_date_from')->get();
            foreach ($users as $user) {
                try {
                    list($from_date, $from_time) = explode(' ', $user->dst_date_from);
                    list($to_date, $to_time) = explode(' ', $user->dst_date_to);
                }
                catch (Exception $e) {
                    continue;
                }
                DB::table('users_dst')->insert([
                    'user_id' => $user->id,
                    'type' => 'exact',
                    'date_from' => $from_date,
                    'time_from' => $from_time,
                    'date_to' => $to_date,
                    'time_to' => $to_time
                ]);
            }

            DB::statement("ALTER TABLE users DROP dst_date_from;");
            DB::statement("ALTER TABLE users DROP dst_date_to;");
        }

        return 'OK';
    }
}