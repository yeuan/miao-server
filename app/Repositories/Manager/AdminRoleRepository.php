<?php

namespace App\Repositories\Manager;

use App\Enums\Status;
use App\Models\Manager\AdminRole;
use App\Repositories\BaseRepository;

class AdminRoleRepository extends BaseRepository
{
    public function __construct(AdminRole $entity)
    {
        parent::__construct($entity);
    }

    public function _doSearch(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        $conditions = [];

        // 單一條件處理
        if (isset($this->_search['id'])) {
            $conditions[] = ['id', '=', $this->_search['id']];
        }
        if (isset($this->_search['name'])) {
            $conditions[] = ['name', 'like', '%'.$this->_search['name'].'%'];
        }
        if (isset($this->_search['status'])) {
            $conditions[] = ['status', '=', $this->_search['status']];
        }

        // 時間範圍條件處理
        foreach (['created_at', 'updated_at'] as $time) {
            $this->applyTimeRangeFilter($conditions, $this->_search, $time);
        }

        return $query->where($conditions);
    }

    public function getRoleList(bool $all = false)
    {
        $where = [];
        if (! $all) {
            $where[] = ['id', '>', 1];
        }
        $where[] = ['status', '=', Status::ENABLE->value];
        $result = $this->where($where)->resultArray();

        return array_column($result, 'name', 'id');
    }
}
