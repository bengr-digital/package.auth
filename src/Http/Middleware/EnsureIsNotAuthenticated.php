<?php

namespace Bengr\Auth\Http\Middleware;

use Bengr\Auth\Exceptions\AlreadyAuthenticatedException;
use Closure;
use Illuminate\Http\Request;

use function Bengr\Support\response;

class EnsureIsNotAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (auth()->guard($guard)->check()) {
                return response()->throw(AlreadyAuthenticatedException::class);
            }
        }

        return $next($request);
    }
}
