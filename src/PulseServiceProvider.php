<?php

namespace Bhhaskin\PulseLaravel;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class PulseServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/pulse-laravel.php', 'pulse-laravel');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerRoutes();

        $this->publishes([
            __DIR__ . '/../config/pulse-laravel.php' => $this->appConfigPath('pulse-laravel.php'),
        ], 'pulse-laravel-config');
    }

    /**
     * Register the API routes that Pulse JS consumes.
     */
    protected function registerRoutes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::group([
            'prefix' => config('pulse-laravel.prefix', 'pulse'),
            'middleware' => config('pulse-laravel.middleware', ['api']),
        ], function (): void {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        });
    }

    /**
     * Resolve the application config path, falling back to the package path for Lumen/testing.
     */
    protected function appConfigPath(string $file): string
    {
        if (function_exists('config_path')) {
            return config_path($file);
        }

        return $this->app->basePath('config/' . $file);
    }
}
