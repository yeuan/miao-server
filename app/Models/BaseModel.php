<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    use ModelTrait;

    protected $dateFormat = 'U';

    protected $guarded = [];

    public $createdBy = true;

    public $updatedBy = true;
}
