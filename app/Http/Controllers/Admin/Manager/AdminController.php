<?php

namespace App\Http\Controllers\Admin\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Manager\AdminRequest;
use App\Http\Resources\Manager\AdminCollection;
use App\Http\Resources\Manager\AdminResource;
use App\Services\Manager\AdminService;

class AdminController extends Controller
{
    public function __construct(
        protected AdminService $adminService
    ) {}

    public function index(AdminRequest $request)
    {
        try {
            [
                'result' => $result,
                'refer' => $refer,
            ] = $this->adminService->list($request->validated());

            return respondCollection(AdminCollection::make($result), compact('refer'));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function show(AdminRequest $request)
    {
        try {
            $id = $request->validated('id');

            return respondSuccess(AdminResource::make($this->adminService->show($id)));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function store(AdminRequest $request)
    {
        try {
            $this->adminService->store($request->validated());

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function update(AdminRequest $request)
    {
        try {
            $id = $request->validated('id');
            $this->adminService->update($request->validated(), $id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function destroy(AdminRequest $request)
    {
        try {
            $id = $request->validated('id');
            $this->adminService->destroy($id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }
}
