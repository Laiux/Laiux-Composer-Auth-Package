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

        if($decoded == null) abort(401);

        //Validate roles

        if (!$this->validateRoles($decoded->id, $roles)) abort(401);

        return $next($request);
    }

    public static function validateRoles(int $userId, array $roles): bool {

        //Starting login for use laravel-permissions

        $authManager = app('auth');
        $guard = $authManager->guard();
        $user = $guard->getProvider()->retrieveById($userId);
        $guard->login($user);

        //Validate roles

        return Auth::user()->hasAllRoles($roles);
    }
}
