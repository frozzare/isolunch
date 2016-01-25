<?php

namespace App\Providers;

use Corcel\Database;
use Illuminate\Support\ServiceProvider;
use joshtronic\GooglePlaces;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */

    public function boot()
    {
        $this->app->bind('Places', function () {
            $instance         = new GooglePlaces(env('GOOGLE_API_KEY'));
            $instance->types  = 'restaurant';
            $instance->rankby = 'distance';

            // Location of Isotop.
            $instance->location = [59.3367395, 18.0652892];

            return $instance;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
