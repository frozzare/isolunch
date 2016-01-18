<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use App\Models\Restaurant as Restaurant;

class ModelsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
//    public function boot()
//    {
//        //
//    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

//        App::bind('Restaurant', function($app)
//        {
//            return new Restaurant;
//        });

//        $this->app->bind('App\Models\Restaurant', 'App\Models\Interfaces\Restaurant_interface');

//        $this->app->singleton(Connection::Restaurant, function ($app) {
//            return new Connection(config('riak'));
//        });
    }
}
