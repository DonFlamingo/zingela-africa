<?php namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Facades\Settings;
use Tobuli\Exceptions\ValidationException;

class PluginsController extends BaseController {

    public function index() {
        $settings = Settings::get('plugins');

        $plugins = [];

        foreach($settings as $key => $plugin) {
            $plugins[] = (object)[
                'key'    => $key,
                'status' => $plugin['status'],
                'options'=> empty($plugin['options']) ? [] : $plugin['options'],
                'name'   => trans('front.' . $key)
            ];
        }

        return View::make('admin::Plugins.index')->with(compact('plugins'));
    }

    public function save() {
        $input = Input::all();

        try
        {
            Settings::set('plugins', $input['plugins']);

            return Redirect::route('admin.plugins.index')->withSuccess(trans('front.successfully_saved'));
        }
        catch (ValidationException $e)
        {
            return Redirect::route('admin.plugins.index')->withInput()->withErrors($e->getErrors());
        }
    }
}
