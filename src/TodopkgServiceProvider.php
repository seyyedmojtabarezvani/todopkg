<?php

namespace Rezvani\Todopkg;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class TodopkgServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRoutes();
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/migrations');

        $this->publishes([
            __DIR__ . '/config/config.php' => config_path('todopkg.php'),
        ], 'config');
    }

    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        });
    }

    protected function routeConfiguration()
    {
        return [
            'middleware' => config('auth:api'),
        ];
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Rezvani\Todopkg\Controllers\MainController');
    }
}
