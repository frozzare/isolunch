<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
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

        return View::make('index')->with('posts', $posts)->with('categories', $categories)->with('tags', $tags);
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
}
