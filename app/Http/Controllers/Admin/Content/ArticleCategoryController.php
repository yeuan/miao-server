<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\ArticleCategoryRequest;
use App\Services\Content\ArticleCategoryService;

class ArticleCategoryController extends Controller
{
    public function __construct(
        protected ArticleCategoryService $articleCategoryService
    ) {}

    public function index(ArticleCategoryRequest $request)
    {
        try {
            [
                'result' => $result,
                'refer' => $refer,
            ] = $this->articleCategoryService->list($request->validated());

            return respondCollection($result, compact('refer'));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function show(ArticleCategoryRequest $request)
    {
        try {
            $id = $request->validated('id');

            return respondSuccess($this->articleCategoryService->show($id));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function store(ArticleCategoryRequest $request)
    {
        try {
            // 取得並過濾參數
            $this->articleCategoryService->store($request->filteredValidated());

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function update(ArticleCategoryRequest $request)
    {
        try {
            $id = $request->validated('id');
            // 取得並過濾參數
            $this->articleCategoryService->update($request->filteredValidated(), $id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function destroy(ArticleCategoryRequest $request)
    {
        try {
            $id = $request->validated('id');
            $this->articleCategoryService->destroy($id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }
}
