<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use App\Restaurant;
use Taxonomy;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Displayes all restaurants.
     *
     * @return mixed
     */
    public function index()
    {
        $posts = Restaurant::published()->get();
        $posts = $posts->shuffle();
        $categories = Taxonomy::where('taxonomy', 'category')->get();
        $tags = Taxonomy::where('taxonomy', 'post_tag')->get();

        $tip_of_the_day = rand(0, count($posts) - 1);

        $tip_of_the_day = $posts[$tip_of_the_day];

        return View::make('index')->with('posts', $posts)->with('categories', $categories)->with('tags',
            $tags)->with('tip_of_the_day', $tip_of_the_day);
    }

    /**
     * Displayes single restaurant.
     * @param $slug of restaurant to display.
     * @return mixed
     */
    public function show($slug)
    {
        return View::make('single')->with('post', Restaurant::where('post_name', $slug)->first());
    }

    /**
     * Search method for ajax requests.
     *
     * @param string $term search term.
     * @return mixed
     */
    public function search($term)
    {

        $all_posts = Restaurant::published()->get();

        $posts = Restaurant::published()->where('post_title', 'LIKE', '%' . $term . '%')->get();

        foreach ($all_posts as $post) {
            foreach ($post->taxonomies as $taxonomy) {
                if (str_contains(strtolower($taxonomy->term->name), strtolower($term))) {
                    $posts->add($post);
                    break;
                }
            }
        }

        $posts = $posts->unique();

        return Response::json(['result' => $posts]);
    }
}
