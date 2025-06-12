<?php

namespace App\Http\Requests\Admin\Content;

use App\Enums\Common\OwnerType;
use App\Enums\Content\NoticeFlag;
use App\Enums\Content\NoticeType;
use App\Enums\Status;
use App\Http\Requests\BaseRequest;
use App\Models\Content\Notice;
use App\Models\Tenant\Tenant;
use App\Repositories\Manager\TagRepository;

class NoticeRequest extends BaseRequest
{
    private string $table;

    private string $tableTenant;

    private string $moduleCode;

    public function __construct()
    {
        parent::__construct();

        $model = new Notice;
        $this->table = $model->getTable();
        $this->moduleCode = getModuleCodeByModel(get_class($model));
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
        $tagField = config('custom.settings.tags.fields', 'tag_ids');
        $allowedTagIds = $this->getAllowedTagIds();

        return [
            'owner_type' => $this->stringInRule(OwnerType::values()),
            'owner_id' => $this->intRule().'|'.$this->requiredIfRule('owner_type', [OwnerType::TENANT->value]).'|'.$this->existsRule($this->tableTenant, 'id'),
            'type' => $this->enumRule(NoticeType::values(), true),
            'title' => 'bail|'.$this->stringRule(config('custom.length.notice.title_max'), true),
            'start_time' => $this->dateRule(),
            'end_time' => $this->endDateRule('start_time'),
            $tagField => $this->arrayRule(),
            $tagField.'.*' => $this->intInRule($allowedTagIds),
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
        $tagField = config('custom.settings.tags.fields', 'tag_ids');
        $allowedTagIds = $this->getAllowedTagIds();

        return [
            'id' => 'bail|'.$this->intRule(true),
            'owner_type' => $this->stringInRule(OwnerType::values(), true),
            'owner_id' => $this->intRule().'|'.$this->requiredIfRule('owner_type', [OwnerType::TENANT->value]).'|'.$this->existsRule($this->tableTenant, 'id'),
            'type' => $this->enumRule(NoticeType::values(), true),
            'title' => 'bail|'.$this->stringRule(config('custom.length.notice.title_max'), true),
            'start_time' => $this->dateRule(),
            'end_time' => $this->endDateRule('start_time'),
            $tagField => $this->arrayRule(),
            $tagField.'.*' => $this->intInRule($allowedTagIds),
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
        $tagField = config('custom.settings.tags.fields', 'tag_ids');
        $allowedTagIds = $this->getAllowedTagIds();

        return [
            'owner_type' => $this->stringInRule(OwnerType::values()),
            'owner_id' => $this->intRule().'|'.$this->existsRule($this->tableTenant, 'id'),
            'type' => $this->enumRule(NoticeType::values()),
            $tagField => $this->arrayRule(),
            $tagField.'.*' => $this->intInRule($allowedTagIds),
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

    /**
     * 共用取得該模組允許的標籤ID
     */
    private function getAllowedTagIds(): array
    {
        return app(TagRepository::class)
            ->getModuleTagIds($this->moduleCode, Status::ENABLE->value) ?? [];
    }
}
