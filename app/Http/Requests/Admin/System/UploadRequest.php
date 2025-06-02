<?php

namespace App\Http\Requests\Admin\System;

use App\Enums\Status;
use App\Enums\System\UploadType;
use App\Http\Requests\BaseRequest;
use App\Models\Log\LogUpload;
use App\Repositories\Manager\ModuleRepository;
use App\Repositories\System\UploadSettingRepository;

class UploadRequest extends BaseRequest
{
    private string $table;

    public function __construct()
    {
        parent::__construct();

        $table = new LogUpload;
        $connection = $table->getConnectionName();

        $this->table = ($connection ? "{$connection}." : '').$table->getTable();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // 利用路由控制器名稱取得type
        $method = $this->route()->getActionMethod();
        $type = $method === 'file' ? 'file' : 'image';
        $moduleCode = $this->input('module_code', '');

        // 取得可用的模組code
        $allowedModules = app(ModuleRepository::class)->getAllModuleCodes(Status::ENABLE->value) ?? [];
        // 取得上傳設置
        // 依 module_code/type 抓設定，否則 fallback
        $uploadSetting = app(UploadSettingRepository::class)->getUploadSetting($moduleCode, UploadType::{strtoupper($type)}->value)
        ?: config("custom.upload.default.$type", []);

        return [
            'module_code' => $this->stringInRule($allowedModules),
            'owner_id' => $this->intRule(),
            'owner_field' => $this->stringRule(config('custom.length.upload.owner_field_max'), true),
            'files' => $this->arrayRule(true),
            'files.*.file' => $this->fileRule($uploadSetting['extensions'] ?? [], config('custom.upload.max_size', 5120), true),
            'files.*.upload_id' => $this->intRule().'|distinct|'.$this->existsRule($this->table, 'id'),
        ];
    }
}
