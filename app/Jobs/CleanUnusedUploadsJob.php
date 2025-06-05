<?php

namespace App\Jobs;

use App\Enums\RecordStatus;
use App\Repositories\Log\LogUploadRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanUnusedUploadsJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected int $minutes,
    ) {
        //
    }

    /**
     * 解除任務唯一鎖的秒數
     */
    public function uniqueFor(): int
    {
        return config('custom.settings.queue.unique_lock_time', 300);
    }

    /**
     * 任務的 unique ID (唯一ID)
     */
    public function uniqueId(): string
    {
        return 'clean-unused-uploads-'.$this->minutes;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $expired = Carbon::now()->subMinutes($this->minutes)->timestamp;
        $chunk = config('custom.settings.upload.cleanup_chunk_size', 500);
        $count = 0;

        $repo = app(LogUploadRepository::class);

        // 查詢所有未關聯且超過指定時間的
        $query = $repo->search([
            'status' => RecordStatus::PENDING->value,
            ['created_at', '<', $expired],
        ])->chunk($chunk, function ($logs) use (&$count) {
            foreach ($logs as $log) {
                try {
                    // 刪除檔案（原圖＋縮圖）
                    if ($log->file_path) {
                        Storage::disk($log->disk ?? 'public')->delete($log->file_path);
                    }
                    if ($log->thumbnail_path) {
                        Storage::disk($log->disk ?? 'public')->delete($log->thumbnail_path);
                    }
                    // 刪除DB log
                    $log->delete();
                    $count++;
                } catch (\Throwable $e) {
                    Log::channel('upload')->warning(
                        now()->toDateTimeString().": 清除檔案及log錯誤 / Clean unused files and log error : {$e->getMessage()}",
                        [
                            'id' => $log->id ?? '',
                            'file_path' => ($log->disk ?? 'public').'/'.($log->file_path ?? ''),
                            'thumbnail_path' => ($log->disk ?? 'public').'/'.($log->thumbnail_path ?? ''),
                        ]
                    );
                }
            }
        });

        Log::channel('upload')->info(
            now()->toDateTimeString().": 成功清除 {$this->minutes} 分鐘前未使用的上傳檔案與紀錄，共計 {$count} 筆。 / Cleaned $count unused uploads older than."
        );
    }
}
