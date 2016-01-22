<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Rate extends Model
{
    protected $table = 'rates';

    const TOTAL_RATING = 'total_rating';

    const RATE = 'rate';

    const NUMBER_OF_VOTERS = 'number_of_voters';

    public function __construct( array $attributes  = null )
    {
        if(!isset($attributes['rate']))
        {
            return;
        }
        $this->{self::NUMBER_OF_VOTERS} = isset ( $this->{self::NUMBER_OF_VOTERS} ) === true ? $this->{self::NUMBER_OF_VOTERS} + 1 : 1;
        $this->{self::TOTAL_RATING} = isset ( $this->{self::TOTAL_RATING} ) === true ? $this->{self::TOTAL_RATING} + $attributes['rate'] : $attributes['rate'];

        $this->calculateRate();
    }

    public function calculateRate( $rate = null )
    {
        $this->{self::RATE} = $this->{self::TOTAL_RATING} / $this->{self::NUMBER_OF_VOTERS};
    }

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['restaurant'];

    /**
     * Get the restaurant that the comment belongs to.
     */
    public function restaurant()
    {
        return $this->belongsTo('App\Models\Restaurant');
    }

}//end class