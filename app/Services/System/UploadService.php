<?php

namespace App\Services\System;

use App\Enums\ApiCode;
use App\Enums\RecordStatus;
use App\Enums\System\ThumbMode;
use App\Enums\System\UploadType;
use App\Exceptions\Api\ApiException;
use App\Repositories\Log\LogUploadRepository;
use App\Repositories\System\UploadSettingRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;

class UploadService
{
    protected bool $rename;

    protected int $quality;

    protected ?string $toExtension;

    protected string $disk = 'public';

    public function __construct(
        protected UploadSettingRepository $uploadSettingRepository,
        protected LogUploadRepository $logUploadRepository,
        protected ImageManager $imageManager
    ) {
        // 是否重新命名
        $this->rename = config('custom.upload.rename_file', true);
        // 上傳品質，預設 90
        $this->quality = config('custom.upload.quality', 90);
        // 轉換的檔案類型（如有設定）
        $this->toExtension = strtolower(config('custom.upload.to_extension'));
    }

    /**
     * 多檔/單檔共用的上傳處理流程
     * $request['files'] 格式：
     * [
     *   ['file' => ..., 'upload_id' => ...], // 覆蓋
     *   ['file' => ...],                     // 新增
     *   ['upload_id' => ...],                // 保留
     * ]
     */
    public function handleUpload(array $request, string $type): array
    {
        // 支援單檔與多檔上傳
        $files = $request['files'];
        $moduleCode = $request['module_code'] ?? '';
        $ownerType = getOwnerTypeByModuleCode($moduleCode);
        $ownerField = $request['owner_field'];
        $ownerId = $request['owner_id'] ?? null;

        // 取得上傳設定
        $setting = $this->getUploadSetting($moduleCode, $type);
        $extensions = $setting['extensions'] ?? [];
        // 判斷是否需要轉檔
        $convertType = $type === 'image' && $this->toExtension && in_array($this->toExtension, $extensions);
        // 取得上傳目錄
        $uploadDir = $this->buildUploadDir($type);

        // 優化這裡：先撈全部舊的log到 array，key 用 id
        $oldLogs = $this->logUploadRepository->getLogsByOwner($ownerType, $ownerField, $ownerId)->keyBy('id');
        $oldLogIds = $oldLogs->keys()->all();

        $results = $newLogIds = [];

        foreach ($files as $file) {
            // 覆蓋（同時有 file+upload_id）
            if (! empty($file['file']) && ! empty($file['upload_id'])) {
                $results[] = $this->handleOverwrite($file, $setting, $convertType, $ownerField, $uploadDir, $oldLogs, $newLogIds);
            }
            // 新增
            elseif (! empty($file['file'])) {
                $results[] = $this->handleNewUpload($file, $ownerType, $ownerId, $setting, $convertType, $ownerField, $uploadDir, $newLogIds);
            }
            // 保留（只傳 upload_id）
            elseif (! empty($file['upload_id'])) {
                $results[] = $this->handleKeep($file, $oldLogs, $newLogIds);
            }
        }

        // 自動清除多餘檔案
        foreach (array_diff($oldLogIds, $newLogIds) as $delId) {
            $this->deleteUploadLog($delId);
        }

        return $results;
    }

    /**
     * 取得上傳設定
     */
    protected function getUploadSetting(string $moduleCode, string $type): array
    {
        return $this->uploadSettingRepository->getUploadSetting($moduleCode, UploadType::{strtoupper($type)}->value)
            ?: config("custom.upload.default.$type", []);
    }

    /**
     * 建立上傳目錄
     */
    protected function buildUploadDir(string $type): string
    {
        // 路徑組合
        $baseDir = config('custom.upload.base_dir', 'uploads');
        // 判斷是否為多租客
        $tenantPath = requestOutParam('tenant_code', null);
        // 判斷是否使用日期目錄
        $datePath = config('custom.upload.use_date_folder') ? date('Y').'/'.date('m-d') : '';

        return trim($baseDir.($tenantPath ? '/'.$tenantPath : '').'/'.$type.($datePath ? '/'.$datePath : ''), '/');
    }

    /**
     * 處理覆蓋上傳
     */
    protected function handleOverwrite(array $file, array $setting, bool $convertType, string $ownerField, string $uploadDir, Collection|array|null $oldLogs, array &$newLogIds): array
    {
        $logId = $file['upload_id'];

        $saveResult = $this->saveFileToStorage($file['file'], $setting, $convertType, $uploadDir);
        $this->logUploadRepository->update([
            'owner_field' => $ownerField,
            'file_path' => $saveResult['file_path'],
            'file_name' => $file['file']->getClientOriginalName(),
            'mime_type' => $this->getMimeType($saveResult['ext']) ?? $file['file']->getClientMimeType(),
            'thumbnail_path' => $saveResult['thumbnail_path'],
            'size' => $file['file']->getSize(),
            'status' => RecordStatus::PENDING->value,
        ], $logId);

        $newLogIds[] = $logId;

        return [
            'id' => (int) $logId,
            'file_path' => $saveResult['file_path'],
            'thumbnail_path' => $saveResult['thumbnail_path'],
        ];
    }

    /**
     * 處理新上傳array
     */
    protected function handleNewUpload(array $file, ?string $ownerType, int|string|null $ownerId, array $setting, bool $convertType, string $ownerField, string $uploadDir, array &$newLogIds): array
    {
        $saveResult = $this->saveFileToStorage($file['file'], $setting, $convertType, $uploadDir);
        $logId = $this->logUploadRepository->create([
            'owner_type' => $ownerType,
            'owner_field' => $ownerField,
            'owner_id' => $ownerId,
            'disk' => $this->disk,
            'file_path' => $saveResult['file_path'],
            'file_name' => $file['file']->getClientOriginalName(),
            'mime_type' => $this->getMimeType($saveResult['ext']) ?? $file['file']->getClientMimeType(),
            'thumbnail_path' => $saveResult['thumbnail_path'],
            'size' => $file['file']->getSize(),
            'status' => RecordStatus::PENDING->value,
        ]);
        $newLogIds[] = $logId;

        return [
            'id' => (int) $logId,
            'file_path' => $saveResult['file_path'],
            'thumbnail_path' => $saveResult['thumbnail_path'],
        ];
    }

    /**
     * 處理保留檔案
     * 只回傳檔案路徑與縮圖路徑
     */
    protected function handleKeep(array $file, Collection|array|null $oldLogs, array &$newLogIds): array
    {
        $newLogIds[] = $file['upload_id'];
        $log = $oldLogs[$file['upload_id']] ?? null;

        return [
            'id' => (int) $file['upload_id'],
            'file_path' => $log?->file_path,
            'thumbnail_path' => $log?->thumbnail_path,
        ];
    }

    /**
     * 刪除上傳紀錄
     */
    private function deleteUploadLog(int $logId): void
    {
        $log = $this->logUploadRepository->row($logId);

        if ($log) {
            $log->delete(); // Observer 會自動清檔案
        }
    }

    /**
     * 儲存檔案＋縮圖＋回傳
     */
    private function saveFileToStorage(UploadedFile $file, array $setting, bool $convertType, string $uploadDir): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $fileName = $this->rename ? (string) Str::uuid() : $file->getClientOriginalName();
        $saveExt = $convertType ? $this->toExtension : $extension;
        $finalName = "$fileName.$saveExt";
        $filePath = "$uploadDir/$finalName";
        $fullPath = storage_path("app/{$this->disk}/$filePath");
        $thumbnailPath = null;

        try {
            if ($convertType) {
                $image = $this->imageManager->read($file->getRealPath());
                $encoded = $this->encodeImage($image, $this->toExtension, $this->quality);
                Storage::disk($this->disk)->put($filePath, $encoded);
            } else {
                $file->storeAs($uploadDir, $finalName, $this->disk);
            }
            if (! Storage::disk($this->disk)->exists($filePath)) {
                throw new ApiException(ApiCode::UPLOAD_SAVE_FAILED->name);
            }
            if (file_exists($fullPath)) {
                ImageOptimizer::optimize($fullPath);
            }
            // 自動化產生縮圖
            $thumbnailPath = $this->maybeCreateThumbnail($file, $setting, $fileName, $saveExt, $uploadDir);

            return [
                'file_path' => $filePath,
                'thumbnail_path' => $thumbnailPath,
                'ext' => $saveExt,
            ];
        } catch (\Throwable $e) {
            $this->cleanupOnError($filePath ?? '', $thumbnailPath ?? null);
            Log::channel('upload')->error(
                now()->toDateTimeString().": 上傳流程錯誤 / Upload process error : {$e->getMessage()}",
                [
                    'file' => $filePath ?? '',
                    'thumb' => $thumbnailPath ?? '',
                ]
            );
            throw $e;
        }
    }

    /**
     * 產生縮圖，支援四種模式，原始圖已經是 Intervention Image 實例
     * 若有需支援 jpg/png 輸出，encode 設計可再強化
     *
     * @param  \Intervention\Image\Image  $image
     * @param  string  $format  // e.g. 'webp', 'jpg'
     */
    private function generateThumbnail($image, array $setting, int $mode, string $thumbPath, string $format = 'webp'): ?string
    {
        try {
            $image = match ($mode) {
                ThumbMode::COVER->value => $image->cover($setting['thumb_width'], $setting['thumb_height']),
                ThumbMode::CONTAIN->value => $image->contain($setting['thumb_width'], $setting['thumb_height'], 'ffffff'),
                ThumbMode::STRETCH->value => $image->resize($setting['thumb_width'], $setting['thumb_height'], keepAspectRatio: false),
                default => $image->resize($setting['thumb_width'], $setting['thumb_height']),
            };
            $encoded = $this->encodeImage($image, $format, $this->quality);
            Storage::disk($this->disk)->put($thumbPath, $encoded);

            return $thumbPath;
        } catch (\Throwable $e) {
            Log::channel('upload')->warning(
                now()->toDateTimeString().": 縮圖產生失敗 / Thumbnail generation failed : {$e->getMessage()}");

            return null;
        }
    }

    /**
     * 自動判斷是否要產生縮圖
     */
    private function maybeCreateThumbnail(UploadedFile $file, array $setting, string $fileName, string $saveExt, string $uploadDir): ?string
    {
        if (
            ! ($setting['thumbnail_enable'] ?? false)
            || empty($setting['thumb_width']) || empty($setting['thumb_height'])
        ) {
            return null;
        }
        $image = $this->imageManager->read($file->getRealPath());
        if ($image->width() <= $setting['thumb_width'] && $image->height() <= $setting['thumb_height']) {
            return null;
        }
        $thumbMode = $setting['thumb_mode'] ?? ThumbMode::FIT->value;
        $thumbName = 'thumb_'.pathinfo($fileName, PATHINFO_FILENAME).'.'.$saveExt;
        $thumbPath = "$uploadDir/$thumbName";
        try {
            $thumbImage = match ($thumbMode) {
                ThumbMode::COVER->value => $image->cover($setting['thumb_width'], $setting['thumb_height']),
                ThumbMode::CONTAIN->value => $image->contain($setting['thumb_width'], $setting['thumb_height'], 'ffffff'),
                ThumbMode::STRETCH->value => $image->resize($setting['thumb_width'], $setting['thumb_height'], keepAspectRatio: false),
                default => $image->resize($setting['thumb_width'], $setting['thumb_height']),
            };
            $encoded = $this->encodeImage($thumbImage, $saveExt, $this->quality);
            Storage::disk($this->disk)->put($thumbPath, $encoded);
            $fullThumbPath = storage_path("app/{$this->disk}/$thumbPath");
            if (file_exists($fullThumbPath)) {
                ImageOptimizer::optimize($fullThumbPath);
            }

            return $thumbPath;
        } catch (\Throwable $e) {
            Log::channel('upload')->warning(now()->toDateTimeString().": 縮圖產生失敗 / Thumbnail generation failed : {$e->getMessage()}");

            return null;
        }
    }

    private function encodeImage(\Intervention\Image\Interfaces\ImageInterface $image, string $ext, int $quality): string
    {
        return match ($ext) {
            'jpg', 'jpeg' => $image->toJpg($quality),
            'png' => $image->toPng(),
            default => $image->toWebp($quality),
        };
    }

    private function getMimeType(string $ext): string
    {
        return match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            default => 'application/octet-stream',
        };
    }

    private function cleanupOnError(string $filePath, ?string $thumbnailPath)
    {
        if (Storage::disk($this->disk)->exists($filePath)) {
            Storage::disk($this->disk)->delete($filePath);
        }
        if ($thumbnailPath && Storage::disk($this->disk)->exists($thumbnailPath)) {
            Storage::disk($this->disk)->delete($thumbnailPath);
        }
    }
}
