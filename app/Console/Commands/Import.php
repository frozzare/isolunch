<?php

namespace App\Console\Commands;

use App\Services\Places;
use App\Restaurant;
use Illuminate\Console\Command;

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
     * Property to store number of resturants that have been fetched.
     *
     * @var int
     */
    private $fetched = 0;

    /**
     * Property to store number of resturants that have been imported.
     *
     * @var int
     */
    private $imported = 0;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $places = new Places();
        $instance = $places->getNearBySearchInstance();

        while ($this->fetched < 100) {
            $results = $instance->nearbysearch();
            $this->importResults($instance, $results['results']);

            if (isset($results['next_page_token'])) {
                $instance->pagetoken = $results['next_page_token'];
            } else {
                break;
            }
        }

        $this->info($this->imported . ' of ' . $this->fetched . ' resturants are now imported.');
    }

    /**
     * Import results from google to wordpress.
     *
     * @param $instance instance of GooglePlaces.
     * @param $results result of search.
     */
    private function importResults($instance, $results)
    {
        $papi_page_type_key = $this->getPapiPageTypeKey();
        $this->fetched += count($results);

        foreach ($results as $result) {
            $instance->placeid = $result['place_id'];

            $details = $instance->details();
            $details = $details['result'];

            $post = Restaurant::where('post_title', $details['name'])->first();

            if (empty($post)) {
                $post = new Restaurant();

                $post->post_title = $details['name'];
                $post->post_author = 1;
                $post->post_status = 'draft';

                if ($post->save()) {
                    $post->meta->place_id = $details['place_id'];
                    if (!empty($details['geometry']['location']['lat'])) {
                        $post->meta->lat = $details['geometry']['location']['lat'];
                    }
                    if (!empty($details['geometry']['location']['lng'])) {
                        $post->meta->lng = $details['geometry']['location']['lng'];
                    }
                    if (!empty($details['vicinity'])) {
                        if (str_contains($details['vicinity'], ',')) {
                            $details['vicinity'] = explode(', ', $details['vicinity']);
                            $post->meta->street_adress = $details['vicinity'][0];
                        } else {
                            $post->meta->street_adress = $details['vicinity'];
                        }
                    }
                    if (!empty($details['formatted_phone_number'])) {
                        $post->meta->phone = $details['formatted_phone_number'];
                    }
                    if (!empty($details['website'])) {
                        $post->meta->website = $details['website'];
                    }
                    $post->meta->$papi_page_type_key = 'PostPageType';
                    if ($post->save()) {
                        $this->imported++;
                        $this->info($details['name'] . ' have been imported.');
                    } else {
                        $this->info($details['name'] . ' have been imported, but have no detailed info.');
                    }
                } else {
                    $this->info($details['name'] . ' could not be imported.');
                }
            } else {
                $this->info($details['name'] . ' have not been imported because it already exists.');
            }
        }
    }

    /**
     * Returns papi page type key.
     *
     * @return string
     */
    private function getPapiPageTypeKey()
    {
        define('WP_USE_THEMES', false);
        require __DIR__ . '/../../../public/wp/wp-blog-header.php';
        return \papi_get_page_type_key();
    }
}
