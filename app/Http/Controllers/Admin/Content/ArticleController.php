<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\ArticleRequest;
use App\Http\Resources\Content\ArticleCollection;
use App\Http\Resources\Content\ArticleResource;
use App\Services\Content\ArticleService;

class ArticleController extends Controller
{
    public function __construct(
        protected ArticleService $articleService
    ) {}

    public function index(ArticleRequest $request)
    {
        try {
            [
                'result' => $result,
                'refer' => $refer,
            ] = $this->articleService->list($request->validated());

            return respondCollection(ArticleCollection::make($result), compact('refer'));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function show(ArticleRequest $request)
    {
        try {
            $id = $request->validated('id');

            return respondSuccess(ArticleResource::make($this->articleService->show($id)));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function store(ArticleRequest $request)
    {
        try {
            // 取得並過濾參數
            $this->articleService->store($request->filteredValidated());

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function update(ArticleRequest $request)
    {
        try {
            $id = $request->validated('id');
            // 取得並過濾參數
            $this->articleService->update($request->filteredValidated(), $id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function destroy(ArticleRequest $request)
    {
        try {
            $id = $request->validated('id');
            $this->articleService->destroy($id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }
}
