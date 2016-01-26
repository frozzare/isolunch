<?php

namespace App\Models;

use Corcel\Post;
use App\Models\Rate;
use Illuminate\Support\Facades\DB;

class Restaurant extends Post
{

    /**
     * Property to store if restaurants image comes from wordpress.
     *
     * @var bool
     */
    public $is_wp_image = true;

    /**
     * Fetcher for meta data.
     *
     * @param $key key for meta data to fetch.
     * @return mixed
     */
    private function getMeta($key)
    {
        foreach ($this->meta as $meta) {
            if ($meta->meta_key === $key) {
                return $meta->meta_value;
            }
        }
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['website', 'lat', 'lng', 'adress', 'filter', 'phone', 'menu'];

    /**
     * Accessor for website meta data.
     *
     * @return mixed
     */
    public function getWebsiteAttribute()
    {
        return $this->getMeta('website');
    }

    /**
     * Accessor for street_adress meta data.
     *
     * @return mixed
     */
    public function getAdressAttribute()
    {
        return $this->getMeta('street_adress');
    }

    /**
     * Accessor for lat meta data.
     *
     * @return mixed
     */
    public function getLatAttribute()
    {
        return $this->getMeta('lat');
    }

    /**
     * Accessor for lng meta data.
     *
     * @return mixed
     */
    public function getLngAttribute()
    {
        return $this->getMeta('lng');
    }

    /**
     * Accessor for phone meta data.
     *
     * @return mixed
     */
    public function getPhoneAttribute()
    {
        return $this->getMeta('phone');
    }

    /**
     * Accessor for menu meta data.
     *
     * @return mixed
     */
    public function getMenuAttribute()
    {
        return $this->getMeta('menu');
    }


    /**
     * Accessor for image.
     *
     * @return mixed|null
     */
    public function getImageAttribute()
    {
        $image = papi_get_field($this->ID, 'selected_image');
        if (!empty($image)) {
            if (!empty($image->sizes['medium']['url'])) {
                return $image->sizes['medium']['url'];
            }
        }

        $this->is_wp_image = false;
        $images = json_decode($this->getMeta('images'));
        if (!empty($images) && is_array($images)) {
            if (array_key_exists(0, $images)) {
                $images = $images[0];
                if (!empty($images->photo_reference)) {
                    $image = $images->photo_reference;
                    return $image;
                }
            }
        }

        return null;
    }

    /**
     * Accessor for concat all taxonomies to an comma seperated string.
     *
     * @return string
     */
    public function getfilterAttribute()
    {
        $tax_array = [];
        foreach ($this->taxonomies as $tax) {
            $tax_array[] = $tax->term->slug;
        }

        return implode(',', $tax_array);
    }

    /**
     * Commantes relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'comment_post_ID');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function rate()
    {
        return $this->belongsTo('App\Models\Rate', 'ID', 'restaurant_id');
    }

    public function setRate($grade)
    {
        if ($this->rate === null) {
            $rate = new Rate(['rate' => $grade]);
            $rate->restaurant_id = $this->ID;
            $rate->save();
            dd('rate e null');
//            dd($this->rate);
//            $this->setRelation('rate', $rate);
        } else {
            $this->rate->calculateRate($grade);
        }
    }

}
