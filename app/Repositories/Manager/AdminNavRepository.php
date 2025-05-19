<?php

namespace App\Repositories\Manager;

use App\Enums\Manager\AdminNavFlag;
use App\Enums\Status;
use App\Models\Manager\AdminNav;
use App\Repositories\BaseRepository;

class AdminNavRepository extends BaseRepository
{
    public function __construct(AdminNav $entity)
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
        if (isset($this->_search['pid'])) {
            $conditions[] = ['pid', '=', $this->_search['pid']];
        }
        if (isset($this->_search['status'])) {
            $conditions[] = ['status', '=', $this->_search['status']];
        }
        if (isset($this->_search['name'])) {
            $conditions[] = ['name', 'like', '%'.$this->_search['name'].'%'];
        }

        // 時間範圍
        foreach (['created_at', 'updated_at'] as $time) {
            $this->applyTimeRangeFilter($conditions, $this->_search, $time);
        }

        // 套用 where 條件
        $query = $query->where($conditions);

        // 附加其他條件（使用原生或特殊條件）
        if (isset($this->_search['ids'])) {
            $query->whereIn('id', $this->_search['ids']);
        }
        if (isset($this->_search['flag'])) {
            $query->whereRaw('flag & ? > 0', [$this->_search['flag']]);
        }
        if (isset($this->_search['route'])) {
            $query->whereRaw('FIND_IN_SET(?, route)', [$this->_search['route']]);
        }

        return $query;
    }

    /**
     * 取得所有導航資料
     *
     * @param  string  $backstage  後台類型
     * @param  array|null  $allowNavIds  允許的nav ids
     */
    public function allNav(string $backstage, ?array $allowNavIds = null): array
    {
        $search['status'] = Status::ENABLE->value;
        $search['backstage'] = $backstage;
        $allowNavIds != null && $search['ids'] = $allowNavIds;

        $result = $this->select([
            'id',
            'pid',
            'icon',
            'name',
            'route',
            'url',
            'flag',
        ])->search($search)->order(['sort', 'asc'])->resultArray();

        return collect($result)->keyBy('id')->toArray();
    }

    /**
     * 遞迴整理導航樹狀結構
     *
     * @param  array  $result  導航清單
     * @param  int  $pid  上層導航 ID
     * @param  string  $pname  上層名稱
     * @param  bool  $final  是否為最後一層
     */
    public function treeNav(array|\Illuminate\Support\Collection $result, int $pid = 0, string $pname = '', bool $final = false): array
    {
        return collect($result)
            ->filter(fn ($row) => $row['pid'] === $pid)
            ->map(function ($row) use ($result, $final, $pname) {
                $row['pname'] = $pname;

                // 如果不是最後一層，遞迴處理子節點
                if (! $final) {
                    $row['children'] = $this->treeNav(
                        $result,
                        $row['id'],
                        $row['name'],
                        ($row['flag'] & AdminNavFlag::FINAL->value) > 0// 可抽象成方法：isFinalFlag($row['flag'])
                    );
                }

                return $row;
            })
            ->values()
            ->all();
    }

    private function _preAction(array $data): array
    {
        if (empty($data['pid'])) {
            // 如果 pid 未設定或為 0，直接設定 path 為 0
            $data['path'] = '0';

            return $data;
        }

        // 使用 pluck 提取父節點的 path，減少查詢負擔
        $parentPath = $this->entity->where('id', $data['pid'])->value('path');

        // 設定 path，並確保格式正確
        $data['path'] = $parentPath !== null
        ? "{$parentPath},{$data['pid']}"
        : (string) $data['pid'];

        return $data;
    }
}
