<?php

namespace App\Http\Controllers;

use App\Models\Rate;
use Corcel\Post;
use Facebook\Facebook;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use App\Models\Comment;
use App\Models\Restaurant;
use Illuminate\Support\Str;
use Corcel\TermTaxonomy as Taxonomy;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Constructor for Controller sets variables for all views.
     */
    public function __construct()
    {
        View::share('highest_rated_restaurants', Rate::orderBy('rate', 'desc')->orderBy('number_of_voters', 'desc')->limit(5)->get());
        $posts = Restaurant::published();
        View::share('latest', $posts->limit(5)->get());
        $posts = $posts->get();
        $tip_of_the_day = rand(0, count($posts) - 1);
        $tip_of_the_day = $posts[$tip_of_the_day];
        View::share('tip_of_the_day', $tip_of_the_day);
    }

    /**
     * Displayes all restaurants.
     *
     * @return mixed
     */
    public function index()
    {

        $food_trucks = $this->food_trucks( ); //TODO REMOVE

        $posts = Restaurant::published()->get();
        $posts = $posts->shuffle();

        $categories = Taxonomy::where('taxonomy', 'category')->get();
        $tags = Taxonomy::where('taxonomy', 'post_tag')->get();

        return View::make('index')->with('posts', $posts)->with('categories', $categories)
            ->with('tags', $tags)
            ->with('food_trucks',$food_trucks);
    }

    /**
     * Displayes single restaurant.
     *
     * @param string $slug of restaurant to display.
     *
     * @return mixed
     */
    public function show($slug)
    {
        $restaurant = Restaurant::where('post_name', $slug)->first();
        return View::make('single')->with('post', $restaurant);
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

    /**
     * Proxy for geting images from google.
     *
     * @param string $photoreference photo reference from google.
     * @return mixed
     */
    public function image($photoreference)
    {
        $url = 'https://maps.googleapis.com/maps/api/place/photo?key=' .
            env('GOOGLE_API_KEY') . '&photoreference=' . $photoreference . '&maxwidth=300';
        $file = null;
        try {
            if (Cache::has($photoreference)) {
                $file = Cache::get($photoreference);
            } else {
                $file = file_get_contents($url);
                Cache::forever($photoreference, $file);
            }

        } catch (\Exception $e) {
        }

        if (!empty($file)) {
            $response = Response::make($file, 200);
            $response->header('Content-Type', 'image/jpeg');
            return $response;
        }
    }

    public function food_trucks( ){

//        fields=name,posts.limit(1){data.limit(1){message}}

//                $this->fb( '/654197174612181?fields=about,posts.limit(1){description}' ); //TODO REMOVE
        $url = '/654197174612181?fields=name,posts.limit(3)';
        $fb = new Facebook();

        $request = $fb->request('GET', $url);

// Send the request to Graph
        try {
            $response = $fb->getClient()->sendRequest($request);
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        $graphNode = $response->getGraphNode();
        $trucks = [];

        $truck = new \stdClass();
        $truck->name =  $graphNode['name'];
        $truck->message =  $graphNode['posts'][0]['message'];
//                'created_time' =>  $graphNode['posts'][0]['created_time']
        $trucks[] = $truck;
        return $trucks;

    }



    /**
     * Creates comment on restaurant.
     *
     * @param string $slug slug of restaurant to create comment on.
     * @return mixed
     */
    public function comment($slug)
    {
        $post = Restaurant::where('post_name', $slug)->first();
        $input = Input::all();

        if (!empty($post)) {
            /*
             * Check for email
             */
            if (Str::contains($input['email'], '@isotop.se')) {
                $has_voted = false;
                /*
                 * Rate
                 */
                if (!empty($input['rating']))
                {
                    $rate = $input['rating'];

                    $has_voted = $post->setRate(intval($rate));
                }

                /*
                 * Comment
                 */
                $comment_content = strip_tags($input['comment']);
                if (!empty($comment_content)) {
                    $comment = new Comment();

                    $email = strip_tags($input['email']);
                    $username = explode('@', $email);
                    $username = $username[0];
                    if (Str::contains($username, '.')) {
                        $username = explode('.', $username);
                        if (count($username) == 2) {
                            $username = ucfirst($username[0]) . ' ' . ucfirst($username[1]);
                        }
                    }

                    if (!empty($username)) {
                        $comment->comment_author = ucfirst($username);
                        $comment->comment_author_email = $email;
                        $comment->comment_content = $comment_content;
                        $comment->comment_approved = 1;
                        $comment->comment_post_ID = $post->ID;

                        if ($comment->save()) {
                            if ($has_voted){
                                return Redirect::back()->with('success', 'Du har nu lämnat din kommentar och röstat');
                            } else {
                                return Redirect::back()->with('success', 'Du har nu lämnat din kommentar.');
                            }
                        }
                        /*
                         * something went wrong when saving comment
                         */
                        return Redirect::back()->with('errors', 'Din kommentar kunde inte skapas, testa igen.');
                    }
                    /*
                     * incorrect email, can't retrive name from email
                     */
                    return Redirect::back()->with('errors', 'Ditt namn kan inte vara tomt.');//din email är inte korrekt
                 }
                /*
                 * no comment
                 */
                if ($has_voted){
                    return Redirect::back()->with('success', 'Tack för din röst.');
                } else {
                    return Redirect::back()->with('errors', 'Du kan inte lämna en tom kommentar.');
                }
            }
            /*
             * no email
             */
            return Redirect::back()->with('errors', 'Du måte uppge din isotop e-post.');

        }
        /*
         * no post
         */
        return Redirect::back()->with('errors', 'Du kan inte kommentera på en restaurang som inte finns.');
    }
}
