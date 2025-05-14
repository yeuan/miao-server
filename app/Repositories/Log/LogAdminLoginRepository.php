<?php

namespace App\Repositories\Log;

use App\Models\Log\LogAdminLogin;
use App\Repositories\BaseRepository;

class LogAdminLoginRepository extends BaseRepository
{
    public function __construct(LogAdminLogin $entity)
    {
        parent::__construct($entity);
        $this->isActionLog = false;
    }
}
