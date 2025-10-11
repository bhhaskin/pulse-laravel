<?php

namespace Bhhaskin\Pulse;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class PulseServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/pulse.php', 'pulse');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerRoutes();
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadFactoriesFrom(__DIR__ . '/../database/factories');

        $this->publishes([
            __DIR__ . '/../config/pulse.php' => $this->appConfigPath('pulse.php'),
        ], 'pulse-config');

        $this->publishes([
            __DIR__ . '/../database/migrations/' => $this->appDatabasePath('migrations'),
        ], 'pulse-migrations');

        $this->publishes([
            __DIR__ . '/../database/factories/' => $this->appDatabasePath('factories'),
        ], 'pulse-factories');

        $this->publishes([
            __DIR__ . '/../database/seeders/' => $this->appDatabasePath('seeders'),
        ], 'pulse-seeders');
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
            'prefix' => config('pulse.prefix', 'pulse'),
            'middleware' => config('pulse.middleware', ['api']),
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

    /**
     * Resolve the application database migrations path with a Lumen/testing fallback.
     */
    protected function appDatabasePath(string $append = ''): string
    {
        if (function_exists('database_path')) {
            return rtrim(database_path($append ?: '.'), '/');
        }

        return rtrim($this->app->databasePath($append ?: '.'), '/');
    }
}
