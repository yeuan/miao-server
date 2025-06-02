<?php

namespace App\Observers\System;

use App\Jobs\PurgeCdnCacheJob;
use App\Models\Log\LogUpload;
use Illuminate\Support\Facades\Storage;

class UploadObserver
{
    public function updating(LogUpload $upload): void
    {
        // 如果檔案路徑或縮圖路徑有變更，則刪除舊檔案
        if ($upload->isDirty('file_path')) {
            Storage::disk($upload->disk ?? 'public')->delete($upload->getOriginal('file_path'));
        }
        if ($upload->isDirty('thumbnail_path')) {
            Storage::disk($upload->disk ?? 'public')->delete($upload->getOriginal('thumbnail_path'));
        }
    }

    public function deleted(LogUpload $upload): void
    {
        // 預設清除緩存路徑
        $purgePaths = [];
        // 自動清檔案
        if ($upload->file_path) {
            $purgePaths[] = $this->getPublicUrl($upload->file_path, $upload->disk ?? 'public');
            Storage::disk($upload->disk ?? 'public')->delete($upload->file_path);
        }
        if ($upload->thumbnail_path) {
            $purgePaths[] = $this->getPublicUrl($upload->thumbnail_path, $upload->disk ?? 'public');
            Storage::disk($upload->disk ?? 'public')->delete($upload->thumbnail_path);
        }

        $shouldPurgeCdn = config('custom.settings.cdn.enabled', false);
        // 判斷是否使用cdn及是否有檔案需要清除緩存
        if ($shouldPurgeCdn && ! empty($purgePaths)) {
            // 判斷是否使用queue
            if (config('custom.settings.queue.use_redis')) {
                dispatch(new PurgeCdnCacheJob($purgePaths))->onQueue('cdnPurgeWorker');
            } else {
                (new PurgeCdnCacheJob($purgePaths))->handle();
            }
        }
    }

    private function getPublicUrl(string $filePath, string $disk): string
    {
        return rtrim(config("filesystems.disks.$disk.url"), '/').'/'.ltrim($filePath, '/');
    }
}
