<?php

namespace App\Repositories\Log;

use App\Models\Log\LogUpload;
use App\Repositories\BaseRepository;

class LogUploadRepository extends BaseRepository
{
    public function __construct(LogUpload $entity)
    {
        parent::__construct($entity);
        $this->isActionLog = false;
    }

    public function _doSearch(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        $conditions = [];

        // 基本 where 條件
        if (isset($this->_search['related_table'])) {
            $conditions[] = ['related_table', '=', $this->_search['related_table']];
        }
        if (isset($this->_search['related_field'])) {
            $conditions[] = ['related_field', '=', $this->_search['related_field']];
        }
        if (isset($this->_search['related_id'])) {
            $conditions[] = ['related_id', '=', $this->_search['related_id']];
        }
        if (isset($this->_search['id'])) {
            $conditions[] = ['id', '=', $this->_search['id']];
        }
        if (isset($this->_search['status'])) {
            $conditions[] = ['status', '=', $this->_search['status']];
        }

        return $query->where($conditions);
    }

    /**
     * 取得Owner的上傳紀錄
     */
    public function getLogsByOwner(?string $relatedTable, ?string $relatedField, ?int $relatedId): object
    {
        // 只要有一個沒傳，直接回傳空陣列
        if (empty($relatedTable) || empty($relatedField) || empty($relatedId)) {
            return collect();
        }

        return $this->search(['related_table' => $relatedTable, 'related_field' => $relatedField, 'related_id' => $relatedId])
            ->select(['id', 'disk', 'file_path', 'thumbnail_path'])
            ->result();
    }
}
