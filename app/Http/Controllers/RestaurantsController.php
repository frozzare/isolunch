<?php
/**
 * Created by PhpStorm.
 * User: asapersson
 * Date: 16-01-12
 * Time: 11:02
 */

namespace App\Http\Controllers;

use App\Models\Restaurant as Restaurant;
use App\Repository\RestaurantRepository;
use Corcel;
class RestaurantsController extends Controller
{
    /**
     * @var Restaurant
     */
    private $restaurant;

    public function __construct(RestaurantRepository $restaurant) {

        $this->restaurant = $restaurant;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showAllRestarants()
    {
        $restaurants = $this->restaurant->getAllRestaurants();
        return view('index', [
            'posts' => $restaurants,
        ]);
    }


}
