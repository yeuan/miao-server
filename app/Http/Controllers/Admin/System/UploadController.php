<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\System\UploadRequest;
use App\Services\System\UploadService;

class UploadController extends Controller
{
    public function __construct(
        protected UploadService $uploadService
    ) {}

    public function image(UploadRequest $request)
    {
        try {
            $result = $this->uploadService->handleUpload($request->validated(), 'image');

            // 回傳 log_upload id 給前端（資料送出時前端再送入給主資料表owner_id配對）
            return respondCollection($result);
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }
}
