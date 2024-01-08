<?php namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Tobuli\Validation\AdminTrackerPortsFormValidator;
use Tobuli\Exceptions\ValidationException;

class PortsController extends BaseController {
    /**
     * @var AdminTrackerPortsFormValidator
     */
    private $adminTrackerPortsFormValidator;

    function __construct(AdminTrackerPortsFormValidator $adminTrackerPortsFormValidator) {
        parent::__construct();
        $this->adminTrackerPortsFormValidator = $adminTrackerPortsFormValidator;
    }

    public function index(Request $request) {
        $ports = DB::table('tracker_ports')->get();

        return View::make('admin::Ports.'.($request->ajax() ? 'table' : 'index'))->with(compact('ports'));
    }

    public function edit($name) {
        $item = DB::table('tracker_ports')->where('name', '=', $name)->first();

        return View::make('admin::Ports.edit')->with(compact('item'));
    }

    public function update($port_name, Request $request) {
        $input = $request->all();
        $item = DB::table('tracker_ports')->where('name', '=', $port_name)->first();

        $port = trim($input['port']);
        $extras = $input['extra'];

        try {
            $this->adminTrackerPortsFormValidator->validate('update', $input, $item->name);

            $arr = [];
            foreach ($extras as $extra) {
                $name = trim($extra['name']);
                $value = trim($extra['value']);
                if (empty($name) || empty($value))
                    continue;

                $arr[$name] = $value;
            }

            DB::table('tracker_ports')->where('name', '=', $port_name)->update([
                'active' => isset($input['active']),
                'port' => $port,
                'extra' => json_encode($arr)
            ]);
        }
        catch (ValidationException $e)
        {
            return Response::json(['errors' => $e->getErrors()]);
        }

        return response()->json(['status' => 1]);
    }

    public function doUpdateConfig() {
        return View::make('admin::Ports.do_update_config');
    }

    public function updateConfig() {
        Artisan::call('generate:config');
        $res = restartTraccar('user_update_config');

        if ($res == 'OK')
            Session::flash('message', trans('admin.successfully_updated_restarted'));
        else
            Session::flash('error', trans('admin.'.$res));
        return response()->json(['status' => 1]);
    }

    public function doResetDefault() {
        return View::make('admin::Ports.do_reset_default');
    }

    public function resetDefault() {
        DB::table('tracker_ports')->delete();
        parsePorts();
        Artisan::call('generate:config');
        $res = restartTraccar('user_update_config');

        if ($res == 'OK')
            Session::flash('message', trans('admin.successfully_reset_default'));
        else
            Session::flash('error', trans('admin.'.$res));

        return response()->json(['status' => 1]);
    }
}
