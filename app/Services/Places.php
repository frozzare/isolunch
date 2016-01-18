<?php namespace App\Services;

use joshtronic\GooglePlaces;

/**
 * Class Places
 * Wrapper for GooglePlaces class.
 */
class Places
{

    /**
     * Property to store instance of GooglePlaces.
     *
     * @var
     */
    private $instance;

    /**
     * Constructor for Places creates instance of GooglePlaces class.
     */
    public function __construct()
    {
        $this->instance = new GooglePlaces(env('GOOGLE_API_KEY'));
    }

    /**
     * Return empty instance of GooglePlaces.
     *
     * @return mixed
     */
    public function getEmptyInstance()
    {
        return $this->instance;
    }

    /**
     * Return instance of GooglePlaces with default values set.
     *
     * @return mixed
     */
    public function getNearBySearchInstance()
    {
        $instance = $this->instance;

        $instance->types = 'restaurant';
        $instance->rankby = 'distance';

        // Location of Isotop.
        $instance->location = [59.3367395, 18.0652892];

        return $instance;
    }
}
