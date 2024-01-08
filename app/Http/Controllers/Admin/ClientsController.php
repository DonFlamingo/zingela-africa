<?php namespace App\Http\Controllers\Admin;

use Facades\Repositories\DeviceRepo;
use Facades\Repositories\MapIconRepo;
use Facades\Repositories\UserRepo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Repositories\BillingPlan\BillingPlanRepositoryInterface as BillingPlan;
use Tobuli\Validation\ClientFormValidator;
use Tobuli\Repositories\Device\DeviceRepositoryInterface as Device;
use Tobuli\Repositories\TraccarDevice\TraccarDeviceRepositoryInterface as TraccarDevice;
use Tobuli\Repositories\Event\EventRepositoryInterface as Event;
use Tobuli\Repositories\Geofence\GeofenceRepositoryInterface as Geofence;
use Tobuli\Repositories\GeofenceGroup\GeofenceGroupRepositoryInterface as GeofenceGroup;
use Tobuli\Repositories\User\UserRepositoryInterface as User;
use Tobuli\Repositories\UserMapIcon\UserMapIconRepositoryInterface as UserMapIcon;
use ModalHelpers\GeofenceModalHelper;
use ModalHelpers\MapIconModalHelper;
use Facades\Validators\ObjectsListSettingsFormValidator;

class ClientsController extends BaseController {
    /**
     * @var ClientFormValidator
     */
    private $clientFormValidator;

    private $section = 'clients';
    /**
     * @var Device
     */
    private $device;
    /**
     * @var TraccarDevice
     */
    private $traccarDevice;
    /**
     * @var Event
     */
    private $event;

    function __construct(ClientFormValidator $clientFormValidator, Device $device, TraccarDevice $traccarDevice, Event $event)
    {
        parent::__construct();
        $this->clientFormValidator = $clientFormValidator;
        $this->device = $device;
        $this->traccarDevice = $traccarDevice;
        $this->event = $event;
    }

    public function index() {
        $input = Input::all();
        $input['filter']=[
            "users.admin_id"=>\auth()->user()->id
        ];
        $items = UserRepo::searchAndPaginate($input, 'email');
$section = $this->section;
        $page = $items->currentPage();
        $total_pages = $items->lastPage();
        $pagination = smartPaginate($items->currentPage(), $total_pages);
        $url_path = $items->resolveCurrentPath();

        return View::make('admin::'.ucfirst($this->section).'.' . (Request::ajax() ? 'table' : 'index'))->with(compact('items','input','section', 'page', 'total_pages', 'pagination', 'url_path'));
    }

    public function create(BillingPlan $billingPlanRepo) {
        $managers = ['0' => '-- '.trans('admin.select').' --'] + UserRepo::getOtherManagers(0)->lists('email', 'id')->all();
        $maps = getMaps();

        $plans = [];
        if (settings('main_settings.enable_plans'))
            $plans = ['0' => '-- '.trans('admin.select').' --'] + $billingPlanRepo->getWhere([], 'objects', 'asc')->lists('title', 'id')->all();

        $objects_limit = null;
        if (hasLimit()) {
            $objects_limit = Auth::User()->devices_limit - getManagerUsedLimit(Auth::User()->id);
            $objects_limit = $objects_limit < 0 ? 0 : $objects_limit;
        }

        $perms = Config::get('tobuli.permissions');

        $def_perms = settings('main_settings.user_permissions');

        # Available devices
        $devices = $this->availableDevices();

        $numeric_sensors = config('tobuli.numeric_sensors');
        $settings = UserRepo::getListViewSettings(null);
        $fields = config('tobuli.listview_fields');
        listviewTrans(null, $settings, $fields);

        return View::make('admin::'.ucfirst($this->section).'.create')->with(compact('managers', 'maps', 'plans', 'objects_limit', 'perms', 'devices', 'def_perms','fields','settings','numeric_sensors'));
    }

    public function store(BillingPlan $billingPlanRepo) {
        $input = Input::all();
        unset($input['id']);

        if ($input['group_id'] != 2)
            $input['manager_id'] = null;

        try
        {
            if (hasLimit())
                $input['enable_devices_limit'] = 1;

            if (isset($input['enable_devices_limit']) && empty($input['devices_limit']))
                throw new ValidationException(['devices_limit' => strtr(trans('validation.required'), [':attribute' => trans('validation.attributes.devices_limit')])]);

            if (isset($input['enable_expiration_date']) && empty($input['expiration_date']))
                throw new ValidationException(['expiration_date' => strtr(trans('validation.required'), [':attribute' => trans('validation.attributes.expiration_date')])]);

            $this->clientFormValidator->validate('create', $input);

            ObjectsListSettingsFormValidator::validate('update', request()->only(['columns','groupby']));

            if (empty($input['manager_id'])) {
                if( isAdmin() ) {
                    $input['manager_id'] = null;
                } else {
                    unset($input['manager_id']);
                }
            }

            if (array_key_exists('billing_plan_id', $input)) {
                $permissions = Config::get('tobuli.permissions');
                $plan = $billingPlanRepo->find($input['billing_plan_id']);
                if (!empty($plan)) {
                    $input['devices_limit'] = $plan->objects;
                    if (empty($input['subscription_expiration']))
                        $input['subscription_expiration'] = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')." + {$plan->duration_value} {$plan->duration_type}"));
                }
                else {
                    $input['billing_plan_id'] = NULL;
                }
            }

            if (hasLimit()) {
                $objects_limit = Auth::User()->devices_limit - getManagerUsedLimit(Auth::User()->id);
                if ($objects_limit < $input['devices_limit'])
                    throw new ValidationException(['devices_limit' => trans('front.devices_limit_reached')]);
            }

            $input['active'] = isset($input['active']);
            $input['lang'] = settings('main_settings.default_language');
            $input['unit_of_altitude'] = settings('main_settings.default_unit_of_altitude');
            $input['unit_of_distance'] = settings('main_settings.default_unit_of_distance');
            $input['unit_of_capacity'] = settings('main_settings.default_unit_of_capacity');
            $input['timezone_id'] = settings('main_settings.default_timezone');
            $input['map_id'] = settings('main_settings.default_map');
            $input['devices_limit'] = !isset($input['enable_devices_limit']) ? NULL : $input['devices_limit'];
            $input['subscription_expiration'] = !isset($input['enable_expiration_date']) ? '0000-00-00 00:00:00' : $input['expiration_date'];
            $input['open_device_groups'] = '["0"]';
            $input['open_geofence_groups'] = '["0"]';
            $input['admin_id'] = Auth::user()->id;

            $user = UserRepo::create($input);

            if (!empty($user)) {
                if (isset($input['select_all_objects']) && $input['select_all_objects'] == 1) {
                    $devices = $this->availableDevices();
                    $user->devices()->sync(array_flip($devices));
                }
                else {
                    if (!empty($input['objects'])) {
                        $user->devices()->sync($input['objects']);
                    }
                }
            }

            if (!array_key_exists('billing_plan_id', $input) || (array_key_exists('billing_plan_id', $input) && empty($plan))) {
                if (array_key_exists('perms', $input)) {
                    if (!empty($input['manager_id'])) {
                        $manager = UserRepo::find($input['manager_id']);
                    } else {
                        $manager = null;
                    }

                    $permissions = Config::get('tobuli.permissions');
                    foreach ($permissions as $key => $val) {
                        if (!array_key_exists($key, $input['perms']))
                            continue;

                        if ($manager) {
                            $val['view'] = $val['view'] && $manager->perm($key, 'view');
                            $val['edit'] = $val['edit'] && $manager->perm($key, 'edit');
                            $val['remove'] = $val['remove'] && $manager->perm($key, 'remove');
                        }

                        DB::table('user_permissions')->insert([
                            'user_id' => $user->id,
                            'name' => $key,
                            'view' => $val['view'] && (!empty(getArrValue($input['perms'][$key], 'view')) || !empty(getArrValue($input['perms'][$key], 'edit')) || !empty(getArrValue($input['perms'][$key], 'remove'))) ? 1 : 0,
                            'edit' => $val['edit'] && !empty(getArrValue($input['perms'][$key], 'edit')) ? 1 : 0,
                            'remove' => $val['remove'] && !empty(getArrValue($input['perms'][$key], 'remove')) ? 1 : 0
                        ]);
                    }
                }
            }

            UserRepo::setListViewSettings($user->id, request()->only(['columns','groupby']));


            return Response::json(['status' => 1]);
        }
        catch (ValidationException $e)
        {
            return Response::json(['errors' => $e->getErrors()]);
        }
    }

    public function edit($id = NULL, BillingPlan $billingPlanRepo) {
        $item = UserRepo::find($id);
        if (empty($item))
            return modalError(dontExist('global.user'));

        $managers = ['0' => '-- '.trans('admin.select').' --'] + UserRepo::getOtherManagers($item->id)->lists('email', 'id')->all();
        $maps = getMaps();
        $plans = [];
        if (settings('main_settings.enable_plans'))
            $plans = ['0' => '-- '.trans('admin.select').' --'] + $billingPlanRepo->getWhere([], 'objects', 'asc')->lists('title', 'id')->all();

        $objects_limit = null;
        if (hasLimit()) {
            $objects_limit = Auth::User()->devices_limit - getManagerUsedLimit(Auth::User()->id, $item->id);
            $objects_limit = $objects_limit < 0 ? 0 : $objects_limit;
        }

        $perms = Config::get('tobuli.permissions');
        if ($manager = $item->manager) {
            foreach ($perms as $key => $val) {
                $perms[$key]['view'] = $val['view'] && $manager->perm($key, 'view');
                $perms[$key]['edit'] = $val['edit'] && $manager->perm($key, 'edit');
                $perms[$key]['remove'] = $val['remove'] && $manager->perm($key, 'remove');
            }
        }

        # Available devices
        $devices = $this->availableDevices();


        $numeric_sensors = config('tobuli.numeric_sensors');
        $settings = UserRepo::getListViewSettings($id);
        $fields = config('tobuli.listview_fields');
        listviewTrans($id, $settings, $fields);

        return View::make('admin::'.ucfirst($this->section).'.edit')->with(compact('item', 'managers', 'maps', 'plans', 'objects_limit', 'perms', 'devices','fields','settings','numeric_sensors'));
    }

    public function update(BillingPlan $billingPlanRepo) {
        $input = Input::all();
        $id = $input['id'];
        $item = UserRepo::find($id);

        if ($input['email'] == 'demo@atrams.com' && Auth::User()->email != 'admin@atrams.com')
            return Response::json(['errors' => ['id' => "Can't edit main admin account."]]);

        if ($_ENV['server'] == 'demo' && $id == 1 && Auth::User()->id != 1)
            return Response::json(['errors' => ['id' => "Can't edit main admin account."]]);

        try
        {
            if (hasLimit())
                $input['enable_devices_limit'] = 1;

            if (isset($input['enable_devices_limit']) && empty($input['devices_limit']))
                throw new ValidationException(['devices_limit' => strtr(trans('validation.required'), [':attribute' => trans('validation.attributes.devices_limit')])]);

            if (isset($input['enable_expiration_date']) && empty($input['expiration_date']))
                throw new ValidationException(['expiration_date' => strtr(trans('validation.required'), [':attribute' => trans('validation.attributes.expiration_date')])]);

            if (isset($input['expiration_date']))
                $input['subscription_expiration'] = $input['expiration_date'];

            $this->clientFormValidator->validate('update', $input, $id);
            if (empty($input['password']))
                unset($input['password']);

            if (!isset($input['permission_to_use_sms_gateway']))
                $input['sms_gateway'] = 0;

            if (empty($input['manager_id'])) {
                if( isAdmin() ) {
                    $input['manager_id'] = null;
                } else {
                    unset($input['manager_id']);
                }
            }

            if ($id == Auth::User()->id)
                unset($input['manager_id'], $input['group_id']);


            ObjectsListSettingsFormValidator::validate('update', request()->only(['columns','groupby']));

            UserRepo::setListViewSettings($id, request()->only(['columns','groupby']));

            DB::table('user_permissions')->where('user_id', '=', $item->id)->delete();
            $plan = NULL;
            $permissions = Config::get('tobuli.permissions');
            if (array_key_exists('billing_plan_id', $input)) {
                $plan = $billingPlanRepo->find($input['billing_plan_id']);
                if (!empty($plan)) {
                    $input['devices_limit'] = $plan->objects;
                    if (empty($input['subscription_expiration']))
                        $input['subscription_expiration'] = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')." + {$plan->duration_value} {$plan->duration_type}"));
                }
            }

            if (empty($plan)) {
                $input['billing_plan_id'] = NULL;
                $input['devices_limit'] = !isset($input['enable_devices_limit']) ? NULL : $input['devices_limit'];
                $input['subscription_expiration'] = !isset($input['enable_expiration_date']) ? '0000-00-00 00:00:00' : $input['expiration_date'];
            }

            if (Auth::User()->group_id == 3 && Auth::User()->id == $item->id) {
                $input['billing_plan_id'] = $item->billing_plan_id;
                $input['devices_limit'] = $item->devices_limit;
                $input['subscription_expiration'] = $item->subscription_expiration;
            }
            else {
                if (array_key_exists('perms', $input)) {
                    if (!empty($input['manager_id'])) {
                        $manager = UserRepo::find($input['manager_id']);
                    } else {
                        $manager = null;
                    }

                    foreach ($permissions as $key => $val) {
                        if (!array_key_exists($key, $input['perms']))
                            continue;

                        if ($manager) {
                            $val['view'] = $val['view'] && $manager->perm($key, 'view');
                            $val['edit'] = $val['edit'] && $manager->perm($key, 'edit');
                            $val['remove'] = $val['remove'] && $manager->perm($key, 'remove');
                        }

                        DB::table('user_permissions')->insert([
                            'user_id' => $item->id,
                            'name' => $key,
                            'view' => $val['view'] && (!empty(getArrValue($input['perms'][$key], 'view')) || !empty(getArrValue($input['perms'][$key], 'edit')) || !empty(getArrValue($input['perms'][$key], 'remove'))) ? 1 : 0,
                            'edit' => $val['edit'] && !empty(getArrValue($input['perms'][$key], 'edit')) ? 1 : 0,
                            'remove' => $val['remove'] && !empty(getArrValue($input['perms'][$key], 'remove')) ? 1 : 0
                        ]);
                    }
                }

            }

            if (hasLimit()) {
                $objects_limit = Auth::User()->devices_limit - getManagerUsedLimit(Auth::User()->id, $item->id);
                if ($objects_limit < $input['devices_limit'] && $input['devices_limit'] > $item->devices_limit)
                    throw new ValidationException(['devices_limit' => trans('front.devices_limit_reached')]);
            }

            $input['active'] = isset($input['active']);

            UserRepo::update($id, $input);

            $user = UserRepo::getWithFirst(['devices'], ['id' => $id]);
            if (!empty($user->devices)) {
                foreach ($user->devices as $device)
                    $this->device->clearCache($device->imei, 'users');
            }

            if (!empty($user)) {
                if (isset($input['select_all_objects']) && $input['select_all_objects'] == 1) {
                    $devices = $this->availableDevices();
                    $user->devices()->sync(array_flip($devices));
                }
                else {
                    if (!empty($input['objects'])) {
                        $user->devices()->sync($input['objects']);
                    }
                }
            }

            return Response::json(['status' => 1]);
        }
        catch (ValidationException $e)
        {
            return Response::json(['errors' => $e->getErrors()]);
        }
    }


    public function importMapIcon(User $userRepo, MapIconModalHelper $mapIconModalHelper) {
        $users = $userRepo->getUsers(Auth::User());

        $icons = $mapIconModalHelper->getIcons();

        return View::make('admin::'.ucfirst($this->section).'.import_map_icon')->with(compact('users','icons'));
    }

    public function importMapIconSet(User $userRepo, MapIconModalHelper $mapIconModalHelper, UserMapIcon $mapIconRepo) {
        $input = Input::all();
        $file = Request::file('file');

        $file_path = $file->getPathName();
        $content = file_get_contents($file_path);

        if (empty($input['user_id']))
            return response()->json( ['status' => 0] );

        $users = $userRepo->getWhereIn($input['user_id']);
        if (empty($users))
            return response()->json( ['status' => 0] );

        foreach ($users as $user) {
            $response = $mapIconModalHelper->import($content, $input['map_icon_id'], $user, $mapIconRepo);
        }

        return response()->json( $response );
    }

    public function importGeofences(User $userRepo) {
        $users = $userRepo->getUsers(Auth::User());

        return View::make('admin::'.ucfirst($this->section).'.import_geofences')->with(compact('users'));
    }

    public function importGeofencesSet(User $userRepo, GeofenceModalHelper $geofenceModalHelper, Geofence $geofenceRepo, GeofenceGroup $geofenceGroupRepo) {
        $input = Input::all();
        $file = Request::file('file');

        $file_path = $file->getPathName();
        $content = file_get_contents($file_path);

        $users = $userRepo->getWhereIn($input['user_id']);
        if (empty($users))
            return;


        foreach ($users as $user) {
            $geofenceModalHelper->import($content, $user, $geofenceRepo, $geofenceGroupRepo);
        }

        return response()->json(['status' => 1]);
    }

    public function getDevices($id) {

        $items = UserRepo::getWithFirst(['devices', 'devices.traccar'], ['id' => $id])->devices;

        return View::make('admin::Clients.get_devices')->with(compact('items'));
    }

    public function destroy() {
        $ids = Input::get('id');
        if (is_array($ids) && count($ids)) {
            foreach($ids as $id) {
                $user = UserRepo::getWithFirst(['devices'], ['id' => $id]);
                if (!empty($user->devices)) {
                    foreach ($user->devices as $device)
                        $this->device->clearCache($device->imei, 'users');
                }
                UserRepo::delete($id);
            }
        }

        return Response::json(['status' => 1]);
    }

    public function loginAs($id) {
        $item = UserRepo::find($id);

        return View::make('admin::Clients.login_as')->with(compact('item'));
    }

    public function loginAsAgree($id) {
        $item = UserRepo::find($id);

        if ( Auth::User()->group_id == 3 && $item && $item->manager_id != Auth::User()->id ) {
            $item = null;
        }

        if (!empty($item) && $item->email != 'admin@atrams.com')
            Auth::loginUsingId($item->id);

        return Redirect::route('home');
    }

    public function getPermissionsTable(BillingPlan $billingPlanRepo, User $userRepo) {
        $input = Input::all();
        $perms = Config::get('tobuli.permissions');

        $plan = NULL;
        $item = NULL;

        if (array_key_exists('id', $input) && !empty($input['id']))
            $plan = $billingPlanRepo->find($input['id']);

        if (!empty($plan))
            $item = $plan;
        else {
            if (array_key_exists('user_id', $input) && !empty($input['user_id']))
                $user = $userRepo->find($input['user_id']);

            if (!empty($user))
                $item = $user;
        }

        $def_perms = settings('main_settings.user_permissions');

        return view('Admin.Clients._perms')->with(compact('perms', 'plan', 'item', 'def_perms'));
    }

    private function availableDevices() {
        # Available devices
        if (Auth::User()->group_id == 1) { # Admin
            $devicesq = \Tobuli\Entities\Device::where('deleted', '!=', '1')->whereHas('users',function ($query){
                $query->where('email',auth()->user()->email);
            })->orderby('name')->get();
        }
        else { # Manager
            $users = Auth::User()->subusers()->lists('id','id');
            $users[] = Auth::User()->id;
            $devicesq = \Tobuli\Entities\Device::join("user_device_pivot", 'devices.id', '=', 'user_device_pivot.device_id')
                ->whereHas('users',function ($query){
                    $query->where('email',auth()->user()->email);
                })
                ->whereIn('user_device_pivot.user_id', $users)
                ->where('deleted', '!=', '1')
                ->groupBy('devices.id')
                ->orderby('devices.name')
                ->get();
        }

        $devices = [];
        if (!empty($devicesq)) {
            foreach ($devicesq as $device)
                $devices[$device->id] = $device->name;
        }

        return $devices;
    }
}
