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
        if (isset($this->_search['owner_type'])) {
            $conditions[] = ['owner_type', '=', $this->_search['owner_type']];
        }
        if (isset($this->_search['owner_field'])) {
            $conditions[] = ['owner_field', '=', $this->_search['owner_field']];
        }
        if (isset($this->_search['owner_id'])) {
            $conditions[] = ['owner_id', '=', $this->_search['owner_id']];
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
    public function getLogsByOwner(?string $ownerType, ?string $ownerField, ?int $ownerId): object
    {
        // 只要有一個沒傳，直接回傳空陣列
        if (empty($ownerType) || empty($ownerField) || empty($ownerId)) {
            return collect();
        }

        return $this->search(['owner_type' => $ownerType, 'owner_field' => $ownerField, 'owner_id' => $ownerId])
            ->select(['id', 'disk', 'file_path', 'thumbnail_path'])
            ->result();
    }
}
