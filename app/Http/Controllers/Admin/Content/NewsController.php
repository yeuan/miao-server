<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\NewsRequest;
use App\Http\Resources\Content\NewsCollection;
use App\Http\Resources\Content\NewsResource;
use App\Services\Content\NewsService;

class NewsController extends Controller
{
    public function __construct(
        protected NewsService $newsService
    ) {}

    public function index(NewsRequest $request)
    {
        try {
            [
                'result' => $result,
                'refer' => $refer,
            ] = $this->newsService->list($request->validated());

            return respondCollection(NewsCollection::make($result), compact('refer'));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function show(NewsRequest $request)
    {
        try {
            $id = $request->validated('id');

            return respondSuccess(NewsResource::make($this->newsService->show($id)));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function store(NewsRequest $request)
    {
        try {
            // 取得並過濾參數
            $this->newsService->store($request->filteredValidated());

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function update(NewsRequest $request)
    {
        try {
            $id = $request->validated('id');
            // 取得並過濾參數
            $this->newsService->update($request->filteredValidated(), $id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function destroy(NewsRequest $request)
    {
        try {
            $id = $request->validated('id');
            $this->newsService->destroy($id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }
}
