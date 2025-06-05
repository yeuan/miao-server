<?php

namespace App\Http\Requests\Admin\Manager;

use App\Enums\Backstage;
use App\Enums\Status;
use App\Http\Requests\BaseRequest;
use App\Models\Manager\Admin;
use App\Models\Manager\AdminRole;
use App\Models\Partner\Agent;
use App\Models\Tenant\Tenant;

class AdminRequest extends BaseRequest
{
    private string $table;

    private string $tableRole;

    private string $tableTenant;

    private string $tableAgent;

    public function __construct()
    {
        parent::__construct();

        $this->table = (new Admin)->getTable();
        $this->tableRole = (new AdminRole)->getTable();
        $this->tableTenant = (new Tenant)->getTable();
        $this->tableAgent = (new Agent)->getTable();
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
        $backstage = (int) $this->input('backstage', 1);

        return [
            'backstage' => $this->enumRule(Backstage::values()),
            'tenant_id' => $this->intRule().'|'.$this->requiredIfRule('backstage', [Backstage::AGENT->value]).'|'.$this->existsRule($this->tableTenant, 'id'),
            'agent_id' => $this->intRule().'|'.$this->requiredIfRule('backstage', [Backstage::TENANT->value]).'|'.$this->existsRule($this->tableAgent, 'id'),
            'username' => 'bail|'.$this->alphaNumBetweenRule(config('custom.length.admin.username_min'), config('custom.length.admin.username_max'), true).'|'.$this->uniqueRule($this->table, 'username'),
            'password' => array_merge(['bail'], $this->passwordRule(config('custom.length.admin.password_min'), config('custom.length.admin.password_max'), true)),
            'role_id' => $this->backstageRoleRule($backstage, $this->tableRole, 'id', true),
            'status' => $this->enumRule(Status::values()),
        ];
    }

    /**
     * 更新 PUT 驗證規則
     */
    private function updateRules(): array
    {
        $id = (int) $this->route('id');
        $backstage = (int) $this->input('backstage', 1);

        return [
            'id' => 'bail|'.$this->intRule(true),
            'backstage' => $this->enumRule(Backstage::values(), true),
            'tenant_id' => $this->intRule().'|'.$this->requiredIfRule('backstage', [Backstage::AGENT->value]).'|'.$this->existsRule($this->tableTenant, 'id'),
            'agent_id' => $this->intRule().'|'.$this->requiredIfRule('backstage', [Backstage::TENANT->value]).'|'.$this->existsRule($this->tableAgent, 'id'),
            'username' => 'bail|'.$this->alphaNumBetweenRule(config('custom.length.admin.username_min'), config('custom.length.admin.username_max'), true).'|'.$this->uniqueRule($this->table, 'username', $id),
            'password' => array_merge(['bail'], $this->passwordRule(config('custom.length.admin.password_min'), config('custom.length.admin.password_max'), false)),
            'role_id' => $this->backstageRoleRule($backstage, $this->tableRole, 'id', true),
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
            'backstage' => $this->enumRule(Backstage::values()),
            'username' => $this->alphaNumBetweenRule(config('custom.length.admin.username_min'), config('custom.length.admin.username_max')),
            'role_id' => $this->intExistsUnlessRule($this->tableRole, 'id'),
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
