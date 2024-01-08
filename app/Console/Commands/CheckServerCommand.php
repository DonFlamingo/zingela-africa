<?php namespace App\Console\Commands;
ini_set('memory_limit', '-1');
set_time_limit(0);

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tobuli\Entities\Config;

use Exception;
use Illuminate\Support\Facades\Redis as Redis5;
class CheckServerCommand extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'server:check';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

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
	 public function fire(Config $config)
 	{
 		$traccar_restart = '';
 		try {
 			$autodetect = ini_get('auto_detect_line_endings');
 			ini_set('auto_detect_line_endings', '1');
 			$lines = file('/var/spool/cron/root', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
 			ini_set('auto_detect_line_endings', $autodetect);
 			foreach ($lines as $key => $line) {
 				if (strpos($line, 'tracker:restart') !== false) {
 					list($time) = explode('php', $line);
 					$traccar_restart = trim($time);
 					break;
 				}
 				//$text .= $line."\r\n";
 			}
 		}
 		catch(\Exception $e) {

 		}
 		$host= gethostname();
 		$ip = gethostbyname($host);

 		if (!is_numeric(substr($ip, 0, 1))) {
 			$command = "/sbin/ifconfig eth0 | grep \"inet addr\" | awk -F: '{print $2}' | awk '{print $1}'";
 			$ip = exec($command);
 		}

 		$cfg = DB::table('configs')->where('title', '=', 'jar_version')->first();
 		if (empty($cfg)) {
 			DB::table('configs')->insert([
 				'title' => 'jar_version',
 				'value' => 1
 			]);
 		}
 		$jar_version = empty($cfg) ? 1 : $cfg->value;

 		$cores = exec("nproc");
 		$ram_used = round(exec("free | awk 'FNR == 3 {print $3/1000000}'"), 2);
 		$ram_all = round(exec("free | awk 'FNR == 3 {print ($3+$4)/1000000}'"), 2);
 		$disk_total = disk_total_space("/");
 		$disk_free = disk_free_space("/");
 		$disk_used = $disk_total - $disk_free;
 		$traccar_status = boolval(strpos(exec("sudo /opt/traccar/bin/traccar status"), 'traccar is running') !== false) ? 1 : 0;
 		$server_version = '1.80';

 		$date = date('Y-m-d H:i:s', time() - 360);
 		$devices = DB::connection('traccar_mysql')->table('devices')->select(DB::Raw('COUNT(id) as nr'))->where('server_time', '>', $date)->orWhere('ack_time', '>', $date)->first();

 		$server_ver_config = DB::table('configs')->where('title', '=', 'server_version')->first();
 		if (!empty($server_ver_config))
 			$server_version = json_decode($server_ver_config->value, TRUE)['version'];

         try {
             //$redis = new \Redis();
			 $redis = Redis5;
             $redis->connect('127.0.0.1', 6379);
             $redis->get('testing.conncetion');
         }
         catch (\Exception $e) {
             $redis = FALSE;
         }

         // Check if memcached php module loaded
         $memcached = class_exists('Memcached');

         // Check if memcached php server is up
         $memcachedServerRunning = false;
         if ($memcached) {
             try {
                 $memcachedStats = Cache::store('memcached')->getMemcached()->getStats();
                 $memcachedServerRunning = true;
             } catch ( Exception $e) {}
         }
 				$redis_keys = $redis ? count( $redis->keys('position.*') ) : 0;
 				$redis_status = $redis ? 1 : 0;

 				$memcached_module_status = $memcached ? 1 : 0 ;
 				$memcached_server_status = $memcachedServerRunning ? 1 : 0;

 			 $this->line('java version =>'  . $jar_version);
 			 $this->line('server_version =>' .  $server_version);
 			 $this->line('app_version => ' . config('tobuli.version'));
 			 $this->line('cores => ' . $cores);
 			 $this->line('ram => ' . $ram_used . ' used - ' . $ram_all . ' total');
 			 $this->line('disk => ' . $disk_used . ' used - ' . $disk_total . ' total');
 			 $this->line('traccar_restart => ' . $traccar_restart);
 			 $this->line('traccar_status => ' . $traccar_status);
 			 $this->line('redis_status => ' . $redis_status);
 			 $this->line('redis_keys => '. $redis_keys);
 			 $this->line('memcached_module_status => ' . $memcached_module_status);
 			 $this->line('memcached_server_status => ' . $memcached_server_status);
 			 $this->line('cores => ' . $cores);
 			 $this->line('devices_online => ' . $devices->nr);
 			 $this->line('admin_user => ' . $_ENV['admin_user']);
 			 $this->line('name => ' . $_ENV['server']);
 			 $this->line('type => ' . config('tobuli.type'));
 			 $this->line('ip => ' . $ip);

 		$config = $config->whereIn('title', ['last_ports_modification', 'last_config_modification'])->get()->lists('value', 'title')->all();

 		if (!isset($config['last_ports_modification'])) {
 			DB::table('configs')->insert([
 				'title' => 'last_ports_modification',
 				'value' => 0
 			]);
 			$config['last_ports_modification'] = 0;
 		}

 		if (!isset($config['last_config_modification'])) {
 			DB::table('configs')->insert([
 				'title' => 'last_config_modification',
 				'value' => 0
 			]);
 			$config['last_config_modification'] = 0;
 		}

 		$date = date('Y-m-d H:i:s', strtotime('-1 days'));
 		DB::statement("DELETE FROM sms_events_queue WHERE created_at < '{$date}'");

 		$this->line('OK');
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
