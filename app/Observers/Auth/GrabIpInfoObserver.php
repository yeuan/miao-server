<?php

namespace App\Observers\Auth;

use App\Jobs\GrabIpInfoJob;
use App\Models\Log\LogAdminLogin;

class GrabIpInfoObserver
{
    public function created(LogAdminLogin $model): void
    {
        $type = ($model instanceof LogAdminLogin) ? 'admin_login' : 'user_login';

        if (config('custom.setting.queue.use_redis')) {
            dispatch(new GrabIpInfoJob($type, $model->id))->onQueue('ipInfoWorker');
        } else {
            (new GrabIpInfoJob($type, $model->id))->handle();
        }
    }
}
