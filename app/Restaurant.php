<?php

namespace App;

use Corcel\Post;

class Restaurant extends Post
{

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
    protected $appends = ['website', 'lat', 'lng', 'adress', 'filter', 'phone'];

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
}
