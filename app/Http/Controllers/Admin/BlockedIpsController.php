<?php namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class BlockedIpsController extends BaseController {

    function __construct() {
        parent::__construct();
    }

    public function index(Request $request) {
        if (!file_exists(storage_path('app/blocked_ips')))
            Storage::makeDirectory("blocked_ips");

        $files = Storage::files("blocked_ips");
        foreach ($files as $key => $file) {
            $arr = explode('/', $file);
            $files[$key] = end($arr);
        }

        return View::make('admin::BlockedIps.'.($request->ajax() ? 'table' : 'index'))->with(compact('files'));
    }

    public function create()
    {
        return View::make('admin::BlockedIps.create');
    }

    public function store(Request $request)
    {
        $ip = $request->get('ip');
        $ip = filter_var($ip, FILTER_VALIDATE_IP);
        if (!empty($ip))
            Storage::put('blocked_ips/'.$ip, "");

        return ['status' => 1];
    }
    
    public function doDestroy($file)
    {
        return View::make('admin::BlockedIps.destroy', compact('file'));
    }

    public function destroy(Request $request)
    {
        $id = $request->get('id');
        $id = filter_var($id, FILTER_VALIDATE_IP);
        $path = storage_path('app/blocked_ips/'.$id);
        if (!empty($id) && file_exists($path))
            unlink($path);

        return ['status' => 1, 'path' => $path];
    }
}
