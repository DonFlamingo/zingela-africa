<?php namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class ToolsController extends BaseController {

    public function index() {

        $tools = [
            'backup'   => \App::call('App\Http\Controllers\Admin\BackupsController@panel'),
            'db_clear' => \App::call('App\Http\Controllers\Admin\DatabaseClearController@panel')
        ];

        return View::make('admin::Tools.index')->with(compact('tools'));
    }
}
