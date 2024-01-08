<?php namespace App\Http\Controllers\Frontend;

use Curl;
use App\Http\Controllers\Controller;
use Facades\ModalHelpers\RegistrationModalHelper;
use Illuminate\Http\Request;
class RegistrationController extends Controller
{
    public function create()
    {
        return view('front::Registration.create');
    }
    
    public function store()
    {
        $data = RegistrationModalHelper::create();
        
        if (!$this->api) {
            if ($data['status']) {
                return redirect()->route('login')->with('success', trans('front.registration_successful'));
            }
            else {
                return redirect()->route('registration.create')->withInput()->withErrors($data['errors']);
            }
        }
        else {
            return $data;
        }
    }

    public function apiRegister(){
        try{
            return $data = RegistrationModalHelper::create();
        }catch(\Exception $e){
            return response([$e->getMessage()],422);
        }
    }
}