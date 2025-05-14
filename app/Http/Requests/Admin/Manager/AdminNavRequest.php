<?php

namespace App\Http\Requests\Admin\Manager;

use App\Enums\Status;
use App\Http\Requests\BaseRequest;
use App\Models\Manager\AdminNav;

class AdminNavRequest extends BaseRequest
{
    private string $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = (new AdminNav)->getTable();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return match ($this->method()) {
            'POST' => $this->storeRules(),
            'PUT' => $this->updateRules(),
            'GET' => $this->route('id') ? $this->showRules() : $this->indexRules(),
            'DELETE' => $this->deleteRules(),
            default => [],
        };
    }

    /**
     * 建立 POST 驗證規則
     */
    private function storeRules(): array
    {
        return [
            'pid' => $this->intExistsUnlessRule($this->table, 'id', false, 0, 'pid'),
            'path' => $this->stringRule(config('custom.length.admin_nav.path_max')),
            'icon' => $this->stringRule(config('custom.length.admin_nav.icon_max')),
            'name' => 'bail|'.$this->stringRule(config('custom.length.admin_nav.name_max'), true).'|'.$this->uniqueRule($this->table),
            'route' => $this->stringRule(config('custom.length.admin_nav.route_max')),
            'url' => $this->urlRule(config('custom.length.admin_nav.url_max')),
            'flag' => $this->intRule(),
            'sort' => $this->intRule(),
            'status' => $this->enumRule(Status::values()),
        ];
    }

    /**
     * 更新 PUT 驗證規則
     */
    private function updateRules(): array
    {
        $id = (int) $this->route('id');

        return [
            'id' => 'bail|'.$this->intRule(true),
            'pid' => $this->intExistsUnlessRule($this->table, 'id', true, 0, 'pid', $id),
            'path' => $this->stringRule(config('custom.length.admin_nav.path_max')),
            'icon' => $this->stringRule(config('custom.length.admin_nav.icon_max')),
            'name' => 'bail|'.$this->stringRule(config('custom.length.admin_nav.name_max'), true).'|'.$this->uniqueRule($this->table, 'name', $id),
            'route' => $this->stringRule(config('custom.length.admin_nav.route_max')),
            'url' => $this->urlRule(config('custom.length.admin_nav.url_max')),
            'flag' => $this->intRule(),
            'sort' => $this->intRule(),
            'status' => $this->enumRule(Status::values()),
        ];
    }

    /**
     * 顯示單筆 GET 驗證規則
     */
    private function showRules(): array
    {
        return [
            'id' => $this->intRule(true),
        ];
    }

    /**
     * 查詢列表 GET 驗證規則
     */
    private function indexRules(): array
    {
        return [
            'name' => $this->stringRule(config('custom.length.admin_nav.name_max')),
            'route' => $this->stringRule(config('custom.length.admin_nav.route_max')),
            'status' => $this->enumRule(Status::values()),
        ];
    }

    /**
     * 刪除 DELETE 驗證規則
     */
    private function deleteRules(): array
    {
        return [
            'id' => $this->intRule(true),
        ];
    }
}
