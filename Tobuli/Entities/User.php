<?php namespace Tobuli\Entities;

use Facades\Repositories\TimezoneRepo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config as LaravelConfig;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

    use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('remember_token');

    protected $fillable = array(
        'id',
        'active',
        'password',
        'group_id',
        'manager_id',
        'billing_plan_id',
        'map_id',
        'email',
        'devices_limit',
        'subscription_expiration',
        'loged_at',
        'admin_id',
        'lang',
        'unit_of_distance',
        'unit_of_capacity',
        'phone',
        'unit_of_altitude',
        'timezone_id',
        'sms_gateway',
        'sms_gateway_url',
        'api_hash',
        'api_hash_expire',
        'available_maps',
        'sms_gateway_params',
        'api_hash',
        'sms_gateway_app_date',
        'open_device_groups',
        'open_geofence_groups',
        'week_start_day',
        'top_toolbar_open',
        'map_controls',
        'first_name',
        'last_name',
    );

    protected $casts = [
        'id' => 'integer',
        'active' => 'integer',
        'group_id' => 'integer',
        'manager_id' => 'integer',
        'billing_plan_id' => 'integer',
        'map_id' => 'integer',
        'devices_limit' => 'integer',
        'timezone_id' => 'integer',
    ];

    private $dst = NULL;

    private $permissions = NULL;

    public function setPasswordAttribute($value)
    {
        if (!empty($value))
            $this->attributes['password'] = Hash::make($value);
    }

    public function setAvailableMapsAttribute($value)
    {
        $this->attributes['available_maps'] = serialize($value);
    }

    public function setSmsGatewayParamsAttribute($value)
    {
        $this->attributes['sms_gateway_params'] = serialize($value);
    }

    public function getTimezoneAttribute()
    {
        if (is_null($this->dst)) {
            $user_dst = DB::table('users_dst')->where('user_id', '=', $this->id)->whereNotNull('type')->first();
            if (!empty($user_dst)) {
                if ($user_dst->type == 'automatic') {
                    $dst_time = DB::table('timezones_dst')->where('id', '=', $user_dst->country_id)->first();
                    if (!empty($dst_time)) {
                        $user_dst->date_from = date("m-d", strtotime($dst_time->from_period." ".date('Y'))).' '.$dst_time->from_time;
                        $user_dst->date_to = date("m-d", strtotime($dst_time->to_period." ".date('Y'))).' '.$dst_time->to_time;
                    }
                }
                elseif ($user_dst->type == 'other') {
                    $user_dst->date_from = date("m-d", strtotime("{$user_dst->week_pos_from} {$user_dst->week_day_from} of ".$user_dst->month_from." ".date('Y')."")).' '.$user_dst->time_from;
                    $user_dst->date_to = date("m-d", strtotime("{$user_dst->week_pos_to} {$user_dst->week_day_to} of ".$user_dst->month_to." ".date('Y')."")).' '.$user_dst->time_to;
                }

                $this->loadDST($user_dst->date_from, $user_dst->date_to);
            }
        }

        if ((!array_key_exists('timezone', $this->relations)))
            $this->load('timezone');

        if ($this->getRelation('timezone'))
            return $this->getRelation('timezone');
        else
            return new Timezone();
    }

    public function getAvailableMapsAttribute($value)
    {
        return unserialize($value);
    }

    public function getSmsGatewayParamsAttribute($value)
    {
        return unserialize($value);
    }

    public function getTimezoneReverseAttribute() {
        return timezoneReverse($this->getTimezoneAttribute()->zone);
    }

    public function getDistanceUnitHourAttribute() {
        return trans("front.dis_h_{$this->unit_of_distance}");
    }

    public function timezone() {
        return $this->hasOne('Tobuli\Entities\Timezone', 'id', 'timezone_id');
    }

    public function manager() {
        return $this->hasOne('Tobuli\Entities\User', 'id', 'manager_id');
    }

    public function billing_plan() {
        return $this->hasOne('Tobuli\Entities\BillingPlan', 'id', 'billing_plan_id');
    }

    public function devices() {
        return $this->belongsToMany('Tobuli\Entities\Device', 'user_device_pivot', 'user_id', 'device_id')->with(['traccar', 'icon'])->withPivot(['group_id', 'current_driver_id', 'active', 'timezone_id'])->where('deleted', 0)->orderBy('name', 'asc');
    }

    public function devices_sms() {
        return $this->belongsToMany('Tobuli\Entities\Device', 'user_device_pivot', 'user_id', 'device_id')->where('sim_number', '!=', '')->where('deleted', 0)->orderBy('name', 'asc');
    }

    public function drivers() {
        return $this->hasMany('Tobuli\Entities\UserDriver', 'user_id', 'id');
    }

    public function subusers() {
        return $this->hasMany('Tobuli\Entities\User', 'manager_id', 'id');
    }

    public function sms_templates() {
        return $this->hasMany('Tobuli\Entities\UserSmsTemplate', 'user_id', 'id');
    }

    public function perm($name, $mode) {
        $mode = trim($mode);
        $modes = LaravelConfig::get('tobuli.permissions_modes');

        if (!array_key_exists($mode, $modes))
            die('Bad permission');

        if (is_null($this->permissions)) {
            $this->permissions = [];
            if (empty($this->billing_plan_id)) {
                $perms = DB::table('user_permissions')
                    ->select('name', 'view', 'edit', 'remove')
                    ->where('user_id', '=', $this->id)
                    ->get();
            }
            else {
                $perms = DB::table('billing_plan_permissions')
                    ->select('name', 'view', 'edit', 'remove')
                    ->where('plan_id', '=', $this->billing_plan_id)
                    ->get();
            }

            if (!empty($perms)) {
                $manager = $this->manager;

                foreach ($perms as $perm) {
                    if ($manager) {
                        $this->permissions[$perm->name] = [
                            'view' => $perm->view && $manager->perm($perm->name, 'view'),
                            'edit' => $perm->edit && $manager->perm($perm->name, 'edit'),
                            'remove' => $perm->remove && $manager->perm($perm->name, 'remove')
                        ];
                    } else {
                        $this->permissions[$perm->name] = [
                            'view' => $perm->view,
                            'edit' => $perm->edit,
                            'remove' => $perm->remove
                        ];
                    }
                }
            }
        }

        return array_key_exists($name, $this->permissions) && array_key_exists($mode, $this->permissions[$name]) ? boolval($this->permissions[$name][$mode]) : FALSE;
    }

    private function loadDST($dst_date_from, $dst_date_to) {
        if (!is_null($this->dst))
            return $this->dst;

        $timezone = TimezoneRepo::find($this->timezone_id);
        if (strpos($timezone->zone, ' ') !== false) {
            list($hours, $minutes) = explode(' ', $timezone->zone);
        }
        else {
            $hours = $timezone->zone;
            $minutes = '';
        }
        $dst_zone = trim((intval(str_replace('hours', ' ', $hours)) + 1).'hours '.(!empty($minutes) ? $minutes : ''));
        if (substr($dst_zone, 0, 1) != '-')
            $dst_zone = '+'.$dst_zone;

        $date_from = strtotime(tdate(date('Y-m-d H:i:s'), $dst_zone));
        $date_to = strtotime(tdate(date('Y-m-d H:i:s'), $timezone->zone));
        $year = date('Y', $date_from);

        $this->dst = FALSE;
        $from = strtotime($year.'-'.$dst_date_from);
        $to = strtotime($year.'-'.$dst_date_to);

        if ($to < $from) {
            if ($date_from > $from || $date_to < $to)
                $this->dst = TRUE;
        }
        else {
            if ($date_from > $from && $date_to < $to)
                $this->dst = TRUE;
        }

        if ($this->dst)
            $timezone->zone = $dst_zone;

        $this->setRelation('timezone', $timezone);

        return $this->dst;
    }

    public function getMapControlsAttribute($value)
    {
        return new \SettingsArray(json_decode($value, true));
    }

    public function setMapControlsAttribute($value)
    {
        $this->attributes['map_controls'] = json_encode($value);
    }

    public function getSettingsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setSettingsAttribute($value)
    {
        $this->attributes['settings'] = json_encode($value);
    }

    public function getSettings($key) {
        if (empty($key))
            return null;

        $keys = explode('.', $key);

        $group = array_shift($keys);

        if (empty($group))
            return null;

        $settings = $this->settings;

        $item = empty($settings[$group]) ? null : $settings[$group];

        if (empty($item))
            return null;

        try {
            $value = $this->get_array_value( $item, $keys );
        }
        catch (\Exception $e) {
            $value = $item;
        }

        return $value;
    }

    public function setSettings($key, $value) {
        if (empty($key))
            return false;

        $keys = explode('.', $key);

        $group = array_shift($keys);

        if (empty($group))
            return false;

        $settings = $this->settings;

        $item = empty($settings[$group]) ? [] : $settings[$group];

        $this->set_array_value( $item, $keys, $value );

        $settings[$group] = $item;

        $this->settings = $settings;

        $this->save();
    }

    private function get_array_value($array, $keys) {
        if (empty($keys))
            return $array;

        $key = array_shift($keys);

        if (isset($array[$key]))
            return $this->get_array_value( $array[$key], $keys );
        else
            return null;
    }

    private function set_array_value(&$array, $keys, $value) {
        if (empty($keys))
            return $array = $value;

        $key = array_shift($keys);

        if (!isset($array[$key]))
            $array[$key] = null;

        return $this->set_array_value( $array[$key], $keys, $value );
    }
}
