<?php

namespace Laiux\Auth\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocalTokenPermissionsValidationMiddleware
{
    public function handle(Request $request, Closure $next, string ...$permissions)
    {

        $decoded = LocalTokenValidationMiddleware::validateJWTToken($request->bearerToken());

        if($decoded == null) abort(401);

        //Validate permissions

        if (!$this->validatePermissions($decoded->id, $permissions)) abort(401);

        return $next($request);
    }

    public static function validatePermissions(int $userId, array $permissions): bool {

        //Starting login for use laravel-permissions

        $authManager = app('auth');
        $guard = $authManager->guard();
        $user = $guard->getProvider()->retrieveById($userId);
        $guard->login($user);

        //Validate permissions

        return Auth::user()->hasAllPermissions($permissions);
    }
}
