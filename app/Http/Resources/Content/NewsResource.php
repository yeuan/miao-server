<?php

namespace App\Http\Resources\Content;

use App\Http\Resources\Manager\TagResource;
use App\Http\Resources\SuccessResource;

class NewsResource extends SuccessResource
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
            'title' => $this->title,
            'cover' => $this->cover,
            'cover_app' => $this->cover_app,
            'summary' => $this->summary,
            'content' => $this->content,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'views' => $this->views,
            'likes' => $this->likes,
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
