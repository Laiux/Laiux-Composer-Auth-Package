<?php

namespace Laiux\Auth;

use Illuminate\Support\ServiceProvider;

class AuthenticationProvider extends ServiceProvider {
    
    public function boot(): void {
        $this->loadMigrations();
    }

    private function loadMigrations(): void {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        $this->publishes(
            [
                __DIR__ . '/database/migrations' => base_path('database/migrations'),
            ],
            'laiux-auth-migrations'
        );
    }

}