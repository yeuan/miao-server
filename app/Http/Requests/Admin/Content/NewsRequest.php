<?php

namespace App\Http\Requests\Admin\Content;

use App\Enums\Common\OwnerType;
use App\Enums\Content\ContentFlag;
use App\Enums\Status;
use App\Models\Content\News;
use App\Models\Content\NewsCategory;
use App\Models\Log\LogUpload;

class NewsRequest extends ContentRequest
{
    private string $table;

    private string $tableLog;

    private string $tableCategory;

    protected ?string $moduleCode;

    public function __construct()
    {
        parent::__construct();

        $model = new News;
        $this->table = $model->getTable();
        $this->moduleCode = getModuleCodeByModel(get_class($model));

        $logModel = new LogUpload;
        $connection = $logModel->getConnectionName();
        $this->tableLog = ($connection ? "{$connection}." : '').$logModel->getTable();

        $this->tableCategory = (new NewsCategory)->getTable();
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
                ...explode('|', $this->stringRule(config('custom.length.news.slug_max'))),
                ...$this->uniqueComboRule($this->table, ['owner_type' => $ownerType, 'owner_id' => $ownerId]),
            ],
            'category_id' => $this->intExistsUnlessRule($this->tableCategory, 'id', false, 0, 'category_id'),
            'title' => 'bail|'.$this->stringRule(config('custom.length.news.title_max'), true),
            'cover' => $this->arrayRule(),
            'cover.path' => $this->stringRule(config('custom.length.news.cover_max')),
            'cover.upload_id' => 'required_with:cover.path|'.$this->intRule().'|distinct|'.$this->existsRule($this->tableLog, 'id'),
            'cover_app' => $this->arrayRule(),
            'cover_app.path' => $this->stringRule(config('custom.length.news.cover_app_max')),
            'cover_app.upload_id' => 'required_with:cover_app.path|'.$this->intRule().'|distinct|'.$this->existsRule($this->tableLog, 'id'),
            'summary' => $this->stringRule(config('custom.length.news.summary_max')),
            'content' => $this->stringRule(),
            'start_time' => $this->dateRule(),
            'end_time' => $this->endDateRule('start_time'),
            $this->tagField => $this->arrayRule(),
            $this->tagField.'.*' => $this->intInRule($allowedTagIds),
            'flag' => $this->flagRule(ContentFlag::names()),
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
        $allowedTagIds = $this->getAllowedTagIds();

        return [
            'id' => 'bail|'.$this->intRule(true),
            'owner_type' => $this->stringInRule(OwnerType::values(), true),
            'owner_id' => $this->intRule().'|'.$this->requiredItruefRule('owner_type', [OwnerType::TENANT->value]).'|'.$this->existsRule($this->tableTenant, 'id'),
            'slug' => [
                'bail',
                ...explode('|', $this->stringRule(config('custom.length.news.slug_max'))),
                ...$this->uniqueComboRule($this->table, ['owner_type' => $ownerType, 'owner_id' => $ownerId], $id),
            ],
            'category_id' => $this->intExistsUnlessRule($this->tableCategory, 'id', true, 0, 'category_id'),
            'title' => 'bail|'.$this->stringRule(config('custom.length.news.title_max'), true),
            'cover' => $this->arrayRule(),
            'cover.path' => $this->stringRule(config('custom.length.news.cover_max')),
            'cover.upload_id' => 'required_with:cover.path|'.$this->intRule().'|distinct|'.$this->existsRule($this->tableLog, 'id'),
            'cover_app' => $this->arrayRule(),
            'cover_app.path' => $this->stringRule(config('custom.length.news.cover_app_max')),
            'cover_app.upload_id' => 'required_with:cover_app.path|'.$this->intRule().'|distinct|'.$this->existsRule($this->tableLog, 'id'),
            'summary' => $this->stringRule(config('custom.length.news.summary_max')),
            'content' => $this->stringRule(),
            'start_time' => $this->dateRule(),
            'end_time' => $this->endDateRule('start_time'),
            $this->tagField => $this->arrayRule(),
            $this->tagField.'.*' => $this->intInRule($allowedTagIds),
            'flag' => $this->flagRule(ContentFlag::names()),
            'sort' => $this->intRule(),
            'status' => $this->enumRule(Status::values()),
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
            'category_id' => $this->intExistsUnlessRule($this->tableCategory, 'id', false, 0, 'category_id'),
            $this->tagField => $this->arrayRule(),
            $this->tagField.'.*' => $this->intInRule($allowedTagIds),
            'flag' => $this->enumRule(ContentFlag::values()),
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
