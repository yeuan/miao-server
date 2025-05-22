<?php

namespace App\Http\Controllers\Admin\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Manager\AdminNavRequest;
use App\Services\Manager\AdminNavService;

class AdminNavController extends Controller
{
    public function __construct(
        protected AdminNavService $adminNavService
    ) {}

    public function index(AdminNavRequest $request)
    {
        try {
            [
                'result' => $result,
                'refer' => $refer,
            ] = $this->adminNavService->list($request->validated());

            return respondCollection($result, compact('refer'));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function show(AdminNavRequest $request)
    {
        try {
            $id = $request->validated('id');

            return respondSuccess($this->adminNavService->show($id));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function store(AdminNavRequest $request)
    {
        try {
            $this->adminNavService->store($request->validated());

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function update(AdminNavRequest $request)
    {
        try {
            $id = $request->validated('id');
            $this->adminNavService->update($request->validated(), $id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function destroy(AdminNavRequest $request)
    {
        try {
            $id = $request->validated('id');
            $this->adminNavService->destroy($id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function sidebar()
    {
        try {
            return respondSuccess($this->adminNavService->getSidebar());
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function permission()
    {
        try {
            return respondSuccess(requestOutParam('permission', []));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }
}
