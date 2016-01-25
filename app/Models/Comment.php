<?php

namespace App\Models;

class Comment extends \Corcel\Comment
{
    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'comment_date';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'comment_date';

    /**
     * Restaurant relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'comment_post_ID');
    }
}
