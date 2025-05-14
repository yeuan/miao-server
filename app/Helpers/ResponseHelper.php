<?php

use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

if (! function_exists('respondSuccess')) {

    /**
     * 回傳單筆成功資料，若未傳入資料則預設為空陣列，支援 Resource / array / Arrayable，可附加 meta。
     */
    function respondSuccess(array|Arrayable|null|JsonResource $data = null, array $additional = []): JsonResource
    {
        $resource = $data instanceof JsonResource
        ? $data
        : new SuccessResource($data ?? []);

        return $resource->additional($additional);
    }
}

if (! function_exists('respondCollection')) {

    /**
     * 回傳多筆成功資料（含分頁）,可接受 ResourceCollection、array 或 LengthAwarePaginator，可附加 meta。
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    function respondCollection(array|LengthAwarePaginator|JsonResource $data, array $additional = []): JsonResource
    {
        $resource = $data instanceof JsonResource
        ? $data
        : SuccessResource::collection($data);

        return $resource->additional($additional);
    }
}

if (! function_exists('respondError')) {

    /**
     * 回傳錯誤格式,需指定錯誤代碼
     */
    function respondError(string $code = 'SYSTEM_FAILED', ?\Throwable $e = null): JsonResource
    {
        return ErrorResource::respond($code, $e);
    }
}
