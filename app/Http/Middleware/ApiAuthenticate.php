<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Tobuli\Entities\User;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;

class ApiAuthenticate {

	/**
	 * Create a new filter instance.
	 *
	 * @param  Guard  $auth
	 * @return void
	 */
	public function __construct()
	{
		Config::set('tobuli.api', 1);
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$input = Input::all();
		$user = null;
		if (isset($input['user_api_hash'])) {
			$hash = $input['user_api_hash'];
			$user = User::where('api_hash', $hash)->first();
		}

		if (empty($user))
			return response(json_encode(['status' => 0, 'message' => trans('front.login_failed')]), 401);


		App::setLocale($user->lang);

        if (!empty($input['lang'])) {
            App::setLocale( $input['lang'] );
        }

		Auth::loginUsingId($user->id);

		Auth::User()->loged_at = date('Y-m-d H:i:s');
		Auth::User()->save();

		return $next($request);
	}

}
