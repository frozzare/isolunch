<?php
namespace App\Http\Controllers;

use App\Models\Restaurant as Restaurant;
use App\Models\Category;


class SingleRestaurantController  extends Controller
{
    public function showRestaurant($attribute)
    {
        $restaurants = Restaurant::find(30);
        echo $restaurants->meta->website;
        $categories = Category::all();


        return view('restaurants', [
            'restaurants' => $restaurants,
            'categories' => $categories ]);
    }
}
