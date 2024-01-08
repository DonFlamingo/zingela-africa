<?php namespace App\Console\Commands;
ini_set('memory_limit', '-1');
set_time_limit(0);

use Illuminate\Console\Command;

class CompressLogsCommand extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'logs:compress';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Updates server database and configuration to the newest version.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		
		# Traccar
		# copy tracker-server.log to tracker-server.log.yyyymmdd
		$log_dat = date('Ymd', strtotime("-1 days"));
		$files = glob('/opt/traccar/logs/tracker-server.log');
		foreach ($files as $file) {
						
			// delete today log file and generate new
			//@exec('rm -r /opt/traccar/logs/tracker-server.log.'. date('Ymd'));
			if (file_exists("/opt/traccar/logs/tracker-server.log.". $log_dat)) {
				@exec("cat /opt/traccar/logs/tracker-server.log >> ". $file .".". $log_dat);
			}else{
				@exec("cp /opt/traccar/logs/tracker-server.log ". $file .".". $log_dat);	
			}	
			
			
			// Empty log file data
			@exec('truncate -s 0 /opt/traccar/logs/tracker-server.log');
		}
		
        $path = rtrim( config('tobuli.logs_path'), '/') . '/*.log.*';
		$files = glob($path);		
		foreach ($files as $file) {
			$arr = explode('.', $file);
			$ex = end($arr);
			if ($ex == 'gz' || $ex != $log_dat)	
				continue;
			
			@exec('gzip '.$file);	
			
			if (file_exists("/opt/traccar/logs/tracker-server.log.". $log_dat)) {
				@exec('rm -r /opt/traccar/logs/tracker-server.log.'. $log_dat);
			}
			//@exec("cp /opt/traccar/logs/tracker-server.log /opt/traccar/logs/tracker-server.log.". date('Ymd'));
		}

		# HTTPD access
		$files = glob('/var/log/httpd/access_log-*');
		foreach ($files as $file) {
			@exec('gzip '.$file);
		}

		# HTTPD error
		$files = glob('/var/log/httpd/error_log-*');
		foreach ($files as $file) {
			@exec('gzip '.$file);
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}
}
