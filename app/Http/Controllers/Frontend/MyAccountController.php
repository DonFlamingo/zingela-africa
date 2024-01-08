<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use ModalHelpers\MyAccountSettingsModalHelper;
use Tobuli\Repositories\User\UserRepositoryInterface as User;
use Tobuli\Validation\UserAccountFormValidator;

class MyAccountController extends Controller {

    public function edit(User $userRepo) {
        $item = $userRepo->find(Auth::User()->id);

        return view('front::MyAccount.edit')->with(compact('item'));
    }

    public function update(MyAccountSettingsModalHelper $myAccountSettingsModalHelper, User $userRepo, UserAccountFormValidator $userAccountFormValidator) {
        $input = Input::all();
        $data = $myAccountSettingsModalHelper->changePassword($input, Auth::User(), $userRepo, $userAccountFormValidator);

        return response()->json($data);
    }

    public function changeMap(User $userRepo) {
        $input = Input::all();
        $selected = trim($input['selected']);
        $maps = Config::get('tobuli.maps');
        $map_id = 1;

        if (isset($maps[$selected]))
            $map_id = $maps[$selected];

        $userRepo->update(Auth::User()->id, [
            'map_id' => $map_id
        ]);

        return response()->json(['status' => 1]);
    }
}