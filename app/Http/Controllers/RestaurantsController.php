<?php
/**
 * Created by PhpStorm.
 * User: asapersson
 * Date: 16-01-12
 * Time: 11:02
 */

namespace App\Http\Controllers;

use App\Models\Restaurant as Restaurant;
use App\Models\Category;

class RestaurantsController extends Controller
{
    public function showAllRestarants()
    {

        $restaurants = Restaurant::all();
        $categories = Category::all();

        $restaurants_posts = \Corcel\Post::published()->get();

        return view('restaurants',
            [
                'wp_post' => $restaurants_posts[0],
                'restaurants' => $restaurants,
                'categories' => $categories
            ]
        );
    }

    protected function create_restaurants( $restaurant_posts )
    {
//        foreach ( $re-staurant_posts as $rp ) {
//            App::make
//        }
    }
}
