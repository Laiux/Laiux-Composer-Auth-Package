<?php

namespace Laiux\Auth;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class AuthenticationProvider extends ServiceProvider {
    
    public function boot(): void {
        $this->loadMigrations();
        $this->loadMiddleware();
    }

    private function loadMigrations(): void {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        $this->publishes(
            [
                __DIR__ . '/../config/laiux_auth.php' => config_path('laiux_auth.php'),
            ],
            'laiux-auth-config'
        );

        $this->publishes(
            [
                __DIR__.'/../database/migrations/create_sessions_table.php.stub' => $this->getMigrationFileName('create_sessions_table.php'),
            ], 
            'laiux-auth-migrations'
        );
    }

    protected function loadMiddleware() {
        $router = $this->app['router'];

        // Registro de middleware locales
        $router->aliasMiddleware('localTokenValidation.middleware', \Laiux\Auth\Middleware\LocalTokenValidationMiddleware::class);
        $router->aliasMiddleware('localTokenRolesValidation.middleware', \Laiux\Auth\Middleware\LocalTokenRolesValidationMiddleware::class);
        $router->aliasMiddleware('localTokenPermissionsValidation.middleware', \Laiux\Auth\Middleware\LocalTokenPermissionsValidationMiddleware::class);

        // Registro de middleware remotos
        $router->aliasMiddleware('remoteTokenValidation.middleware', \Laiux\Auth\Middleware\RemoteTokenValidationMiddleware::class);
        $router->aliasMiddleware('remoteTokenRolesValidation.middleware', \Laiux\Auth\Middleware\RemoteTokenRolesValidationMiddleware::class);
        $router->aliasMiddleware('remoteTokenPermissionsValidation.middleware', \Laiux\Auth\Middleware\RemoteTokenPermissionsValidationMiddleware::class);
    }

    private function getMigrationFileName(string $migrationFileName): string
    {
        $timestamp = date('Y_m_d_His');

        $filesystem = $this->app->make(Filesystem::class);

        return Collection::make([$this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR])
            ->flatMap(fn ($path) => $filesystem->glob($path.'*_'.$migrationFileName))
            ->push($this->app->databasePath()."/migrations/{$timestamp}_{$migrationFileName}")
            ->first();
    }

}