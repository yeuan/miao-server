<?php

namespace App\Http\Controllers\Admin\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Manager\AdminRoleRequest;
use App\Services\Manager\AdminRoleService;

class AdminRoleController extends Controller
{
    public function __construct(
        protected AdminRoleService $adminRoleService
    ) {}

    public function index(AdminRoleRequest $request)
    {
        try {
            [
                'result' => $result,
                'refer' => $refer,
            ] = $this->adminRoleService->list($request->validated());

            return respondCollection($result, compact('refer'));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function show(AdminRoleRequest $request)
    {
        try {
            $id = $request->validated('id');

            return respondSuccess($this->adminRoleService->show($id));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function store(AdminRoleRequest $request)
    {
        try {
            // 取得並過濾參數
            $this->adminRoleService->store($request->filteredValidated());

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function update(AdminRoleRequest $request, $id)
    {
        try {
            $id = $request->validated('id');
            // 取得並過濾參數
            $this->adminRoleService->update($request->filteredValidated(), $id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function destroy(AdminRoleRequest $request)
    {
        try {
            $id = $request->validated('id');
            $this->adminRoleService->destroy($id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }
}
