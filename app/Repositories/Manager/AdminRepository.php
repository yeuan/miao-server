<?php

namespace App\Repositories\Manager;

use App\Models\Manager\Admin;
use App\Repositories\BaseRepository;

class AdminRepository extends BaseRepository
{
    public function __construct(Admin $entity)
    {
        parent::__construct($entity);
    }

    public function create(array $data): int
    {
        $data = $this->_preAction($data);

        return parent::create($data);
    }

    public function update(array $row, int $id = 0): void
    {
        $row = $this->_preAction($row);
        parent::update($row, $id);
    }

    public function _doSearch(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        $conditions = [];

        // 基本 where 條件
        if (isset($this->_search['id'])) {
            $conditions[] = ['id', '=', $this->_search['id']];
        }
        if (isset($this->_search['backstage'])) {
            $conditions[] = ['backstage', '=', $this->_search['backstage']];
        }
        if (isset($this->_search['role_id'])) {
            $conditions[] = ['role_id', '=', $this->_search['role_id']];
        }
        if (isset($this->_search['status'])) {
            $conditions[] = ['status', '=', $this->_search['status']];
        }
        if (isset($this->_search['username'])) {
            $conditions[] = ['username', 'like', '%'.$this->_search['username'].'%'];
        }

        // 時間範圍
        foreach (['created_at', 'updated_at'] as $time) {
            $this->applyTimeRangeFilter($conditions, $this->_search, $time);
        }

        return $query->where($conditions);
    }

    private function _preAction(array $row): array
    {
        if (array_key_exists('password', $row)) {
            $row['password'] = $row['password'] !== ''
            ? bcrypt($row['password'])
            : null;

            if (is_null($row['password'])) {
                unset($row['password']);
            }
        }

        return $row;
    }
}
