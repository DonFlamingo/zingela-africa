<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;

class Authenticate {

	/**
	 * The Guard implementation.
	 *
	 * @var Guard
	 */
	protected $auth;

	/**
	 * Create a new filter instance.
	 *
	 * @param  Guard  $auth
	 * @return void
	 */
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
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
		if ($this->auth->guest())
		{
			if ($request->ajax())
			{
				return response('Unauthorized.', 401);
			}
			else
			{
				return redirect()->guest(route('home'));
			}
		}

		if (!Auth::User()->active) {
			Auth::logout();
			if ($request->ajax())
			{
				return response('Unauthorized.', 401);
			}
			else {
				return redirect()->guest(route('home'));
			}
		}

		Auth::User()->loged_at = date('Y-m-d H:i:s');
		Auth::User()->save();

		return $next($request);
	}

}
