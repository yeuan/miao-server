<?php

namespace App\Http\Requests\Admin\Content;

use App\Enums\Common\OwnerType;
use App\Enums\Content\BannerFlag;
use App\Enums\Content\BannerLinkType;
use App\Enums\Content\BannerType;
use App\Enums\Status;
use App\Models\Content\Banner;
use App\Models\Log\LogUpload;

class BannerRequest extends ContentRequest
{
    private string $table;

    private string $tableLog;

    protected ?string $moduleCode;

    public function __construct()
    {
        parent::__construct();

        $model = new Banner;
        $this->table = $model->getTable();
        $this->moduleCode = getModuleCodeByModel(get_class($model));

        $logModel = new LogUpload;
        $connection = $logModel->getConnectionName();
        $this->tableLog = ($connection ? "{$connection}." : '').$logModel->getTable();
    }

    /**
     * 建立 POST 驗證規則Tenant
     */
    protected function storeRules(): array
    {
        $allowedTagIds = $this->getAllowedTagIds();

        return [
            'owner_type' => $this->stringInRule(OwnerType::values()),
            'owner_id' => $this->intRule().'|'.$this->requiredIfRule('owner_type', [OwnerType::TENANT->value]).'|'.$this->existsRule($this->tableTenant, 'id'),
            'type' => $this->enumRule(BannerType::values(), true),
            'image' => $this->arrayRule(),
            'image.path' => $this->stringRule(config('custom.length.banner.image_max')),
            'image.upload_id' => 'required_with:image.path|'.$this->intRule().'|distinct|'.$this->existsRule($this->tableLog, 'id'),
            'image_app' => $this->arrayRule(),
            'image_app.path' => $this->stringRule(config('custom.length.banner.image_app_max')),
            'image_app.upload_id' => 'required_with:image_app.path|'.$this->intRule().'|distinct|'.$this->existsRule($this->tableLog, 'id'),
            'url' => $this->urlRule(config('custom.length.banner.url_max')),
            'link_type' => $this->enumRule(BannerLinkType::values(), true),
            'module_id' => $this->intRule().'|'.$this->requiredIfRule('link_type', [BannerLinkType::MODULE->value]),
            'object_id' => $this->intRule().'|'.$this->requiredIfRule('link_type', [BannerLinkType::MODULE->value, BannerLinkType::GAME->value]),
            'start_time' => $this->dateRule(),
            'end_time' => $this->endDateRule('start_time'),
            $this->tagField => $this->arrayRule(),
            $this->tagField.'.*' => $this->intInRule($allowedTagIds),
            'flag' => $this->flagRule(BannerFlag::names()),
            'sort' => $this->intRule(),
            'status' => $this->enumRule(Status::values()),
        ];
    }

    /**
     * 更新 PUT 驗證規則
     */
    protected function updateRules(): array
    {
        $allowedTagIds = $this->getAllowedTagIds();

        return [
            'id' => 'bail|'.$this->intRule(true),
            'owner_type' => $this->stringInRule(OwnerType::values(), true),
            'owner_id' => $this->intRule().'|'.$this->requiredIfRule('owner_type', [OwnerType::TENANT->value]).'|'.$this->existsRule($this->tableTenant, 'id'),
            'type' => $this->enumRule(BannerType::values()),
            'image' => $this->arrayRule(),
            'image.path' => $this->stringRule(config('custom.length.banner.image_max')),
            'image.upload_id' => $this->intRule().'|distinct|'.$this->existsRule($this->tableLog, 'id'),
            'image_app' => $this->arrayRule(),
            'image_app.path' => $this->stringRule(config('custom.length.banner.image_app_max')),
            'image_app.upload_id' => $this->intRule().'|distinct|'.$this->existsRule($this->tableLog, 'id'),
            'url' => $this->urlRule(config('custom.length.banner.url_max')),
            'link_type' => $this->enumRule(BannerLinkType::values()),
            'module_id' => $this->intRule().'|'.$this->requiredIfRule('link_type', [BannerLinkType::MODULE->value]),
            'object_id' => $this->intRule().'|'.$this->requiredIfRule('link_type', [BannerLinkType::MODULE->value, BannerLinkType::GAME->value]),
            'start_time' => $this->dateRule(),
            'end_time' => $this->endDateRule('start_time'),
            $this->tagField => $this->arrayRule(),
            $this->tagField.'.*' => $this->intInRule($allowedTagIds),
            'flag' => $this->flagRule(BannerFlag::names()),
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
            'type' => $this->enumRule(BannerType::values()),
            $this->tagField => $this->arrayRule(),
            $this->tagField.'.*' => $this->intInRule($allowedTagIds),
            'flag' => $this->enumRule(BannerFlag::values()),
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
