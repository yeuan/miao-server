<?php

namespace App\Models;

use App\Casts\DateTimeCast;
use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    use ModelTrait;

    protected $dateFormat = 'U';

    protected $guarded = [];

    public $createdBy = true;

    public $updatedBy = true;

    protected $casts = [];

    protected $baseCasts = [
        'start_time' => DateTimeCast::class,
        'end_time' => DateTimeCast::class,
        'created_at' => DateTimeCast::class,
        'updated_at' => DateTimeCast::class,
    ];
}
