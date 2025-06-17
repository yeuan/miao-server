<?php

namespace App\Repositories\Manager;

use App\Enums\Manager\AdminNavFlag;
use App\Enums\Status;
use App\Models\Manager\AdminNav;
use App\Repositories\BaseRepository;
use App\Traits\TreeTrait;

class AdminNavRepository extends BaseRepository
{
    use TreeTrait;

    public function __construct(AdminNav $entity)
    {
        parent::__construct($entity);
    }

    public function create(array $data): int
    {
        $data['path'] = $this->buildPath($data['pid'] ?? 0, $this->entity);

        return parent::create($data);
    }

    public function update(array $row, int $id = 0): void
    {
        $row['path'] = $this->buildPath($row['pid'] ?? 0, $this->entity);

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
     * 取得導航樹狀結構
     */
    public function getNavTree(int $backstage): array
    {
        $nav = $this->allNav($backstage, requestOutParam('allow_nav_ids'));
        // ==== 模組過濾 ====
        // 取得啟用 module code 陣列
        $activeModuleCodes = app(ModuleRepository::class)->getAllModuleCodes(Status::ENABLE->value);
        // 過濾掉未啟用的 nav
        $nav = array_filter($nav, function ($row) use ($activeModuleCodes) {
            // 如果沒有模組綁定直接顯示，有綁才做判斷
            if (empty($row['module_code'])) {
                return true;
            }

            return in_array($row['module_code'], $activeModuleCodes);
        });

        return $this->treeNav($nav);
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
            'module_code',
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
}
