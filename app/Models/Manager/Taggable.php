<?php

namespace App\Models\Manager;

use App\Casts\DateTimeCast;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class Taggable extends MorphPivot
{
    protected $table = 'taggables';

    protected $dateFormat = 'U';

    protected $guarded = ['id'];

    protected $casts = [
        'created_at' => DateTimeCast::class,
        'updated_at' => DateTimeCast::class,
    ];
}
