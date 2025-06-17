<?php

namespace App\Http\Requests\Admin\Content;

use App\Enums\Common\OwnerType;
use App\Enums\Status;
use App\Models\Content\Page;

class PageRequest extends ContentRequest
{
    private string $table;

    protected ?string $moduleCode;

    public function __construct()
    {
        parent::__construct();

        $model = new Page;
        $this->table = $model->getTable();
        $this->moduleCode = getModuleCodeByModel(get_class($model));
    }

    /**
     * 建立 POST 驗證規則
     */
    protected function storeRules(): array
    {
        $ownerType = $this->input('owner_type', 'platform');
        $ownerId = (int) $this->input('owner_id', 0);
        $allowedTagIds = $this->getAllowedTagIds();

        return [
            'owner_type' => $this->stringInRule(OwnerType::values()),
            'owner_id' => $this->intRule().'|'.$this->requiredIfRule('owner_type', [OwnerType::TENANT->value]).'|'.$this->existsRule($this->tableTenant, 'id'),
            'slug' => [
                'bail',
                ...explode('|', $this->stringRule(config('custom.length.page.slug_max'))),
                ...$this->uniqueComboRule($this->table, ['owner_type' => $ownerType, 'owner_id' => $ownerId]),
            ],
            'title' => 'bail|'.$this->stringRule(config('custom.length.page.title_max'), true),
            'start_time' => $this->dateRule(),
            'end_time' => $this->endDateRule('start_time'),
            $this->tagField => $this->arrayRule(),
            $this->tagField.'.*' => $this->intInRule($allowedTagIds),
            'status' => $this->enumRule(Status::values()),
            'content' => $this->stringRule(),
            'summary' => $this->stringRule(config('custom.length.page.summary_max')),
        ];
    }

    /**
     * 更新 PUT 驗證規則
     */
    protected function updateRules(): array
    {
        $id = (int) $this->route('id');
        $ownerType = $this->input('owner_type', 'platform');
        $ownerId = (int) $this->input('owner_id', 0);
        $allowedTagIds = $this->getAllowedTagIds();

        return [
            'id' => 'bail|'.$this->intRule(true),
            'owner_type' => $this->stringInRule(OwnerType::values(), true),
            'owner_id' => $this->intRule().'|'.$this->requiredIfRule('owner_type', [OwnerType::TENANT->value]).'|'.$this->existsRule($this->tableTenant, 'id'),
            'slug' => [
                'bail',
                ...explode('|', $this->stringRule(config('custom.length.page.slug_max'))),
                ...$this->uniqueComboRule($this->table, ['owner_type' => $ownerType, 'owner_id' => $ownerId], $id),
            ],
            'title' => 'bail|'.$this->stringRule(config('custom.length.page.title_max'), true),
            'start_time' => $this->dateRule(),
            'end_time' => $this->endDateRule('start_time'),
            $this->tagField => $this->arrayRule(),
            $this->tagField.'.*' => $this->intInRule($allowedTagIds),
            'status' => $this->enumRule(Status::values()),
            'content' => $this->stringRule(),
            'summary' => $this->stringRule(config('custom.length.page.summary_max')),
        ];
    }

    /**
     * 查詢列表 GET 驗證規則
     */
    protected function indexRules(): array
    {
        $allowedTagIds = $this->getAllowedTagIds();

        return [
            'owner_type' => $this->stringInRule(OwnerType::values()),
            'owner_id' => $this->intRule().'|'.$this->existsRule($this->tableTenant, 'id'),
            $this->tagField => $this->arrayRule(),
            $this->tagField.'.*' => $this->intInRule($allowedTagIds),
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
}
