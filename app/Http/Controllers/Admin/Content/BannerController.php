<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\BannerRequest;
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

            return respondCollection($result, compact('refer'));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function show(BannerRequest $request)
    {
        try {
            $id = $request->validated('id');

            return respondSuccess($this->bannerService->show($id));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function store(BannerRequest $request)
    {
        try {
            $this->bannerService->store($request->validated());

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function update(BannerRequest $request, $id)
    {
        try {
            $id = $request->validated('id');
            $this->bannerService->update($request->validated(), $id);

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
