<?php

namespace App\Http\Requests\Admin\Content;

use App\Enums\Common\OwnerType;
use App\Enums\Content\BannerFlag;
use App\Enums\Content\BannerLinkType;
use App\Enums\Content\BannerType;
use App\Enums\Status;
use App\Http\Requests\BaseRequest;
use App\Models\Content\Banner;
use App\Models\Log\LogUpload;
use App\Models\Tenant\Tenant;
use App\Repositories\Manager\TagRepository;

class BannerRequest extends BaseRequest
{
    private string $table;

    private string $tableLog;

    private string $tableTenant;

    private string $moduleCode;

    public function __construct()
    {
        parent::__construct();

        $model = new Banner;
        $this->table = $model->getTable();
        $this->moduleCode = getModuleCodeByModel(get_class($model));
        $this->tableTenant = (new Tenant)->getTable();

        $logModel = new LogUpload;
        $connection = $logModel->getConnectionName();
        $this->tableLog = ($connection ? "{$connection}." : '').$logModel->getTable();
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
     * 建立 POST 驗證規則Tenant
     */
    private function storeRules(): array
    {
        $tagField = config('custom.settings.tags.fields', 'tag_ids');
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
            $tagField => $this->arrayRule(),
            $tagField.'.*' => $this->intInRule($allowedTagIds),
            'flag' => $this->flagRule(BannerFlag::names()),
            'sort' => $this->intRule(),
            'status' => $this->enumRule(Status::values()),
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
            $tagField => $this->arrayRule(),
            $tagField.'.*' => $this->intInRule($allowedTagIds),
            'flag' => $this->flagRule(BannerFlag::names()),
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
        $tagField = config('custom.settings.tags.fields', 'tag_ids');
        $allowedTagIds = $this->getAllowedTagIds();

        return [
            'owner_type' => $this->stringInRule(OwnerType::values()),
            'owner_id' => $this->intRule().'|'.$this->existsRule($this->tableTenant, 'id'),
            'type' => $this->enumRule(BannerType::values()),
            $tagField => $this->arrayRule(),
            $tagField.'.*' => $this->intInRule($allowedTagIds),
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
