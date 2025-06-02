<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\System\UploadSettingRequest;
use App\Services\System\UploadSettingService;

class UploadSettingsController extends Controller
{
    public function __construct(
        protected UploadSettingService $uploadSettingService
    ) {}

    public function index(UploadSettingRequest $request)
    {
        try {
            [
                'result' => $result,
                'refer' => $refer,
            ] = $this->uploadSettingService->list($request->validated());

            return respondCollection($result, compact('refer'));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function show(UploadSettingRequest $request)
    {
        try {
            $id = $request->validated('id');

            return respondSuccess($this->uploadSettingService->show($id));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function update(UploadSettingRequest $request, $id)
    {
        try {
            $id = $request->validated('id');
            $this->uploadSettingService->update($request->validated(), $id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }
}
