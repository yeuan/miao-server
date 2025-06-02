<?php

namespace App\Repositories\Manager;

use App\Enums\Status;
use App\Models\Manager\Module;
use App\Repositories\BaseRepository;

class ModuleRepository extends BaseRepository
{
    public function __construct(Module $entity)
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
     * 取得所有啟用中的模組
     */
    public function getActiveModules(): array
    {
        $search['status'] = Status::ENABLE->value;

        return $this->select([
            'id',
            'namespace',
            'code',
            'name',
        ])->search($search)->order(['sort', 'asc'])->resultArray();
    }

    /**
     * 取出所有模組的code
     */
    public function getAllModuleCodes(?int $status = null): array
    {
        $search = [];
        if (! is_null($status)) {
            $search['status'] = $status;
        }

        // 只撈 code 欄位
        $codes = $this->select(['code'])
            ->search($search)
            ->resultArray();

        // 只回傳一維陣列 ['a', 'b', ...]
        return array_column($codes, 'code');
    }
}
