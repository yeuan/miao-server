<?php

namespace App\Http\Controllers\Admin\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Manager\TagRequest;
use App\Services\Manager\TagService;

class TagController extends Controller
{
    public function __construct(
        protected TagService $tagService
    ) {}

    public function index(TagRequest $request)
    {
        try {
            [
                'result' => $result,
                'refer' => $refer,
            ] = $this->tagService->list($request->validated());

            return respondCollection($result, compact('refer'));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function show(TagRequest $request)
    {
        try {
            $id = $request->validated('id');

            return respondSuccess($this->tagService->show($id));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function store(TagRequest $request)
    {
        try {
            // 取得並過濾參數
            $this->tagService->store($request->filteredValidated());

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function update(TagRequest $request, $id)
    {
        try {
            $id = $request->validated('id');
            // 取得並過濾參數
            $this->tagService->update($request->filteredValidated(), $id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function destroy(TagRequest $request)
    {
        try {
            $id = $request->validated('id');
            $this->tagService->destroy($id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }
}
