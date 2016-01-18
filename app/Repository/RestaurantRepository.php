<?php
namespace App\Repository;

use Bosnadev\Repositories\Contracts\RepositoryInterface;
use Bosnadev\Repositories\Eloquent\Repository;
use App\Models\Restaurant;
use Corcel\Post as Post;

/**
 * Created by PhpStorm.
 * User: asapersson
 * Date: 16-01-18
 * Time: 13:16
 */
class RestaurantRepository extends Repository {

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\Models\Restaurant';
    }

    public function getAllRestaurants() {
        $restaurants = $this->createRestaurants();
        return $restaurants;
    }

    /**
     * @return array
     */
    private function createRestaurants()
    {
        $restaurants_posts = Post::published()->get();
        $restaurants = Restaurant::all();
        $restaurants = $this->all();
        $all_saved_restaurant_names = Restaurant::lists('name');

        foreach ( $restaurants_posts as $rp ) {
//            $taxonomy = $rp->taxonomies()->first();

//            dd($taxonomy);

            if ( !$all_saved_restaurant_names->contains( $rp->post_title ) ){





                $restaurant = new  Restaurant();
                $restaurant->{Restaurant::RESTAURANT_NAME} = $rp->post_title;
                $restaurant->{Restaurant::RESTAURANT_STREET_ADDRESS} = $rp->meta->street_adress;
                $restaurant->{Restaurant::RESTAURANT_DESCRIPTION} = $rp->post_content;
                $restaurant->{Restaurant::RESTAURANT_LAT} = $rp->meta->lat;
                $restaurant->{Restaurant::RESTAURANT_LONG} = $rp->meta->lng;
                $restaurant->{Restaurant::RESTAURANT_WEB_SITE} = $rp->meta->website;

                $restaurant->{Restaurant::RESTAURANT_GOOGLE_PLACE_ID} = $rp->meta->website;
                $restaurant->save();
//            $restaurant->{Restaurant::RESTAURANT_IMAGE} = $rp->image;
                $restaurants[] = $restaurant;
            }

        }
        return $restaurants;
    }
}
