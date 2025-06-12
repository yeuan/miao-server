<?php

namespace App\Repositories\Manager;

use App\Models\Manager\Tag;
use App\Repositories\BaseRepository;

class TagRepository extends BaseRepository
{
    public function __construct(Tag $entity)
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
        if (isset($this->_search['module_code'])) {
            $conditions[] = ['module_code', '=', $this->_search['module_code']];
        }
        if (isset($this->_search['owner_type'])) {
            $conditions[] = ['owner_type', '=', $this->_search['owner_type']];
        }
        if (isset($this->_search['owner_id'])) {
            $conditions[] = ['owner_id', '=', $this->_search['owner_id']];
        }
        if (isset($this->_search['status'])) {
            $conditions[] = ['status', '=', $this->_search['status']];
        }
        if (isset($this->_search['name'])) {
            $conditions[] = ['name', 'like', '%'.$this->_search['name'].'%'];
        }

        // 套用 where 條件
        $query = $query->where($conditions);

        return $query;
    }

    /**
     * 取出所有該模組可用的標籤id
     */
    public function getModuleTagIds(string $moduleCode, ?int $status = null): array
    {
        $search = [
            'module_code' => $moduleCode,
        ];

        if (! is_null($status)) {
            $search['status'] = $status;
        }

        // 只撈 id 欄位
        $ids = $this->select(['id'])
            ->search($search)
            ->resultArray();

        // 只回傳一維陣列 ['a', 'b', ...]
        return array_column($ids, 'id');
    }
}
