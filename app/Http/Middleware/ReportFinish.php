<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\TerminableMiddleware;

class ReportFinish implements TerminableMiddleware {

	public function handle($request, Closure $next)
	{
		return $next($request);
	}

	public function terminate($request, $response)
	{
		file_put_contents(public_path('text.txt'), json_encode($response->headers));
	}

}