<?php

namespace App\Http\Resources;

use App\Enums\HttpStatus;
use App\Traits\FormatTrait;
use App\Traits\ResourceTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class SuccessResource extends JsonResource
{
    use FormatTrait, ResourceTrait;

    public function __construct($resource = [])
    {
        // 僅限防呆：若誤將 Exception 傳入 SuccessResource，應使用 ErrorResource
        if ($resource instanceof \Throwable) {
            throw new \RuntimeException('INVALID_RESOURCE_USAGE', 0, $resource);
        }

        parent::__construct($resource);
        $this->setStatusCode(HttpStatus::SUCCESS->value);
    }

    public function toArray($request): array
    {
        $data = parent::toArray($request);

        // payload欄位 強制轉換成物件
        // if (isset($data['payload']) && ! is_object($data['payload'])) {
        //     $data['payload'] = (object) $data['payload'];
        // }
        return $data;
    }

    /**
     * 自動回傳自定義 SuccessCollection
     */
    public static function collection($resource)
    {
        return tap(new SuccessCollection($resource), function ($collection) {
            $collection->collects = static::class;
        });
    }
}
