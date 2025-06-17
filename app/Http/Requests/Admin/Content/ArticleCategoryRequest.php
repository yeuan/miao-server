<?php

namespace App\Http\Requests\Admin\Content;

use App\Enums\Common\OwnerType;
use App\Enums\Status;
use App\Models\Content\ArticleCategory;

class ArticleCategoryRequest extends ContentRequest
{
    private string $table;

    public function __construct()
    {
        parent::__construct();

        $this->table = (new ArticleCategory)->getTable();
    }

    /**
     * 建立 POST 驗證規則        $this->moduleCode = getModuleCodeByModel(get_class($model));
     */
    protected function storeRules(): array
    {
        $ownerType = $this->input('owner_type', 'platform');
        $ownerId = (int) $this->input('owner_id', 0);

        return [
            'owner_type' => $this->stringInRule(OwnerType::values()),
            'owner_id' => $this->intRule().'|'.$this->requiredIfRule('owner_type', [OwnerType::TENANT->value]).'|'.$this->existsRule($this->tableTenant, 'id'),
            'slug' => [
                'bail',
                ...explode('|', $this->stringRule(config('custom.length.article_category.slug_max'))),
                ...$this->uniqueComboRule($this->table, ['owner_type' => $ownerType, 'owner_id' => $ownerId]),
            ],
            'pid' => $this->intExistsUnlessRule($this->table, 'id', false, 0, 'pid'),
            'path' => $this->stringRule(config('custom.length.article_category.path_max')),
            'name' => 'bail|'.$this->stringRule(config('custom.length.article_category.name_max'), true),
            'sort' => $this->intRule(),
            'status' => $this->enumRule(Status::values()),
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

        return [
            'id' => 'bail|'.$this->intRule(true),
            'owner_type' => $this->stringInRule(OwnerType::values(), true),
            'owner_id' => $this->intRule().'|'.$this->requiredIfRule('owner_type', [OwnerType::TENANT->value]).'|'.$this->existsRule($this->tableTenant, 'id'),
            'slug' => [
                'bail',
                ...explode('|', $this->stringRule(config('custom.length.article_category.slug_max'))),
                ...$this->uniqueComboRule($this->table, ['owner_type' => $ownerType, 'owner_id' => $ownerId], $id),
            ],
            'pid' => $this->intExistsUnlessRule($this->table, 'id', true, 0, 'pid'),
            'path' => $this->stringRule(config('custom.length.article_category.path_max')),
            'name' => 'bail|'.$this->stringRule(config('custom.length.article_category.name_max'), true),
            'sort' => $this->intRule(),
            'status' => $this->enumRule(Status::values()),
        ];
    }

    /**
     * 查詢列表 GET 驗證規則
     */
    protected function indexRules(): array
    {
        return [
            'owner_type' => $this->stringInRule(OwnerType::values()),
            'owner_id' => $this->intRule().'|'.$this->existsRule($this->tableTenant, 'id'),
            'status' => $this->enumRule(Status::values()),
            // 'enable' => $this->enumRule(Status::values()),
            'sort_by' => $this->sortRule($this->getTableColumns($this->table)),
            'page' => $this->intRule(),
            'per_page' => $this->intWithMaxRule(config('custom.length.pagination.per_page_max')),
            'created_at_1' => $this->dateRule(),
            'created_at_2' => $this->endDateRule('created_at_1'),
            'updated_at_1' => $this->dateRule(),
            'updated_at_2' => $this->endDateRule('updated_at_1'),
        ];
    }
}
