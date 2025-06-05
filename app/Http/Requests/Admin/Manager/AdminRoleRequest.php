<?php

namespace App\Http\Requests\Admin\Manager;

use App\Enums\Backstage;
use App\Enums\Status;
use App\Http\Requests\BaseRequest;
use App\Models\Manager\AdminRole;

class AdminRoleRequest extends BaseRequest
{
    private string $table;

    public function __construct()
    {
        parent::__construct();

        $this->table = (new AdminRole)->getTable();
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
            'backstage' => $this->enumRule(Backstage::values()),
            'name' => 'bail|'.$this->stringRule(config('custom.length.admin_role.name_max'), true).'|'.$this->uniqueRule($this->table),
            'status' => $this->enumRule(Status::values()),
            'allow_nav' => $this->jsonRule(true),
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
            'backstage' => $this->enumRule(Backstage::values(), true),
            'name' => 'bail|'.$this->stringRule(config('custom.length.admin_role.name_max'), true).'|'.$this->uniqueRule($this->table, 'name', $id),
            'status' => $this->enumRule(Status::values()),
            'allow_nav' => $this->jsonRule(true),
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
            'backstage' => $this->enumRule(Backstage::values()),
            'name' => $this->stringRule(config('custom.length.admin_role.name_max')),
            'status' => $this->enumRule(Status::values()),
            'sort_by' => $this->sortRule($this->getTableColumns($this->table)),
            'page' => $this->intRule(),
            'per_page' => $this->intWithMaxRule(config('custom.length.pagination.per_page_max')),
            'created_at_1' => $this->dateRule(),
            'created_at_2' => $this->endDateRule('created_at_1'),
            'updated_at_1' => $this->dateRule(),
            'updated_at_2' => $this->endDateRule('updated_at_1'),
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
