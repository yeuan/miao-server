<?php

namespace App\Repositories\Log;

use App\Models\Log\LogAdminApi;
use App\Repositories\BaseRepository;

class LogAdminApiRepository extends BaseRepository
{
    public function __construct(LogAdminApi $entity)
    {
        parent::__construct($entity);
        $this->isActionLog = false;
    }
}
