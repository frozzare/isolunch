<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    const RESTAURANT_ID = 'id';
    const RESTAURANT_GOOGLE_PLACE_ID = 'google_place_id';
    const RESTAURANT_NAME            = 'name';
    const RESTAURANT_IMAGE            = 'image';
    const RESTAURANT_LONG            = 'lng';
    const RESTAURANT_LAT            = 'lat';
    const RESTAURANT_STREET_ADDRESS = 'street_adress';
    const RESTAURANT_WEB_SITE       = 'website';
    const RESTAURANT_DESCRIPTION    = 'description';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'restaurants';

    protected $connection = 'mysql_laravel';
}//end class
