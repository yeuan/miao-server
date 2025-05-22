<?php

namespace App\Models\System;

use App\Models\BaseModel;

class Module extends BaseModel
{
    const CREATED_AT = null;

    public $createdBy = false;

    protected $table = 'modules';

    protected $guarded = ['id'];
}
