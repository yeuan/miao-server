<?php

namespace App\Models\Manager;

use App\Models\BaseModel;

class AdminNav extends BaseModel
{
    protected $table = 'admin_nav';

    protected $guarded = ['id'];

    // protected $attributes = [
    //     'icon'   => '',
    //     'sort'   => 0,
    //     'status' => 1,
    //     'flag'   => 15,
    // ];
}
