<?php

namespace App\Http\Middleware;

namespace App\Http\Middleware;

use Closure;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\Redirect;

class AuthenticateApi
{
    // public function handle($request, Closure $next)
	// {
	// 	if (!auth('api')->check()) {
	// 		throw new ApiException('UNAUTHORIZED EXCEPTION', null, 401, 401);
	// 	}

	// 	return $next($request);
	// }
}
