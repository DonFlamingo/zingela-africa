<?php namespace App\Http\Controllers\Admin;

use Facades\Repositories\UserRepo;
use Facades\Repositories\DeviceRepo;
use Facades\Repositories\EventRepo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Repositories\User\UserRepositoryInterface as User;
use Tobuli\Validation\ClientFormValidator;
use Tobuli\Repositories\Device\DeviceRepositoryInterface as Device;
use Tobuli\Repositories\TraccarDevice\TraccarDeviceRepositoryInterface as TraccarDevice;
use Tobuli\Repositories\Event\EventRepositoryInterface as Event;
use Illuminate\Support\Facades\Artisan;

class ObjectsController extends BaseController {
    /**
     * @var ClientFormValidator
     */
    private $clientFormValidator;

    private $section = 'objects';
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
        $users = NULL;
        if (Auth::User()->group_id == 3) {
            $users = Auth::User()->subusers()->lists('id', 'id')->all();
            $users[] = Auth::User()->id;
        }

        $items = $this->device->searchAndPaginateAdmin($input, 'name', 'asc', 100, $users);
        $section = $this->section;
        $page = $items->currentPage();
        $total_pages = $items->lastPage();
        $pagination = smartPaginate($items->currentPage(), $total_pages);
        $url_path = $items->resolveCurrentPath();

        return View::make('admin::'.ucfirst($this->section).'.' . (Request::ajax() ? 'table' : 'index'))->with(compact('items','input','section', 'page', 'total_pages', 'pagination', 'url_path'));
    }

    public function create() {
        $managers = ['0' => '-- '.trans('admin.select').' --'] + UserRepo::getOtherManagers(0)->lists('email', 'id')->all();

        return View::make('admin::'.ucfirst($this->section).'.create')->with(compact('managers'));
    }

    public function destroy() {
        $ids = Input::get('id');

        if (is_array($ids) && count($ids)) {
            foreach($ids as $id) {
                $item = DeviceRepo::find($id);

                if (empty($item) || (!$item->users->contains($this->user->id) && !isAdmin()))
                    continue;

                beginTransaction();

                try {
                    $item->users()->sync([]);

                    DB::connection('traccar_mysql')->table('devices')->where('id', '=', $item->traccar_device_id)->delete();
                    EventRepo::deleteWhere(['device_id' => $item->id]);
                    DeviceRepo::delete($item->id);

                    DB::table('user_device_pivot')->where('device_id', $item->id)->delete();
                    DB::table('device_sensors')->where('device_id', $item->id)->delete();
                    DB::table('device_services')->where('device_id', $item->id)->delete();
                    DB::table('user_drivers')->where('device_id', $item->id)->update(['device_id' => null]);

                    if (Schema::connection('traccar_mysql')->hasTable('positions_'.$item->traccar_device_id))
                        DB::connection('traccar_mysql')->table('positions_'.$item->traccar_device_id)->truncate();

                    if (Schema::connection('sensors_mysql')->hasTable('sensors_'.$item->traccar_device_id))
                        DB::connection('sensors_mysql')->table('sensors_'.$item->traccar_device_id)->truncate();

                    if (Schema::connection('engine_hours_mysql')->hasTable('engine_hours_'.$item->traccar_device_id))
                        DB::connection('engine_hours_mysql')->table('engine_hours_'.$item->traccar_device_id)->truncate();

                    Schema::connection('traccar_mysql')->dropIfExists('positions_'.$item->traccar_device_id);
                    Schema::connection('sensors_mysql')->dropIfExists('sensors_'.$item->traccar_device_id);
                    Schema::connection('engine_hours_mysql')->dropIfExists('engine_hours_'.$item->traccar_device_id);

                    clearCache($item->imei, ['device', 'alerts', 'users']);
                    commitTransaction();
                }
                catch (\Exception $e) {
                    rollbackTransaction();
                }
            }
        }

        return Response::json(['status' => 1]);
    }

    public function restartTraccar() {		
        $status = 0;
		$exitCode = Artisan::call('tracker:restart');
		$res = Artisan::output(); 		
		if ($res == 'OK' || $res == "Ok\n")
            return Redirect::route('admin.clients.index')->withSuccess(trans('admin.tracking_service_restarted'));
        else
            return Redirect::route('admin.clients.index')->withError(trans('admin.'.$res));
		
		
        /*
		$res =  restartTraccar('user_manual_restart');
        if ($res == 'OK')
            return Redirect::route('admin.clients.index')->withSuccess(trans('admin.tracking_service_restarted'));
        else
            return Redirect::route('admin.clients.index')->withError(trans('admin.'.$res));
		*/
    }
}
