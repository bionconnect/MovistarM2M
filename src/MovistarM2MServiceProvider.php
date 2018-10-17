<?php

namespace BionConnection\MovistarM2M;

use Illuminate\Support\ServiceProvider;


class MovistarM2MServiceProvider extends ServiceProvider
{
    protected $defer = false;
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
   
        $this->mergeConfigFrom(dirname(__FILE__).'/../config/movistarm2m.php', 'movistarm2m');

        
        $this->app->singleton('MovistarM2M', function() {

			return new MovistarM2M();

		});

		$this->app->booting(function() {

		  $loader = \Illuminate\Foundation\AliasLoader::getInstance();

		  $loader->alias('MovistarM2M', 'BionConnection\MovistarM2M\Facades\MovistarM2M');

		});
		/*$this->publishes([
			dirname(__FILE__).'/../config/movistarm2m.php'=>config_path('movistarm2m'),
		]);*/
        
     
        
        
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['MovistarM2M'];
    }
    

}
