<?php

namespace Laiux\Auth\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Laiux\Auth\Models\Session;

class LocalTokenValidationMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        //Load configs

        $secret = config('laiux_auth.secret');
        $alg = config('laiux_auth.algorithm');

        //Get and validate the access token

        $token = $request->bearerToken();

        if($token == null){
            return response('', 401);
        }

        //Validate the access token
        //1st filter validation of JWT
        $decoded = null;
        try {
            $decoded = JWT::decode($token, new Key($secret, $alg));
        } catch (\Throwable $th) {
            return response('', 401);
        }
        //2nd filter validation exists in the sessions
        $session = Session::where('token', $token)->where('user_id', $decoded->id)->first();
        if($session == null){
            return response('', 401);
        }

        return $next($request);
    }
}
