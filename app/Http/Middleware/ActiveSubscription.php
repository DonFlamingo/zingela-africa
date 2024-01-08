<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ActiveSubscription {

	/**
	 * Create a new filter instance.
	 *
	 * @param  Guard  $auth
	 * @return void
	 */
	public function __construct()
	{
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
		if (strtotime(Auth::User()->subscription_expiration) < time() && Auth::User()->subscription_expiration != '0000-00-00 00:00:00') {
			if (!is_null(Auth::User()->billing_plan_id)) {
				return redirect(route('subscriptions.renew'))->with(['message' => trans('front.subscription_expired')]);
			}

			Auth::logout();
			return redirect(route('login'))->with(['message' => trans('front.subscription_expired')]);
		}

		return $next($request);
	}

}
