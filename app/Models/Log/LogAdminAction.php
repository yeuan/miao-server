<?php

namespace App\Models\Log;

use App\Models\BaseModel;

class LogAdminAction extends BaseModel
{
    const UPDATED_AT = null;

    public $updatedBy = false;

    protected $connection = 'log';

    protected $table = 'log_admin_actions';

    protected $casts = [
        'info' => 'array',
        'sql' => 'array',
    ];
}
