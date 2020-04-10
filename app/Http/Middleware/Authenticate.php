<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

// use PHPUnit\Runner\Exception;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    // Override handle method
    public function handle($request, Closure $next, ...$guards)
    {
        try {
            if ($this->authenticate($request, $guards) === 'authentication_failed') {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
        } catch (Exception $e) {
            return response(['message' => 'Token Anda tidak valid'], 401);
        }
        return $next($request);
    }
    // Override authentication method
    protected function authenticate($request, array $guards)
    {
        try {
            if (empty($guards)) {
                $guards = [null];
            }
            foreach ($guards as $guard) {
                if ($this->auth->guard($guard)->check()) {
                    return $this->auth->shouldUse($guard);
                }
            }
            return 'authentication_failed';
        } catch (Exception $e) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }
}
