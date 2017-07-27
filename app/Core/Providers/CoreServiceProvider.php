<?php namespace Core\Providers;

use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerEmailInterface();
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        
    }


    public function registerEmailInterface()
    {

        $this->app->bind('Core\Repositories\Email\EmailInterface', function($app) {
            return new \Core\Repositories\Email\LaravelEmail();
        });

    }
}
