<?php

namespace App\Http\Resources\Content;

use App\Http\Resources\Manager\TagResource;
use App\Http\Resources\SuccessResource;

class FaqResource extends SuccessResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'owner_type' => $this->owner_type,
            'owner_id' => $this->owner_id,
            'category_id' => $this->category_id,
            'category_name' => optional($this->whenLoaded('category'))->name ?? '',
            'slug' => $this->slug,
            'question' => $this->question,
            'answer' => $this->answer,
            'flag' => $this->flag,
            'sort' => $this->sort,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            $this->mergeWhen($this->relationLoaded('tags'), [
                'tags' => TagResource::collection($this->tags),
            ]),
        ];
    }
}
