<?php

namespace App\Observers\Auth;

use App\Enums\ApiCode;
use App\Enums\Backstage;
use App\Enums\Success;
use App\Jobs\LogApiJob;
use App\Models\Manager\Admin;

class AuthObserver
{
    // 監聽模型更新事件
    public function updating(Admin $model): void
    {
        // 如果 token 被更新，更新 login_ip 和 login_time
        if ($model->isDirty('token')) {
            $model->login_ip = getRealIp();
            $model->login_time = time();
            $model->login_count = $model->login_count + 1;
        }

        $backstage = ($model instanceof Admin) ? Backstage::ADMIN->value : Backstage::AGENT->value; // 判斷是 Admin 還是 Agent
        // 記錄成功登入日誌
        if ($model->isDirty('token') && $model->status != 0) {
            $log = [
                'backstage' => $backstage,
                'admin_id' => $model->id ?? 0,
                'status' => Success::SUCCESS->value,
                'message' => Success::SUCCESS->label(),
                'created_by' => $model->username ?? '',
            ];

            // 判斷是否寫入登入成功日誌
            if (config('custom.log.save_admin_login_success_log')) {
                $this->logAction($log);
            }
        }

        // 失敗登入的日誌記錄(錯誤太多次關閉帳號)
        if ($model->status == 0) {
            $log = [
                'backstage' => $backstage,
                'admin_id' => $model->id ?? 0,
                'status' => Success::FAIL->value,
                'message' => ApiCode::AUTH_LOGIN_TIMES->label(),
                'created_by' => $model->username ?? '',
            ];

            // 判斷是否寫入登入失敗日誌
            if (config('custom.log.save_admin_login_error_log')) {
                $this->logAction($log);
            }
        }
    }

    private function logAction(array $log): void
    {
        $log['db'] = 'admin_login';
        $log['ip'] = getRealIp();

        if (config('custom.settings.queue.use_redis')) {
            dispatch(new LogApiJob(collect($log)))->onQueue('logWorker');
        } else {
            (new LogApiJob(collect($log)))->handle();
        }
    }
}
