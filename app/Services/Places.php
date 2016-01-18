<?php namespace App\Services;

use joshtronic\GooglePlaces;

/**
 * Class Places
 * Wrapper for GooglePlaces class.
 */
class Places
{

    /**
     * @var
     */
    private $instance;

    /**
     *
     */
    public function __construct()
    {
        $this->instance = new GooglePlaces(env('GOOGLE_API_KEY'));
    }

    /**
     * @return mixed
     */
    public function getEmptyInstance()
    {
        return $this->instance;
    }

    /**
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
