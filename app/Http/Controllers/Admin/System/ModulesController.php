<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\System\ModuleRequest;
use App\Services\System\ModuleService;

class ModulesController extends Controller
{
    public function __construct(
        protected ModuleService $moduleService
    ) {}

    public function index(ModuleRequest $request)
    {
        try {
            [
                'result' => $result,
                'refer' => $refer,
            ] = $this->moduleService->list($request->validated());

            return respondCollection($result, compact('refer'));
        } catch (\Throwable $e) {

            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function update(ModuleRequest $request, $id)
    {
        try {
            $id = $request->validated('id');
            $this->moduleService->update($request->validated(), $id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function active()
    {
        try {
            return respondSuccess($this->moduleService->getActiveModules());
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }
}
