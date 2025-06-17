<?php

namespace App\Repositories\Content;

use App\Models\Content\NewsCategory;
use App\Repositories\BaseRepository;
use App\Traits\TreeTrait;

class NewsCategoryRepository extends BaseRepository
{
    use TreeTrait;

    public function __construct(NewsCategory $entity)
    {
        parent::__construct($entity);
    }

    public function create(array $data): int
    {
        $data['path'] = (string) $this->buildPath($data['pid'] ?? 0, $this->entity);

        return parent::create($data);
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
        if (isset($this->_search['slug'])) {
            $conditions[] = ['slug', '=', $this->_search['slug']];
        }
        if (isset($this->_search['pid'])) {
            $conditions[] = ['pid', '=', $this->_search['pid']];
        }
        if (isset($this->_search['status'])) {
            $conditions[] = ['status', '=', $this->_search['status']];
        }

        // 時間範圍
        foreach (['created_at', 'updated_at'] as $time) {
            $this->applyTimeRangeFilter($conditions, $this->_search, $time);
        }

        // 套用 where 條件
        $query = $query->where($conditions);

        return $query;
    }
}
