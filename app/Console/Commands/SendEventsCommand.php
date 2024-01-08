<?php namespace App\Console\Commands;
ini_set('memory_limit', '-1');
set_time_limit(0);

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tobuli\Repositories\EmailTemplate\EmailTemplateRepositoryInterface as EmailTemplate;
use Tobuli\Repositories\SmsTemplate\SmsTemplateRepositoryInterface as SmsTemplate;
use Tobuli\Repositories\Timezone\TimezoneRepositoryInterface as Timezone;

use Bugsnag\BugsnagLaravel\BugsnagFacade as Bugsnag;
use App\Console\ProcessManager;

class SendEventsCommand extends Command {
    /**
     * @var EmailTemplate
     */
    private $emailTemplate;
    private $template;
    private $sms_template;
    private $lang = [];
    private $addresses = [];

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'events:check';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';
    /**
     * @var SmsTemplate
     */
    private $smsTemplate;
    /**
     * @var Timezone
     */
    private $timezone;

    /**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(EmailTemplate $emailTemplate, SmsTemplate $smsTemplate, Timezone $timezone)
	{
		parent::__construct();
        $this->emailTemplate = $emailTemplate;
        $this->smsTemplate = $smsTemplate;
        $this->timezone = $timezone;

        # Load
        $this->lang = [];
        $dirs = File::directories(base_path('resources/lang'));
        foreach ($dirs as $dir) {
            $lg = explode('/', $dir);
            end($lg);
            $this->lang[$lg[key($lg)]] = require($dir.'/front.php');
        }
    }

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $this->processManager = new ProcessManager($this->name, $timeout = 120, $limit = 2);
		
		/* comment by pinakin 19-11-2019
        if (!$this->processManager->canProcess())
        {
            echo "Cant process \n";
            return false;
        }
		*/

        $this->template = $this->emailTemplate->whereName('event');
        $this->sms_template = $this->smsTemplate->whereName('event');

        DB::disableQueryLog();

        /* comment by pinakin 19-11-2019
		while ( $this->processManager->canProcess() )
        {
            $count = $this->sending();

            if ( empty($count) )
                break;

            sleep(1);
        }*/
		$this->sending();

        return 'DONE';
	}

	private function sending() {
        $ids = [];

        $items = DB::table('events_queue')
            ->select('events_queue.id', 'events_queue.user_id', 'events_queue.data', 'events_queue.type', 'users.sms_gateway', 'users.sms_gateway_url', 'users.sms_gateway_params', 'users.lang', 'users.unit_of_altitude', 'users.unit_of_distance', 'users.timezone_id', 'user_device_pivot.timezone_id as device_timezone_id', 'user_device_pivot.device_id')
            ->leftJoin('users', 'events_queue.user_id', '=', 'users.id')
            ->leftJoin('user_device_pivot', function($query) {
                $query->on('events_queue.device_id', '=', 'user_device_pivot.device_id');
                $query->on('events_queue.user_id', '=', 'user_device_pivot.user_id');
            })
            ->orderBy('id', 'asc')
            ->take(100)
            ->get();

        foreach ($items as $item) {
            if (is_null($item->device_id) || is_null($item->lang)) {
                $ids[] = $item->id;
                continue;
            }

			/* comment by pinakin 19-11-2019
            if ( !$this->processManager->lock( $item->id ) )
                continue;
			*/

            $data = json_decode($item->data, true);
            $sms_gateway_arr = [
                'status' => $item->sms_gateway,
                'url' => $item->sms_gateway_url,
                'mobile_phone' => $data['mobile_phone'],
                'params' => unserialize($item->sms_gateway_params)
            ];

            $message = null;
            if ($item->type == 'zone_out' || $item->type == 'zone_in')
                $message = $this->lang[$item->lang][$item->type];

            if ($item->type == 'overspeed')
                $message = $this->lang[$item->lang]['overspeed'].'('.$data['overspeed_speed'].' '.($data['overspeed_distance'] == 1 ? $this->lang[$item->lang]['km'] : $this->lang[$item->lang]['mi']).')';

            if ($item->type == 'driver')
                $message = sprintf($this->lang[$item->lang]['driver_alert'], $data['driver']);

            if ($item->type == 'custom')
                $message = $data['message'];

            Auth::loginUsingId($item->user_id);

            try {
                DB::table('events_queue')->where('id', $item->id)->delete();
                sendEmailTemplate($this->template, $data['email'], [
                    '[event]' => $message,
                    '[geofence]' => (isset($data['geofence']) ? $data['geofence'] : ''),
                    '[device]' => $data['device_name'],
                    '[address]' => $this->getAddress($data['latitude'], $data['longitude']),
                    '[position]' => $data['latitude'] . '&deg;, ' . $data['longitude'] . '&deg;',
                    '[lat]' => $data['latitude'],
                    '[lon]' => $data['longitude'],
                    '[heading]' => $data['course'],
                    '[preview]' => '<a href="http://maps.google.com/maps?q='.$data['latitude'].','.$data['longitude'].'&t=m&hl='.$item->lang.'">'.trans('front.preview').'</a>',
                    '[altitude]' => $item->unit_of_altitude == 'ft' ? round(metersToFeets($data['altitude'])).' '.$this->lang[$item->lang]['ft'] : round($data['altitude']) .' '.$this->lang[$item->lang]['mt'],
                    '[speed]' => $item->unit_of_distance == 'mi' ? round(kilometersToMiles($data['speed'])).' '.$this->lang[$item->lang]['dis_h_mi'] : round($data['speed']) .' '.$this->lang[$item->lang]['dis_h_km'],
                    '[time]' => datetime($data['time'])
                ], 'front::Emails.template', $sms_gateway_arr, $this->sms_template, $item->user_id, $item->lang);
            }
            catch(\Exception $e) {
                Bugsnag::notifyException($e);
            }
        }

        if (!empty($ids)) {
            DB::table('events_queue')->whereIn('id', $ids)->delete();
        }

        return count( $items );
    }

    private function getAddress($latitude, $longitude) {
        $id = $latitude.'_'.$longitude;
        if (isset($this->addresses[$id]))
            return $this->addresses[$id];

        $this->addresses[$id] = getGeoAddress($latitude, $longitude);
        return $this->addresses[$id];
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
