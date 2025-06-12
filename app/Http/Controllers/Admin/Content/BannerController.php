<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\BannerRequest;
use App\Http\Resources\Content\BannerCollection;
use App\Http\Resources\Content\BannerResource;
use App\Services\Content\BannerService;

class BannerController extends Controller
{
    public function __construct(
        protected BannerService $bannerService
    ) {}

    public function index(BannerRequest $request)
    {
        try {
            [
                'result' => $result,
                'refer' => $refer,
            ] = $this->bannerService->list($request->validated());

            return respondCollection(BannerCollection::make($result), compact('refer'));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function show(BannerRequest $request)
    {
        try {
            $id = $request->validated('id');

            return respondSuccess(BannerResource::make($this->bannerService->show($id)));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function store(BannerRequest $request)
    {
        try {
            // 取得並過濾參數
            $this->bannerService->store($request->filteredValidated());

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function update(BannerRequest $request)
    {
        try {
            $id = $request->validated('id');
            // 取得並過濾參數
            $this->bannerService->update($request->filteredValidated(), $id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function destroy(BannerRequest $request)
    {
        try {
            $id = $request->validated('id');
            $this->bannerService->destroy($id);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }
}
