<?php

namespace Thotam\ThotamFileLibrary;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Thotam\ThotamFileLibrary\Console\Commands\CleanPublicDisk_Command;

class ThotamFileLibraryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'thotam-file-library');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'thotam-file-library');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        Route::domain('beta.' . env('APP_DOMAIN', 'cpc1hn.com.vn'))->group(function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/routes.php');
        });

        $this->loadRoutesFrom(__DIR__ . '/../routes/home_routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('thotam-file-library.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/thotam-file-library'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/thotam-file-library'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/thotam-file-library'),
            ], 'lang');*/

            // Registering package commands.
            $this->commands([
                CleanPublicDisk_Command::class
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Seed Service Provider need on boot() method
        |--------------------------------------------------------------------------
        */
        $this->app->register(SeedServiceProvider::class);
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'thotam-file-library');

        // Register the main class to use with the facade
        $this->app->singleton('thotam-file-library', function () {
            return new ThotamFileLibrary;
        });
    }
}
