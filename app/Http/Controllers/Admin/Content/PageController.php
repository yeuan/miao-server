<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\PageRequest;
use App\Http\Resources\Content\PageCollection;
use App\Http\Resources\Content\PageResource;
use App\Services\Content\PageService;

class PageController extends Controller
{
    public function __construct(
        protected PageService $pageService
    ) {}

    public function index(PageRequest $request)
    {
        try {
            [
                'result' => $result,
                'refer' => $refer,
            ] = $this->pageService->list($request->validated());

            return respondCollection(PageCollection::make($result), compact('refer'));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function show(PageRequest $request)
    {
        try {
            $id = $request->validated('id');

            return respondSuccess(PageResource::make($this->pageService->show($id)));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function store(PageRequest $request)
    {
        try {
            // 取得並過濾參數
            $this->pageService->store($request->filteredValidated());

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function update(PageRequest $request, $id)
    {
        try {
            $id = $request->validated('id');
            // 取得並過濾參數
            $this->pageService->update($request->filteredValidated(), $id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function destroy(PageRequest $request)
    {
        try {
            $id = $request->validated('id');
            $this->pageService->destroy($id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }
}
