<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\FaqRequest;
use App\Http\Resources\Content\FaqCollection;
use App\Http\Resources\Content\FaqResource;
use App\Services\Content\FaqService;

class FaqController extends Controller
{
    public function __construct(
        protected FaqService $faqService
    ) {}

    public function index(FaqRequest $request)
    {
        try {
            [
                'result' => $result,
                'refer' => $refer,
            ] = $this->faqService->list($request->validated());

            return respondCollection(FaqCollection::make($result), compact('refer'));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function show(FaqRequest $request)
    {
        try {
            $id = $request->validated('id');

            return respondSuccess(FaqResource::make($this->faqService->show($id)));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function store(FaqRequest $request)
    {
        try {
            // 取得並過濾參數
            $this->faqService->store($request->filteredValidated());

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function update(FaqRequest $request)
    {
        try {
            $id = $request->validated('id');
            // 取得並過濾參數
            $this->faqService->update($request->filteredValidated(), $id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function destroy(FaqRequest $request)
    {
        try {
            $id = $request->validated('id');
            $this->faqService->destroy($id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }
}
