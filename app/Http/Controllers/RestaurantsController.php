<?php
/**
 * Created by PhpStorm.
 * User: asapersson
 * Date: 16-01-12
 * Time: 11:02
 */

namespace App\Http\Controllers;

use App\Models\Restaurant as Restaurant;
use App\Models\Category as C;
use Illuminate\Support\Facades\App;
use Corcel\Post as Post;
use Corcel\TermTaxonomy as Taxonomy;
use Corcel;
class RestaurantsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showAllRestarants()
    {
//        dd('LURRE');
        $restaurants = $this->createRestaurants();
//        $categories = Category::all();
//        $categories = \Corcel\Post::published()->get();
//        $cat = Taxonomy::where('taxonomy', 'category')->with('posts')->get();
//
//        $cat = Taxonomy::category()->slug('hamburgare')->posts()->get();

        // clean and simple all posts from a category
        $cat = \Category::slug('hamburgare')->posts()->first();
//        dd($cat->posts[0]->post_title);
        $cat->posts->each(function($post) {
            echo $post->post_title;
        });

//        $cat->each(function($category) {
//            var_dump($category->name);
//        });

        return view('restaurants', [
            'restaurants' => $restaurants,
//            'categories' => $categories
        ]);
    }

    /**
     * @return array
     */
    protected function createRestaurants()
    {
        $restaurants_posts = Post::published()->get();
        $restaurants = [];


        foreach ( $restaurants_posts as $rp ) {


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

        return $restaurants;
    }
}
