<?php

namespace App\Observers\System;

use App\Enums\RecordStatus;
use App\Models\Log\LogUpload;
use App\Repositories\Log\LogUploadRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class AttachUploadObserver
{
    /**
     * 新增或修改主表時，commit 所有對應 log_upload
     */
    public function saved($model): void
    {
        // 取得所有上傳欄位的 upload_id（回傳 ['image'=>[], 'image_app'=>[]]）
        $uploadIdsByField = $this->extractUploadIdsFromModel($model);

        // 沒有異動圖片就直接 return
        if (empty($uploadIdsByField)) {
            return;
        }

        $repository = app(LogUploadRepository::class);
        $relatedTable = get_class($model);
        $relatedId = $model->id;

        foreach ($uploadIdsByField as $relatedField => $uploadIds) {
            if (empty($relatedField)) {
                continue;
            } // 避免 related_field 為空
            // 只處理有上傳 id 的欄位
            $oldLogs = $repository->getLogsByOwner($relatedTable, $relatedField, $relatedId)->keyBy('id');
            $oldLogIds = $oldLogs->keys()->all();

            // 比對被移除的 id
            foreach (array_diff($oldLogIds, $uploadIds) as $id) {
                if ($log = $oldLogs[$id] ?? null) {
                    $this->deleteLogUpload($log);
                }
            }

            // 將現有的設為 ACTIVE 並補欄位資訊
            if ($uploadIds) {
                $repository->updateBatch(
                    array_map(fn ($id) => [
                        'id' => $id,
                        'related_table' => $relatedTable,
                        'related_id' => $relatedId,
                        'related_field' => $relatedField,
                        'status' => RecordStatus::ACTIVE->value,
                    ], $uploadIds)
                );
            }
        }
    }

    /**
     * 刪除主表時，**每個欄位都要一起 DELETED**
     */
    public function deleted($model): void
    {
        $repository = app(LogUploadRepository::class);
        $relatedTable = get_class($model);
        $relatedId = $model->id;
        $fields = method_exists($model, 'getUploadFields') ? $model->getUploadFields() : [];

        foreach ($fields as $field) {
            foreach ($repository->getLogsByOwner($relatedTable, $field, $relatedId) as $log) {
                $this->deleteLogUpload($log);
            }
        }
    }

    /**
     * 回傳 ['image'=>[1,2], 'image_app'=>[3]]
     */
    private function extractUploadIdsFromModel($model): array
    {
        $fields = method_exists($model, 'getUploadFields') ? $model->getUploadFields() : [];
        $result = [];
        $req = request();

        foreach ($fields as $field) {
            $value = $req->input($field);
            $ids = [];

            if (is_array($value) && isset($value[0]['upload_id'])) {
                foreach ($value as $item) {
                    if (! empty($item['upload_id'])) {
                        $ids[] = (int) $item['upload_id'];
                    }
                }
            } elseif (is_array($value) && isset($value['upload_id'])) {
                $ids[] = (int) $value['upload_id'];
            }

            $result[$field] = array_unique(array_filter($ids));
        }

        return $result;
    }

    /**
     * 刪除圖檔及 log_upload 紀錄
     */
    private function deleteLogUpload(Collection|LogUpload $log): void
    {
        if ($log->file_path) {
            Storage::disk($log->disk ?? 'public')->delete($log->file_path);
        }
        if ($log->thumbnail_path) {
            Storage::disk($log->disk ?? 'public')->delete($log->thumbnail_path);
        }
        $log->delete();
    }
}
