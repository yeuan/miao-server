<?php

namespace App\Http\Requests\Admin\System;

use App\Enums\Status;
use App\Http\Requests\BaseRequest;
use App\Models\System\Module;

class ModuleRequest extends BaseRequest
{
    private string $table;

    public function __construct()
    {
        parent::__construct();

        $this->table = (new Module)->getTable();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return match ($this->method()) {
            'PUT' => $this->updateRules(),
            'GET' => $this->indexRules(),
            default => [],
        };
    }

    /**
     * 更新 PUT 驗證規則
     */
    private function updateRules(): array
    {
        return [
            'id' => 'bail|'.$this->intRule(true),
            'name' => $this->stringRule(config('custom.length.modules.name_max')),
            'sort' => $this->intRule(),
            'status' => $this->enumRule(Status::values()),
        ];
    }

    /**
     * 查詢列表 GET 驗證規則
     */
    private function indexRules(): array
    {
        return [
            'name' => $this->stringRule(config('custom.length.modules.name_max')),
            'status' => $this->enumRule(Status::values()),
            'sort_by' => $this->sortRule($this->getTableColumns($this->table)),
        ];
    }
}
