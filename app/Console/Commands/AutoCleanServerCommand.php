<?php namespace App\Console\Commands;
ini_set('memory_limit', '-1');
set_time_limit(0);

use Carbon\Carbon;
use Illuminate\Console\Command;
use Tobuli\Repositories\Config\ConfigRepositoryInterface as Config;

class AutoCleanServerCommand extends Command {
    /**
     * @var Config
     */
    private $config;
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'server:autoclean';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';


	public function __construct(Config $config)
	{
		parent::__construct();

        $this->config = $config;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $item = $this->config->findWhere(['title' => 'db_clear']);
        if (empty($item))
            $settings = [];
        else
            $settings = unserialize($item->value);

        if ( isset($settings['status']) && $settings['status'] && $settings['days'] > 0 ) {
            $date = Carbon::now()->subDays($settings['days']);
            $diff = $date->diffInDays( Carbon::now(), false);
            $min  = config('tobuli.min_database_clear_days');

            if ( $diff < $min ) {
                $this->line("Days to keep not reached: min - $min, current - $diff.\n");
            } else {
                $this->call('server:clean', [
                    'date' => $date->format('Y-m-d')
                ]);

                $this->call('server:reportlogclean', [
                    'date' => $date->format('Y-m-d')
                ]);
            }
        } else {
            $this->line("Auto cleanup disabled.\n");
        }
	}
}
