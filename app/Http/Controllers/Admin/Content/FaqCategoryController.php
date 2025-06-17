<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\FaqCategoryRequest;
use App\Services\Content\FaqCategoryService;

class FaqCategoryController extends Controller
{
    public function __construct(
        protected FaqCategoryService $faqCategoryService
    ) {}

    public function index(FaqCategoryRequest $request)
    {
        try {
            [
                'result' => $result,
                'refer' => $refer,
            ] = $this->faqCategoryService->list($request->validated());

            return respondCollection($result, compact('refer'));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function show(FaqCategoryRequest $request)
    {
        try {
            $id = $request->validated('id');

            return respondSuccess($this->faqCategoryService->show($id));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function store(FaqCategoryRequest $request)
    {
        try {
            // 取得並過濾參數
            $this->faqCategoryService->store($request->filteredValidated());

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function update(FaqCategoryRequest $request)
    {
        try {
            $id = $request->validated('id');
            // 取得並過濾參數
            $this->faqCategoryService->update($request->filteredValidated(), $id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function destroy(FaqCategoryRequest $request)
    {
        try {
            $id = $request->validated('id');
            $this->faqCategoryService->destroy($id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }
}
