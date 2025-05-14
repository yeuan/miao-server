<?php

namespace App\Http\Resources;

use App\Traits\ResourceTrait;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SuccessCollection extends ResourceCollection
{
    use ResourceTrait;

    protected array $withoutFields = [];

    private bool $hide = true;

    public function toArray($request): array
    {
        return [
            'data' => $this->collection->map(function ($item) {
                return $this->hide
                ? collect($item)->except($this->withoutFields)->all()
                : collect($item)->only($this->withoutFields)->all();
            }),
        ];
    }

    // 自定義分頁資訊
    public function paginationInformation($request, $paginated, $default): array
    {
        return [
            'meta' => [
                'page' => $paginated['current_page'],
                'per_page' => $paginated['per_page'],
                'total' => $paginated['total'],
                'last_page' => $paginated['last_page'],
            ],
        ];
    }
}
