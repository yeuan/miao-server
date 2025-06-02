<?php

namespace App\Enums\System;

use App\Traits\EnumTrait;

enum UploadType: int
{
    use EnumTrait;

    case IMAGE = 1; // 圖片
    case FILE = 2; // 檔案
    // case VIDEO = 3; // 影片
    // case AUDIO = 4; // 音訊
    // case DOCUMENT = 5; // 文件
}
