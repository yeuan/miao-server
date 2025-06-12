<?php

namespace App\Http\Controllers\Admin\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Manager\ModuleRequest;
use App\Services\Manager\ModuleService;

class ModuleController extends Controller
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
