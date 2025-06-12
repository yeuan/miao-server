<?php

namespace App\Models\Log;

use App\Models\BaseModel;

class LogAdminLogin extends BaseModel
{
    const UPDATED_AT = null;

    public $updatedBy = false;

    protected $connection = 'log';

    protected $table = 'log_admin_logins';

    protected $attributes = [
        'ip_info' => '{}',
    ];

    protected $casts = [
        'ip_info' => 'array',
    ];
}
