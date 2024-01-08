<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Update195 {
    use \Tobuli\Updates\UpdateTransport;

    function setUp() 
	{
		if ( !Schema::hasTable('report_logs') )
		{
            DB::statement("CREATE TABLE report_logs (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`user_id` int(10) unsigned NOT NULL,
				`title` varchar(255) NOT NULL,
				`type` tinyint(3) NOT NULL,
				`format` varchar(10) NOT NULL,
				`size` int(10) unsigned NOT NULL,
				`is_send` tinyint(1) NOT NULL DEFAULT '0',
				`data` longtext  NOT NULL,
				`created_at` TIMESTAMP,
				`updated_at` TIMESTAMP,
				PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
				
			DB::statement("ALTER TABLE report_logs ADD CONSTRAINT report_logs_user_id_index FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE;");
        }
        
        return 'OK';
    }
}