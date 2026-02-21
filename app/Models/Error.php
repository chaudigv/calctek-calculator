<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Error extends Model
{
    protected $fillable = [
        'message',
        'errorable_type',
        'errorable_id',
    ];

    /** @return MorphTo<Model, $this> */
    public function errorable(): MorphTo
    {
        return $this->morphTo();
    }
}
