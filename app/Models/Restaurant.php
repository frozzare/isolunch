<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Interfaces\Restaurant_interface;

class Restaurant extends Model implements Restaurant_interface
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'restaurants';

    protected $connection = 'mysql_laravel';


}
