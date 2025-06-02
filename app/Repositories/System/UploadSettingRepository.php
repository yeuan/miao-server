<?php

namespace App\Repositories\System;

use App\Enums\Status;
use App\Models\System\UploadSetting;
use App\Repositories\BaseRepository;

class UploadSettingRepository extends BaseRepository
{
    public function __construct(UploadSetting $entity)
    {
        parent::__construct($entity);
    }

    public function _doSearch(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        $conditions = [];

        // 基本 where 條件
        if (isset($this->_search['id'])) {
            $conditions[] = ['id', '=', $this->_search['id']];
        }
        if (isset($this->_search['type'])) {
            $conditions[] = ['type', '=', $this->_search['type']];
        }
        if (isset($this->_search['module_code'])) {
            $conditions[] = ['module_code', '=', $this->_search['module_code']];
        }
        if (isset($this->_search['status'])) {
            $conditions[] = ['status', '=', $this->_search['status']];
        }

        // 套用 where 條件
        $query = $query->where($conditions);

        return $query;
    }

    /**
     * 取得所有啟用中的設置
     */
    public function getUploadSetting(?string $moduleCode, int $type): array
    {
        $search = ['type' => $type, 'module_code' => $moduleCode, 'status' => Status::ENABLE->value];

        return $this->select([
            'id',
            'extensions',
            'thumbnail_enable',
            'thumb_width',
            'thumb_height',
            'thumb_mode',
        ])->search($search)->resultOneArray();
    }
}
