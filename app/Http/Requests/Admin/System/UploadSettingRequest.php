<?php

namespace App\Http\Requests\Admin\System;

use App\Enums\Status;
use App\Enums\System\ThumbMode;
use App\Enums\System\UploadType;
use App\Http\Requests\BaseRequest;
use App\Models\System\UploadSetting;
use App\Repositories\Manager\ModuleRepository;

class UploadSettingRequest extends BaseRequest
{
    private string $table;

    public function __construct()
    {
        parent::__construct();

        $this->table = (new UploadSetting)->getTable();
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
            'GET' => $this->route('id') ? $this->showRules() : $this->indexRules(),
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
            'extensions' => $this->arrayRule(),
            'thumbnail_enable' => $this->enumRule(Status::values()),
            'thumb_width' => $this->intWithMaxRule(config('custom.length.upload_settings.thumb_width_max')),
            'thumb_height' => $this->intWithMaxRule(config('custom.length.upload_settings.thumb_height_max')),
            'thumb_mode' => $this->enumRule(ThumbMode::values()),
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
            'type' => $this->enumRule(UploadType::values()),
            'module_code' => $this->stringInRule($allowedModules),
            'status' => $this->enumRule(Status::values()),
            'sort_by' => $this->sortRule($this->getTableColumns($this->table)),
        ];
    }
}
