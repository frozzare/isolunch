<?php

namespace App\Http\Controllers;

use App\Comment;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use App\Restaurant;
use Illuminate\Support\Str;
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

        return View::make('index')->with('posts', $posts)->with('categories', $categories)
            ->with('tags', $tags)->with('tip_of_the_day', $tip_of_the_day);
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
            $comment_content = strip_tags($input['comment']);

            if (!empty($comment_content)) {
                if (Str::contains($input['email'], '@isotop.se')) {
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
                        $comment->comment_author = $username;
                        $comment->comment_author_email = $email;
                        $comment->comment_content = $comment_content;
                        $comment->comment_approved = 1;
                        $comment->comment_post_ID = $post->ID;

                        if ($comment->save()) {
                            return Redirect::back()->with('success', 'Du har nu lämnat din kommentar.');
                        }
                        return Redirect::back()->with('errors', 'Din kommentar kunde inte skapas, testa igen.');
                    }
                    return Redirect::back()->with('errors', 'Ditt namn kan inte vara tomt.');
                }
                return Redirect::back()->with('errors', 'Du måte uppge din isotop e-post.');
            }
            return Redirect::back()->with('errors', 'Du kan inte lämna en tom kommentar.');
        }
        return Redirect::back()->with('errors', 'Du kan inte kommentera på en restaurang som inte finns.');
    }
}
