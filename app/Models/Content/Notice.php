<?php

namespace App\Models\Content;

use App\Models\BaseModel;

class Notice extends BaseModel
{
    protected $table = 'notice';

    protected $guarded = ['id'];
}
