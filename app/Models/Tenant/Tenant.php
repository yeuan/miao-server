<?php

namespace App\Models\Tenant;

use App\Models\BaseModel;

class Tenant extends BaseModel
{
    protected $table = 'tenants';

    protected $guarded = ['id'];
}
