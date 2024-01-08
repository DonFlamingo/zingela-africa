<?php namespace ModalHelpers;

use Facades\Repositories\DeviceRepo;
use Facades\Repositories\UserGprsTemplateRepo;
use Facades\Repositories\UserRepo;
use Facades\Repositories\UserSmsTemplateRepo;
use Facades\Validators\SendCommandFormValidator;
use Facades\Validators\SendCommandGprsFormValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tobuli\Exceptions\ValidationException;

class SendCommandModalHelper extends ModalHelper
{
    public function createData()
    {
        if ($this->api && !$this->user->perm('send_command', 'view'))
            return ['status' => 0, 'perm' => 0];

        $devices_sms = UserRepo::getDevicesSms($this->user->id)->lists('name', 'id')->all();

        $devices = UserRepo::getDevices($this->user->id);
        $devices_gprs = $devices->lists('name', 'id')->all();
        $devices_protocols = UserRepo::getDevicesProtocols($this->user->id);

        $gprs_templates_devices = [];
        foreach ($devices as $device) {
            if (!$device->gprs_templates_only)
                continue;

            $gprs_templates_devices[$device->id] = $device->id;
        }

        $commands = [
            'engineStop' => trans('front.engine_stop'),
            'engineResume' => trans('front.engine_resume'),
            'alarmArm' => trans('front.alarm_arm'),
            'alarmDisarm' => trans('front.alarm_disarm'),
            'positionSingle' => trans('front.position_single'),
            'positionPeriodic' => trans('front.periodic_reporting'),
            'positionStop' => trans('front.stop_reporting'),
            'movementAlarm' => trans('front.movement_alarm'),
            'setTimezone' => trans('front.set_timezone'),
            'rebootDevice' => trans('front.reboot_device'),
            'sendSms' => trans('front.send_sms'),
            'requestPhoto' => trans('front.request_photo'),
            'custom' => trans('front.custom_command'),
        ];

        $commands_all = [
            'default' => $commands,
            'watch'=> [
                'sosNumber' => trans('front.sos_number_setting'),
                'alarmSos' => trans('front.sos_message_alarm'),
                'alarmBattery' => trans('front.low_battery_alarm'),
                'alarmRemove' => trans('front.alarm_of_taking_watch'),
                'rebootDevice' => trans('front.restart'),
                'silenceTime' => trans('front.time_interval_setting_of_silencetime'),
                'alarmClock' => trans('front.alarm_clock_setting_order'),
                'setPhonebook' => trans('front.phone_book_setting_order'),
                'requestPhoto' => trans('front.request_photo'),
                'custom' => trans('front.custom_command'),
            ],
            'pt502' => [
                'engineStop' => trans('front.engine_stop'),
                'engineResume' => trans('front.engine_resume'),
                'doorOpen' => trans('front.door_open'),
                'doorClose' => trans('front.door_close'),
                'requestPhoto' => trans('front.request_photo'),
                'custom' => trans('front.custom_command'),
            ]
        ];

        $units = [
            'second' => trans('front.second'),
            'minute' => trans('front.minute'),
            'hour' => trans('front.hour')
        ];

        $number_index = [
            '1' => trans('front.first'),
            '2' => trans('front.second'),
            '3' => trans('front.third'),
            '0' => trans('front.three_sos_numbers'),
        ];

        $actions = [
            '1' => trans('front.on'),
            '0' => trans('front.off'),
        ];

        $gprs_templates_only = [];

        if ($this->api) {
            $sms_templates = [['id' => '0', 'title' => trans('front.no_template'), 'message' => null]];
            $results = UserSmsTemplateRepo::getWhere(['user_id' => $this->user->id], 'title');
            foreach ($results as $row)
                array_push($sms_templates, ['id' => $row->id, 'title' => $row->title, 'message' => $row->message]);

            $gprs_templates = [['id' => '0', 'title' => trans('front.no_template'), 'message' => null]];
            $results = UserGprsTemplateRepo::getWhere(['user_id' => $this->user->id], 'title');
            foreach ($results as $row)
                array_push($gprs_templates, ['id' => $row->id, 'title' => $row->title, 'message' => $row->message]);

            $devices_sms_arr = [];
            foreach ($devices_sms as $key => $value)
                array_push($devices_sms_arr, ['id' => $key, 'value' => $value]);
            $devices_sms = $devices_sms_arr;

            $devices_gprs_arr = [];
            foreach ($devices_gprs as $key => $value)
                array_push($devices_gprs_arr, ['id' => $key, 'value' => $value]);
            $devices_gprs = $devices_gprs_arr;

            $commands = apiArray($commands);
            $units = apiArray($units);
            $number_index = apiArray($number_index);
            $actions = apiArray($actions);
        }
        else {
            $sms_templates = ['0' => trans('front.no_template')] + UserSmsTemplateRepo::getWhere(['user_id' => $this->user->id], 'title')->lists('title', 'id')->all();

            $gprs_templates_only = UserGprsTemplateRepo::getWhere(['user_id' => $this->user->id], 'title')->lists('title', 'id')->all();
            $gprs_templates = ['0' => trans('front.no_template')] + $gprs_templates_only;
        }

        $device_id = request()->get('id');

        return compact('devices_sms', 'devices_gprs', 'sms_templates', 'gprs_templates', 'commands', 'units', 'number_index', 'actions', 'devices_protocols', 'commands_all', 'gprs_templates_devices', 'gprs_templates_only', 'device_id');
    }

    public function create()
    {
        $this->data['message'] = isset($this->data['message']) ? $this->data['message'] : '';
        $this->data['message'] = isset($this->data['message_sms']) ? $this->data['message_sms'] : $this->data['message'];
        
        try
        {
            if (!$this->user->sms_gateway)
                throw new ValidationException(['id' => trans('front.sms_gateway_disabled')]);

            SendCommandFormValidator::validate('create', $this->data);

            $devices = DeviceRepo::getWhereInWith($this->data['devices'], 'id', ['users']);

            $sms_gateway = [
                'status' => 1,
                'url' => $this->user->sms_gateway_url,
                'params' => $this->user->sms_gateway_params
            ];

            foreach ($devices as $device) {
                if (!$device->users->contains($this->user->id))
                    continue;

                $sms_gateway['mobile_phone'] = $device->sim_number;
                $sms_template = new \stdClass();
                $sms_template->note = isset($_POST['message_sms']) ? $_POST['message_sms'] : $_POST['message'];
                sendEmailTemplate('', '', NULL, NULL, $sms_gateway, $sms_template, $this->user->id, $this->user->lang);
            }

            return $this->api ? ['status' => 1] : ['status' => 0, 'trigger' => 'send_command'];
        }
        catch (ValidationException $e)
        {
            return ['statuts' => 0, 'errors' => $e->getErrors()];
        }
    }

    public function gprsCreate()
    {
        try
        {
            if (!$this->user->perm('send_command', 'view'))
                return ['status' => 0, 'errors' => ['id' => trans('front.dont_have_permission')]];

            $device = DeviceRepo::find($this->data['device_id']);

            if ($device->gprs_templates_only) {
                $this->data['type'] = 'custom';
                if (empty($this->data['gprs_template_only_id']))
                    throw new ValidationException(['gprs_template_only_id' => strtr(trans('validation.required'), [':attribute' => trans('validation.attributes.gprs_template_id')])]);

                $this->data['gprs_template_id'] = $this->data['gprs_template_only_id'];
            }
            else {
                SendCommandGprsFormValidator::validate($this->data['type'], $this->data);
            }
			
            $data = [
                //'uniqueId' => $device->imei,
				'deviceId' => $device->traccar_device_id,
				'description' => $this->data['type'],
                'type' => strtr($this->data['type'], [
                    'watch_' => '',
                    'pt502_' => '',
                ])
            ];

            if ($this->data['type'] == 'movementAlarm') {
                $data['attributes'] = [
                    'radius' => $this->data['parameter']
                ];
            }

            if ($this->data['type'] == 'setTimezone') {
                $data['attributes'] = [
                    'timezone' => $this->data['parameter']
                ];
            }

            if ($this->data['type'] == 'positionPeriodic') {
                $freq = $this->data['frequency'];
                if ($this->data['unit'] == 'minute')
                    $freq *= 60;
                if ($this->data['unit'] == 'hour')
                    $freq *= 3600;

                $data['attributes'] = [
                    'frequency' => $freq
                ];
            }

            if ( in_array($this->data['type'], ['custom', 'watch_custom', 'pt502_custom']) ) {
                $imei = $device->imei;
                if ($device->traccar->protocol == 'tk103') {
                    $imei = '0' . substr($imei, -11);
                }
                $message = strtr($this->data['message'], [
                    '[%IMEI%]' => $imei
                ]);

                $data['attributes'] = ['data' => $message];
            }

            if ($this->data['type'] == 'sendSms') {
                $data['attributes'] = [
                    'message' => $this->data['message'],
                    'phoneNumber' => $this->data['sim_number']
                ];
            }


            if ($this->data['type'] == 'watch_sosNumber') {
                $data['attributes'] = [
                    'index' => $this->data['index'] == 0 ? '' : $this->data['index'],
                    'phone' => $this->data['phone_number']
                ];
            }

            if ($this->data['type'] == 'watch_silenceTime') {
                $data['attributes'] = [
                    'data' => $this->data['time'],
                ];
            }

            if ($this->data['type'] == 'watch_alarmClock' || $this->data['type'] == 'watch_setPhonebook') {
                $data['attributes'] = [
                    'data' => $this->data['order'],
                ];
            }

            if ($this->data['type'] == 'watch_alarmSos' || $this->data['type'] == 'watch_alarmBattery' || $this->data['type'] == 'watch_alarmRemove') {
                $data['attributes'] = [
                    'enable' => $this->data['action']
                ];
            }

            if ($this->data['type'] == 'pt502_engineStop') {
                $data['type'] = 'outputControl';
                $data['attributes'] = [
                    'index' => 1,
                    'data' => 1,
                    'password' => $this->data['password'],
                ];
            }

            if ($this->data['type'] == 'pt502_engineResume') {
                $data['type'] = 'outputControl';
                $data['attributes'] = [
                    'index' => 1,
                    'data' => 0,
                    'password' => $this->data['password'],
                ];
            }

            if ($this->data['type'] == 'pt502_doorOpen') {
                $data['type'] = 'outputControl';
                $data['attributes'] = [
                    'index' => 2,
                    'data' => 0,
                    'password' => $this->data['password'],
                ];
            }

            if ($this->data['type'] == 'pt502_doorClose') {
                $data['type'] = 'outputControl';
                $data['attributes'] = [
                    'index' => 2,
                    'data' => 1,
                    'password' => $this->data['password'],
                ];
            }

            $result = send_command($data);

            $res_arr = json_decode($result, true);

            if ( is_null($res_arr) ) {
                return ['status' => 0, 'trigger' => 'send_command', 'error' => 'Failed', 'result' => $result];
            }

            if (array_key_exists('message', $res_arr)) {
                $message = is_null($res_arr['message']) ? $res_arr['details'] : $res_arr['message'];
                if ($this->api)
                    throw new ValidationException(['id' => $message]);
                else
                    return ['status' => 0, 'trigger' => 'send_command', 'error' => $message];
            }

            return $this->api ? ['status' => 1] : ['status' => 0, 'trigger' => 'send_command'];
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'trigger' => 'send_command', 'errors' => $e->getErrors()];
        }
    }

    function getDeviceSimNumber()
    {
        $id = array_key_exists('geofence_id', $this->data) ? $this->data['geofence_id'] : $this->data['id'];
        $item = DeviceRepo::find($id);
        if (empty($item) || (!$item->users->contains($this->user->id) && !isAdmin()))
            return ['sim_number' => ''];

        return ['sim_number' => $item->sim_number];
    }
}