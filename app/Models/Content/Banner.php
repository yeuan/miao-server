<?php

namespace App\Models\Content;

use App\Models\BaseModel;

class Banner extends BaseModel
{
    protected $table = 'banner';

    protected $guarded = ['id'];

    // 只要有上傳欄位
    protected array $uploadFields = ['image', 'image_app'];
}
