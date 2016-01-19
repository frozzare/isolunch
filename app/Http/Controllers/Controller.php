<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\View;

use App\Models\Restaurant as Restaurant;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

     /** Displayes all restaurants.
     *
     * @return mixed
     */
    public function index()
    {
        $posts = Restaurant::published()->get();

        return View::make('index')->with('posts', $posts);
    }

    /**
     * Displayes single restaurant.
     * @param $slug of restaurant to display.
     * @return mixed
     */
    public function show($slug)
    {
        $restaurant =  Restaurant::where('post_name', $slug)->first();
        $restaurant->setRate(3);
        return View::make('single')->with('post',$restaurant );
    }
}
