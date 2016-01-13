<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use joshtronic\GooglePlaces;

class Import extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports resturants from Google Places.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $instance         = new GooglePlaces( env( 'GOOGLE_API_KEY' ) );
        $instance->types  = 'restaurant';
        $instance->rankby = 'distance';

        // Location of Isotop.
        $instance->location = [59.3367395, 18.0652892];

        $results = $instance->nearbysearch();
        $results = $results['results'];


        dd(\get_posts());



    }
}
