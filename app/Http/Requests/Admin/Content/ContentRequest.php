<?php

namespace App\Http\Requests\Admin\Content;

use App\Enums\Status;
use App\Http\Requests\BaseRequest;
use App\Models\Tenant\Tenant;
use App\Repositories\Manager\TagRepository;

class ContentRequest extends BaseRequest
{
    protected string $tableTenant;

    protected string $tagField;

    public function __construct()
    {
        parent::__construct();

        // 只需要執行一次即可
        $this->tableTenant = (new Tenant)->getTable();
        $this->tagField = config('custom.settings.tags.fields', 'tag_ids');
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
     * 顯示單筆 GET 驗證規則
     */
    protected function showRules(): array
    {
        return [
            'id' => $this->intRule(true),
        ];
    }

    /**
     * 刪除 DELETE 驗證規則
     */
    protected function deleteRules(): array
    {
        return [
            'id' => $this->intRule(true),
        ];
    }

    /**
     * 共用取得該模組允許的標籤ID
     */
    protected function getAllowedTagIds(): array
    {
        return app(TagRepository::class)
            ->getModuleTagIds($this->moduleCode, Status::ENABLE->value) ?? [];
    }
}
