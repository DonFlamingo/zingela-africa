<?php namespace App\Console\Commands;
ini_set('memory_limit', '-1');
set_time_limit(0);

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Tobuli\Repositories\Config\ConfigRepositoryInterface as Config;

class BackupMysqlCommand extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'backup:mysql';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';
	/**
	 * @var Config
	 */
	private $config;

	private $use_passive = 0;
	private $conn_id;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
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
		$item = $this->config->findWhere(['title' => 'backups']);
		if (empty($item))
			return;

		$settings = unserialize($item->value);
		if (!isset($settings['ftp_server']) || !isset($settings['next_backup']) || time() < $settings['next_backup'])
			return;


		$path = '/var/www/html/images/';
		$filename = 'backup-'.date('Y-m-d').'-All.sql.gz';
		$message = trans('front.successfully_uploaded');
		$status = 1;

		if (file_exists($path . 'dumping.so')) {
			$time = (time() - filemtime($path.'dumping.so'));
			if ((time() - filemtime($path.'dumping.so')) > 1800) {
				if (file_exists($path . 'dumping.so'))
					@unlink($path . 'dumping.so');
			}
			else {
				dd('Working for '.$time.' sek.');
			}
		}

		try {
			@file_put_contents($path.'dumping.so', 'started');
			$login = 'ncftpput -c -u '.$settings['ftp_username'].' -p '.$settings['ftp_password'].' -P '.$settings['ftp_port'].' '.$settings['ftp_server'];
			exec('mysqldump --single-transaction=TRUE --lock-tables=false -u '.$_ENV['web_username'].' --password='.$_ENV['web_password'].' --databases '.$_ENV['web_database'].' '.$_ENV['traccar_database'].' tracking_sensors tracking_engine_hours | gzip -9 | '.$login.' '.$settings['ftp_path'].$filename);
		}
		catch(\Exception $e) {
			$message = trans('front.unexpected_error');
			$status = 0;
		}

		@unlink($path.'dumping.so');

		if (!isset($settings['messages']))
			$settings['messages'] = [];

		array_unshift($settings['messages'], [
			'status' => $status,
			'date' => date('Y-m-d H:i'),
			'path' => $settings['ftp_path'],
			'message' => $message
		]);

		$settings['messages'] = array_slice($settings['messages'], 0, 5);
		$settings['next_backup'] = strtotime(date('Y-m-d', strtotime('+'.$settings['period'].' days')).' '.$settings['hour']);
		$this->config->update($item->id, [
			'value' => serialize($settings)
		]);

		$this->line("Job done[{$status}]\n");
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
