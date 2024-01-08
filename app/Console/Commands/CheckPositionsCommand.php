<?php namespace App\Console\Commands;
ini_set('memory_limit', '-1');
set_time_limit(0);

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tobuli\Entities\Config;

use Exception;
use Redis;
class CheckPositionsCommand extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'positions:check';

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

        try {
            //$this->redis = new \Redis();
            //$this->redis->connect('127.0.0.1', 6379);
			$this->redis = Redis;
        }
        catch (\Exception $e) {
            $this->redis = FALSE;
        }
    }

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire(Config $config)
	{
        $devices = [];

        $keys = $this->redis->keys('position.*');

        asort($keys);

        foreach ($keys as $key) {
            $part = explode('.', $key);

            $time = $part[1] / 1000;
            $imei = $part[2];

            if ($time > 1504953144) $time = 0;

            if ( empty($devices[$imei]) ) {
                $devices[$imei] = [
                    'all' => 0,
                    'count' => 0,
                    'imei' => $imei,
                    'last' => $time,
                    'sum' => 0,
                    'min' => PHP_INT_MAX,
                    'max' => 0
                ];
            }

            if (empty($time) or empty($devices[$imei]['last'])) {
                $diff = 0;
            } else {
                $diff = ($time - $devices[$imei]['last']);
            }

            $devices[$imei]['all'] += 1;

            if (!$diff)
                continue;

            $devices[$imei]['count'] += 1;
            $devices[$imei]['sum']   += $diff;
            $devices[$imei]['last']   = $time;
            $devices[$imei]['min']    = $diff < $devices[$imei]['min'] ? $diff : $devices[$imei]['min'];
            $devices[$imei]['max']    = $diff > $devices[$imei]['max'] ? $diff : $devices[$imei]['max'];
        }

        $sorting = [];
        foreach ($devices as $imei => $device) {
            $devices[$imei]['avg'] = $device['count'] ? $device['sum'] / $device['count'] : 0;

            $sorting[$imei] = $device['all'];
        }

        array_multisort($sorting, SORT_DESC, $devices);

        $devices = array_slice($devices, 0, 100);

        foreach ($devices as $device) {
            echo
                "IMEI: " . $device['imei'] . ";" .
                "ALL: " . $device['all'] . ";" .
                "POS: " . $device['count'] . ";" .
                "AVG: " . $device['avg'] . ";" .
                "MIN: " . $device['min'] . ";" .
                "MAX: " . $device['max'] . ";" .
                PHP_EOL;
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
