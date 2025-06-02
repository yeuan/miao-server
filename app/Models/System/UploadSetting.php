<?php

namespace App\Models\System;

use App\Models\BaseModel;

class UploadSetting extends BaseModel
{
    protected $table = 'upload_settings';

    protected $guarded = ['id'];

    protected $casts = [
        'extensions' => 'array',
    ];
}
