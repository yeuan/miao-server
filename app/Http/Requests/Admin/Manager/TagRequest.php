<?php

namespace App\Http\Requests\Admin\Manager;

use App\Enums\Common\OwnerType;
use App\Enums\Status;
use App\Http\Requests\BaseRequest;
use App\Models\Manager\Tag;
use App\Models\Tenant\Tenant;
use App\Repositories\Manager\ModuleRepository;

class TagRequest extends BaseRequest
{
    private string $table;

    private string $tableTenant;

    public function __construct()
    {
        parent::__construct();
        $this->table = (new Tag)->getTable();
        $this->tableTenant = (new Tenant)->getTable();
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
        $ownerType = $this->input('owner_type', 'platform');
        $ownerId = (int) $this->input('owner_id', 0);
        $moduleCode = $this->input('module_code', '');

        // 取得可用的模組code
        $allowedModules = app(ModuleRepository::class)->getAllModuleCodes(Status::ENABLE->value) ?? [];

        return [
            'name' => [
                'bail',
                ...explode('|', $this->stringRule(config('custom.length.tag.name_max'), true)),
                ...$this->uniqueComboRule($this->table, ['module_code' => $moduleCode, 'owner_type' => $ownerType, 'owner_id' => $ownerId]),
            ],
            'module_code' => $this->stringInRule($allowedModules, true),
            'owner_type' => $this->stringInRule(OwnerType::values()),
            'owner_id' => $this->intRule().'|'.$this->requiredIfRule('owner_type', [OwnerType::TENANT->value]).'|'.$this->existsRule($this->tableTenant, 'id'),
            'color' => $this->stringRule(config('custom.length.color.max')),
            'used_count' => $this->intRule(),
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
        $ownerType = $this->input('owner_type', 'platform');
        $ownerId = (int) $this->input('owner_id', 0);
        $moduleCode = $this->input('module_code', '');

        // 取得可用的模組code
        $allowedModules = app(ModuleRepository::class)->getAllModuleCodes(Status::ENABLE->value) ?? [];

        return [
            'id' => 'bail|'.$this->intRule(true),
            'name' => [
                'bail',
                ...explode('|', $this->stringRule(config('custom.length.tag.name_max'), true)),
                ...$this->uniqueComboRule($this->table, ['module_code' => $moduleCode, 'owner_type' => $ownerType, 'owner_id' => $ownerId], $id),
            ],
            'module_code' => $this->stringInRule($allowedModules, true),
            'owner_type' => $this->stringInRule(OwnerType::values(), true),
            'owner_id' => $this->intRule().'|'.$this->requiredIfRule('owner_type', [OwnerType::TENANT->value]).'|'.$this->existsRule($this->tableTenant, 'id'),
            'color' => $this->stringRule(config('custom.length.color.max')),
            'used_count' => $this->intRule(),
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
        // 取得可用的模組code
        $allowedModules = app(ModuleRepository::class)->getAllModuleCodes(Status::ENABLE->value) ?? [];

        return [
            'name' => $this->stringRule(config('custom.length.tag.name_max')),
            'module_code' => $this->stringInRule($allowedModules),
            'owner_type' => $this->stringInRule(OwnerType::values()),
            'owner_id' => $this->intRule().'|'.$this->requiredIfRule('owner_type', [OwnerType::TENANT->value]).'|'.$this->existsRule($this->tableTenant, 'id'),
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
