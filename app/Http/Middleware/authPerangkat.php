<?php

namespace App\Http\Middleware;

use Closure;
use App\Perangkat;

class authPerangkat
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->hash_id == null || $request->hash_pass == null) {
            return response()->json(['message' => 'Unauthorized Devices'], 401);
        }
        $perangkat = Perangkat::where([['hash_id', '=', $request->hash_id], ['hash_pass', '=', $request->hash_pass]])->first();
        if ($perangkat != null) {
            $request->perangkat = $perangkat;
            return $next($request);
        } else
            return response()->json(['message' => 'Unauthorized'], 401);
    }
}
