<?php

namespace App\Http\Resources\Content;

use App\Http\Resources\Manager\TagResource;
use App\Http\Resources\SuccessResource;

class BannerResource extends SuccessResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'owner_type' => $this->owner_type,
            'owner_id' => $this->owner_id,
            'type' => $this->type,
            'image' => $this->image,
            'image_app' => $this->image_app,
            'url' => $this->url,
            'link_type' => $this->link_type,
            'module_id' => $this->module_id,
            'object_id' => $this->object_id,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
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
