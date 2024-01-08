<?php namespace App\Console\Commands;

set_time_limit(0);

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanUnregisteredDeviceLogCommand extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'unregistered:clean';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';


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
        DB::connection('traccar_mysql')
            ->table('unregistered_devices_log')
            ->join('devices', 'devices.uniqueId', '=', 'unregistered_devices_log.imei')
            ->whereNotNull('devices.id')
            ->delete();

        $devices = DB::connection('traccar_mysql')
            ->table('unregistered_devices_log')
            ->where('port', '6002')
            ->get();

        foreach ($devices as $device) {

            $query = DB::connection('traccar_mysql')
                ->table('devices')
                ->select('devices.*');

            if ( strlen($device->imei) == 15 ) {
                $old_imei = '0' . substr($device->imei, 4);
                $query->where('devices.uniqueId', $old_imei);
            } else {
                $old_imei = ltrim($device->imei, '0');
                $query->where('devices.uniqueId', 'like', '%'.$old_imei);
            }

            $result = $query->first();

            if ($result) {
                DB::connection('traccar_mysql')
                    ->table('unregistered_devices_log')
                    ->where('imei', $device->imei)
                    ->delete();
            }
        }

		$this->line("Job done[OK]\n");
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
