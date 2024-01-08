<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Facades\Repositories\UserRepo;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Tobuli\Repositories\User\UserRepositoryInterface as User;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Foundation\Auth\ResetsPasswords;

class PasswordReminderController extends Controller {

    /*
	|--------------------------------------------------------------------------
	| Password Reset Controller
	|--------------------------------------------------------------------------
	|
	| This controller is responsible for handling password reset requests
	| and uses a simple trait to include this behavior. You're free to
	| explore this trait and override any methods you wish to tweak.
	|
	*/

    use ResetsPasswords;

    /**
     * The password broker implementation.
     *
     * @var PasswordBroker
     */
    protected $passwords;

    function __construct(PasswordBroker $passwords)
    {
        $this->passwords = $passwords;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return View::make('front::PasswordReminder.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $response = $this->passwords->sendResetLink(Input::only('email'), function($m)
        {
            $m->subject(trans('front.password_reminder'));
        });

        return Redirect::route('password_reminder.create')->withInput()->with(($response == PasswordBroker::RESET_LINK_SENT ? 'success' : 'message'), trans($response));

    }

    public function reset($token)
    {
        return View::make('front::PasswordReminder.reset')->with('token', $token);
    }

    public function update()
    {
        $input = Input::only(['email', 'password', 'password_confirmation', 'token']);

        $res = Password::reset($input, function($user, $password)
        {
            UserRepo::update($user->id, ['password' => $password]);
        });

        if ($res == Password::PASSWORD_RESET)
            return Redirect::route('login')->with('success', trans($res));
        else
            return Redirect::route('password_reminder.reset', $input['token'])->with('message', trans($res))->withInput();
    }
} 