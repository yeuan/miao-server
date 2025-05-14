<?php

namespace App\Models\Log;

use App\Models\BaseModel;

class LogAdminApi extends BaseModel
{
    const UPDATED_AT = null;

    public $createdBy = false;

    public $updatedBy = false;

    protected $connection = 'log';

    protected $table = 'log_admin_api';

    protected $casts = [
        'params' => 'array',
        'headers' => 'array',
        'response' => 'array',
        'exception' => 'array',
    ];
}
