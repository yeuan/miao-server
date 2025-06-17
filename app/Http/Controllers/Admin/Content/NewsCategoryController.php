<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\NewsCategoryRequest;
use App\Services\Content\NewsCategoryService;

class NewsCategoryController extends Controller
{
    public function __construct(
        protected NewsCategoryService $newsCategoryService
    ) {}

    public function index(NewsCategoryRequest $request)
    {
        try {
            [
                'result' => $result,
                'refer' => $refer,
            ] = $this->newsCategoryService->list($request->validated());

            return respondCollection($result, compact('refer'));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function show(NewsCategoryRequest $request)
    {
        try {
            $id = $request->validated('id');

            return respondSuccess($this->newsCategoryService->show($id));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function store(NewsCategoryRequest $request)
    {
        try {
            // 取得並過濾參數
            $this->newsCategoryService->store($request->filteredValidated());

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function update(NewsCategoryRequest $request)
    {
        try {
            $id = $request->validated('id');
            // 取得並過濾參數
            $this->newsCategoryService->update($request->filteredValidated(), $id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function destroy(NewsCategoryRequest $request)
    {
        try {
            $id = $request->validated('id');
            $this->newsCategoryService->destroy($id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }
}
