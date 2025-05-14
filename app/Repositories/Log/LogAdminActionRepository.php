<?php

namespace App\Repositories\Log;

use App\Models\Log\LogAdminAction;
use App\Repositories\BaseRepository;

class LogAdminActionRepository extends BaseRepository
{
    public function __construct(LogAdminAction $entity)
    {
        parent::__construct($entity);
        $this->isActionLog = false;
    }
}
