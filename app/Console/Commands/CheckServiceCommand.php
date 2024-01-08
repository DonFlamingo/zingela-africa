<?php namespace App\Console\Commands;
ini_set('memory_limit', '-1');
set_time_limit(0);

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Tobuli\Repositories\EmailTemplate\EmailTemplateRepositoryInterface as EmailTemplate;
use Tobuli\Repositories\SmsTemplate\SmsTemplateRepositoryInterface as SmsTemplate;
use Tobuli\Repositories\DeviceService\DeviceServiceRepositoryInterface as DeviceService;

class CheckServiceCommand extends Command {
    /**
     * @var EmailTemplate
     */
    private $emailTemplate;

    private $template;
    private $sms_template;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'service:check';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';
    /**
     * @var DeviceService
     */
    private $deviceService;
    /**
     * @var SmsTemplate
     */
    private $smsTemplate;

    /**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(EmailTemplate $emailTemplate, DeviceService $deviceService, SmsTemplate $smsTemplate)
	{
		parent::__construct();
        $this->emailTemplate = $emailTemplate;
        $this->deviceService = $deviceService;
        $this->smsTemplate = $smsTemplate;
    }

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $this->template = $this->emailTemplate->whereName('service_expiration');
        $this->sms_template = $this->smsTemplate->whereName('service_expiration');

        $ids = [];
        $date = date('Y-m-d H:i:s');

        $items = DB::table('device_services as services')
            ->select('services.*', 'users.sms_gateway', 'users.sms_gateway_url', 'users.sms_gateway_params', 'devices.name as device_name', 'users.lang')
            ->join('devices', 'services.device_id', '=', 'devices.id')
            ->join('users', 'services.user_id', '=', 'users.id')
            ->join('timezones', 'users.timezone_id', '=', 'timezones.id')
            ->where([
                'services.expiration_by' => 'days',
                'services.expired' => 0,
                'services.event_sent' => 0,
            ])
            ->whereRaw("((timezones.prefix = 'plus' && DATE(DATE_ADD('$date', INTERVAL timezones.time HOUR_MINUTE)) >= DATE(services.remind_date)) OR (timezones.prefix = 'minus' && DATE(DATE_SUB('$date', INTERVAL timezones.time HOUR_MINUTE)) >= DATE(services.remind_date)))")
            ->groupBy('services.id')
            ->get();

        foreach ($items as $item) {
            $sms_gateway_arr = [
                'status' => $item->sms_gateway,
                'url' => $item->sms_gateway_url,
                'mobile_phone' => $item->mobile_phone,
                'params' => unserialize($item->sms_gateway_params)
            ];
            sendEmailTemplate($this->template, $item->email, [
                '[device]' => htmlentities($item->device_name),
                '[service]' => htmlentities($item->name),
                '[left]' => $item->trigger_event_left.' d.'
            ], 'front::Emails.template', $sms_gateway_arr, $this->sms_template, $item->user_id, $item->lang);

            $ids[] = $item->id;
        }

        $items = DB::table('device_services as services')
            ->select(DB::raw('
            services.*,
            users.sms_gateway,
            users.sms_gateway_url,
            users.sms_gateway_params,
            devices.name as device_name,
            sensors.odometer_value_by,
            sensors.odometer_value,
            sensors.odometer_value_unit,
            sensors.value,
            sensors.unit_of_measurement,
            users.lang
            '))
            ->join('devices', 'services.device_id', '=', 'devices.id')
            ->join('users', 'services.user_id', '=', 'users.id')
            ->join('device_sensors as sensors', function($query) {
                $query->on('devices.id', '=', 'sensors.device_id');
                $query->where('sensors.type', '=', 'odometer');
            })
            ->where([
                'services.expiration_by' => 'odometer',
                'services.expired' => 0,
                'services.event_sent' => 0,
            ])
            ->whereRaw("((sensors.odometer_value_by = 'virtual_odometer' AND ((sensors.odometer_value_unit = 'km' && sensors.odometer_value >= services.remind) OR (sensors.odometer_value_unit = 'mi' && (sensors.odometer_value * 0.621371192) >= services.remind))) OR (sensors.odometer_value_by = 'connected_odometer' AND sensors.value_formula >= services.remind))")
            ->groupBy('services.id')
            ->get();

        foreach ($items as $item) {
            $sms_gateway_arr = [
                'status' => $item->sms_gateway,
                'url' => $item->sms_gateway_url,
                'mobile_phone' => $item->mobile_phone,
                'params' => unserialize($item->sms_gateway_params)
            ];

            sendEmailTemplate($this->template, $item->email, [
                '[device]' => htmlentities($item->device_name),
                '[service]' => htmlentities($item->name),
                '[left]' => $item->trigger_event_left.' '.$item->unit_of_measurement,
            ], 'front::Emails.template', $sms_gateway_arr, $this->sms_template, $item->user_id, $item->lang);

            $ids[] = $item->id;
        }

        $items = DB::table('device_services as services')
            ->select('services.*', 'users.sms_gateway', 'users.sms_gateway_url', 'users.sms_gateway_params', 'devices.name as device_name', 'sensors.value', 'sensors.unit_of_measurement', 'users.lang')
            ->join('devices', 'services.device_id', '=', 'devices.id')
            ->join('users', 'services.user_id', '=', 'users.id')
            ->join('device_sensors as sensors', function($query) {
                $query->on('devices.id', '=', 'sensors.device_id');
                $query->where('sensors.type', '=', 'engine_hours');
            })
            ->where([
                'services.expiration_by' => 'engine_hours',
                'services.expired' => 0,
                'services.event_sent' => 0,
            ])
            ->whereRaw("sensors.value >= services.remind")
            ->groupBy('services.id')
            ->get();

        foreach ($items as $item) {
            $sms_gateway_arr = [
                'status' => $item->sms_gateway,
                'url' => $item->sms_gateway_url,
                'mobile_phone' => $item->mobile_phone,
                'params' => unserialize($item->sms_gateway_params)
            ];

            sendEmailTemplate($this->template, $item->email, [
                '[device]' => htmlentities($item->device_name),
                '[service]' => htmlentities($item->name),
                '[left]' => $item->trigger_event_left.' '.$item->unit_of_measurement,
            ], 'front::Emails.template', $sms_gateway_arr, $this->sms_template, $item->user_id, $item->lang);

            $ids[] = $item->id;
        }
        if (!empty($ids)) {
            DB::table('device_services')->whereIn('id', $ids)->update([
                'event_sent' => 1
            ]);
        }

        return 'DONE';
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
