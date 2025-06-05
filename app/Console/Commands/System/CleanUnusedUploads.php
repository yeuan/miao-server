<?php

namespace App\Console\Commands\System;

use App\Jobs\CleanUnusedUploadsJob;
use Illuminate\Console\Command;

class CleanUnusedUploads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uploads:clean-unused {--minutes=60}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '清理未被主表綁定且超時的檔案與 log_upload';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $minutes = (int) $this->option('minutes');
        $isJobMessage = '';
        if (config('custom.settings.queue.use_redis')) {
            $isJobMessage = '丟入 Job，';
            dispatch(new CleanUnusedUploadsJob($minutes));
        } else {
            (new CleanUnusedUploadsJob($minutes))->handle();
        }

        $this->info("已{$isJobMessage}清理 {$minutes} 分鐘前未使用的圖檔。");
    }
}
