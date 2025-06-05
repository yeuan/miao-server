<?php

namespace App\Repositories\Content;

use App\Models\Content\Banner;
use App\Repositories\BaseRepository;

class BannerRepository extends BaseRepository
{
    public function __construct(Banner $entity)
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
        if (isset($this->_search['owner_type'])) {
            $conditions[] = ['owner_type', '=', $this->_search['owner_type']];
        }
        if (isset($this->_search['owner_id'])) {
            $conditions[] = ['owner_id', '=', $this->_search['owner_id']];
        }
        if (isset($this->_search['type'])) {
            $conditions[] = ['type', '=', $this->_search['type']];
        }
        if (isset($this->_search['status'])) {
            $conditions[] = ['status', '=', $this->_search['status']];
        }

        // 時間範圍
        foreach (['created_at', 'updated_at'] as $time) {
            $this->applyTimeRangeFilter($conditions, $this->_search, $time);
        }
        // publish_at_1, publish_at_2 對應 start_time / end_time
        $this->applyPublishAtRange($conditions, $this->_search);
        // enable 時間範圍
        if (isset($this->_search['enable'])) {
            $this->applyEnableTimeFilter($conditions);
        }

        // 套用 where 條件
        $query = $query->where($conditions);

        // 附加其他條件（使用原生或特殊條件）
        if (isset($this->_search['flag'])) {
            $query->whereRaw('flag & ? > 0', [$this->_search['flag']]);
        }

        return $query;
    }
}
