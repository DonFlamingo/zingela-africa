<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Facades\Repositories\UserRepo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Tobuli\Repositories\User\UserRepositoryInterface as User;

class LoginController extends Controller
{

    public function __construct()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($id = NULL)
    {
        if (Auth::check()) {
            return Redirect::route('objects.index');
        }

        if (!is_null($id)) {
            $user = UserRepo::find($id);
            if (!empty($user) && $user->group_id == 3) {
                Session::set('referer_id', $user->id);
            }

            return redirect()->route('login');
        }

        return View::make('front::Login.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()

    {
        $loginData = [
            "password" => request()->get('password')];

        if (filter_var(request()->get('user_name'), FILTER_VALIDATE_EMAIL)) {
            $loginData['email']=request()->get('user_name');

        } else {
            $loginData['phone']=request()->get('user_name');

        }
        if (Auth::attempt(array_merge($loginData, ['active' => '1']), (Input::get('remember_me') == 1 ? TRUE : FALSE))) {
            /*if (Auth::User()->id == 2)
                Auth::loginUsingId(6);*/

            return Redirect::route('objects.index');
        }
        else {
            return Redirect::route('login')->withInput()->with('message', trans('front.login_failed'));
        }


    }

    /**
     * @param null $id
     * @return mixed
     */
    public function destroy($id = NULL)
    {
        $referer_id = Session::get('referer_id', null);

        Auth::logout();

        if ($referer_id) {
            return Redirect::route('login', $referer_id);
        } else {
            return Redirect::route('home');
        }
    }


    public function loginAs()
    {
        $sub = explode('.', $_SERVER['HTTP_HOST'])['0'];
        return View::make('front::LoginAs.index')->with(compact('sub'));
    }

    public function supporteddevices()
    {
        return View::make('front::Devicesupported.supporteddevicelist');
    }

}
