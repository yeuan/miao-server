<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\NoticeRequest;
use App\Http\Resources\Content\NoticeCollection;
use App\Http\Resources\Content\NoticeResource;
use App\Services\Content\NoticeService;

class NoticeController extends Controller
{
    public function __construct(
        protected NoticeService $noticeService
    ) {}

    public function index(NoticeRequest $request)
    {
        try {
            [
                'result' => $result,
                'refer' => $refer,
            ] = $this->noticeService->list($request->validated());

            return respondCollection(NoticeCollection::make($result), compact('refer'));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function show(NoticeRequest $request)
    {
        try {
            $id = $request->validated('id');

            return respondSuccess(NoticeResource::make($this->noticeService->show($id)));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function store(NoticeRequest $request)
    {
        try {
            // 取得並過濾參數
            $this->noticeService->store($request->filteredValidated());

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function update(NoticeRequest $request, $id)
    {
        try {
            $id = $request->validated('id');
            // 取得並過濾參數
            $this->noticeService->update($request->filteredValidated(), $id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function destroy(NoticeRequest $request)
    {
        try {
            $id = $request->validated('id');
            $this->noticeService->destroy($id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }
}
