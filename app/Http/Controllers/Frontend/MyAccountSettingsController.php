<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Facades\ModalHelpers\MyAccountSettingsModalHelper;
use Facades\Repositories\UserRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MyAccountSettingsController extends Controller
{
    public function edit()
    {
        $data = MyAccountSettingsModalHelper::editData();

        return !$this->api ? view('front::MyAccountSettings.edit')->with($data) : $data;
    }

    public function update()
    {
        return MyAccountSettingsModalHelper::edit();
    }
    
    public function ChangePassword()
    {
        return MyAccountSettingsModalHelper::changePassword();
    }

    public function changeLang($lang)
    {
        Session::put('language', $lang);

        return redirect()->route('home');
    }

    public function changeTopToolbar() {
        $status = request()->get('status');
        $status = $status == 1 ? 1 : 0;

        Auth::User()->update(['top_toolbar_open' => $status]);

        return ['status' => 1];
    }

    public function changeMapSettings(Request $request) {
        if ( isDemoUser() )
            return ['status' => 1];

        $settings = array_flip([
            'm_open',
            'm_objects',
            'm_geofences',
            'm_routes',
            'm_poi',
            'm_show_names',
            'm_show_tails',
            'history_control_arrows',
            'history_control_route',
            'history_control_stops',
            'history_control_events',
        ]);

        $param = $request->get('param');
        $value = $request->get('value');
        if (array_key_exists($param, $settings)) {
            $array = Auth::User()->map_controls->getArray();
            $array[$param] = ($value == 'true' ? 1 : 0);

            Auth::User()->update(['map_controls' => $array]);
        }

        return ['status' => 1];
    }
}