<?php

namespace App\Http\Requests\Admin\Content;

use App\Enums\Content\NoticeFlag;
use App\Enums\Content\NoticeType;
use App\Enums\Status;
use App\Http\Requests\BaseRequest;
use App\Models\Content\Notice;

class NoticeRequest extends BaseRequest
{
    private string $table;

    private static $noticeColumns = null;

    public function __construct()
    {
        parent::__construct();

        $this->table = (new Notice)->getTable();
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
            'type' => $this->enumRule(NoticeType::values(), true),
            'title' => 'bail|'.$this->stringRule(config('custom.length.notice.title_max'), true),
            'start_time' => $this->dateRule(),
            'end_time' => $this->endDateRule('start_time'),
            'flag' => $this->flagRule(NoticeFlag::names()),
            'sort' => $this->intRule(),
            'status' => $this->enumRule(Status::values()),
            'content' => $this->stringRule(),
        ];
    }

    /**
     * 更新 PUT 驗證規則
     */
    private function updateRules(): array
    {
        return [
            'id' => 'bail|'.$this->intRule(true),
            'type' => $this->enumRule(NoticeType::values(), true),
            'title' => 'bail|'.$this->stringRule(config('custom.length.notice.title_max'), true),
            'start_time' => $this->dateRule(),
            'end_time' => $this->endDateRule('start_time'),
            'flag' => $this->flagRule(NoticeFlag::names()),
            'sort' => $this->intRule(),
            'status' => $this->enumRule(Status::values()),
            'content' => $this->stringRule(),
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
            'type' => $this->enumRule(NoticeType::values()),
            'flag' => $this->flagRule(NoticeFlag::values()),
            'status' => $this->enumRule(Status::values()),
            // 'enable' => $this->enumRule(Status::values()),
            'sort_by' => $this->sortRule($this->getTableColumns($this->table)),
            'page' => $this->intRule(),
            'per_page' => $this->intWithMaxRule(config('custom.length.pagination.per_page_max')),
            'created_at_1' => $this->dateRule(),
            'created_at_2' => $this->endDateRule('created_at_1'),
            'updated_at_1' => $this->dateRule(),
            'updated_at_2' => $this->endDateRule('updated_at_1'),
            'publish_at_1' => $this->dateRule(),
            'publish_at_2' => $this->endDateRule('publish_at_1'),
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
