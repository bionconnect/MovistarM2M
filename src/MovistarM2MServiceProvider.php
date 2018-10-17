<?php

namespace BionConnection\MovistarM2M;

use Illuminate\Support\ServiceProvider;

class MovistarM2MServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'bionconnection');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'bionconnection');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/movistarm2m.php', 'movistarm2m');

        // Register the service the package provides.
        $this->app->singleton('movistarm2m', function () {
            $movistar = new Api(config('movistarm2m.apiKey'));
            return MovistarM2M($movistar);
            
        });
        
        
     
        
        
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['movistarm2m'];
    }
    
    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/movistarm2m.php' => config_path('movistarm2m.php'),
        ], 'movistarm2m.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/bionconnection'),
        ], 'movistarm2m.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/bionconnection'),
        ], 'movistarm2m.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/bionconnection'),
        ], 'movistarm2m.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
