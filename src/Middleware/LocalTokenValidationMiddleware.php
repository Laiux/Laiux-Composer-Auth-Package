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

        //Execute the validation of token
        $isValid = $this->validateJWTToken($request->bearerToken()) != null;

        if(!$isValid){
            abort(401);
        }

        return $next($request);
    }

    public static function validateJWTToken(string $token): object | null {

        //Load configs

        $secret = config('laiux_auth.secret');
        $alg = config('laiux_auth.algorithm');

        if($token == null){
            return null;
        }

        //Validate the access token
        //1st filter validation of JWT
        $decoded = null;
        try {
            $decoded = JWT::decode($token, new Key($secret, $alg));
        } catch (\Throwable $th) {
            return null;
        }
        //2nd filter validation exists in the sessions
        $session = Session::where('token', $token)->where('user_id', $decoded->id)->first();
        if($session == null){
            return null;
        }

        //Returns the token decoded

        return $decoded;
    }
}
