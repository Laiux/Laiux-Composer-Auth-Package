<?php

namespace Laiux\Auth\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocalTokenRolesValidationMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {

        $decoded = LocalTokenValidationMiddleware::validateJWTToken($request->bearerToken());

        if($decoded == null){
            abort(401);
        }

        //Starting login for use laravel-permissions

        $authManager = app('auth');
        $guard = $authManager->guard();
        $user = $guard->getProvider()->retrieveById($decoded->id);
        $guard->login($user);

        //Validate roles
        if (!Auth::user()->hasAllRoles($roles)) {
            
            abort(401);
        }

        return $next($request);
    }
}
