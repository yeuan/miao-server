<?php

namespace App\Models\Log;

use App\Models\BaseModel;

class LogUpload extends BaseModel
{
    protected $connection = 'log';

    protected $table = 'log_uploads';
}
