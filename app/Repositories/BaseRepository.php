<?php

namespace App\Repositories;

use App\Jobs\LogApiJob;
use App\Traits\QueryCacheTrait;
use App\Traits\RepositoryTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

abstract class BaseRepository
{
    use QueryCacheTrait, RepositoryTrait;

    protected $db;                 // 取資料時使用 用完還原成預設Model

    protected bool $isActionLog = true; // 是否寫入操作日誌

    protected bool $redisCache = true; // 是否使用Redis快取

    // 預設走 'cache'，如需切換，可被子類覆寫
    protected string $redisConnectionName = 'query_cache'; // 切換redis cache連線

    protected array $logExcludeFields = []; // 日誌排除欄位

    protected string $_useIndex = '';

    protected string $_forceIndex = '';

    protected int $_paginate = 0;

    protected array $_select = [];

    protected array $_search = [];

    protected array $_where = [];

    protected array $_whereRaw = [];

    protected array $_join = [];

    protected array $_joinSub = [];

    protected array $_order = [];

    protected array $_group = [];

    protected array $_having = [];

    protected array $_limit = [];

    protected array $_relation = [];

    protected array $_when = [];

    public function __construct(
        protected Model $entity
    ) {
        $this->db = $this->entity;
        $this->logExcludeFields = config('custom.log.exclude_fields', []);
        DB::connection()->enableQueryLog();
    }

    public function __call(string $method, array $arguments): mixed
    {
        return $this->entity->{$method}(...$arguments);
    }

    public function getEntity(): Model
    {
        return $this->entity;
    }

    /**
     * 指定使用索引
     */
    public function useIndex(string $data): self
    {
        $this->_useIndex = $data;

        return $this;
    }

    /**
     * 強制使用索引
     */
    public function forceIndex(string $data): self
    {
        $this->_forceIndex = $data;

        return $this;
    }

    public function paginate(int $data): self
    {
        $this->_paginate = $data;

        return $this;
    }

    public function select(array $data): self
    {
        $this->_select = $data;

        return $this;
    }

    /**
     * search 條件（自定義使用）
     */
    public function search(array $data): self
    {
        $this->_search = $data;

        return $this;
    }

    public function where(array $data): self
    {
        $this->_where = $data;

        return $this;
    }

    public function whereRaw(array $data): self
    {
        $this->_whereRaw = $data;

        return $this;
    }

    public function join(array ...$data): self
    {
        $this->_join = $data;

        return $this;
    }

    public function joinSub(array ...$data): self
    {
        $this->_joinSub = $data;

        return $this;
    }

    public function order(array $data): self
    {
        $this->_order = $data;

        return $this;
    }

    public function group(array ...$data): self
    {
        $this->_group = $data;

        return $this;
    }

    public function having(array ...$data): self
    {
        $this->_having = $data;

        return $this;
    }

    public function limit(array ...$data): self
    {
        $this->_limit = $data;

        return $this;
    }

    public function relation(array $data): self
    {
        $this->_relation = $data;

        return $this;
    }

    public function when(array $data): self
    {
        $this->_when = $data;

        return $this;
    }

    public function reset(): self
    {
        foreach (get_object_vars($this) as $prop => $value) {
            if (str_starts_with($prop, '_')) {
                $this->$prop = is_array($value) ? [] : (is_int($value) ? 0 : '');
            }
        }

        $this->db = $this->entity; // 重置查詢起點

        return $this;
    }

    public function _doSearch(Builder $query): Builder
    {
        return $query;
    }

    /**
     * 執行實際查詢條件組合邏輯
     */
    public function _doAction(): self
    {
        $query = $this->entity->newQuery(); // 乾淨起始點

        // 基礎查詢選項
        if ($this->_select) {
            $query = $query->select($this->_select);
        }

        if ($this->_useIndex) {
            $query = $query->useIndex($this->_useIndex);
        }

        if ($this->_forceIndex) {
            $query = $query->forceIndex($this->_forceIndex);
        }

        // join 與 joinSub
        foreach ($this->_join as $join) {
            $query = $query->{$join[0]}(...array_slice($join, 1));
        }

        foreach ($this->_joinSub as $js) {
            $query = $query->{$js[0]}($js[1], $js[2], fn ($join) => $join->on($js[3], $js[4], $js[5]));
        }

        $query = $this->_doSearch($query); // 若有覆寫可做更多條件

        // where 條件支援陣列或具名鍵值
        if (isAssocArray($this->_where)) {
            foreach ($this->_where as $key => $val) {
                $query = is_array($val)
                ? $query->whereIn($key, $val)
                : $query->where($key, $val);
            }
        } else {
            foreach ($this->_where as $cond) {
                $query = ($cond[1] === 'in')
                ? $query->whereIn($cond[0], $cond[2])
                : $query->where(...$cond);
            }
        }

        // whereRaw 陣列支援
        foreach ($this->_whereRaw as $raw) {
            $query = $query->whereRaw($raw);
        }

        // group by 與 having 支援 raw 語法
        foreach ($this->_group as $group) {
            $query = str_contains($group, '(')
            ? $query->groupByRaw($group)
            : $query->groupBy($group);
        }

        foreach ($this->_having as $having) {
            $query = str_contains($having, '(')
            ? $query->havingRaw($having)
            : $query->having($having);
        }

        if ($this->_order) {
            if (array_is_list($this->_order)) {
                $query = strtolower($this->_order[0]) === 'rand()'
                ? $query->orderByRaw('RAND()')
                : $query->orderBy($this->_order[0], $this->_order[1]);
            } else {
                foreach ($this->_order as $col => $dir) {
                    $query = $query->orderBy($col, $dir);
                }
            }
        }

        // offset + limit
        if ($this->_limit) {
            $query = $query->offset($this->_limit[0])->limit($this->_limit[1]);
        }

        if ($this->_relation) {
            $query = $query->with($this->_relation);
        }

        // 條件式 when 語法
        foreach ($this->_when as [$value, $method, $params]) {
            $query = $query->when($value, fn ($q) => $q->$method(...$params));
        }

        $this->db = $query; // Builder 存進 $db，不動 $entity

        return $this;

    }

    public function row(int|string $id, int $lock = 0): Model
    {
        $this->_doAction();

        $query = $this->db->where($this->entity->getKeyName(), $id);

        $row = match ($lock) {
            1 => $query->lockForUpdate()->firstOrFail(),
            2 => $query->sharedLock()->firstOrFail(),
            default => $query->firstOrFail(),
        };

        return tap($row, fn () => $this->reset()); // 查完自動 reset
    }

    public function rowArray(int|string $id): array
    {
        $cacheGroup = $this->entity->getTable();
        $cacheKey = $cacheGroup.':id:'.$id;

        if ($this->redisCache && ($cache = $this->getCache($cacheKey))) {
            return json_decode($cache, true);
        }

        $row = $this->row($id)->toArray(); // findOrFail() 已會拋錯

        $this->redisCache && $this->storeCacheWithTrack($cacheGroup, $cacheKey, $row);

        return $row;
    }

    /**
     * 執行查詢並回傳結果（含分頁邏輯）
     */
    public function result(): Collection|LengthAwarePaginator
    {
        $this->_doAction();
        $result = $this->_paginate > 0
        ? $this->db->paginate($this->_paginate)
        : $this->db->get();

        return tap($result, fn () => $this->reset());
    }

    public function resultArray()
    {
        $cacheGroup = $this->entity->getTable();
        $cacheKey = $cacheGroup.':'.$this->getCompiledSelect(false);

        if ($this->redisCache && ($result = $this->getCache($cacheKey))) {
            $this->reset();

            return json_decode($result, true);
        }

        $result = $this->result()->toArray();

        $this->redisCache && $this->storeCacheWithTrack($cacheGroup, $cacheKey, $result);

        return $result;
    }

    public function resultOne($lock = 0): ?Model
    {
        $this->_doAction();

        $row = match ($lock) {
            1 => $this->db->lockForUpdate()->firstOrFail(),
            2 => $this->db->sharedLock()->firstOrFail(),
            default => $this->db->first()->firstOrFail(),
        };

        return tap($row, fn () => $this->reset()); // 查完自動 reset
    }

    /**
     * 建立資料
     */
    public function create(array $data): int
    {
        $operator = $data['created_by'] ?? requestOutParam('username');
        $this->entity->createdBy && $data['created_by'] = $operator;
        $this->entity->updatedBy && $data['updated_by'] = $operator;

        $create = $this->entity->create($data);
        $id = $create->getKey();

        // 判斷是否需要寫入操作日誌
        if ($this->shouldLogAction()) {
            // 過濾欄位
            $filteredData = filterFields($data, $this->logExcludeFields);
            $this->logAction($create, $filteredData, $id > 0 ? 1 : 0);
        }

        return $id;
    }

    /**
     * 更新資料
     */
    public function update(array $data, int $id): void
    {
        $operator = requestOutParam('username');
        $this->entity->updatedBy && $data['updated_by'] = $operator;

        if ($id == 0) {
            // $this->_doAction();
            // $this->entity->update($data);
            // $this->reset();
        } else {
            $row = $this->entity->findOrFail($id);
            $filteredRow = filterFields($row->toArray(), $this->logExcludeFields);
            $filteredData = filterFields($data, $this->logExcludeFields);

            $changedFields = diffAssocArray($filteredData, $filteredRow);
            if (empty($changedFields)) {
                return;
            }

            $row->update($data);
        }
        // 更新後刪除Redis快取
        $this->flushTableCache($this->entity->getTable());

        // 判斷是否需要寫入操作日誌
        if ($this->shouldLogAction()) {
            $this->logAction($filteredRow, $changedFields);
        }
    }

    public function delete(int|string $id = 0): void
    {
        if ($id == 0) {
            // $this->_doAction();
            // $this->db->delete();
            // $this->reset();
        } else {
            $row = $this->db->findOrFail($id);
            $row->delete();
        }
        // 更新後刪除Redis快取
        $this->flushTableCache($this->entity->getTable());

        // 判斷是否需要寫入操作日誌
        if ($this->shouldLogAction()) {
            $this->logAction($row);
        }
    }

    /**
     * 判斷是否啟用操作日誌功能
     */
    private function shouldLogAction(): bool
    {
        return $this->isActionLog
        && requestOutParam('backstage', 0)
        && config('custom.log.save_admin_action_log', false);
    }

    /**
     * 實際觸發操作日誌寫入（支援 Redis queue）
     */
    private function logAction(array|Model $original, array $data = [], int $status = 1): void
    {
        $routePrefix = getRoutePrefix();

        $db = match (true) {
            $routePrefix === config('custom.routes.subdomain.admin_domain'),
            $routePrefix === config('custom.routes.provider.admin_prefix') => 'admin_action',
            default => 'api_action',
        };

        $info = $this->getActionInfo($original, $data);

        $log = [
            'db' => $db,
            'backstage' => requestOutParam('backstage', 0),
            'admin_id' => requestOutParam('id', 0),
            'route' => optional(\Route::current())->getName() ?? '',
            'sql' => $this->lastQuery(),
            'info' => $info,
            'ip' => getRealIp(),
            'status' => $status,
            'created_by' => requestOutParam('username'),
        ];

        if (config('custom.setting.queue.use_redis', false)) {
            dispatch(new LogApiJob(collect($log)))->onQueue('logWorker');
        } else {
            (new LogApiJob(collect($log)))->handle();
        }
    }

    /**
     * 比對操作變動欄位，組合日誌格式
     */
    private function getActionInfo(array|Model $original, array $changedData): array
    {
        $originalArray = $original instanceof Model ? $original->toArray() : $original;

        // 回傳操作紀錄Log用資訊
        $log = [
            'target' => [
                'id' => (int) ($originalArray['id'] ?? 0),
                'table' => $this->entity->getTable(),
            ],
            'data' => [],
        ];

        $hasChanged = ! empty($changedData);
        $hasOriginal = ! empty($originalArray);

        if (! $hasChanged && $hasOriginal) {
            // DELETE → 只記錄整筆原始資料
            $log['data']['original'] = $originalArray;

            return $log;
        }

        if ($hasChanged) {
            $log['data']['changed'] = $changedData;

            // 判斷如果 changed 跟 original 相等 → 則是 create → 不加 original
            if (! isSameAsOriginal($changedData, $originalArray)) {
                // UPDATE → 加入 changed 對應的原始值
                $log['data']['original'] = filterOriginalByKeys($changedData, $originalArray);
            }
        }

        return $log;
    }

    /**
     * 回傳最後一條 SQL 查詢語句
     */
    private function lastQuery(): string
    {
        $queryLog = DB::getQueryLog();
        $last = end($queryLog);

        if (! is_array($last) || ! isset($last['query'])) {
            return '';
        }

        $sql = str_replace('?', '%s', $last['query']);

        return vsprintf($sql, $last['bindings'] ?? []);
    }

    /**
     * 取得組合後SQL字串
     */
    private function getCompiledSelect(bool $reset = true): string
    {
        $this->_doAction();
        $sql = str_replace('?', '%s', $this->db->toSql());
        $sql = vsprintf($sql, $this->db->getBindings());

        if ($reset) {
            $this->reset();
        }

        return $sql;
    }
}
