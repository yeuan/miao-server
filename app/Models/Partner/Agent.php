<?php

namespace App\Models\Partner;

use App\Models\BaseModel;

class Agent extends BaseModel
{
    protected $table = 'agents';

    protected $guarded = ['id'];
}
