<?php

namespace Laiux\Auth\Middleware;

use Closure;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Laiux\Auth\Models\Session;

class LocalTokenValidationMiddleware
{
    public function handle(Request $request, Closure $next)
    {

        //Execute the validation of token
        $decodedToken = $this->validateJWTToken($request->bearerToken());
        $isValid =  $decodedToken != null;

        if(!$isValid) abort(401);

        //Start session
        $authManager = app('auth');
        $guard = $authManager->guard();
        $user = $guard->getProvider()->retrieveById($decodedToken->id);
        $guard->login($user);

        return $next($request);
    }

    public static function validateJWTToken(string $token): object | null {

        //Load configs

        $secret = config('laiux_auth.secret');
        $alg = config('laiux_auth.algorithm');

        if($token == null) return null;

        //Validate the access token
        //1st filter validation of JWT
        $decoded = null;
        try {
            $decoded = JWT::decode($token, new Key($secret, $alg));
        } catch (ExpiredException $ex){
            $session = Session::where('token', $token)->first();
            if($session == null) return null;
            $session->delete();
            return null;
        } catch (\Throwable $th) {
            return null;
        }
        //2nd filter validation exists in the sessions
        $session = Session::where('token', $token)->where('user_id', $decoded->id)->first();
        if($session == null) return null;

        //Returns the token decoded

        return $decoded;
    }
}
