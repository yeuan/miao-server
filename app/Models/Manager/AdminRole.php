<?php

namespace App\Models\Manager;

use App\Models\BaseModel;

class AdminRole extends BaseModel
{
    protected $table = 'admin_roles';

    protected $guarded = ['id'];

    protected $casts = [
        'allow_nav' => 'array',
    ];
}
