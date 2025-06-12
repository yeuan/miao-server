<?php

namespace App\Models\Manager;

use App\Models\BaseModel;

class Tag extends BaseModel
{
    protected $table = 'tags';

    protected $guarded = ['id'];
}
