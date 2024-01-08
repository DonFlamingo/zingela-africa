<?php namespace ModalHelpers;

use Facades\Repositories\DeviceGroupRepo;
use Facades\Repositories\EventCustomRepo;
use Facades\Repositories\SmsEventQueueRepo;
use Facades\Repositories\TimezoneRepo;
use Facades\Repositories\UserDriverRepo;
use Facades\Repositories\UserGprsTemplateRepo;
use Facades\Repositories\UserRepo;
use Facades\Repositories\UserSmsTemplateRepo;
use Facades\Validators\UserAccountFormValidator;
use Facades\Validators\UserAccountSettingsFormValidator;
use Facades\ModalHelpers\SmsGatewayModalHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Tobuli\Exceptions\ValidationException;

class MyAccountSettingsModalHelper extends ModalHelper
{
    private $data_group = [];
    
    public function editData()
    {
        $item = UserRepo::find($this->user->id)->toArray();
        $timezones = TimezoneRepo::order()->lists('title', 'id')->all();
        $groups = DeviceGroupRepo::getWhere(['user_id' => $this->user->id]);
        if (!$this->api) {
            $drivers = UserDriverRepo::searchAndPaginate(['filter' => ['user_id' => $this->user->id]], 'id', 'desc', 10);
            $events = EventCustomRepo::searchAndPaginate(['filter' => ['user_id' => $this->user->id]], 'id', 'desc', 10);
            $user_sms_templates = UserSmsTemplateRepo::searchAndPaginate(['filter' => ['user_id' => $this->user->id]], 'id', 'desc', 10);
            $user_gprs_templates = UserGprsTemplateRepo::searchAndPaginate(['filter' => ['user_id' => $this->user->id]], 'id', 'desc', 10);
            $widgets = UserRepo::getSettings($this->user->id, 'widgets');
            if (empty($widgets)) {
                $widgets = settings('widgets');
            }
        }
        $sms_queue_count = SmsEventQueueRepo::countwhere(['user_id' => $this->user->id]);
        $user_dst = DB::table('users_dst')->where('user_id', '=', $this->user->id)->first();

        if (!$item['timezone_id'])
            $item['timezone_id'] = 17;

        if (!is_array($item['sms_gateway_params']) || !array_key_exists('request_method', $item['sms_gateway_params']))
            $item['sms_gateway_params']['request_method'] = null;

        if (!is_array($item['sms_gateway_params']) || !array_key_exists('encoding', $item['sms_gateway_params']))
            $item['sms_gateway_params']['encoding'] = null;

        if (!is_array($item['sms_gateway_params']) || !array_key_exists('authentication', $item['sms_gateway_params']))
            $item['sms_gateway_params']['authentication'] = null;

        if (!is_array($item['sms_gateway_params']) || !array_key_exists('username', $item['sms_gateway_params']))
            $item['sms_gateway_params']['username'] = null;

        $units_of_distance = [
            'km' => trans('front.kilometer'),
            'mi' => trans('front.mile')
        ];

        $units_of_capacity = [
            'lt' => trans('front.liter'),
            'gl' => trans('front.gallon')
        ];

        $units_of_altitude = [
            'mt' => trans('front.meter'),
            'ft' => trans('front.feet')
        ];

        $request_method_select = [
            'get' => 'GET',
            'post' => 'POST',
            'app' => trans('front.sms_gateway_app'),
            'plivo' => 'Plivo'
        ];
        $encoding_select = [0 => trans('global.no'), 'json' => 'JSON'];
        $authentication_select = [0 => trans('global.no'), 1 => trans('global.yes')];

        $dst_types = [
            'none' => trans('front.none'),
            'exact' => trans('front.exact_date'),
            'automatic' => trans('front.automatic'),
            'other' => trans('front.other')
        ];
        $months = [
            'january' => trans('front.january'),
            'february' => trans('front.february'),
            'march' => trans('front.march'),
            'april' => trans('front.april'),
            'may' => trans('front.may'),
            'june' => trans('front.june'),
            'july' => trans('front.july'),
            'august' => trans('front.august'),
            'september' => trans('front.september'),
            'october' => trans('front.october'),
            'november' => trans('front.november'),
            'december' => trans('front.december')
        ];

        $weekdays = [
            'monday' => trans('front.monday'),
            'tuesday' => trans('front.tuesday'),
            'wednesday' => trans('front.wednesday'),
            'thursday' => trans('front.thursday'),
            'friday' => trans('front.friday'),
            'saturday' => trans('front.saturday'),
            'sunday' => trans('front.sunday')
        ];

        $week_pos = [
            'first' => trans('front.first'),
            'last' => trans('front.last'),
        ];

        $dst_countries_q = DB::table('timezones_dst')->get();
        $dst_countries = [];
        foreach ($dst_countries_q as $dst_c)
            $dst_countries[$dst_c->id] = $dst_c->country;

        $week_start_days = [
            '1' => trans('front.monday'),
            '0' => trans('front.sunday'),
        ];

        if ($this->api) {
            $timezones = apiArray($timezones);
            $units_of_distance = apiArray($units_of_distance);
            $units_of_capacity = apiArray($units_of_capacity);
            $units_of_altitude = apiArray($units_of_altitude);
            $request_method_select = apiArray($request_method_select);
            $encoding_select = apiArray($encoding_select);
            $authentication_select = apiArray($authentication_select);
            $dst_types = apiArray($dst_types);
            $months = apiArray($months);
            $dst_countries = apiArray($dst_countries);
            $week_start_days = apiArray($week_start_days);
        }

        if ($this->api)
            return compact('item', 'timezones', 'units_of_distance', 'units_of_capacity', 'units_of_altitude', 'groups', 'sms_queue_count', 'request_method_select', 'encoding_select', 'authentication_select', 'dst_types', 'user_dst', 'months', 'weekdays', 'week_pos', 'dst_countries', 'week_start_days');
        else
            return compact('item', 'timezones', 'units_of_distance', 'units_of_capacity', 'units_of_altitude', 'groups', 'sms_queue_count', 'request_method_select', 'encoding_select', 'authentication_select', 'drivers', 'events', 'user_sms_templates', 'user_gprs_templates', 'dst_types', 'user_dst', 'months', 'weekdays', 'week_pos', 'dst_countries', 'week_start_days', 'widgets');
    }

    public function edit()
    {

        $this->data['sms_gateway'] = (isset($this->data['sms_gateway']) && $this->data['sms_gateway']);
        $this->data['sms_gateway_url'] = isset($this->data['sms_gateway_url']) ? $this->data['sms_gateway_url'] : '';
        $item = $this->user;
        $user_dst = DB::table('users_dst')->where('user_id', '=', $this->user->id)->first();

        try
        {

            if (isset($this->data['request_method']))
                SmsGatewayModalHelper::validate($this->data);

            UserAccountSettingsFormValidator::validate('update', $this->data, $item->id);

            $array = auth()->user()->map_controls->getArray();

            $update = [
                'sms_gateway' => $this->data['sms_gateway'],
                'sms_gateway_url' => $this->data['sms_gateway_url'],
                'unit_of_distance' => $this->data['unit_of_distance'],
                'unit_of_capacity' => $this->data['unit_of_capacity'],
                'unit_of_altitude' => $this->data['unit_of_altitude'],
                'timezone_id' => $this->data['timezone_id'],
                'week_start_day' => (isset($this->data['week_start_day']) ? $this->data['week_start_day'] : 1),
                'map_controls' => $array,
            ];

            if (isset($this->data['request_method'])) {
                $fields = [
                    'request_method',
                    'authentication',
                    'username',
                    'password',
                    'encoding',
                    'auth_id',
                    'auth_token',
                    'senders_phone'
                ];
                $update['sms_gateway_params'] = [];
                foreach ($fields as $field) {
                    $value = '';
                    if (isset($this->data[$field]))
                        $value = $this->data[$field];
                    else {
                        if (isset($item->sms_gateway_params[$field]))
                            $value = $item->sms_gateway_params[$field];
                    }
                    $update['sms_gateway_params'][$field] = $value;
                }
            }

            UserRepo::update($item->id, $update);

            if (isset($this->data['dst_type'])) {
                # Daylight saving time
                if ($this->data['dst_type'] == 'exact' || $this->data['dst_type'] == 'other' || $this->data['dst_type'] == 'automatic') {
                    $dst_arr = [
                        'user_id' => $this->user->id,
                        'country_id' => NULL,
                        'type' => $this->data['dst_type'],
                        'date_from' => NULL,
                        'date_to' => NULL,
                        'month_from' => NULL,
                        'month_to' => NULL,
                        'week_pos_from' => NULL,
                        'week_pos_to' => NULL,
                        'week_day_from' => NULL,
                        'week_day_to' => NULL,
                        'time_from' => NULL,
                        'time_to' => NULL
                    ];

                    if ($this->data['dst_type'] == 'exact') {
                        $dst_arr['date_from'] = $this->data['date_from'];
                        $dst_arr['date_to'] = $this->data['date_to'];
                    }
                    elseif ($this->data['dst_type'] == 'other') {
                        $dst_arr['month_from'] = $this->data['month_from'];
                        $dst_arr['month_to'] = $this->data['month_to'];
                        $dst_arr['week_pos_from'] = $this->data['week_pos_from'];
                        $dst_arr['week_pos_to'] = $this->data['week_pos_to'];
                        $dst_arr['week_day_from'] = $this->data['week_day_from'];
                        $dst_arr['week_day_to'] = $this->data['week_day_to'];
                        $dst_arr['time_from'] = $this->data['time_from'];
                        $dst_arr['time_to'] = $this->data['time_to'];
                    }
                    elseif ($this->data['dst_type'] == 'automatic') {
                        $dst_arr['country_id'] = $this->data['dst_country_id'];
                    }

                    if (!empty($user_dst)) {
                        $unchanged = array_intersect_assoc(json_decode(json_encode($user_dst), TRUE), $dst_arr);
                        if (count($unchanged) != count($dst_arr))
                            DB::table('users_dst')->where('user_id', '=', $this->user->id)->update($dst_arr);
                    }
                    else
                        DB::table('users_dst')->insert($dst_arr);


                }
                else
                    DB::table('users_dst')->where('user_id', '=', $this->user->id)->delete();
            }

            # Object groups
            if (!$this->api) {
                $edit_group = isset($this->data['edit_group']) ? $this->data['edit_group'] : [];
                $edit_arr = [];
                foreach ($edit_group as $id => $title) {
                    if (empty($title))
                        continue;

                    $edit_arr[$id] = $id;
                    DeviceGroupRepo::updateWhere(['id' => $id, 'user_id' => $this->user->id], ['title' => $title]);
                }

                DeviceGroupRepo::deleteUsersWhereNotIn($edit_arr, $this->user->id);

                $add_group = isset($this->data['add_group']) ? $this->data['add_group'] : [];
                foreach ($add_group as $id => $title) {
                    if (empty($title))
                        continue;

                    DeviceGroupRepo::create(['title' => $title, 'user_id' => $this->user->id]);
                }
            }
            else {
                $arr = [];
                $groups = DeviceGroupRepo::getWhere(['user_id' => $item->id]);
                if (!$groups->isEmpty())
                    $groups = $groups->lists('id', 'id')->all();

                $this->data_group = isset($this->data['groups']) ? json_decode($this->data['groups'], TRUE) : [];
                foreach ($this->data_group as $key => $group) {
                    $title = $group['title'];
                    $id = $group['id'];
                    if (empty($title))
                        continue;

                    if (array_key_exists($group['id'], $groups)) {
                        $arr[$id] = $id;
                        DeviceGroupRepo::updateWhere(['id' => $id, 'user_id' => $this->user->id], ['title' => $title]);
                    }
                    else {
                        $itemd = DeviceGroupRepo::create(['title' => $title, 'user_id' => $item->id]);
                        $id = $itemd->id;
                        $arr[$id] = $id;
                    }
                }

                DeviceGroupRepo::deleteUsersWhereNotIn($arr, $item->id);
            }

            if (!$this->api) {
                $widgets = empty($this->data['widgets']) ? null : $this->data['widgets'];
                UserRepo::setSettings($item->id, 'widgets', $widgets);
            }

            return ['status' => 1, 'id' => $item->id];
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

    public function changePassword()
    {
        // if (isDemoUser())
        //     throw new ValidationException(['id' => trans('front.demo_acc')]);

        $item = UserRepo::find($this->user->id);
        try
        {
            UserAccountFormValidator::validate('update', $this->data, $item->id);

            $update = [
                'email' => $this->data['email']
            ];

            if (!empty($this->data['password'])) {
                $update['password'] = $this->data['password'];
                while(!empty(UserRepo::findWhere(['api_hash' => $hash = Hash::make($this->data['email'].':'.$this->data['password'])])));
                $update['api_hash'] = $hash;
            }

            UserRepo::update($item->id, $update);
            
            return ['status' => 1, 'id' => $item->id, 'email_changed' => ($this->data['email'] != $item->email)];
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }
}